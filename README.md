# Dynamic Tag to Category Products

A minimal WooCommerce admin tool that appends a chosen product tag to every
product in a chosen category.

## Usage

1. Install and activate WooCommerce.
2. Install and activate this plugin.
3. Open **Tools → Add Tag to Category**.
4. Enter a product-category name or slug and a product-tag name.
5. Select **Apply tag**.

Existing tags are preserved. Products that already have the chosen tag are
skipped.

## Security and compatibility

- Requires WordPress 6.4+, PHP 7.4+, and WooCommerce.
- Restricts the tool to users with the `manage_woocommerce` capability.
- Protects the bulk update with a WordPress nonce.
- Sanitizes submitted values and escapes admin output.
- Processes product IDs in bounded batches.

## Provenance

This Coded Letter release develops and secures the original
[`Ys-sudo/dynamic-tag-category-plugin`](https://github.com/Ys-sudo/dynamic-tag-category-plugin)
prototype by George Lazaridis.

## License

MIT. See [LICENSE](LICENSE).
