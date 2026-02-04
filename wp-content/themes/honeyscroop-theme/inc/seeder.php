<?php
/**
 * Seeder to generate dummy products if needed.
 *
 * @package Honeyscroop
 */

function honeyscroop_seed_products() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    // Check count
    $count = wp_count_posts( 'product' );
    if ( $count->publish >= 6 ) return; // We have enough

    $titles = [
        'Wildflower Honey 500g', 
        'Eucalyptus Honey 1kg', 
        'Comb Honey Section', 
        'Creamed Honey Jar', 
        'Beeswax Candle Pair',
        'Propolis Tincture',
        'Honey Dipper (Wooden)',
        'Gift Set: Trio'
    ];

    $images = [
        'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1612476686016-17b1f6323cc6?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1471943311132-c66ad85a2989?auto=format&fit=crop&w=800&q=80'
    ];

    foreach ( $titles as $key => $title ) {
        if ( $count->publish + $key >= 8 ) break;

        // Check if exists
        $exists = get_page_by_title( $title, OBJECT, 'product' );
        if ( $exists ) continue;

        $post_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'post_status'  => 'publish',
            'post_type'    => 'product',
            'post_author'  => get_current_user_id(),
        ) );

        if ( $post_id ) {
            update_post_meta( $post_id, '_product_price', rand(500, 5000) ); // $5.00 - $50.00
            update_post_meta( $post_id, '_product_sku', 'SKU-' . rand(1000, 9999) );
            update_post_meta( $post_id, '_product_availability', 'in_stock' );

            // Ideally we would sideload images, but for now we rely on placeholders or manual assignment
            // Or if we have local attachment IDs, we could assign them.
        }
    }
}
add_action( 'init', 'honeyscroop_seed_products' );
