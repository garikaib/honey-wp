<?php
/**
 * Product Custom Post Type and Meta Fields
 *
 * @package Honeyscroop
 * @since   1.0.0
 */

declare(strict_types=1);

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Product CPT.
 */
function honeyscroop_register_product_cpt(): void {
	$labels = array(
		'name'                  => _x( 'Products', 'Post type general name', 'honeyscroop' ),
		'singular_name'         => _x( 'Product', 'Post type singular name', 'honeyscroop' ),
		'menu_name'             => _x( 'Products', 'Admin Menu text', 'honeyscroop' ),
		'add_new'               => __( 'Add New', 'honeyscroop' ),
		'add_new_item'          => __( 'Add New Product', 'honeyscroop' ),
		'edit_item'             => __( 'Edit Product', 'honeyscroop' ),
		'new_item'              => __( 'New Product', 'honeyscroop' ),
		'view_item'             => __( 'View Product', 'honeyscroop' ),
		'search_items'          => __( 'Search Products', 'honeyscroop' ),
		'not_found'             => __( 'No products found', 'honeyscroop' ),
		'not_found_in_trash'    => __( 'No products found in Trash', 'honeyscroop' ),
		'all_items'             => __( 'All Products', 'honeyscroop' ),
		'featured_image'        => __( 'Product Image', 'honeyscroop' ),
		'set_featured_image'    => __( 'Set product image', 'honeyscroop' ),
		'remove_featured_image' => __( 'Remove product image', 'honeyscroop' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true, // Enable Block Editor and REST API.
		'rest_base'           => 'product',
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'product' ),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => false,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-cart',
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
	);

	register_post_type( 'product', $args );
}
add_action( 'init', 'honeyscroop_register_product_cpt' );

/**
 * Register Product Taxonomies.
 */
function honeyscroop_register_product_taxonomies(): void {
	$labels = array(
		'name'              => _x( 'Product Categories', 'taxonomy general name', 'honeyscroop' ),
		'singular_name'     => _x( 'Product Category', 'taxonomy singular name', 'honeyscroop' ),
		'search_items'      => __( 'Search Categories', 'honeyscroop' ),
		'all_items'         => __( 'All Categories', 'honeyscroop' ),
		'parent_item'       => __( 'Parent Category', 'honeyscroop' ),
		'parent_item_colon' => __( 'Parent Category:', 'honeyscroop' ),
		'edit_item'         => __( 'Edit Category', 'honeyscroop' ),
		'update_item'       => __( 'Update Category', 'honeyscroop' ),
		'add_new_item'      => __( 'Add New Category', 'honeyscroop' ),
		'new_item_name'     => __( 'New Category Name', 'honeyscroop' ),
		'menu_name'         => __( 'Categories', 'honeyscroop' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'product-category' ),
		'show_in_rest'      => true,
	);

	register_taxonomy( 'product_category', array( 'product' ), $args );
}
add_action( 'init', 'honeyscroop_register_product_taxonomies' );

/**
 * Register meta fields for Product CPT.
 */
function honeyscroop_register_product_meta(): void {
	// Price meta.
	register_post_meta(
		'product',
		'_product_price',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'number',
			'sanitize_callback' => 'absint',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// SKU meta.
	register_post_meta(
		'product',
		'_product_sku',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// Availability meta (in_stock, out_of_stock).
	register_post_meta(
		'product',
		'_product_availability',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
			'default'           => 'in_stock',
		)
	);
}
add_action( 'init', 'honeyscroop_register_product_meta' );

/**
 * Add meta box for Product fields.
 */
function honeyscroop_add_product_meta_box(): void {
	add_meta_box(
		'honeyscroop_product_details',
		__( 'Product Details', 'honeyscroop' ),
		'honeyscroop_render_product_meta_box',
		'product',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'honeyscroop_add_product_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post Current post object.
 */
function honeyscroop_render_product_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'honeyscroop_product_meta', 'honeyscroop_product_meta_nonce' );

	$price        = get_post_meta( $post->ID, '_product_price', true );
	$sku          = get_post_meta( $post->ID, '_product_sku', true );
	$availability = get_post_meta( $post->ID, '_product_availability', true );
	?>
	<p>
		<label for="product_price"><strong><?php esc_html_e( 'Price (USD cents)', 'honeyscroop' ); ?></strong></label><br>
		<input type="number" id="product_price" name="product_price" value="<?php echo esc_attr( $price ); ?>" min="0" style="width:100%">
	</p>
	<p>
		<label for="product_sku"><strong><?php esc_html_e( 'SKU / Product Number', 'honeyscroop' ); ?></strong></label><br>
		<input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr( $sku ); ?>" style="width:100%">
	</p>
	<p>
		<label for="product_availability"><strong><?php esc_html_e( 'Availability', 'honeyscroop' ); ?></strong></label><br>
		<select id="product_availability" name="product_availability" style="width:100%">
			<option value="in_stock" <?php selected( $availability, 'in_stock' ); ?>><?php esc_html_e( 'In Stock', 'honeyscroop' ); ?></option>
			<option value="out_of_stock" <?php selected( $availability, 'out_of_stock' ); ?>><?php esc_html_e( 'Out of Stock', 'honeyscroop' ); ?></option>
		</select>
	</p>
	<?php
}

/**
 * Save meta box data.
 *
 * @param int $post_id Post ID.
 */
function honeyscroop_save_product_meta( int $post_id ): void {
	if ( ! isset( $_POST['honeyscroop_product_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['honeyscroop_product_meta_nonce'] ) ), 'honeyscroop_product_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['product_price'] ) ) {
		update_post_meta( $post_id, '_product_price', absint( $_POST['product_price'] ) );
	}

	if ( isset( $_POST['product_sku'] ) ) {
		update_post_meta( $post_id, '_product_sku', sanitize_text_field( wp_unslash( $_POST['product_sku'] ) ) );
	}

	if ( isset( $_POST['product_availability'] ) ) {
		update_post_meta( $post_id, '_product_availability', sanitize_text_field( wp_unslash( $_POST['product_availability'] ) ) );
	}
}
add_action( 'save_post_product', 'honeyscroop_save_product_meta' );

/**
 * Expose meta fields in easier-to-access REST field.
 */
function honeyscroop_register_product_rest_fields(): void {
	register_rest_field(
		'product',
		'product_details',
		array(
			'get_callback' => function ( $post_arr ) {
				$post_id = $post_arr['id'];
				return array(
					'price'        => (int) get_post_meta( $post_id, '_product_price', true ),
					'sku'          => get_post_meta( $post_id, '_product_sku', true ),
					'availability' => get_post_meta( $post_id, '_product_availability', true ),
					// Seeded Data for Demo
					'is_bestseller' => ( $post_id % 3 === 0 ), // Mock: Every 3rd product
					'is_featured'   => ( $post_id % 2 === 0 ), // Mock: Every 2nd product
					'related_ids'   => get_posts( array(
						'post_type'      => 'product',
						'fields'         => 'ids',
						'posts_per_page' => 4,
						'orderby'        => 'rand',
						'exclude'        => array( $post_id ),
					) ),
				);
			},
			'schema'       => array(
				'description' => __( 'Product details', 'honeyscroop' ),
				'type'        => 'object',
				'properties'  => array(
					'price'        => array( 'type' => 'integer' ),
					'sku'          => array( 'type' => 'string' ),
					'availability' => array( 'type' => 'string' ),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'honeyscroop_register_product_rest_fields' );
