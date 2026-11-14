# ITMOSOFT Laravel Product Variants

Product variants, SKUs, option values and variant-level price overrides for Laravel 12 and 13.

## Features

- Attach variants to any Eloquent product model through a polymorphic relation.
- SKU and barcode fields.
- JSON option values such as size, color, material and capacity.
- Optional price, compare-at price, cost and weight overrides.
- Active/inactive state and sort order.
- One default variant per product through manager operations.
- Configurable variant model and table prefix.
- Laravel package auto-discovery and migrations.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-product-variants
php artisan vendor:publish --tag=product-variants-config
php artisan migrate
```

## Add variants to your product model

```php
use Illuminate\Database\Eloquent\Model;
use ItmoSoft\ProductVariants\Concerns\HasProductVariants;

class Product extends Model
{
    use HasProductVariants;
}
```

## Create variants

```php
use ItmoSoft\ProductVariants\ProductVariantsManager;

$variant = app(ProductVariantsManager::class)->create($product, [
    'name' => 'Large / Black',
    'sku' => 'TSHIRT-L-BLK',
    'barcode' => '1234567890123',
    'options' => [
        'size' => 'Large',
        'color' => 'Black',
    ],
    'price' => 29.99,
    'compare_at_price' => 34.99,
    'cost' => 12.50,
    'weight' => 0.350,
    'is_default' => true,
]);
```

## Query variants

```php
$product->variants;
$product->activeVariants;
$product->defaultVariant();
```

## Update and manage variants

```php
$manager = app(ProductVariantsManager::class);

$manager->update($variant, ['price' => 27.99]);
$manager->setDefault($variant);
$manager->deactivate($variant);
$manager->activate($variant);
$manager->delete($variant);
```

## Configuration

Publish `product-variants-config` to customize:

- `table_prefix`
- `models.variant`
- `allow_duplicate_skus`
- `sku.required`
- `sku.max_length`

SKU uniqueness is global at the database table level in this initial release. If your store needs SKU uniqueness per tenant or per vendor, customize the migration and validation policy accordingly.

## Testing

```bash
composer install
composer validate --strict
composer test
```

## Scope and integration boundaries

This package manages variant identity, option data and optional variant-level commercial values. It does not create product attributes or option definitions, generate every possible option combination, manage inventory/stock, reserve stock, calculate final prices, manage images, or expose HTTP routes/controllers. Those responsibilities belong to the catalog, attributes, inventory, pricing and storefront layers.

The application must validate option names and values against its product attribute rules. Do not trust client-supplied prices, costs, SKU ownership or variant IDs without authorization and server-side validation.

## License

MIT.
