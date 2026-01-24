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
define( 'HONEYSCROOP_DIR', get_template_directory() );
define( 'HONEYSCROOP_URI', get_template_directory_uri() );

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
			'primary' => esc_html__( 'Primary Menu', 'honeyscroop' ),
			'footer'  => esc_html__( 'Footer Menu', 'honeyscroop' ),
		)
	);
}
add_action( 'after_setup_theme', 'honeyscroop_setup' );

// Load modular includes.
require_once get_template_directory() . '/inc/assets.php';
require_once get_template_directory() . '/inc/cpt-honey-variety.php';
require_once get_template_directory() . '/inc/cpt-partner.php';

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
