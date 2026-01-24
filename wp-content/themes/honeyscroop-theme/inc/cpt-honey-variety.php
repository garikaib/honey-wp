<?php
/**
 * Honey Variety Custom Post Type and Meta Fields
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
 * Register Honey Variety CPT.
 */
function honeyscroop_register_honey_variety_cpt(): void {
	$labels = array(
		'name'                  => _x( 'Honey Varieties', 'Post type general name', 'honeyscroop' ),
		'singular_name'         => _x( 'Honey Variety', 'Post type singular name', 'honeyscroop' ),
		'menu_name'             => _x( 'Honey Varieties', 'Admin Menu text', 'honeyscroop' ),
		'add_new'               => __( 'Add New', 'honeyscroop' ),
		'add_new_item'          => __( 'Add New Honey Variety', 'honeyscroop' ),
		'edit_item'             => __( 'Edit Honey Variety', 'honeyscroop' ),
		'new_item'              => __( 'New Honey Variety', 'honeyscroop' ),
		'view_item'             => __( 'View Honey Variety', 'honeyscroop' ),
		'search_items'          => __( 'Search Honey Varieties', 'honeyscroop' ),
		'not_found'             => __( 'No honey varieties found', 'honeyscroop' ),
		'not_found_in_trash'    => __( 'No honey varieties found in Trash', 'honeyscroop' ),
		'all_items'             => __( 'All Honey Varieties', 'honeyscroop' ),
		'featured_image'        => __( 'Honey Image', 'honeyscroop' ),
		'set_featured_image'    => __( 'Set honey image', 'honeyscroop' ),
		'remove_featured_image' => __( 'Remove honey image', 'honeyscroop' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true, // Enable Block Editor and REST API.
		'rest_base'           => 'honey_variety',
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'honey' ),
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => false,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-carrot',
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
	);

	register_post_type( 'honey_variety', $args );
}
add_action( 'init', 'honeyscroop_register_honey_variety_cpt' );

/**
 * Register meta fields for Honey Variety CPT.
 */
function honeyscroop_register_honey_meta(): void {
	// Price meta.
	register_post_meta(
		'honey_variety',
		'_honey_price',
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

	// Nectar Source meta.
	register_post_meta(
		'honey_variety',
		'_honey_nectar_source',
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

	// Region meta.
	register_post_meta(
		'honey_variety',
		'_honey_region',
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
}
add_action( 'init', 'honeyscroop_register_honey_meta' );

/**
 * Add meta box for Honey Variety fields in classic editor fallback.
 */
function honeyscroop_add_honey_meta_box(): void {
	add_meta_box(
		'honeyscroop_honey_details',
		__( 'Honey Details', 'honeyscroop' ),
		'honeyscroop_render_honey_meta_box',
		'honey_variety',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'honeyscroop_add_honey_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post Current post object.
 */
function honeyscroop_render_honey_meta_box( WP_Post $post ): void {
	// Nonce for security.
	wp_nonce_field( 'honeyscroop_honey_meta', 'honeyscroop_honey_meta_nonce' );

	// Get current values.
	$price         = get_post_meta( $post->ID, '_honey_price', true );
	$nectar_source = get_post_meta( $post->ID, '_honey_nectar_source', true );
	$region        = get_post_meta( $post->ID, '_honey_region', true );
	?>
	<p>
		<label for="honey_price"><strong><?php esc_html_e( 'Price (USD cents)', 'honeyscroop' ); ?></strong></label><br>
		<input type="number" id="honey_price" name="honey_price" value="<?php echo esc_attr( $price ); ?>" min="0" style="width:100%">
	</p>
	<p>
		<label for="honey_nectar_source"><strong><?php esc_html_e( 'Nectar Source', 'honeyscroop' ); ?></strong></label><br>
		<input type="text" id="honey_nectar_source" name="honey_nectar_source" value="<?php echo esc_attr( $nectar_source ); ?>" style="width:100%" placeholder="e.g., Acacia, Wildflower">
	</p>
	<p>
		<label for="honey_region"><strong><?php esc_html_e( 'Region', 'honeyscroop' ); ?></strong></label><br>
		<input type="text" id="honey_region" name="honey_region" value="<?php echo esc_attr( $region ); ?>" style="width:100%" placeholder="e.g., Eastern Highlands">
	</p>
	<?php
}

/**
 * Save meta box data with nonce verification.
 *
 * @param int $post_id Post ID.
 */
function honeyscroop_save_honey_meta( int $post_id ): void {
	// Check nonce.
	if ( ! isset( $_POST['honeyscroop_honey_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['honeyscroop_honey_meta_nonce'] ) ), 'honeyscroop_honey_meta' ) ) {
		return;
	}

	// Check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Save price.
	if ( isset( $_POST['honey_price'] ) ) {
		update_post_meta( $post_id, '_honey_price', absint( $_POST['honey_price'] ) );
	}

	// Save nectar source.
	if ( isset( $_POST['honey_nectar_source'] ) ) {
		update_post_meta( $post_id, '_honey_nectar_source', sanitize_text_field( wp_unslash( $_POST['honey_nectar_source'] ) ) );
	}

	// Save region.
	if ( isset( $_POST['honey_region'] ) ) {
		update_post_meta( $post_id, '_honey_region', sanitize_text_field( wp_unslash( $_POST['honey_region'] ) ) );
	}
}
add_action( 'save_post_honey_variety', 'honeyscroop_save_honey_meta' );

/**
 * Expose meta fields in REST API response.
 */
function honeyscroop_register_rest_fields(): void {
	register_rest_field(
		'honey_variety',
		'honey_details',
		array(
			'get_callback' => function ( $post_arr ) {
				$post_id = $post_arr['id'];
				return array(
					'price'        => (int) get_post_meta( $post_id, '_honey_price', true ),
					'nectar_source' => get_post_meta( $post_id, '_honey_nectar_source', true ),
					'region'       => get_post_meta( $post_id, '_honey_region', true ),
				);
			},
			'schema'       => array(
				'description' => __( 'Honey variety details', 'honeyscroop' ),
				'type'        => 'object',
				'properties'  => array(
					'price'        => array( 'type' => 'integer' ),
					'nectar_source' => array( 'type' => 'string' ),
					'region'       => array( 'type' => 'string' ),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'honeyscroop_register_rest_fields' );
