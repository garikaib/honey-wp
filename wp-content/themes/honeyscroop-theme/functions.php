<?php
/**
 * Honeyscroop Theme Functions
 *
 * @package Honeyscroop
 * @since   1.0.0
 */

declare(strict_types=1);

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Theme constants.
define( 'HONEYSCROOP_VERSION', '1.0.0' );
define( 'HONEYSCROOP_DIR', get_stylesheet_directory() );
define( 'HONEYSCROOP_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue parent and child theme styles.
 */
function honeyscroop_enqueue_styles(): void {
	// Enqueue parent theme stylesheet.
	wp_enqueue_style(
		'twentytwentysix-style',
		get_parent_theme_file_uri( 'style.css' ),
		array(),
		wp_get_theme( 'twentytwentysix' )->get( 'Version' )
	);

	// Enqueue child theme stylesheet.
	wp_enqueue_style(
		'honeyscroop-style',
		get_stylesheet_uri(),
		array( 'twentytwentysix-style' ),
		HONEYSCROOP_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'honeyscroop_enqueue_styles' );

/**
 * Theme setup.
 */
function honeyscroop_setup(): void {
	// Add theme support.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );

	// Register nav menus.
	register_nav_menus(
		array(
			'primary'          => esc_html__( 'Primary Menu', 'honeyscroop' ),
			'footer_navigate'  => esc_html__( 'Footer: Navigate', 'honeyscroop' ),
			'footer_products'  => esc_html__( 'Footer: Products', 'honeyscroop' ),
			'footer_company'   => esc_html__( 'Footer: Company', 'honeyscroop' ),
		)
	);
}
add_action( 'after_setup_theme', 'honeyscroop_setup' );

/**
 * Filter menu link attributes to add Tailwind classes.
 *
 * @param array    $atts The HTML attributes applied to the menu item's `<a>` element.
 * @param WP_Post  $item The current menu item.
 * @param stdClass $args An object of wp_nav_menu() arguments.
 * @return array Modified attributes.
 */
function honeyscroop_menu_link_atts( $atts, $item, $args ) {
	// Add classes to footer menu links
	if ( isset( $args->theme_location ) && strpos( $args->theme_location, 'footer_' ) === 0 ) {
		$atts['class'] = 'link link-hover hover:text-white transition-colors';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'honeyscroop_menu_link_atts', 10, 3 );

/**
 * Seed footer menus if they don't exist.
 */
function honeyscroop_seed_menus() {
	// Check if we've already seeded to avoid running this on every load unnecessarily, 
    // though wp_get_nav_menu_object is fast enough for checking existence.
    // We'll trust the check for existence.
	
	$menus = array(
		'Navigate' => array(
			'location' => 'footer_navigate',
			'items'    => array(
				'Home'    => '/',
				'Shop'    => '/shop',
				'About'   => '/about',
				'Contact' => '/contact',
				'FAQs'    => '/faqs',
			),
		),
		'Our Products' => array(
			'location' => 'footer_products',
			'items'    => array(
				'Raw Honey'     => '/shop/raw-honey',
				'Peanut Butter' => '/shop/peanut-butter',
				'Honeyscoops'   => '/shop/honeyscoops',
				'Wholesale'     => '/shop/wholesale',
				'Gifts'         => '/shop/gifts',
			),
		),
		'Company' => array(
			'location' => 'footer_company',
			'items'    => array(
				'Founder Story'    => '/our-story',
				'Vision & Mission' => '/mission',
				'Meet The Clients' => '/partners',
				'Sustainability'   => '/sustainability',
				'Store Location'   => '/store-locator',
			),
		),
		'Primary' => array(
			'location' => 'primary',
			'items'    => array(
				'Home' => '/',
				'Shop' => '/shop',
			),
		),
	);

    $locations = get_theme_mod( 'nav_menu_locations' );
    $updated   = false;

	foreach ( $menus as $name => $config ) {
		$menu_name = 'Footer - ' . $name;
		$menu_exists = wp_get_nav_menu_object( $menu_name );

		if ( ! $menu_exists ) {
			$menu_id = wp_create_nav_menu( $menu_name );

			if ( ! is_wp_error( $menu_id ) ) {
				foreach ( $config['items'] as $title => $url ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'  => $title,
							'menu-item-url'    => $url,
							'menu-item-status' => 'publish',
							'menu-item-type'   => 'custom',
						)
					);
				}

                // Assign to location
                $locations[ $config['location'] ] = $menu_id;
                $updated = true;
			}
		} else {
            // Ensure connection if menu exists but location not set (e.g. after theme switch)
             if ( ! isset( $locations[ $config['location'] ] ) || $locations[ $config['location'] ] !== $menu_exists->term_id ) {
                $locations[ $config['location'] ] = $menu_exists->term_id;
                $updated = true;
            }
        }
	}

    if ( $updated ) {
        set_theme_mod( 'nav_menu_locations', $locations );
    }
}
add_action( 'init', 'honeyscroop_seed_menus' );

/**
 * Helper to get menu items for a location.
 *
 * @param string $location Menu location slug.
 * @return array Formatted menu items.
 */
function honeyscroop_get_menu_items( string $location ): array {
	$locations = get_nav_menu_locations();
	if ( ! isset( $locations[ $location ] ) ) {
		return array();
	}

	$menu = wp_get_nav_menu_object( $locations[ $location ] );
	if ( ! $menu ) {
		return array();
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	if ( ! $menu_items ) {
		return array();
	}

	$formatted = array();
	foreach ( $menu_items as $item ) {
		if ( 0 === (int) $item->menu_item_parent ) {
			$formatted[ $item->ID ] = array(
				'label'    => $item->title,
				'href'     => $item->url,
				'children' => array(),
			);
		}
	}

	foreach ( $menu_items as $item ) {
		if ( 0 !== (int) $item->menu_item_parent && isset( $formatted[ $item->menu_item_parent ] ) ) {
			$formatted[ $item->menu_item_parent ]['children'][] = array(
				'label' => $item->title,
				'href'  => $item->url,
			);
		}
	}

	return array_values( $formatted );
}

// Load modular includes.
require_once get_stylesheet_directory() . '/inc/assets.php';
require_once get_stylesheet_directory() . '/inc/cpt-honey-variety.php';
require_once get_stylesheet_directory() . '/inc/cpt-partner.php';
require_once get_stylesheet_directory() . '/inc/customizer-social.php';

/**
 * Customizer Settings
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function honeyscroop_customize_register( $wp_customize ): void {
	// Announcement Bar Section.
	$wp_customize->add_section(
		'honeyscroop_announcement',
		array(
			'title'    => __( 'Announcement Bar', 'honeyscroop' ),
			'priority' => 20,
		)
	);

	// Announcement Text.
	$wp_customize->add_setting(
		'announcement_text',
		array(
			'default'           => __( 'Free delivery for orders over US$50', 'honeyscroop' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'announcement_text',
		array(
			'label'   => __( 'Announcement Text', 'honeyscroop' ),
			'section' => 'honeyscroop_announcement',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'honeyscroop_customize_register' );
