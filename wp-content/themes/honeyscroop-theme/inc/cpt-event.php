<?php
/**
 * Custom Post Type: Event
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function honeyscroop_register_event_cpt() {
    $labels = array(
        'name'                  => _x( 'Events', 'Post Type General Name', 'honeyscroop' ),
        'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'honeyscroop' ),
        'menu_name'             => __( 'Events', 'honeyscroop' ),
        'all_items'             => __( 'All Events', 'honeyscroop' ),
        'add_new_item'          => __( 'Add New Event', 'honeyscroop' ),
        'edit_item'             => __( 'Edit Event', 'honeyscroop' ),
        'view_item'             => __( 'View Event', 'honeyscroop' ),
    );
    $args = array(
        'label'                 => __( 'Event', 'honeyscroop' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-calendar-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'event', $args );

    // Register Meta Fields
    $meta_fields = [
        'event_start_date' => 'string',
        'event_end_date'   => 'string',
        'event_location'   => 'string',
        'event_price'      => 'string',
    ];

    foreach ( $meta_fields as $key => $type ) {
        register_post_meta( 'event', $key, array(
            'show_in_rest' => true,
            'single'       => true,
            'type'         => $type,
        ) );
    }
}
add_action( 'init', 'honeyscroop_register_event_cpt' );

// Seeding Logic
function honeyscroop_seed_events() {
    if ( get_option( 'honeyscroop_events_seeded_v1' ) ) {
        return;
    }

    $existing = get_posts( [ 'post_type' => 'event', 'posts_per_page' => 1 ] );
    if ( ! empty( $existing ) ) {
        update_option( 'honeyscroop_events_seeded_v1', true );
        return;
    }

    $images = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 20, 'orderby' => 'rand' ] );
    $image_ids = wp_list_pluck( $images, 'ID' );

    $events_data = [
        [ 'title' => 'Honey Harvesting Workshop', 'excerpt' => 'Join us for a hands-on experience in ethical honey harvesting.' ],
        [ 'title' => 'Beekeeping Masterclass', 'excerpt' => 'Learn the art of beekeeping from our expert apiarists.' ],
        [ 'title' => 'Taste of Zimbabwe Festival', 'excerpt' => 'A culinary journey featuring Honeyscoop products.' ],
        [ 'title' => 'Sustainable Farming Expo', 'excerpt' => 'Exploring eco-friendly farming practices in the region.' ],
        [ 'title' => 'Community Market Day', 'excerpt' => 'Meet the farmers and taste the freshest produce.' ],
        [ 'title' => 'Sweet & Savory Cooking Class', 'excerpt' => 'Cooking with honey: from glaze to dessert.' ],
        [ 'title' => 'The Hive Charity Gala', 'excerpt' => 'Supporting rural communities through sustainable trade.' ],
        [ 'title' => 'Butterscoop Launch Party', 'excerpt' => 'Celebrating the launch of our new peanut butter range.' ],
        [ 'title' => 'Wild Flower Tour', 'excerpt' => 'Discover the flora that gives our honey its unique flavor.' ],
        [ 'title' => 'Sunset Honey Tasting', 'excerpt' => 'An evening of fine honey and wine pairing.' ],
    ];

    foreach ( $events_data as $index => $data ) {
        $post_id = wp_insert_post( [
            'post_title'   => $data['title'],
            'post_excerpt' => $data['excerpt'],
            'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'post_status'  => 'publish',
            'post_type'    => 'event',
        ] );

        if ( ! is_wp_error( $post_id ) ) {
            // Meta
            $start_date = date( 'Y-m-d H:i:s', strtotime( '+' . ( $index * 3 + 2 ) . ' days 10:00:00' ) );
            $end_date   = date( 'Y-m-d H:i:s', strtotime( '+' . ( $index * 3 + 2 ) . ' days 14:00:00' ) );
            update_post_meta( $post_id, 'event_start_date', $start_date );
            update_post_meta( $post_id, 'event_end_date', $end_date );
            update_post_meta( $post_id, 'event_location', 'Greendale, Harare' );
            update_post_meta( $post_id, 'event_price', ( $index % 2 == 0 ) ? 'Free' : '$' . ( $index * 5 + 10 ) );
            
            // Image
            if ( ! empty( $image_ids ) ) {
                $random_image = $image_ids[ array_rand( $image_ids ) ];
                set_post_thumbnail( $post_id, $random_image );
            }
        }
    }

    update_option( 'honeyscroop_events_seeded_v1', true );
}
add_action( 'init', 'honeyscroop_seed_events' );
