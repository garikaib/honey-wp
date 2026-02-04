<?php
/**
 * Shop Order Custom Post Type and Logic
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
 * Register Shop Order CPT.
 */
function honeyscroop_register_order_cpt(): void {
	$labels = array(
		'name'                  => _x( 'Orders', 'Post type general name', 'honeyscroop' ),
		'singular_name'         => _x( 'Order', 'Post type singular name', 'honeyscroop' ),
		'menu_name'             => _x( 'Shop Orders', 'Admin Menu text', 'honeyscroop' ),
		'add_new'               => __( 'Add New', 'honeyscroop' ),
		'add_new_item'          => __( 'Add New Order', 'honeyscroop' ),
		'edit_item'             => __( 'Edit Order', 'honeyscroop' ),
		'new_item'              => __( 'New Order', 'honeyscroop' ),
		'view_item'             => __( 'View Order', 'honeyscroop' ),
		'search_items'          => __( 'Search Orders', 'honeyscroop' ),
		'not_found'             => __( 'No orders found', 'honeyscroop' ),
		'not_found_in_trash'    => __( 'No orders found in Trash', 'honeyscroop' ),
		'all_items'             => __( 'All Orders', 'honeyscroop' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => false, // Not visible on frontend
		'publicly_queryable'  => false,
		'show_ui'             => true, // Visible in admin
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => 6,
		'menu_icon'           => 'dashicons-clipboard',
		'supports'            => array( 'title', 'custom-fields' ), // Title will be Order ID
	);

	register_post_type( 'shop_order', $args );

	// Register Custom Statuses.
	register_post_status( 'lead', array(
		'label'                     => _x( 'Lead', 'post', 'honeyscroop' ),
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Lead <span class="count">(%s)</span>', 'Leads <span class="count">(%s)</span>', 'honeyscroop' ),
	) );
}
add_action( 'init', 'honeyscroop_register_order_cpt' );

/**
 * Register REST API route for order submission.
 */
function honeyscroop_register_order_routes(): void {
	register_rest_route( 'honeyscroop/v1', '/submit-order', array(
		'methods'             => 'POST',
		'callback'            => 'honeyscroop_handle_order_submission',
		'permission_callback' => '__return_true', // Open endpoint
	) );
}
add_action( 'rest_api_init', 'honeyscroop_register_order_routes' );

/**
 * Handle Order Submission.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response Response object.
 */
function honeyscroop_handle_order_submission( WP_REST_Request $request ): WP_REST_Response {
	$params = $request->get_json_params();

	$customer_name  = sanitize_text_field( $params['name'] ?? '' );
	$customer_email = sanitize_email( $params['email'] ?? '' );
	$customer_phone = sanitize_text_field( $params['phone'] ?? '' );
	$items          = $params['items'] ?? array();
	$total          = absint( $params['total'] ?? 0 );
	$type           = sanitize_text_field( $params['type'] ?? 'whatsapp' );
	$create_account = ! empty( $params['createAccount'] );

	if ( empty( $items ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => 'No items in cart' ), 400 );
	}

	$generated_password = null;
	$user_id = null;

	// Create account if requested and email doesn't exist
	if ( $create_account && ! empty( $customer_email ) && ! email_exists( $customer_email ) ) {
		// Generate a random password
		$generated_password = wp_generate_password( 12, true, false );
		
		// Create username from email
		$username = sanitize_user( current( explode( '@', $customer_email ) ), true );
		$base_username = $username;
		$counter = 1;
		while ( username_exists( $username ) ) {
			$username = $base_username . $counter;
			$counter++;
		}

		// Create user
		$user_id = wp_create_user( $username, $generated_password, $customer_email );
		
		if ( ! is_wp_error( $user_id ) ) {
			// Update display name
			wp_update_user( array(
				'ID' => $user_id,
				'display_name' => $customer_name,
				'first_name' => explode( ' ', $customer_name )[0] ?? '',
				'last_name' => explode( ' ', $customer_name )[1] ?? ''
			) );
			
			// Save phone to user meta
			update_user_meta( $user_id, 'phone', $customer_phone );
			
			// Send welcome email (if email templates exist)
			if ( function_exists( 'honeyscroop_send_welcome_email' ) ) {
				honeyscroop_send_welcome_email( $customer_email, $customer_name, $username, $generated_password );
			}
		} else {
			$generated_password = null;
		}
	}

	// Create Order Post
	$post_id = wp_insert_post( array(
		'post_type'   => 'shop_order',
		'post_status' => 'lead',
		'post_title'  => 'Order #' . uniqid(),
	) );

	if ( is_wp_error( $post_id ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => 'Could not create order' ), 500 );
	}

	// Update Title with ID
	wp_update_post( array(
		'ID'         => $post_id,
		'post_title' => 'Order #' . $post_id . ' (' . $type . ')',
	) );

	// Save Meta
	update_post_meta( $post_id, '_order_customer_name', $customer_name );
	update_post_meta( $post_id, '_order_customer_email', $customer_email );
	update_post_meta( $post_id, '_order_customer_phone', $customer_phone );
	update_post_meta( $post_id, '_order_items', wp_json_encode( $items ) );
	update_post_meta( $post_id, '_order_total', $total );
	update_post_meta( $post_id, '_order_type', $type );
	
	// Link user if created
	if ( $user_id ) {
		update_post_meta( $post_id, '_order_user_id', $user_id );
	}

	$response = array(
		'success'  => true,
		'order_id' => $post_id,
		'message'  => 'Order created successfully',
	);
	
	// Include password if account was created
	if ( $generated_password ) {
		$response['password'] = $generated_password;
		$response['account_created'] = true;
	}

	return new WP_REST_Response( $response, 200 );
}

