=== Dynamic Tag to Category Products ===
Contributors: codedletter
Tags: woocommerce, products, categories, tags, bulk edit
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.1
License: MIT
License URI: https://opensource.org/license/mit

Append a chosen WooCommerce product tag to every product in a chosen category.

== Description ==

Dynamic Tag to Category Products adds a focused tool under Tools > Add Tag to
Category. Enter a WooCommerce product-category name or slug and a product-tag
name. The plugin appends that tag to matching products without replacing their
existing tags.

The operation is capability checked, nonce protected, sanitized, escaped, and
processed in bounded batches.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate WooCommerce.
3. Activate Dynamic Tag to Category Products.
4. Open Tools > Add Tag to Category.

== Changelog ==

= 1.1.1 =
* Include scheduled products in category updates.
* Bulk-load existing tag relationships for each product batch.

= 1.1.0 =
* Publish the secured Coded Letter release.
* Add nonce and capability checks.
* Add explicit validation and operation totals.
* Process matching product IDs in bounded batches.

= 1.0.0 =
* Initial prototype.
