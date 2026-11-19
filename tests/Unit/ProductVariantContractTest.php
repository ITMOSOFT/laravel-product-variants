<?php

namespace ItmoSoft\ProductVariants\Tests\Unit;

use ItmoSoft\ProductVariants\Contracts\ProductVariantContract;
use ItmoSoft\ProductVariants\Models\ProductVariant;
use ItmoSoft\ProductVariants\Tests\TestCase;

class ProductVariantContractTest extends TestCase
{
    public function test_default_variant_model_implements_the_shared_contract(): void
    {
        $this->assertInstanceOf(ProductVariantContract::class, new ProductVariant());
    }
}
