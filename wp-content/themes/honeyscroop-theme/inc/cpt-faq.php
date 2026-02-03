<?php
/**
 * FAQ Custom Post Type
 *
 * Registers the 'faq' post type for managing Frequently Asked Questions.
 * Includes automatic Schema.org JSON-LD generation.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register FAQ Custom Post Type.
 */
function honeyscroop_register_faq_cpt() {
	$labels = array(
		'name'               => _x( 'FAQs', 'post type general name', 'honeyscroop' ),
		'singular_name'      => _x( 'FAQ', 'post type singular name', 'honeyscroop' ),
		'menu_name'          => _x( 'FAQs', 'admin menu', 'honeyscroop' ),
		'name_admin_bar'     => _x( 'FAQ', 'add new on admin bar', 'honeyscroop' ),
		'add_new'            => _x( 'Add New', 'faq', 'honeyscroop' ),
		'add_new_item'       => __( 'Add New FAQ', 'honeyscroop' ),
		'new_item'           => __( 'New FAQ', 'honeyscroop' ),
		'edit_item'          => __( 'Edit FAQ', 'honeyscroop' ),
		'view_item'          => __( 'View FAQ', 'honeyscroop' ),
		'all_items'          => __( 'All FAQs', 'honeyscroop' ),
		'search_items'       => __( 'Search FAQs', 'honeyscroop' ),
		'not_found'          => __( 'No FAQs found.', 'honeyscroop' ),
		'not_found_in_trash' => __( 'No FAQs found in Trash.', 'honeyscroop' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true, // Publicly queryable
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'faq' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-format-chat',
		'supports'           => array( 'title', 'editor' ), // Title = Question, Editor = Answer
		'show_in_rest'       => true, // Essential for React Frontend
	);

	register_post_type( 'faq', $args );
}
add_action( 'init', 'honeyscroop_register_faq_cpt' );

/**
 * Output FAQ Schema.org JSON-LD.
 *
 * Automatically generates FAQPage schema for the FAQs page.
 */
function honeyscroop_faq_schema() {
	// Only output on the specific FAQs page template or archive
	if ( ! is_page_template( 'page-faqs.php' ) && ! is_post_type_archive( 'faq' ) ) {
		return;
	}

	$args = array(
		'post_type'      => 'faq',
		'posts_per_page' => -1, // Get all FAQs
		'post_status'    => 'publish',
	);

	$faqs = get_posts( $args );

	if ( empty( $faqs ) ) {
		return;
	}

	$schema_data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array(),
	);

	foreach ( $faqs as $faq ) {
		// Strip tags from content to ensure clean JSON text, or allow basic HTML if Schema supports it (usually better plain text for summaries)
		// For Answer, Schema allows HTML, so we use the full content.
		$schema_data['mainEntity'][] = array(
			'@type'          => 'Question',
			'name'           => $faq->post_title,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => apply_filters( 'the_content', $faq->post_content ),
			),
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema_data ) . '</script>';
}
add_action( 'wp_head', 'honeyscroop_faq_schema' );
