<?php

namespace ItmoSoft\ProductVariants\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ItmoSoft\ProductVariants\Models\ProductVariant;

trait HasProductVariants
{
    public function variants(): MorphMany
    {
        $model = config('product-variants.models.variant', ProductVariant::class);

        return $this->morphMany($model, 'product');
    }

    public function activeVariants(): MorphMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function defaultVariant(): ?ProductVariant
    {
        return $this->variants()->where('is_default', true)->first();
    }
}
