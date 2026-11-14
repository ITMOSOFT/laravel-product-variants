<?php

return [
    'table_prefix' => 'itmosoft_',
    'models' => [
        'variant' => ItmoSoft\ProductVariants\Models\ProductVariant::class,
    ],
    'allow_duplicate_skus' => false,
    'sku' => [
        'required' => false,
        'max_length' => 100,
    ],
];
