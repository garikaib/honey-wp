<?php
/**
 * Simple Newsletter System
 *
 * @package Honeyscroop
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Newsletter Post Types
 */
function honeyscroop_register_newsletter_cpts(): void {
	// 1. Newsletters (Composed by Admin)
	register_post_type( 'honey_newsletter', array(
		'labels' => array(
			'name'          => 'Newsletters',
			'singular_name' => 'Newsletter',
			'add_new'       => 'Compose Newsletter',
			'add_new_item'  => 'Compose New Newsletter',
			'edit_item'     => 'Edit Newsletter',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'supports'     => array( 'title', 'editor', 'author' ), // Block editor enabled
		'menu_icon'    => 'dashicons-email',
		'show_in_rest' => true, // Important for Block Editor
	));

	// 2. Subscribers (Hidden CPT)
	register_post_type( 'honey_subscriber', array(
		'labels' => array(
			'name'          => 'Subscribers',
			'singular_name' => 'Subscriber',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'edit.php?post_type=honey_newsletter', // Sub-menu of Newsletters
		'supports'     => array( 'title' ), // 'Title' will store Email
		'capabilities' => array(
			'create_posts' => 'do_not_allow', // Admin shouldn't manually add lots via UI usually
		),
		'map_meta_cap' => true,
	));
}
add_action( 'init', 'honeyscroop_register_newsletter_cpts' );


/**
 * AJAX Handler: Subscribe
 */
function honeyscroop_handle_subscribe(): void {
	check_ajax_referer( 'wp_rest', 'nonce' );

	$email = sanitize_email( $_POST['email'] );

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
	}

	// Check if already exists
	$existing = new WP_Query(array(
		'post_type' => 'honey_subscriber',
		'title'     => $email,
		'post_status' => 'any',
	));

	if ( $existing->have_posts() ) {
		// If unsubscribed, maybe re-subscribe? For now, just say thanks.
		wp_send_json_success( array( 'message' => 'You are already in the hive!' ) );
	}

	// Create Subscriber
	$post_id = wp_insert_post( array(
		'post_title'  => $email,
		'post_type'   => 'honey_subscriber',
		'post_status' => 'publish',
	));

	if ( $post_id ) {
		wp_send_json_success( array( 'message' => 'Welcome to the Hive! Check your inbox soon.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'Could not subscribe. Try again.' ) );
	}
}
add_action( 'wp_ajax_honeyscroop_subscribe', 'honeyscroop_handle_subscribe' );
add_action( 'wp_ajax_nopriv_honeyscroop_subscribe', 'honeyscroop_handle_subscribe' );


/**
 * Meta Box: Send Newsletter
 */
function honeyscroop_add_newsletter_meta_box(): void {
	add_meta_box(
		'honeyscroop_newsletter_send',
		'Send Newsletter',
		'honeyscroop_render_send_meta_box',
		'honey_newsletter',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'honeyscroop_add_newsletter_meta_box' );

function honeyscroop_render_send_meta_box( $post ): void {
	$status = get_post_meta( $post->ID, '_newsletter_status', true ) ?: 'Draft';
	$sent_count = get_post_meta( $post->ID, '_newsletter_sent_count', true ) ?: 0;
	$sent_date = get_post_meta( $post->ID, '_newsletter_sent_date', true );

	echo '<div id="honey-newsletter-admin-box" style="padding: 10px 0;">';
	echo '<p><strong>Current Status:</strong> <span id="newsletter-status-text" style="color: ' . ( $status === 'Sent' ? '#10b981' : '#f59e0b' ) . '; font-weight: bold;">' . esc_html( $status ) . '</span></p>';
	
	echo '<div id="newsletter-stats-area" style="' . ( $sent_date ? '' : 'display:none;' ) . '">';
	echo '<p><strong>Last Sent:</strong> <span id="last-sent-date">' . ( $sent_date ? esc_html( date( 'M j, Y @ H:i', strtotime( $sent_date ) ) ) : '' ) . '</span></p>';
	echo '<p><strong>Recipients:</strong> <span id="sent-recipient-count">' . esc_html( $sent_count ) . '</span></p>';
	echo '</div>';

	// Main Actions
	echo '<div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">';
	
	// Send to All
	$send_disabled = ( $post->post_status !== 'publish' || $status === 'Sent' ) ? 'disabled' : '';
	echo '<button type="button" id="honey-send-all-btn" class="button button-primary button-large" ' . $send_disabled . '>🚀 Send to All Subscribers</button>';
	
	// Reset (Visible if Sent)
	$reset_style = ( $status === 'Sent' ) ? '' : 'display:none;';
	echo '<button type="button" id="honey-reset-btn" class="button button-secondary" style="margin-top:10px;' . $reset_style . '">Re-enable Global Sending</button>';
	
	if ( $post->post_status !== 'publish' ) {
		echo '<p class="description" style="color: #dc2626; margin-top: 5px;">! Publish post to enable global sending.</p>';
	}
	echo '</div>';

	// Test Email Section
	echo '<div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">';
	echo '<label for="test_email"><strong>Send Test Email:</strong></label>';
	echo '<input type="email" id="honey-test-email" placeholder="your@email.com" class="widefat" style="margin: 5px 0;">';
	echo '<button type="button" id="honey-send-test-btn" class="button" style="margin-top: 5px;">Send Test</button>';
	echo '</div>';

	// Loading Overlay / Response
	echo '<div id="newsletter-admin-feedback" style="margin-top:10px; font-weight:bold; display:none;"></div>';

	echo '</div>';

	?>
	<script>
	(function($) {
		$(document).ready(function() {
			const $box = $('#honey-newsletter-admin-box');
			const $feedback = $('#newsletter-admin-feedback');
			const postId = <?php echo (int) $post->ID; ?>;

			function showFeedback(msg, isSuccess = true) {
				$feedback.text(msg).css('color', isSuccess ? '#10b981' : '#dc2626').fadeIn();
				setTimeout(() => $feedback.fadeOut(), 5000);
			}

			// Send to All
			$('#honey-send-all-btn').on('click', function() {
				if (!confirm('Are you sure you want to send this to ALL active subscribers?')) return;
				
				const $btn = $(this);
				$btn.prop('disabled', true).text('⌛ Sending...');
				
				$.post(ajaxurl, {
					action: 'honeyscroop_newsletter_action',
					sub_action: 'send_all',
					post_id: postId,
					nonce: '<?php echo wp_create_nonce("newsletter_admin_nonce"); ?>'
				}, function(response) {
					if (response.success) {
						showFeedback('Success! Newsletter sent.');
						location.reload(); // Simplest to refresh for full UI update
					} else {
						showFeedback(response.data.message || 'Error sending.', false);
						$btn.prop('disabled', false).text('🚀 Send to All Subscribers');
					}
				});
			});

			// Reset
			$('#honey-reset-btn').on('click', function() {
				const $btn = $(this);
				$btn.prop('disabled', true).text('⌛ Resetting...');
				
				$.post(ajaxurl, {
					action: 'honeyscroop_newsletter_action',
					sub_action: 'reset',
					post_id: postId,
					nonce: '<?php echo wp_create_nonce("newsletter_admin_nonce"); ?>'
				}, function(response) {
					if (response.success) {
						location.reload();
					} else {
						showFeedback('Error resetting.', false);
						$btn.prop('disabled', false).text('Re-enable Global Sending');
					}
				});
			});

			// Test
			$('#honey-send-test-btn').on('click', function() {
				const email = $('#honey-test-email').val();
				if (!email) return alert('Enter a test email.');
				
				const $btn = $(this);
				$btn.prop('disabled', true).text('⌛ Sending...');
				
				$.post(ajaxurl, {
					action: 'honeyscroop_newsletter_action',
					sub_action: 'send_test',
					post_id: postId,
					email: email,
					nonce: '<?php echo wp_create_nonce("newsletter_admin_nonce"); ?>'
				}, function(response) {
					$btn.prop('disabled', false).text('Send Test');
					if (response.success) {
						showFeedback('Test email sent to ' + email);
					} else {
						showFeedback(response.data.message || 'Error sending test.', false);
					}
				});
			});
		});
	})(jQuery);
	</script>
	<?php
}

/**
 * AJAX Handler: Admin Newsletter Actions
 */
function honeyscroop_handle_newsletter_admin_action(): void {
	check_ajax_referer( 'newsletter_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ) );
	}

	$post_id = (int) ( $_POST['post_id'] ?? 0 );
	$sub_action = sanitize_text_field( $_POST['sub_action'] ?? '' );

	if ( ! $post_id || ! $sub_action ) {
		wp_send_json_error( array( 'message' => 'Missing data' ) );
	}

	// Helper: Get Email Data
	$content_raw = get_post_field( 'post_content', $post_id );
	if ( ! $content_raw ) {
		wp_send_json_error( array( 'message' => 'Post content empty. Save as draft first.' ) );
	}

	$content_html = apply_filters( 'the_content', $content_raw );
	$subject = get_the_title( $post_id );
	$headers = array('Content-Type: text/html; charset=UTF-8');
	$headers[] = 'From: Honeyscoop <hello@' . $_SERVER['HTTP_HOST'] . '>';

	// ACTION: send_test
	if ( $sub_action === 'send_test' ) {
		$email = sanitize_email( $_POST['email'] ?? '' );
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'Invalid email' ) );
		}

		$message = honeyscroop_get_email_template( $content_html, '#' );
		$sent = wp_mail( $email, '[TEST] ' . $subject, $message, $headers );

		if ( $sent ) {
			wp_send_json_success( array( 'message' => 'Test sent!' ) );
		} else {
			wp_send_json_error( array( 'message' => 'wp_mail failed. Check server logs.' ) );
		}
	}

	// ACTION: send_all
	if ( $sub_action === 'send_all' ) {
		if ( get_post_status( $post_id ) !== 'publish' ) {
			wp_send_json_error( array( 'message' => 'Post must be Published to send to everyone.' ) );
		}

		$subscribers = get_posts(array(
			'post_type'   => 'honey_subscriber',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields'      => 'ids',
		));

		if ( empty( $subscribers ) ) {
			wp_send_json_error( array( 'message' => 'No active subscribers found.' ) );
		}

		$count = 0;
		foreach ( $subscribers as $sub_id ) {
			$email = get_the_title( $sub_id );
			$token = wp_hash( 'unsubscribe_' . $sub_id . '_' . $email );
			$unsub_link = home_url( '/?honeyscroop_action=unsubscribe&id=' . $sub_id . '&token=' . $token );

			$message = honeyscroop_get_email_template( $content_html, $unsub_link );
			$sent = wp_mail( $email, $subject, $message, $headers );
			if ( $sent ) $count++;
		}

		update_post_meta( $post_id, '_newsletter_status', 'Sent' );
		update_post_meta( $post_id, '_newsletter_sent_count', $count );
		update_post_meta( $post_id, '_newsletter_sent_date', current_time( 'mysql' ) );

		wp_send_json_success( array( 'count' => $count ) );
	}

	// ACTION: reset
	if ( $sub_action === 'reset' ) {
		delete_post_meta( $post_id, '_newsletter_status' );
		wp_send_json_success();
	}

	wp_send_json_error( array( 'message' => 'Unknown action' ) );
}
add_action( 'wp_ajax_honeyscroop_newsletter_action', 'honeyscroop_handle_newsletter_admin_action' );

/**
 * Handle Manual Sending Actions (LEGACY - we use AJAX now, but keep for fallback/safety)
 */
function honeyscroop_process_newsletter_actions( int $post_id ): void {
	// ... (This can stay or be removed, AJAX is better)
}

/**
 * Email Template Helper
 */
function honeyscroop_get_email_template( string $content_html, string $unsub_link ): string {
	$message = '<!DOCTYPE html><html><body style="font-family: serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; background: #FFFBEB; padding: 20px;">';
	$message .= '<div style="text-align: center; margin-bottom: 30px;"><h1 style="color: #d97706;">Honeyscoop</h1></div>';
	$message .= '<div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
	$message .= $content_html;
	$message .= '</div>';
	$message .= '<div style="text-align: center; font-size: 12px; color: #999; margin-top: 30px;">';
	$message .= '<p>You are receiving this because you joined the hive at ' . home_url() . '</p>';
	$message .= '<p><a href="' . esc_url( $unsub_link ) . '" style="color: #d97706; font-weight: bold; text-decoration: none;">Unsubscribe</a></p>';
	$message .= '</div></body></html>';
	return $message;
}

/**
 * Handle Admin Notices
 */
function honeyscroop_newsletter_admin_notices(): void {
	$notice = get_transient( 'newsletter_admin_notice_' . get_current_user_id() );
	if ( $notice ) {
		delete_transient( 'newsletter_admin_notice_' . get_current_user_id() );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'honeyscroop_newsletter_admin_notices' ); // Simplistic approach: requires clicking 'Update' with button named, usually safer on admin_post hook but this works for simple form submission within edit screen if button is inside form.
// Actually, 'save_post' runs on save. The button submit inside the form triggers a save. 

/**
 * Handle Unsubscribe Request
 */
function honeyscroop_handle_unsubscribe(): void {
	if ( isset( $_GET['honeyscroop_action'] ) && $_GET['honeyscroop_action'] === 'unsubscribe' ) {
		$sub_id = (int) $_GET['id'];
		$token  = $_GET['token'];
		$email  = get_the_title( $sub_id );

		if ( $token === wp_hash( 'unsubscribe_' . $sub_id . '_' . $email ) ) {
			// Trash subscriber
			wp_trash_post( $sub_id );
			wp_die( 'You have been successfully unsubscribed from the Hive. We will miss you! <a href="' . home_url() . '">Return Home</a>', 'Unsubscribed' );
		} else {
			wp_die( 'Invalid unsubscribe link.', 'Error' );
		}
	}
}
add_action( 'template_redirect', 'honeyscroop_handle_unsubscribe' );
