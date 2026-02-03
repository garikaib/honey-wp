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
 * Helper to resolve hashed filenames from Vite.
 */
function honeyscroop_get_asset_path( string $filename ): string {
	$pattern = HONEYSCROOP_DIR . '/dist/' . str_replace( '.js', '.*.js', $filename );
    if ( strpos( $filename, '.css' ) !== false ) {
        $pattern = HONEYSCROOP_DIR . '/dist/' . str_replace( '.css', '.*.css', $filename );
    }

	$files = glob( $pattern );

	if ( ! empty( $files ) ) {
		return HONEYSCROOP_URI . '/dist/' . basename( $files[0] );
	}

	return HONEYSCROOP_URI . '/dist/' . $filename;
}

/**
 * Add preconnect hints for Google Fonts.
 */
function honeyscroop_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'honeyscroop_resource_hints', 10, 2 );

/**
 * Enqueue frontend styles and scripts.
 */
function honeyscroop_enqueue_assets(): void {
	// Global Fonts
	wp_enqueue_style(
		'honeyscroop-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Outfit:wght@300;400;500;600&display=swap',
		array(),
		null
	);

	// Compiled theme CSS (vanilla + Tailwind utilities).
	$css_src = honeyscroop_get_asset_path( 'style.css' );
    wp_enqueue_style(
        'honeyscroop-dist-style',
        $css_src,
        array(),
        null // Version is in the filename hash
    );

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
    $finder_src = honeyscroop_get_asset_path( 'honey-finder.js' );
    wp_enqueue_script(
        'honeyscroop-honey-finder',
        $finder_src,
        array(),
        null,
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

	// Header Navigation React App.
    $nav_src = honeyscroop_get_asset_path( 'header-nav.js' );
    wp_enqueue_script(
        'honeyscroop-header-nav',
        $nav_src,
        array('honeyscroop-global'),
        null,
        true
    );

		// Localize header nav with dynamic menu data.
		wp_localize_script(
			'honeyscroop-header-nav',
			'honeyscroopHeaderData',
			array(
				'primaryMenu' => honeyscroop_get_menu_items( 'primary' ),
			)
		);

    // Product Grid React App.
    $grid_src = honeyscroop_get_asset_path( 'product-grid.js' );
    wp_enqueue_script(
        'honeyscroop-product-grid',
        $grid_src,
        array(),
        null,
        true
    );

    // Partner Ticker React App.
    $partner_src = honeyscroop_get_asset_path( 'partner-ticker.js' );
    wp_enqueue_script(
        'honeyscroop-partner-ticker',
        $partner_src,
        array('honeyscroop-global'),
        null,
        true
    );

        // Fetch partners for localized data
        $partners_query = new WP_Query(array(
            'post_type' => 'partner',
            'posts_per_page' => 20,
            'fields' => 'ids',
        ));

        $localized_partners = [];
        if ($partners_query->have_posts()) {
            foreach ($partners_query->posts as $post_id) {
                $logo_id = get_post_thumbnail_id($post_id);
                $logo_url = '';
                if ($logo_id) {
                    $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
                }
                
                $localized_partners[] = [
                    'id' => $post_id,
                    'title' => get_the_title($post_id),
                    'logoUrl' => $logo_url
                ];
            }
        }
        wp_reset_postdata();

        wp_localize_script(
            'honeyscroop-partner-ticker',
            'honeyscroopPartnerData',
            array(
                'partners' => $localized_partners,
            )
        );
	// Vendor Script (GSAP)
    $vendor_src = honeyscroop_get_asset_path( 'vendor.js' );
    wp_enqueue_script(
        'honeyscroop-vendor',
        $vendor_src,
        array(),
        null,
        true
    );

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

		$bees_src = honeyscroop_get_asset_path( 'bees.js' );
        wp_enqueue_script(
            'honeyscroop-bees',
            $bees_src,
            array( 'honeyscroop-vendor' ),
            null,
            true
        );
	}

	// 404 Hive Scene (404 Page only)
	if ( is_404() ) {
		$hive_src = honeyscroop_get_asset_path( 'hive-scene.js' );
        wp_enqueue_script(
            'honeyscroop-hive-scene',
            $hive_src,
            array( 'honeyscroop-vendor' ),
            null,
            true
        );
	}
    if ( is_page_template( 'page-events.php' ) ) {
        // Define development mode explicitly or relying on a constant/helper if available
        // For now, assume production if no explicit dev logic is robustly passed here variables from outside scope
        // Re-declaring variables or using simpler logic:
        $is_development = defined( 'WP_ENV' ) && 'development' === WP_ENV; 

        $events_js = $is_development
            ? 'http://localhost:5173/src/events-calendar/index.jsx'
            : honeyscroop_get_asset_path( 'events-calendar.js' );

        wp_enqueue_script( 'honeyscroop-events-calendar', $events_js, array(), null, true );
    }

    // Shop Archive & Taxonomy
    if ( is_page_template( 'page-shop.php' ) || is_tax( 'product_category' ) ) {
        $shop_js = defined( 'WP_ENV' ) && 'development' === WP_ENV
            ? 'http://localhost:5173/src/shop/archive.jsx'
            : honeyscroop_get_asset_path( 'shop-archive.js' );

        wp_enqueue_script( 'honeyscroop-shop-archive', $shop_js, array(), null, true );
        
        wp_localize_script( 'honeyscroop-shop-archive', 'shopData', array(
            'restUrl'     => esc_url_raw( rest_url( 'wp/v2/product' ) ),
            'categories'  => get_terms( array( 'taxonomy' => 'product_category', 'hide_empty' => false ) ),
            'nonce'       => wp_create_nonce( 'wp_rest' ),
            'currentTerm' => is_tax( 'product_category' ) ? get_queried_object_id() : null,
        ) );
    }

    // Single Product
    if ( is_singular( 'product' ) ) {
        $single_js = defined( 'WP_ENV' ) && 'development' === WP_ENV
            ? 'http://localhost:5173/src/shop/single.jsx'
            : honeyscroop_get_asset_path( 'shop-single.js' );

        wp_enqueue_script( 'honeyscroop-shop-single', $single_js, array(), null, true );

        wp_localize_script( 'honeyscroop-shop-single', 'productData', array(
            'productId' => get_the_ID(),
            'restUrl'   => esc_url_raw( rest_url( 'wp/v2/product/' . get_the_ID() ) ),
            'orderUrl'  => esc_url_raw( rest_url( 'honeyscroop/v1/submit-order' ) ),
            'nonce'     => wp_create_nonce( 'wp_rest' ),
        ) );
    }

    // Cart Page
    if ( is_page_template( 'page-cart.php' ) ) {
        $cart_js = defined( 'WP_ENV' ) && 'development' === WP_ENV
            ? 'http://localhost:5173/src/shop/cart.jsx'
            : honeyscroop_get_asset_path( 'shop-cart.js' );

        wp_enqueue_script( 'honeyscroop-shop-cart', $cart_js, array(), null, true );

        wp_localize_script( 'honeyscroop-shop-cart', 'cartData', array(
            'orderUrl' => esc_url_raw( rest_url( 'honeyscroop/v1/submit-order' ) ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
        ) );
    }

    // FAQs Page
    if ( is_page_template( 'page-faqs.php' ) ) {
        $faq_js = defined( 'WP_ENV' ) && 'development' === WP_ENV
            ? 'http://localhost:5173/src/faqs/index.jsx'
            : honeyscroop_get_asset_path( 'faqs.js' );

        wp_enqueue_script( 'honeyscroop-faqs', $faq_js, array(), null, true );
        
        wp_localize_script( 'honeyscroop-faqs', 'faqData', array(
            'restUrl' => esc_url_raw( rest_url( 'wp/v2/faq' ) ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'honeyscroop_enqueue_assets' );

// Editor styles enqueued via add_editor_style in functions.php

/**
 * Add type="module" to enqueued React scripts.
 *
 * @param string $tag    The <script> tag.
 * @param string $handle The script handle.
 * @return string The modified <script> tag.
 */

/**
 * Localize global data for all scripts (Currency, Settings)
 */
function honeyscroop_localize_global_data() {
    $currency_settings = get_option('honeyscroop_option_currency', []);
    
    // Default fallback if empty
    if (empty($currency_settings)) {
        $currency_settings = [
            'activeCurrencies' => ['USD'],
            'rates' => ['USD' => 1]
        ];
    }

    $data = array(
        'currencySettings' => $currency_settings,
        'settings'         => get_option( 'honeyscroop_option_settings', array() ),
        'siteUrl'          => home_url(),
        'restUrl'          => rest_url(),
        'nonce'            => wp_create_nonce( 'wp_rest' ),
    );

    wp_register_script('honeyscroop-global', '', [], '', false); // Load in Head
    wp_enqueue_script('honeyscroop-global');
    wp_localize_script('honeyscroop-global', 'honeyShopData', $data);
}
add_action('wp_enqueue_scripts', 'honeyscroop_localize_global_data');


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
        'honeyscroop-shop-archive',
        'honeyscroop-shop-single',
        'honeyscroop-shop-cart',
        'honeyscroop-faqs',
        'honeyscroop-admin-spa',
        'honeyscroop-vendor',
	);

	if ( in_array( $handle, $module_handles, true ) ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'honeyscroop_add_module_type_to_scripts', 10, 2 );

/**
 * Helper to inline small CSS files.
 *
 * @param string $handle The stylesheet handle.
 * @param string $src    The stylesheet URL.
 */
function honeyscroop_inline_css( string $handle, string $src ): void {
	$path = '';

	if ( strpos( $src, HONEYSCROOP_URI ) !== false ) {
		$path = str_replace( HONEYSCROOP_URI, HONEYSCROOP_DIR, $src );
	} elseif ( strpos( $src, get_template_directory_uri() ) !== false ) {
		$path = str_replace( get_template_directory_uri(), get_template_directory(), $src );
	}

	if ( $path && file_exists( $path ) ) {
		$content = file_get_contents( $path );
		// Basic minification: remove comments and extra whitespace
		$content = preg_replace( '/\/\*.*?\*\//s', '', $content );
		$content = preg_replace( '/\s+/', ' ', $content );
		printf( '<style id="%s-inline">%s</style>', esc_attr( $handle ), $content );
	} else {
		// Fallback to enqueue if file not found or external
		wp_enqueue_style( $handle, $src );
	}
}

