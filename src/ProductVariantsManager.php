<?php

namespace ItmoSoft\ProductVariants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use ItmoSoft\ProductVariants\Models\ProductVariant;

class ProductVariantsManager
{
    public function create(Model $product, array $attributes): ProductVariant
    {
        $this->validateSku($attributes['sku'] ?? null);

        return DB::transaction(function () use ($product, $attributes): ProductVariant {
            $model = config('product-variants.models.variant', ProductVariant::class);
            $variant = new $model();
            $variant->fill($this->allowedAttributes($attributes));

            if ((bool) ($attributes['is_default'] ?? false)) {
                $product->variants()->update(['is_default' => false]);
            }

            $product->variants()->save($variant);

            return $variant->refresh();
        });
    }

    public function update(ProductVariant $variant, array $attributes): ProductVariant
    {
        if (array_key_exists('sku', $attributes)) {
            $this->validateSku($attributes['sku'], $variant->getKey());
        }

        return DB::transaction(function () use ($variant, $attributes): ProductVariant {
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

    public function setDefault(ProductVariant $variant): ProductVariant
    {
        return DB::transaction(function () use ($variant): ProductVariant {
            $product = $variant->product()->lockForUpdate()->firstOrFail();

            $product->variants()
                ->whereKeyNot($variant->getKey())
                ->update(['is_default' => false]);

            $variant->forceFill(['is_default' => true])->save();

            return $variant->refresh();
        });
    }

    public function activate(ProductVariant $variant): ProductVariant
    {
        $variant->forceFill(['is_active' => true])->save();

        return $variant->refresh();
    }

    public function deactivate(ProductVariant $variant): ProductVariant
    {
        $variant->forceFill(['is_active' => false])->save();

        return $variant->refresh();
    }

    public function delete(ProductVariant $variant): void
    {
        $variant->delete();
    }

    private function validateSku(mixed $sku, mixed $ignoreId = null): void
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
                $model = config('product-variants.models.variant', ProductVariant::class);
                $query = $model::query()->where('sku', $sku);

                if ($ignoreId !== null) {
                    $query->whereKeyNot($ignoreId);
                }

                if ($query->exists()) {
                    throw new InvalidArgumentException('The SKU has already been assigned to another variant.');
                }
            }
        }
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
