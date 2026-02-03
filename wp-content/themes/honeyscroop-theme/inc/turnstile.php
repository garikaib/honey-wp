<?php
/**
 * Cloudflare Turnstile Integration
 *
 * Provides reusable widget rendering and server-side verification
 * for bot protection on forms.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load credentials from .env file
function honeyscroop_load_turnstile_credentials() {
	static $credentials = null;
	
	if ( $credentials !== null ) {
		return $credentials;
	}
	
	$env_file = get_stylesheet_directory() . '/.env';
	$credentials = array(
		'site_key'   => '',
		'secret_key' => ''
	);
	
	if ( file_exists( $env_file ) ) {
		$lines = file( $env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		foreach ( $lines as $line ) {
			if ( strpos( $line, '#' ) === 0 ) continue;
			if ( strpos( $line, '=' ) === false ) continue;
			
			list( $key, $value ) = explode( '=', $line, 2 );
			$key = trim( $key );
			$value = trim( $value );
			
			if ( $key === 'TURNSTILE_SITE_KEY' ) {
				$credentials['site_key'] = $value;
			} elseif ( $key === 'TURNSTILE_SECRET_KEY' ) {
				$credentials['secret_key'] = $value;
			}
		}
	}
	
	return $credentials;
}

/**
 * Enqueue Turnstile script.
 */
function honeyscroop_enqueue_turnstile() {
	$creds = honeyscroop_load_turnstile_credentials();
	if ( empty( $creds['site_key'] ) ) {
		return;
	}
	
	wp_enqueue_script(
		'cloudflare-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null,
		false // Move to head as recommended by Cloudflare for better preloading
	);
}

/**
 * Add async and defer attributes to the Turnstile script.
 */
function honeyscroop_add_async_defer_to_turnstile( $tag, $handle ) {
	if ( 'cloudflare-turnstile' !== $handle ) {
		return $tag;
	}
	return str_replace( ' src', ' async defer src', $tag );
}
add_filter( 'script_loader_tag', 'honeyscroop_add_async_defer_to_turnstile', 10, 2 );

/**
 * Render Turnstile Widget HTML.
 *
 * @param string $form_id Unique identifier for the form.
 * @return string HTML for the widget.
 */
function honeyscroop_render_turnstile( $form_id = 'default' ) {
	$creds = honeyscroop_load_turnstile_credentials();
	
	if ( empty( $creds['site_key'] ) ) {
		return '<!-- Turnstile: No site key configured -->';
	}
	
	// Enqueue script if not already done
	honeyscroop_enqueue_turnstile();
	
	return sprintf(
		'<div class="turnstile-wrapper" style="margin: 1.5rem 0;">
			<div class="cf-turnstile" data-sitekey="%s" data-theme="light" data-size="normal" id="turnstile-%s"></div>
		</div>',
		esc_attr( $creds['site_key'] ),
		esc_attr( $form_id )
	);
}

/**
 * Verify Turnstile Response.
 *
 * @param string $token The cf-turnstile-response token from form submission.
 * @return bool True if verified, false otherwise.
 */
function honeyscroop_verify_turnstile( $token ) {
	$creds = honeyscroop_load_turnstile_credentials();
	
	if ( empty( $creds['secret_key'] ) || empty( $token ) ) {
		honeyscroop_log_turnstile_failure( 'Missing credentials or token' );
		return false;
	}
	
	$response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
		'body' => array(
			'secret'   => $creds['secret_key'],
			'response' => $token,
			'remoteip' => honeyscroop_get_client_ip()
		),
		'timeout' => 10
	) );
	
	if ( is_wp_error( $response ) ) {
		honeyscroop_log_turnstile_failure( 'API request failed: ' . $response->get_error_message() );
		return false;
	}
	
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	
	if ( ! isset( $body['success'] ) || $body['success'] !== true ) {
		$error_codes = isset( $body['error-codes'] ) ? implode( ', ', $body['error-codes'] ) : 'unknown';
		honeyscroop_log_turnstile_failure( 'Verification failed: ' . $error_codes );
		return false;
	}
	
	return true;
}

/**
 * Get client IP address.
 */
