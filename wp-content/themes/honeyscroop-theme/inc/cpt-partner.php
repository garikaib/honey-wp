<?php
/**
 * Custom Post Type: Partner
 *
 * @package Honeyscroop
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register 'partner' post type.
 */
function honeyscroop_register_partner_cpt(): void {
	$labels = array(
		'name'               => _x( 'Partners', 'post type general name', 'honeyscroop' ),
		'singular_name'      => _x( 'Partner', 'post type singular name', 'honeyscroop' ),
		'menu_name'          => _x( 'Partners', 'admin menu', 'honeyscroop' ),
		'name_admin_bar'     => _x( 'Partner', 'add new on admin bar', 'honeyscroop' ),
		'add_new'            => _x( 'Add New', 'partner', 'honeyscroop' ),
		'add_new_item'       => __( 'Add New Partner', 'honeyscroop' ),
		'new_item'           => __( 'New Partner', 'honeyscroop' ),
		'edit_item'          => __( 'Edit Partner', 'honeyscroop' ),
		'view_item'          => __( 'View Partner', 'honeyscroop' ),
		'all_items'          => __( 'All Partners', 'honeyscroop' ),
		'search_items'       => __( 'Search Partners', 'honeyscroop' ),
		'not_found'          => __( 'No partners found.', 'honeyscroop' ),
		'not_found_in_trash' => __( 'No partners found in Trash.', 'honeyscroop' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => false, // No single partner page needed
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'partner' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'          => 'dashicons-groups',
		'supports'           => array( 'title', 'thumbnail' ), // Only need Title and Logo
		'show_in_rest'       => true, // Essential for React fetching
	);

	register_post_type( 'partner', $args );
}
add_action( 'init', 'honeyscroop_register_partner_cpt' );
