<?php
/**
 * Admin SPA and Options API
 */

// 1. Register Admin Menu
function honeyscroop_register_admin_menu() {
    add_menu_page(
        'HoneyScoop Options',
        'HoneyScoop',
        'manage_options',
        'honeyscroop',
        'honeyscroop_render_admin_app',
        'dashicons-store',
        2
    );
     
    // Submenu: Shop (Overview)
    add_submenu_page(
        'honeyscroop',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'honeyscroop'
    );
}
add_action( 'admin_menu', 'honeyscroop_register_admin_menu' );

// 2. Render React Root
function honeyscroop_render_admin_app() {
    ?>
    <div id="honey-admin-root"></div>
    <?php
}

// 3. Enqueue Admin Script
function honeyscroop_admin_enqueue_scripts( $hook ) {
    if ( 'toplevel_page_honeyscroop' !== $hook ) {
        return;
    }

    $asset_file = get_stylesheet_directory() . '/dist/admin-spa.js';
    
    // In production, we assume only one file or managed by index logic
    // For now, simpler implementation assuming build output
    
    wp_enqueue_script(
        'honeyscroop-admin-spa',
        get_stylesheet_directory_uri() . '/dist/admin-spa.js',
        array( 'wp-element', 'wp-api-fetch', 'wp-i18n' ),
        file_exists($asset_file) ? filemtime($asset_file) : '1.0.0',
        true
    );

    wp_enqueue_style(
        'honeyscroop-admin-style',
        get_stylesheet_directory_uri() . '/dist/style.css',
        array(),
        '1.0.0'
    );
    
    // Localize Data
    wp_localize_script( 'honeyscroop-admin-spa', 'honeyAdmin', array(
        'nonce'  => wp_create_nonce( 'wp_rest' ),
        'apiUrl' => rest_url( 'honeyscroop/v1/' )
    ));
}
add_action( 'admin_enqueue_scripts', 'honeyscroop_admin_enqueue_scripts' );


// 4. REST API Endpoint for Options
add_action( 'rest_api_init', function () {
    register_rest_route( 'honeyscroop/v1', '/options', array(
        'methods'             => 'POST',
        'callback'            => 'honeyscroop_save_options',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        }
    ));

    register_rest_route( 'honeyscroop/v1', '/options', array(
        'methods'             => 'GET',
        'callback'            => 'honeyscroop_get_options',
        'permission_callback' => 'is_user_logged_in' // Allow read for basic checks if needed
    ));

    // Stats endpoint for dashboard
    register_rest_route( 'honeyscroop/v1', '/stats', array(
        'methods'             => 'GET',
        'callback'            => 'honeyscroop_get_stats',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        }
    ));
});

function honeyscroop_save_options( $request ) {
    $params = $request->get_json_params();
    $group = sanitize_text_field( $params['group'] ); // e.g., 'currency', 'settings'
    $data = $params['data']; // Arbitrary JSON data

    if ( ! $group || empty( $data ) ) {
        return new WP_Error( 'invalid_data', 'Missing group or data', array( 'status' => 400 ) );
    }

    if ( $group === 'smtp' ) {
        if ( function_exists( 'honeyscroop_extend_options_for_smtp' ) ) {
            // honeyscroop_extend_options_for_smtp handles the update_option themselves
            honeyscroop_extend_options_for_smtp( array( 'smtp_settings' => $data ) );
        } else {
             update_option( 'honeyscroop_smtp_settings', $data );
        }
    } else {
        update_option( 'honeyscroop_option_' . $group, $data );
    }

    return rest_ensure_response( array( 'success' => true ) );
}

function honeyscroop_get_options( $request ) {
    // Return all relevant options
    return rest_ensure_response( array(
        'currency' => get_option( 'honeyscroop_option_currency', [] ),
        'settings' => get_option( 'honeyscroop_option_settings', [] ),
        'smtp'     => get_option( 'honeyscroop_smtp_settings', [] )
    ));
}

function honeyscroop_get_stats() {
    // Count orders (from 'shop_order' CPT)
    $orders_count = wp_count_posts( 'shop_order' );
    
    // Count 'lead' (initial state), 'publish' (if manually changed), etc.
    $leads = isset( $orders_count->lead ) ? (int) $orders_count->lead : 0;
    $published = isset( $orders_count->publish ) ? (int) $orders_count->publish : 0;
    $total_orders = $leads + $published;
    
    // Count products
    $products_count = wp_count_posts( 'product' );
    $total_products = isset( $products_count->publish ) ? (int) $products_count->publish : 0;
    
    return rest_ensure_response( array(
        'orders'   => $total_orders,
        'leads'    => $leads,
        'products' => $total_products
    ));
}
