<?php
/**
 * Product Reviews Logic
 *
 * Handles review submissions, rating calculation, and cookie prevention.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register REST API routes for reviews.
 */
function honeyscroop_register_review_routes() {
	register_rest_route( 'honeyscroop/v1', '/submit-review', array(
		'methods'             => 'POST',
		'callback'            => 'honeyscroop_submit_review',
		'permission_callback' => '__return_true', // Anyone can review for now (spam filtered)
	) );

    register_rest_route( 'honeyscroop/v1', '/product-reviews/(?P<id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'honeyscroop_get_product_reviews',
		'permission_callback' => '__return_true',
	) );
}
add_action( 'rest_api_init', 'honeyscroop_register_review_routes' );

/**
 * Handle Review Submission.
 */
function honeyscroop_submit_review( WP_REST_Request $request ) {
	$product_id = (int) $request->get_param( 'product_id' );
	$rating     = (int) $request->get_param( 'rating' );
	$name       = sanitize_text_field( $request->get_param( 'name' ) );
	$email      = sanitize_email( $request->get_param( 'email' ) );
	$comment    = sanitize_textarea_field( $request->get_param( 'comment' ) );

	if ( ! $product_id || ! $rating || ! $name || ! $email || ! $comment ) {
		return new WP_Error( 'missing_fields', 'All fields are required.', array( 'status' => 400 ) );
	}

    // Prevension: Check Cookie
    $cookie_name = 'honeyscroop_reviewed_' . $product_id;
    if ( isset( $_COOKIE[ $cookie_name ] ) ) {
        return new WP_Error( 'already_reviewed', 'You have already reviewed this product.', array( 'status' => 403 ) );
    }

	$comment_data = array(
		'comment_post_ID'      => $product_id,
		'comment_author'       => $name,
		'comment_author_email' => $email,
		'comment_content'      => $comment,
		'comment_type'         => 'review',
		'comment_parent'       => 0,
		'user_id'              => get_current_user_id(),
		'comment_approved'     => 1, // Auto-approve for demo, usually holds for moderation
	);

	$comment_id = wp_insert_comment( $comment_data );

	if ( $comment_id ) {
		// Store rating meta
		update_comment_meta( $comment_id, '_rating', $rating );

		// Update product stats
		honeyscroop_update_product_rating_stats( $product_id );

        // Set Cookie (valid for 30 days)
        setcookie( $cookie_name, '1', time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Review submitted successfully!',
		), 200 );
	}

	return new WP_Error( 'submission_failed', 'Failed to submit review.', array( 'status' => 500 ) );
}

/**
 * Get Reviews for a Product.
 */
function honeyscroop_get_product_reviews( WP_REST_Request $request ) {
    $product_id = (int) $request->get_param( 'id' );
    
    $comments = get_comments( array(
        'post_id' => $product_id,
        'status'  => 'approve',
        'type'    => 'review',
    ) );

    $formatted = array();
    foreach ( $comments as $c ) {
        $formatted[] = array(
            'id'      => $c->comment_ID,
            'author'  => $c->comment_author,
            'date'    => get_comment_date( 'M j, Y', $c ),
            'content' => $c->comment_content,
            'rating'  => (int) get_comment_meta( $c->comment_ID, '_rating', true ),
        );
    }

    return new WP_REST_Response( array(
        'reviews' => $formatted,
        'has_reviewed' => isset( $_COOKIE[ 'honeyscroop_reviewed_' . $product_id ] ),
    ), 200 );
}

/**
 * Calculate and Update Product Rating Stats.
 */
function honeyscroop_update_product_rating_stats( int $product_id ) {
	$comments = get_comments( array(
		'post_id' => $product_id,
		'status'  => 'approve',
		'type'    => 'review',
	) );

	$total_rating = 0;
	$count        = 0;

	foreach ( $comments as $c ) {
		$rating = get_comment_meta( $c->comment_ID, '_rating', true );
		if ( $rating ) {
			$total_rating += (int) $rating;
			$count++;
		}
	}

	$average = $count > 0 ? round( $total_rating / $count, 1 ) : 0;

	update_post_meta( $product_id, '_product_average_rating', $average );
	update_post_meta( $product_id, '_product_review_count', $count );
}
