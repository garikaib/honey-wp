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
	// Compiled theme CSS (vanilla + Tailwind utilities).
	$css_path = HONEYSCROOP_DIR . '/dist/style.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'honeyscroop-dist-style',
			HONEYSCROOP_URI . '/dist/style.css',
			array(),
			filemtime( $css_path )
		);
	}

	// Google Fonts (Story Page)
	if ( is_page_template( 'page-our-story.php' ) ) {
		wp_enqueue_style(
			'honeyscroop-story-fonts',
			'https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Satisfy&display=swap',
			array(),
			null
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

		// Localize header nav with dynamic menu data.
		wp_localize_script(
			'honeyscroop-header-nav',
			'honeyscroopHeaderData',
			array(
				'primaryMenu' => honeyscroop_get_menu_items( 'primary' ),
			)
		);
	}

    // Product Grid React App.
    $grid_js_path = HONEYSCROOP_DIR . '/dist/product-grid.js';
    if ( file_exists( $grid_js_path ) ) {
        wp_enqueue_script(
            'honeyscroop-product-grid',
            HONEYSCROOP_URI . '/dist/product-grid.js',
            array(),
            filemtime( $grid_js_path ),
            true
        );
    }

    // Partner Ticker React App.
    $partner_js_path = HONEYSCROOP_DIR . '/dist/partner-ticker.js';
    if ( file_exists( $partner_js_path ) ) {
        wp_enqueue_script(
            'honeyscroop-partner-ticker',
            HONEYSCROOP_URI . '/dist/partner-ticker.js',
            array(),
            filemtime( $partner_js_path ),
            true
        );
    }
	// Vendor Script (GSAP)
	$vendor_js_path = HONEYSCROOP_DIR . '/dist/vendor.js';
	if ( file_exists( $vendor_js_path ) ) {
		wp_enqueue_script(
			'honeyscroop-vendor',
			HONEYSCROOP_URI . '/dist/vendor.js',
			array(),
			filemtime( $vendor_js_path ),
			true
		);
	}

	// Bees Animation & Custom Dropdown (Contact Page only)
	if ( is_page_template( 'page-contact.php' ) ) {
		// Alpine.js for interactive dropdown
		wp_enqueue_script(
			'honeyscroop-alpine',
			'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
			array(),
			null,
			true
		);

		$bees_js_path = HONEYSCROOP_DIR . '/dist/bees.js';
		if ( file_exists( $bees_js_path ) ) {
			wp_enqueue_script(
				'honeyscroop-bees',
				HONEYSCROOP_URI . '/dist/bees.js',
				array( 'honeyscroop-vendor' ),
				filemtime( $bees_js_path ),
				true
			);
		}
	}

	// 404 Hive Scene (404 Page only)
	if ( is_404() ) {
		$hive_js_path = HONEYSCROOP_DIR . '/dist/hive-scene.js';
		if ( file_exists( $hive_js_path ) ) {
			wp_enqueue_script(
				'honeyscroop-hive-scene',
				HONEYSCROOP_URI . '/dist/hive-scene.js',
				array( 'honeyscroop-vendor' ),
				filemtime( $hive_js_path ),
				true
			);
		}
	}
    if ( is_page_template( 'page-events.php' ) ) {
        // Define development mode explicitly or relying on a constant/helper if available
        // For now, assume production if no explicit dev logic is robustly passed here variables from outside scope
        // Re-declaring variables or using simpler logic:
        $is_development = defined( 'WP_ENV' ) && 'development' === WP_ENV; 
        
        $events_js = $is_development
            ? 'http://localhost:5173/src/events-calendar/index.jsx'
            : get_template_directory_uri() . '/dist/events-calendar.js';

        wp_enqueue_script( 'honeyscroop-events-calendar', $events_js, array(), '1.0.0', true );
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
        'honeyscroop-product-grid',
        'honeyscroop-partner-ticker',
        'honeyscroop-vendor',
        'honeyscroop-bees',
        'honeyscroop-hive-scene',
        'honeyscroop-events-calendar',
	);

	if ( in_array( $handle, $module_handles, true ) ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'honeyscroop_add_module_type_to_scripts', 10, 2 );

