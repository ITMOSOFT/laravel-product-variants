<?php

namespace ItmoSoft\ProductVariants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use ItmoSoft\ProductVariants\Contracts\ProductVariantContract;
use ItmoSoft\ProductVariants\Models\ProductVariant;

class ProductVariantsManager
{
    public function create(Model $product, array $attributes): ProductVariantContract
    {
        $model = $this->variantModelForProduct($product);
        $this->validateSku($attributes['sku'] ?? null, null, $model);

        return DB::transaction(function () use ($product, $attributes, $model): ProductVariantContract {
            $variant = new $model();
            $variant->fill($this->allowedAttributes($attributes));

            if ((bool) ($attributes['is_default'] ?? false)) {
                $product->variants()->update(['is_default' => false]);
            }

            $product->variants()->save($variant);

            return $variant->refresh();
        });
    }

    public function update(ProductVariantContract $variant, array $attributes): ProductVariantContract
    {
        if (array_key_exists('sku', $attributes)) {
            $this->validateSku($attributes['sku'], $variant->getKey(), $variant::class);
        }

        return DB::transaction(function () use ($variant, $attributes): ProductVariantContract {
            if ((bool) ($attributes['is_default'] ?? false)) {
                $variant->product()->firstOrFail()
                    ->variants()
                    ->whereKeyNot($variant->getKey())
                    ->update(['is_default' => false]);
            }

            $variant->fill($this->allowedAttributes($attributes));
            $variant->save();

            return $variant->refresh();
        });
    }

    public function setDefault(ProductVariantContract $variant): ProductVariantContract
    {
        return DB::transaction(function () use ($variant): ProductVariantContract {
            $product = $variant->product()->lockForUpdate()->firstOrFail();

            $product->variants()
                ->whereKeyNot($variant->getKey())
                ->update(['is_default' => false]);

            $variant->forceFill(['is_default' => true])->save();

            return $variant->refresh();
        });
    }

    public function activate(ProductVariantContract $variant): ProductVariantContract
    {
        $variant->forceFill(['is_active' => true])->save();

        return $variant->refresh();
    }

    public function deactivate(ProductVariantContract $variant): ProductVariantContract
    {
        $variant->forceFill(['is_active' => false])->save();

        return $variant->refresh();
    }

    public function delete(ProductVariantContract $variant): void
    {
        $variant->delete();
    }

    private function validateSku(mixed $sku, mixed $ignoreId = null, ?string $modelClass = null): void
    {
        $required = (bool) config('product-variants.sku.required', false);

        if ($required && (! is_string($sku) || trim($sku) === '')) {
            throw new InvalidArgumentException('A SKU is required for this product variant.');
        }

        if ($sku !== null && ! is_string($sku)) {
            throw new InvalidArgumentException('SKU must be a string or null.');
        }

        if (is_string($sku)) {
            $sku = trim($sku);
            $max = (int) config('product-variants.sku.max_length', 100);

            if (mb_strlen($sku) > $max) {
                throw new InvalidArgumentException("SKU cannot exceed {$max} characters.");
            }

            if ($sku !== '' && ! (bool) config('product-variants.allow_duplicate_skus', false)) {
                $modelClass ??= config('product-variants.models.variant', ProductVariant::class);
                $query = $modelClass::query()->where('sku', $sku);

                if ($ignoreId !== null) {
                    $query->whereKeyNot($ignoreId);
                }

                if ($query->exists()) {
                    throw new InvalidArgumentException('The SKU has already been assigned to another variant.');
                }
            }
        }
    }


    /** Resolve an optional model override for this product class. */
    private function variantModelForProduct(Model $product): string
    {
        foreach (config('product-variants.models.by_product', []) as $productClass => $variantClass) {
            if (is_string($productClass) && is_a($product, $productClass)) {
                return $variantClass;
            }
        }

        return config('product-variants.models.variant', ProductVariant::class);
    }

    private function allowedAttributes(array $attributes): array
    {
        $allowed = [
            'sku', 'barcode', 'name', 'options', 'price', 'compare_at_price',
            'cost', 'weight', 'is_active', 'is_default', 'sort_order',
        ];

        $filtered = array_intersect_key($attributes, array_flip($allowed));

        if (isset($filtered['sku']) && is_string($filtered['sku'])) {
            $filtered['sku'] = trim($filtered['sku']) ?: null;
        }

        if (isset($filtered['barcode']) && is_string($filtered['barcode'])) {
            $filtered['barcode'] = trim($filtered['barcode']) ?: null;
        }

        return $filtered;
    }
}
