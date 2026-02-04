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
 * Inline parent and child theme styles for performance.
 */
function honeyscroop_inline_styles(): void {
	// Inline parent theme stylesheet.
	if ( function_exists( 'honeyscroop_inline_css' ) ) {
		honeyscroop_inline_css(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' )
		);

		// Inline child theme stylesheet.
		honeyscroop_inline_css(
			'honeyscroop-style',
			get_stylesheet_uri()
		);
	} else {
		// Fallback to regular enqueue if helper is missing
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme( 'twentytwentyfive' )->get( 'Version' )
		);
		wp_enqueue_style(
			'honeyscroop-style',
			get_stylesheet_uri(),
			array( 'twentytwentyfive-style' ),
			HONEYSCROOP_VERSION
		);
	}
}
add_action( 'wp_head', 'honeyscroop_inline_styles', 5 );

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
	add_editor_style( 'dist/editor.css' );
	add_theme_support( 'wp-block-styles' );

	// Image sizes
	add_image_size( 'blog-card', 600, 400, true );
	add_image_size( 'blog-hero', 1600, 900, true );

	// Register nav menus.
	register_nav_menus(
		array(
			'primary'          => esc_html__( 'Top Menu', 'honeyscroop' ),
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
require_once get_stylesheet_directory() . '/inc/cpt-honey-variety.php'; // Keeping for legacy/reference if needed, or remove if replacing completely.
require_once get_stylesheet_directory() . '/inc/cpt-partner.php';
require_once get_stylesheet_directory() . '/inc/cpt-event.php';
require_once get_stylesheet_directory() . '/inc/cpt-faq.php'; // FAQ CPT
require_once get_stylesheet_directory() . '/inc/cpt-product.php'; // New Product CPT
require_once get_stylesheet_directory() . '/inc/cpt-order.php';   // New Order CPT
require_once get_stylesheet_directory() . '/inc/email-templates.php'; // Branded Email Templates
require_once get_stylesheet_directory() . '/inc/magic-link.php';      // Magic Link Auth
require_once get_stylesheet_directory() . '/inc/smtp.php';            // SMTP Settings
require_once get_stylesheet_directory() . '/inc/admin-spa.php';    // Admin React App
require_once get_stylesheet_directory() . '/inc/cron-currency.php'; // Currency Cron
require_once get_stylesheet_directory() . '/inc/customizer-social.php';
require_once get_stylesheet_directory() . '/inc/meta-boxes.php';
require_once get_stylesheet_directory() . '/inc/account.php';
require_once get_stylesheet_directory() . '/inc/newsletter.php'; // Newsletter System
require_once get_stylesheet_directory() . '/inc/turnstile.php'; // Cloudflare Turnstile
require_once get_stylesheet_directory() . '/inc/login-styling.php'; // Premium Login Styling
require_once get_stylesheet_directory() . '/inc/seeder.php'; // Data Seeder

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
