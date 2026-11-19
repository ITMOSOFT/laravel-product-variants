# Product Variants v1.0.1

## Release title

`v1.0.1 — Add host-model contracts and per-product variant model resolution`

## Release description

This release preserves the existing standalone Product Variants behavior while allowing compatible host packages to provide their own canonical variant model.

### Added
- `ProductVariantContract` for variant models backed by different product packages.
- `models.by_product` configuration to select a variant model for specific product model classes.
- `models.by_product` configuration for selecting a compatible variant model by product class.
- `migrations.enabled` configuration, defaulting to `true`, so integrations can opt out of the standalone variants table migration.
- Unit coverage confirming the default model implements the shared contract.

### Compatibility
- The default model remains `ItmoSoft\\ProductVariants\\Models\\ProductVariant`.
- The default table prefix remains `itmosoft_`.
- Standalone migrations remain enabled by default.
- Existing polymorphic product models continue using the default model unless explicitly mapped.
