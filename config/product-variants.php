<?php

return [
    'table_prefix' => 'itmosoft_',
    'models' => [
        'variant' => ItmoSoft\ProductVariants\Models\ProductVariant::class,
        'by_product' => [],
    ],
    'allow_duplicate_skus' => false,
    'sku' => [
        'required' => false,
        'max_length' => 100,
    ],
    // Existing standalone behavior remains enabled unless an integration opts out.
    'migrations' => [
        'enabled' => env('PRODUCT_VARIANTS_MIGRATIONS_ENABLED', true),
    ],
];
