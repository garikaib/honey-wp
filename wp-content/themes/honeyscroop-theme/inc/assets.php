<?php
/**
 * Asset Enqueuing and Localization
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
 * Enqueue frontend styles and scripts.
 */
function honeyscroop_enqueue_assets(): void {
	// Tailwind compiled CSS.
	$css_path = HONEYSCROOP_DIR . '/dist/style.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'honeyscroop-style',
			HONEYSCROOP_URI . '/dist/style.css',
			array(),
			filemtime( $css_path )
		);
	}

	// Honey Finder React App.
	$js_path = HONEYSCROOP_DIR . '/dist/honey-finder.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'honeyscroop-honey-finder',
			HONEYSCROOP_URI . '/dist/honey-finder.js',
			array(),
			filemtime( $js_path ),
			true
		);

		// Localize script with REST API URL and nonce.
		wp_localize_script(
			'honeyscroop-honey-finder',
			'honeyscroopData',
			array(
				'restUrl' => esc_url_raw( rest_url( 'wp/v2/honey_variety' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'siteUrl' => esc_url_raw( home_url() ),
			)
		);
	}

	// Header Navigation React App.
	$nav_js_path = HONEYSCROOP_DIR . '/dist/header-nav.js';
	if ( file_exists( $nav_js_path ) ) {
		wp_enqueue_script(
			'honeyscroop-header-nav',
			HONEYSCROOP_URI . '/dist/header-nav.js',
			array(),
			filemtime( $nav_js_path ),
			false // Load in head for faster hydration
		);
	}
}
add_action( 'wp_enqueue_scripts', 'honeyscroop_enqueue_assets' );

/**
 * Enqueue editor styles.
 */
function honeyscroop_enqueue_editor_assets(): void {
	$css_path = HONEYSCROOP_DIR . '/dist/editor.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'honeyscroop-editor-style',
			HONEYSCROOP_URI . '/dist/editor.css',
			array(),
			filemtime( $css_path )
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'honeyscroop_enqueue_editor_assets' );

/**
 * Add type="module" to enqueued React scripts.
 *
 * @param string $tag    The <script> tag.
 * @param string $handle The script handle.
 * @return string The modified <script> tag.
 */
function honeyscroop_add_module_type_to_scripts( string $tag, string $handle ): string {
	$module_handles = array(
		'honeyscroop-honey-finder',
		'honeyscroop-header-nav',
	);

	if ( in_array( $handle, $module_handles, true ) ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'honeyscroop_add_module_type_to_scripts', 10, 2 );

