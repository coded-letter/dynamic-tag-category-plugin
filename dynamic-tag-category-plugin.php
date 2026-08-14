<?php
/**
 * Plugin Name:       Dynamic Tag to Category Products
 * Plugin URI:        https://github.com/coded-letter/dynamic-tag-category-plugin
 * Description:       Add a chosen WooCommerce product tag to every product in a chosen category.
 * Version:           1.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Coded Letter
 * Author URI:        https://codedletter.com
 * License:           MIT
 * License URI:       https://opensource.org/license/mit
 * Text Domain:       dynamic-tag-category-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the tool beneath WordPress Tools.
 */
function coded_letter_dtcp_add_admin_menu() {
	add_management_page(
		__( 'Add Tag to Category', 'dynamic-tag-category-plugin' ),
		__( 'Add Tag to Category', 'dynamic-tag-category-plugin' ),
		'manage_woocommerce',
		'coded-letter-add-tag-to-category',
		'coded_letter_dtcp_render_admin_page'
	);
}
add_action( 'admin_menu', 'coded_letter_dtcp_add_admin_menu' );

/**
 * Add a product tag to every product assigned to a category.
 *
 * @param string $category_identifier Category slug or name.
 * @param string $tag_name            Product tag name.
 * @return array|WP_Error Operation totals or an actionable error.
 */
function coded_letter_dtcp_apply_tag( $category_identifier, $tag_name ) {
	if ( ! taxonomy_exists( 'product_cat' ) || ! taxonomy_exists( 'product_tag' ) ) {
		return new WP_Error(
			'woocommerce_required',
			__( 'WooCommerce must be active before this tool can update products.', 'dynamic-tag-category-plugin' )
		);
	}

	$category = get_term_by( 'slug', $category_identifier, 'product_cat' );
	if ( ! $category ) {
		$category = get_term_by( 'name', $category_identifier, 'product_cat' );
	}

	if ( ! $category || is_wp_error( $category ) ) {
		return new WP_Error(
			'category_not_found',
			__( 'No product category matched that name or slug.', 'dynamic-tag-category-plugin' )
		);
	}

	$updated = 0;
	$skipped = 0;
	$failed  = 0;
	$page    = 1;

	do {
		$query = new WP_Query(
			array(
				'post_type'              => 'product',
				'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page'         => 200,
				'paged'                  => $page,
				'fields'                 => 'ids',
				'no_found_rows'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => array( (int) $category->term_id ),
					),
				),
			)
		);

		foreach ( $query->posts as $product_id ) {
			if ( has_term( $tag_name, 'product_tag', $product_id ) ) {
				++$skipped;
				continue;
			}

			$result = wp_set_post_terms( $product_id, array( $tag_name ), 'product_tag', true );
			if ( is_wp_error( $result ) ) {
				++$failed;
				continue;
			}

			++$updated;
		}

		++$page;
	} while ( $page <= (int) $query->max_num_pages );

	wp_reset_postdata();

	return array(
		'category' => $category->name,
		'updated'  => $updated,
		'skipped'  => $skipped,
		'failed'   => $failed,
	);
}

/**
 * Render the protected admin form and operation result.
 */
function coded_letter_dtcp_render_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage WooCommerce products.', 'dynamic-tag-category-plugin' ) );
	}

	$result = null;

	if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		check_admin_referer( 'coded_letter_dtcp_apply_tag' );

		$category_identifier = isset( $_POST['category_identifier'] )
			? sanitize_text_field( wp_unslash( $_POST['category_identifier'] ) )
			: '';
		$tag_name = isset( $_POST['tag_name'] )
			? sanitize_text_field( wp_unslash( $_POST['tag_name'] ) )
			: '';

		if ( '' === $category_identifier || '' === $tag_name ) {
			$result = new WP_Error(
				'missing_fields',
				__( 'Enter both a category name or slug and a product tag.', 'dynamic-tag-category-plugin' )
			);
		} else {
			$result = coded_letter_dtcp_apply_tag( $category_identifier, $tag_name );
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Add Tag to Products in a Category', 'dynamic-tag-category-plugin' ); ?></h1>
		<p><?php esc_html_e( 'Choose a WooCommerce product category and append one product tag without replacing existing tags.', 'dynamic-tag-category-plugin' ); ?></p>

		<?php if ( is_wp_error( $result ) ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html( $result->get_error_message() ); ?></p></div>
		<?php elseif ( is_array( $result ) ) : ?>
			<div class="notice notice-success">
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1: Category name, 2: Updated count, 3: Skipped count, 4: Failed count. */
							__( '%1$s: %2$d updated, %3$d already tagged, %4$d failed.', 'dynamic-tag-category-plugin' ),
							$result['category'],
							$result['updated'],
							$result['skipped'],
							$result['failed']
						)
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'coded_letter_dtcp_apply_tag' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="category_identifier"><?php esc_html_e( 'Category name or slug', 'dynamic-tag-category-plugin' ); ?></label></th>
					<td><input class="regular-text" id="category_identifier" name="category_identifier" required type="text" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="tag_name"><?php esc_html_e( 'Product tag', 'dynamic-tag-category-plugin' ); ?></label></th>
					<td><input class="regular-text" id="tag_name" name="tag_name" required type="text" /></td>
				</tr>
			</table>
			<?php submit_button( __( 'Apply tag', 'dynamic-tag-category-plugin' ) ); ?>
		</form>
	</div>
	<?php
}
