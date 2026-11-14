# Release checklist

1. Run `composer validate --strict`.
2. Run `composer test` on PHP 8.3 with Laravel 12 and Laravel 13.
3. Review `git diff` and ensure no `.env`, `vendor/`, or local cache files are included.
4. Commit and push `main`.
5. Create and push the annotated version tag:
   ```bash
   git tag -a v1.0.0 -m "Initial release"
   git push origin v1.0.0
   ```
6. Create a GitHub Release titled `v1.0.0 — Initial Release`.
7. Submit `https://github.com/ITMOSOFT/laravel-product-variants` to Packagist and configure its GitHub webhook.
8. Verify installation with `composer require itmosoft/laravel-product-variants`.

Do not publish until both Laravel matrix jobs pass.
