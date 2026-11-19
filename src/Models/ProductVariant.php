<?php

namespace ItmoSoft\ProductVariants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ItmoSoft\ProductVariants\Contracts\ProductVariantContract;

class ProductVariant extends Model implements ProductVariantContract
{
    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTable(): string
    {
        return $this->table ?: config('product-variants.table_prefix', 'itmosoft_').'product_variants';
    }
}