/**
 * Add meta box to view Order Details in Admin.
 */
function honeyscroop_add_order_meta_box(): void {
	add_meta_box(
		'honeyscroop_order_data',
		__( 'Order Data', 'honeyscroop' ),
		'honeyscroop_render_order_meta_box',
		'shop_order',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'honeyscroop_add_order_meta_box' );

/**
 * Render Order Data Meta Box.
 */
function honeyscroop_render_order_meta_box( WP_Post $post ): void {
	$name  = get_post_meta( $post->ID, '_order_customer_name', true );
	$email = get_post_meta( $post->ID, '_order_customer_email', true );
	$phone = get_post_meta( $post->ID, '_order_customer_phone', true );
	$items = json_decode( get_post_meta( $post->ID, '_order_items', true ), true );
	$total = get_post_meta( $post->ID, '_order_total', true );
	$type  = get_post_meta( $post->ID, '_order_type', true );

	echo '<table class="form-table">';
	echo '<tr><th>Name:</th><td>' . esc_html( $name ) . '</td></tr>';
	echo '<tr><th>Email:</th><td><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></td></tr>';
	echo '<tr><th>Phone:</th><td>' . esc_html( $phone ) . '</td></tr>';
	echo '<tr><th>Type:</th><td>' . esc_html( ucfirst( $type ) ) . '</td></tr>';
	echo '</table>';

	echo '<h3>Items</h3>';
	echo '<table class="widefat fixed striped">';
	echo '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>';
	echo '<tbody>';
	if ( $items ) {
		foreach ( $items as $item ) {
			echo '<tr>';
			echo '<td>' . esc_html( $item['name'] ?? 'Unknown' ) . '</td>';
			echo '<td>' . esc_html( number_format( ( $item['price'] ?? 0 ) / 100, 2 ) ) . '</td>';
			echo '<td>' . esc_html( $item['quantity'] ?? 1 ) . '</td>';
			echo '<td>$' . esc_html( number_format( ( ( $item['price'] ?? 0 ) * ( $item['quantity'] ?? 1 ) ) / 100, 2 ) ) . '</td>';
			echo '</tr>';
		}
	}
	echo '</tbody>';
	echo '<tfoot><tr><th colspan="3" style="text-align:right">Total:</th><th>$' . esc_html( number_format( (float) $total / 100, 2 ) ) . '</th></tr></tfoot>';
	echo '</table>';
}
