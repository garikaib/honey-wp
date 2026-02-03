<?php
/**
 * SMTP Configuration
 *
 * Overrides WordPress default mail with custom SMTP settings.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get SMTP settings from options.
 *
 * @return array SMTP settings.
 */
function honeyscroop_get_smtp_settings() {
	return get_option( 'honeyscroop_smtp_settings', array(
		'enabled'    => false,
		'host'       => '',
		'port'       => 587,
		'username'   => '',
		'password'   => '',
		'encryption' => 'tls', // 'none', 'ssl', 'tls'
		'from_name'  => get_bloginfo( 'name' ),
		'from_email' => get_option( 'admin_email' ),
	) );
}

/**
 * Configure PHPMailer with SMTP settings.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
 */
function honeyscroop_configure_smtp( $phpmailer ) {
	$settings = honeyscroop_get_smtp_settings();
	
	// Only configure if enabled and host is set
	if ( empty( $settings['enabled'] ) || empty( $settings['host'] ) ) {
		return;
	}
	
	$phpmailer->isSMTP();
	$phpmailer->Host       = $settings['host'];
	$phpmailer->Port       = absint( $settings['port'] );
	$phpmailer->SMTPAuth   = ! empty( $settings['username'] );
	$phpmailer->Username   = $settings['username'];
	$phpmailer->Password   = $settings['password'];
	
	// Encryption
	if ( $settings['encryption'] === 'ssl' ) {
		$phpmailer->SMTPSecure = 'ssl';
	} elseif ( $settings['encryption'] === 'tls' ) {
		$phpmailer->SMTPSecure = 'tls';
	} else {
		$phpmailer->SMTPSecure = '';
		$phpmailer->SMTPAutoTLS = false;
	}
	
	// From address
	if ( ! empty( $settings['from_email'] ) ) {
		$phpmailer->From = $settings['from_email'];
        
        // Fix Message-ID to use valid domain instead of local ddev.site
        $email_parts = explode( '@', $settings['from_email'] );
        if ( count( $email_parts ) === 2 ) {
            $phpmailer->Hostname = $email_parts[1];
        }
	}
	if ( ! empty( $settings['from_name'] ) ) {
		$phpmailer->FromName = $settings['from_name'];
	}
}
add_action( 'phpmailer_init', 'honeyscroop_configure_smtp' );

/**
 * Register REST API route for SMTP test.
 */
function honeyscroop_register_smtp_routes() {
	register_rest_route( 'honeyscroop/v1', '/test-smtp', array(
		'methods'             => 'POST',
		'callback'            => 'honeyscroop_test_smtp',
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );
}
add_action( 'rest_api_init', 'honeyscroop_register_smtp_routes' );

/**
 * Test SMTP configuration by sending a test email.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response Response.
 */
function honeyscroop_test_smtp( WP_REST_Request $request ) {
	$to = 'garikaib@gmail.com';
	$subject = 'Honeyscroop SMTP Test';
	$site_name = get_bloginfo( 'name' );
	
	$now = gmdate( 'Y-m-d H:i:s' );
	$message = <<<HTML
<div style="font-family: sans-serif; padding: 20px;">
    <h2 style="color: #F59E0B;">🍯 SMTP Test Successful!</h2>
    <p>This email confirms that your SMTP settings for {$site_name} are working correctly.</p>
    <p style="color: #6B7280; font-size: 14px;">Sent at: {$now} UTC</p>
</div>
HTML;
	
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	
	// Enable debug for this request
    $debug_output = '';
    $enable_debug = function( $phpmailer ) use ( &$debug_output ) {
        $phpmailer->SMTPDebug = 3; // Verbose debug output
        $phpmailer->Debugoutput = function( $str, $level ) use ( &$debug_output ) {
            $debug_output .= "$level: $str\n";
        };
    };
    add_action( 'phpmailer_init', $enable_debug, 999 );

	$result = wp_mail( $to, $subject, $message, $headers );

    // Cleanup
    remove_action( 'phpmailer_init', $enable_debug, 999 );
	
	if ( $result ) {
		return new WP_REST_Response( array(
			'success' => true,
			'message' => "Test email sent successfully to {$to}",
            'debug'   => $debug_output, // Return debug info to admin
		), 200 );
	} else {
		global $phpmailer;
		$error = '';
		if ( isset( $phpmailer ) && is_object( $phpmailer ) ) {
			$error = $phpmailer->ErrorInfo;
		}
		return new WP_REST_Response( array(
			'success' => false,
			'message' => 'Failed to send test email. ' . $error,
            'debug'   => $debug_output,
		), 500 );
	}
}

/**
 * Save SMTP settings via REST API.
 */
function honeyscroop_extend_options_for_smtp( $settings ) {
	// This function can be used to validate SMTP settings before saving
	if ( isset( $settings['smtp_settings'] ) ) {
		$smtp = $settings['smtp_settings'];
		
		// Sanitize settings
		$clean = array(
			'enabled'    => ! empty( $smtp['enabled'] ),
			'host'       => sanitize_text_field( $smtp['host'] ?? '' ),
			'port'       => absint( $smtp['port'] ?? 587 ),
			'username'   => sanitize_text_field( $smtp['username'] ?? '' ),
			'password'   => $smtp['password'] ?? '', // Don't over-sanitize password
			'encryption' => in_array( $smtp['encryption'] ?? 'tls', array( 'none', 'ssl', 'tls' ) ) ? $smtp['encryption'] : 'tls',
			'from_name'  => sanitize_text_field( $smtp['from_name'] ?? '' ),
			'from_email' => sanitize_email( $smtp['from_email'] ?? '' ),
		);
		
		update_option( 'honeyscroop_smtp_settings', $clean );
		return $clean;
	}
	
	return $settings;
}
/**
 * Force the "From" email address to match SMTP settings.
 *
 * @param string $original_email_address Original email.
 * @return string Filtered email.
 */
function honeyscroop_mail_from( $original_email_address ) {
	$settings = honeyscroop_get_smtp_settings();
	if ( ! empty( $settings['enabled'] ) && ! empty( $settings['from_email'] ) ) {
		return $settings['from_email'];
	}
	return $original_email_address;
}
add_filter( 'wp_mail_from', 'honeyscroop_mail_from', 99 );

/**
 * Force the "From" name to match SMTP settings.
 *
 * @param string $original_email_from Original from name.
 * @return string Filtered from name.
 */
function honeyscroop_mail_from_name( $original_email_from ) {
	$settings = honeyscroop_get_smtp_settings();
	if ( ! empty( $settings['enabled'] ) && ! empty( $settings['from_name'] ) ) {
		return $settings['from_name'];
	}
	return $original_email_from;
}
add_filter( 'wp_mail_from_name', 'honeyscroop_mail_from_name', 99 );

/**
 * Show Admin Notice if SMTP is not configured properly.
 */
function honeyscroop_smtp_admin_notice() {
	// Only for admins
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = honeyscroop_get_smtp_settings();
	$screen = get_current_screen();

	// Don't show on our own options page to avoid clutter
	if ( $screen && strpos( $screen->id, 'honeyscroop' ) !== false ) {
		return;
	}

	if ( empty( $settings['enabled'] ) || empty( $settings['host'] ) ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<strong>🍯 HoneyScoop:</strong> Email settings are not fully configured.
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=honeyscroop' ) ); ?>">Configure SMTP</a> to ensure order emails are delivered reliably.
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'honeyscroop_smtp_admin_notice' );
