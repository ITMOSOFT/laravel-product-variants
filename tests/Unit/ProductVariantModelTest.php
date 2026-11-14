<?php

namespace ItmoSoft\ProductVariants\Tests\Unit;

use ItmoSoft\ProductVariants\Models\ProductVariant;
use ItmoSoft\ProductVariants\Tests\TestCase;

class ProductVariantModelTest extends TestCase
{
    public function test_variant_model_uses_configured_table_prefix(): void
    {
        $this->assertSame('itmosoft_product_variants', (new ProductVariant())->getTable());
    }
}
