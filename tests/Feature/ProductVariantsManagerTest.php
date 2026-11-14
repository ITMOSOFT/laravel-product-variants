<?php

namespace ItmoSoft\ProductVariants\Tests\Feature;

use ItmoSoft\ProductVariants\ProductVariantsManager;
use ItmoSoft\ProductVariants\Tests\Support\Product;
use ItmoSoft\ProductVariants\Tests\TestCase;

class ProductVariantsManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate')->run();
    }

    public function test_variant_can_be_created_with_options_and_price(): void
    {
        $product = Product::create(['name' => 'T-Shirt']);

        $variant = app(ProductVariantsManager::class)->create($product, [
            'name' => 'Large / Black',
            'sku' => 'TS-L-BLK',
            'options' => ['size' => 'L', 'color' => 'Black'],
            'price' => 24.99,
            'is_default' => true,
        ]);

        $this->assertSame('TS-L-BLK', $variant->sku);
        $this->assertSame('L', $variant->options['size']);
        $this->assertSame('24.99', $variant->price);
        $this->assertTrue($product->defaultVariant()->is($variant));
    }

    public function test_setting_default_variant_unsets_previous_default(): void
    {
        $product = Product::create(['name' => 'Shoes']);
        $manager = app(ProductVariantsManager::class);

        $first = $manager->create($product, ['name' => 'Size 8', 'is_default' => true]);
        $second = $manager->create($product, ['name' => 'Size 9']);

        $manager->setDefault($second);

        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($second->fresh()->is_default);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        $product = Product::create(['name' => 'Cap']);
        $manager = app(ProductVariantsManager::class);

        $manager->create($product, ['name' => 'Red', 'sku' => 'CAP-RED']);

        $this->expectException(\InvalidArgumentException::class);
        $manager->create($product, ['name' => 'Blue', 'sku' => 'CAP-RED']);
    }
}
