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

This Coded Letter release develops and secures George Lazaridis's original
prototype. Its exact pre-migration history is preserved on the
[`legacy/ys-sudo-main`](https://github.com/coded-letter/dynamic-tag-category-plugin/tree/legacy/ys-sudo-main)
branch.

## License

MIT. See [LICENSE](LICENSE).
