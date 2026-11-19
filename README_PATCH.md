# Product Variants v1.0.1 prerequisite patch

Apply the included files to `ITMOSOFT/laravel-product-variants`:

- Add `src/Contracts/ProductVariantContract.php`.
- Replace `src/Models/ProductVariant.php` so the default polymorphic model implements the contract.
- Replace `src/ProductVariantsManager.php` so its create/update/default/activate/deactivate/delete methods accept and return the contract, and `create()` resolves optional `models.by_product` model overrides before falling back to the configured default model.
- Replace `config/product-variants.php` and `src/ProductVariantsServiceProvider.php` to add `models.by_product` and `migrations.enabled` (default `true`).
- Add `tests/Unit/ProductVariantContractTest.php` to verify the default model implements the contract.

This is additive for standalone users: the default model, table prefix, migration behavior and manager operations remain the same. The manager can now also work with a compatible host package model selected by product class in `models.by_product`, whose `product()` method returns an Eloquent relation, including Product Catalog's existing `BelongsTo` relation. The default model remains unchanged for all unmapped product classes.

Run the complete Product Variants package PHPUnit suite and the Laravel 12/13 CI matrix before publishing v1.0.1. This patch has only received PHP syntax validation in this delivery; its PHPUnit suite has not been run here.
