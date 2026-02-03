<?php
/**
 * Magic Link Authentication
 *
 * Provides passwordless login via email magic links.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate a magic link token for a user.
 *
 * @param int $user_id User ID.
 * @return string The magic link token.
 */
function honeyscroop_generate_magic_token( $user_id ) {
	$token = wp_generate_password( 32, false, false );
	$expiry = time() + ( 24 * HOUR_IN_SECONDS ); // 24 hours
	
	// Store token hash for security
	update_user_meta( $user_id, '_magic_link_token', wp_hash_password( $token ) );
	update_user_meta( $user_id, '_magic_link_expiry', $expiry );
	
	return $token;
}

/**
 * Verify a magic link token.
 *
 * @param int $user_id User ID.
 * @param string $token Token to verify.
 * @return bool Whether the token is valid.
 */
function honeyscroop_verify_magic_token( $user_id, $token ) {
	$stored_hash = get_user_meta( $user_id, '_magic_link_token', true );
	$expiry = get_user_meta( $user_id, '_magic_link_expiry', true );
	
	if ( empty( $stored_hash ) || empty( $expiry ) ) {
		return false;
	}
	
	// Check expiry
	if ( time() > $expiry ) {
		honeyscroop_clear_magic_token( $user_id );
		return false;
	}
	
	// Verify token
	if ( ! wp_check_password( $token, $stored_hash ) ) {
		return false;
	}
	
	return true;
}

/**
 * Clear magic link token after use.
 *
 * @param int $user_id User ID.
 */
function honeyscroop_clear_magic_token( $user_id ) {
	delete_user_meta( $user_id, '_magic_link_token' );
	delete_user_meta( $user_id, '_magic_link_expiry' );
}

/**
 * Register REST API routes for magic link.
 */
function honeyscroop_register_magic_link_routes() {
	register_rest_route( 'honeyscroop/v1', '/send-magic-link', array(
		'methods'             => 'POST',
		'callback'            => 'honeyscroop_handle_send_magic_link',
		'permission_callback' => '__return_true',
		'args'                => array(
			'email' => array(
				'required' => true,
				'type'     => 'string',
				'format'   => 'email',
			),
		),
	) );
}
add_action( 'rest_api_init', 'honeyscroop_register_magic_link_routes' );

/**
 * Handle magic link send request.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response Response.
 */
function honeyscroop_handle_send_magic_link( WP_REST_Request $request ) {
	$email = sanitize_email( $request->get_param( 'email' ) );
	
	if ( empty( $email ) ) {
		return new WP_REST_Response( array(
			'success' => false,
			'message' => 'Please provide a valid email address.',
		), 400 );
	}
	
	// Always return success (even if user doesn't exist) to prevent enumeration
	$user = get_user_by( 'email', $email );
	
	if ( $user ) {
		// Generate token and link
		$token = honeyscroop_generate_magic_token( $user->ID );
		$magic_link = add_query_arg( array(
			'magic_login' => '1',
			'user_id'     => $user->ID,
			'token'       => $token,
		), home_url() );
		
		// Send email
		if ( function_exists( 'honeyscroop_send_magic_link_email' ) ) {
			honeyscroop_send_magic_link_email( $email, $user->display_name, $magic_link );
		}
	}
	
	// Always return the same response
	return new WP_REST_Response( array(
		'success' => true,
		'message' => 'If an account exists with this email, you will receive a magic link shortly.',
	), 200 );
}

/**
 * Process magic link login on page load.
 */
function honeyscroop_process_magic_login() {
	if ( empty( $_GET['magic_login'] ) || empty( $_GET['user_id'] ) || empty( $_GET['token'] ) ) {
		return;
	}
	
	// Already logged in
	if ( is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/account/' ) );
		exit;
	}
	
	$user_id = absint( $_GET['user_id'] );
	$token = sanitize_text_field( $_GET['token'] );
	
	if ( honeyscroop_verify_magic_token( $user_id, $token ) ) {
		// Clear token (one-time use)
		honeyscroop_clear_magic_token( $user_id );
		
		// Log user in
		wp_set_auth_cookie( $user_id, true );
		wp_set_current_user( $user_id );
		
		// Redirect to account page
		wp_safe_redirect( home_url( '/account/' ) );
		exit;
	} else {
		// Invalid or expired token
		wp_safe_redirect( add_query_arg( 'magic_error', 'expired', home_url( '/account/' ) ) );
		exit;
	}
}
add_action( 'template_redirect', 'honeyscroop_process_magic_login', 1 );

/**
 * Add magic link form to login page.
 */
function honeyscroop_add_magic_link_form() {
	?>
	<div id="magic-link-section" style="display: none; margin-top: 24px; padding-top: 24px; border-top: 1px solid #E5E7EB;">
		<p style="text-align: center; color: #6B7280; font-size: 14px; margin-bottom: 16px;">
			— Or sign in without a password —
		</p>
		<div id="magic-link-form">
			<input 
				type="email" 
				id="magic-link-email" 
				placeholder="Enter your email"
				style="width: 100%; padding: 12px 16px; border: 2px solid #E5E7EB; border-radius: 12px; font-size: 16px; margin-bottom: 12px; outline: none;"
			>
			<button 
				type="button" 
				id="magic-link-btn"
				style="width: 100%; padding: 14px; background: linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%); color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer;"
			>
				✨ Send Magic Link
			</button>
			<p id="magic-link-message" style="text-align: center; margin-top: 12px; font-size: 14px; display: none;"></p>
		</div>
	</div>

	<div id="magic-link-toggle-wrapper" style="text-align: center; margin-top: 20px;">
		<button 
			type="button" 
			id="toggle-magic-link"
			style="background: none; border: none; color: #9CA3AF; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: color 0.2s;"
			onmouseover="this.style.color='#D97706'"
			onmouseout="this.style.color='#9CA3AF'"
		>
			✨ Use Magic Link instead
		</button>
	</div>

	<script>
		document.getElementById('toggle-magic-link').addEventListener('click', function() {
			const section = document.getElementById('magic-link-section');
			const wrapper = document.getElementById('magic-link-toggle-wrapper');
			section.style.display = 'block';
			wrapper.style.display = 'none';
		});

		document.getElementById('magic-link-btn').addEventListener('click', async function() {
			const email = document.getElementById('magic-link-email').value;
			const btn = this;
			const msg = document.getElementById('magic-link-message');
			
			if (!email) {
				msg.textContent = 'Please enter your email';
				msg.style.color = '#EF4444';
				msg.style.display = 'block';
				return;
			}
			
			btn.disabled = true;
			btn.textContent = 'Sending...';
			
			try {
				const res = await fetch('<?php echo esc_url( rest_url( 'honeyscroop/v1/send-magic-link' ) ); ?>', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ email })
				});
				const data = await res.json();
				
				msg.textContent = data.message;
				msg.style.color = data.success ? '#10B981' : '#EF4444';
				msg.style.display = 'block';
				
				if (data.success) {
					btn.textContent = '✓ Check Your Email';
					btn.style.background = '#10B981';
				} else {
					btn.textContent = '✨ Send Magic Link';
					btn.disabled = false;
				}
			} catch (e) {
				msg.textContent = 'Something went wrong. Please try again.';
				msg.style.color = '#EF4444';
				msg.style.display = 'block';
				btn.textContent = '✨ Send Magic Link';
				btn.disabled = false;
			}
		});
	</script>
	<?php
}
// This will be called from account.php or login-styling.php