function honeyscroop_get_client_ip() {
	$ip = '';
	
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
		$ip = trim( $ips[0] );
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * Log Turnstile failures for fail2ban parsing.
 *
 * Log format: [TURNSTILE_FAIL] IP=x.x.x.x REASON=xxx
 *
 * @param string $reason Failure reason.
 */
function honeyscroop_log_turnstile_failure( $reason = '' ) {
	$ip = honeyscroop_get_client_ip();
	$log_message = sprintf(
		'[TURNSTILE_FAIL] IP=%s REASON=%s URI=%s',
		$ip,
		$reason,
		isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : 'unknown'
	);
	
	// Log to PHP error log (which can be parsed by fail2ban)
	error_log( $log_message );
	
	// Also log to dedicated file if writable
	$log_file = WP_CONTENT_DIR . '/turnstile-failures.log';
	
	// Log rotation: Keep it under 1MB for easy parsing
	if ( file_exists( $log_file ) && filesize( $log_file ) > 1024 * 1024 ) {
		// Use .old extension for the rotated log
		rename( $log_file, $log_file . '.old' );
	}
	
	$timestamp = date( 'Y-m-d H:i:s' );
	file_put_contents( $log_file, "[$timestamp] $log_message\n", FILE_APPEND | LOCK_EX );
}

// ============================================
// WORDPRESS LOGIN FORM INTEGRATION
// ============================================

/**
 * Add Turnstile to WP Login Form.
 */
function honeyscroop_add_turnstile_to_login() {
	if ( is_user_logged_in() ) {
		return;
	}
	
	echo honeyscroop_render_turnstile( 'wp-login' );
}
add_action( 'login_form', 'honeyscroop_add_turnstile_to_login' );

/**
 * Verify Turnstile on WP Login.
 */
function honeyscroop_verify_turnstile_on_login( $user, $username, $password ) {
	// Skip if empty credentials (not a real login attempt)
	if ( empty( $username ) && empty( $password ) ) {
		return $user;
	}
	
	// Check if credentials are configured
	$creds = honeyscroop_load_turnstile_credentials();
	if ( empty( $creds['site_key'] ) || empty( $creds['secret_key'] ) || (defined('WP_ENV') && WP_ENV === 'development') ) {
		return $user; // Skip verification if not configured or in development
	}
	
	$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
	
	if ( empty( $token ) ) {
		honeyscroop_log_turnstile_failure( 'No token submitted on login' );
		return new WP_Error( 'turnstile_missing', '<strong>Security Check Failed</strong>: Please complete the CAPTCHA verification.' );
	}
	
	if ( ! honeyscroop_verify_turnstile( $token ) ) {
		return new WP_Error( 'turnstile_failed', '<strong>Security Check Failed</strong>: Bot verification failed. Please try again.' );
	}
	
	return $user;
}
add_filter( 'authenticate', 'honeyscroop_verify_turnstile_on_login', 30, 3 );

/**
 * Enqueue Turnstile script on WP Login page.
 */
function honeyscroop_enqueue_turnstile_login() {
	$creds = honeyscroop_load_turnstile_credentials();
	if ( ! empty( $creds['site_key'] ) ) {
		wp_enqueue_script( 'cloudflare-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, false );
	}
}
add_action( 'login_enqueue_scripts', 'honeyscroop_enqueue_turnstile_login' );

// ============================================
// WORDPRESS REGISTRATION FORM INTEGRATION
// ============================================

/**
 * Add Turnstile to WP Registration Form.
 */
function honeyscroop_add_turnstile_to_registration() {
	echo honeyscroop_render_turnstile( 'wp-register' );
}
add_action( 'register_form', 'honeyscroop_add_turnstile_to_registration' );

/**
 * Verify Turnstile on WP Registration.
 */
function honeyscroop_verify_turnstile_on_registration( $errors, $sanitized_user_login, $user_email ) {
	$creds = honeyscroop_load_turnstile_credentials();
	if ( empty( $creds['site_key'] ) || empty( $creds['secret_key'] ) ) {
		return $errors;
	}
	
	$token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( $_POST['cf-turnstile-response'] ) : '';
	
	if ( empty( $token ) ) {
		honeyscroop_log_turnstile_failure( 'No token submitted on registration' );
		$errors->add( 'turnstile_missing', '<strong>Security Check Failed</strong>: Please complete the CAPTCHA verification.' );
	} elseif ( ! honeyscroop_verify_turnstile( $token ) ) {
		$errors->add( 'turnstile_failed', '<strong>Security Check Failed</strong>: Bot verification failed. Please try again.' );
	}
	
	return $errors;
}
add_filter( 'registration_errors', 'honeyscroop_verify_turnstile_on_registration', 10, 3 );
