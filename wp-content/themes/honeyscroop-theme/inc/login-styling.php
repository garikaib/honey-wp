<?php
/**
 * Native WordPress Login Styling
 *
 * Customizes the appearance of wp-login.php to match the Honeyscroop brand.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue custom login styles and update logo.
 */
function honeyscroop_login_styling() {
	$logo_url = home_url( '/wp-content/uploads/2026/01/honescoop_logo.webp' );
	?>
	<style type="text/css">
		/* Import Outfit Font */
		@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

		body.login {
			background-color: #FFFEF7 !important;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			min-height: 100vh !important;
			font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
		}

		#login {
			width: 100% !important;
			max-width: 480px !important;
			padding: 20px !important;
			margin: 0 !important;
		}

		/* Logo Customization - Override WordPress Default */
		#login h1 a,
		.login h1 a {
			background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
			background-size: contain !important;
			background-position: center center !important;
			background-repeat: no-repeat !important;
			width: 280px !important;
			height: 90px !important;
			margin: 0 auto 30px auto !important;
			display: block !important;
			text-indent: -9999px !important;
		}

		/* Form Styling */
		#loginform,
		.login form {
			background: #ffffff !important;
			border: 1px solid #FEF3C7 !important;
			border-radius: 24px !important;
			box-shadow: 0 10px 40px -10px rgba(245, 158, 11, 0.15) !important;
			padding: 45px !important;
			position: relative !important;
			overflow: hidden !important;
		}

		/* Decorative Top Border */
		#loginform::before,
		.login form::before {
			content: "" !important;
			position: absolute !important;
			top: 0 !important;
			left: 0 !important;
			right: 0 !important;
			height: 6px !important;
			background: linear-gradient(to right, #F59E0B, #FBBF24, #F59E0B) !important;
		}

		/* Labels */
		.login label {
			font-size: 12px !important;
			font-weight: 700 !important;
			text-transform: uppercase !important;
			letter-spacing: 0.08em !important;
			color: #6B7280 !important;
			margin-bottom: 10px !important;
			display: block !important;
		}

		/* Input Fields */
		.login input[type="text"],
		.login input[type="password"],
		#user_login,
		#user_pass {
			background: #F9FAFB !important;
			border: 1px solid #E5E7EB !important;
			border-radius: 12px !important;
			padding: 14px 18px !important;
			font-size: 16px !important;
			font-family: 'Outfit', sans-serif !important;
			box-shadow: none !important;
			transition: all 0.2s ease !important;
			width: 100% !important;
			margin-bottom: 5px !important;
		}

		.login input[type="text"]:focus,
		.login input[type="password"]:focus,
		#user_login:focus,
		#user_pass:focus {
			border-color: #FBBF24 !important;
			background: #ffffff !important;
			box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.15) !important;
			outline: none !important;
		}

		/* Autofill Fix */
		.login input:-webkit-autofill,
		.login input:-webkit-autofill:hover,
		.login input:-webkit-autofill:focus {
			-webkit-box-shadow: 0 0 0 30px #F9FAFB inset !important;
			-webkit-text-fill-color: #1F2937 !important;
		}

		/* Submit Button */
		.login .button-primary,
		#wp-submit {
			background: linear-gradient(135deg, #F59E0B 0%, #EAB308 100%) !important;
			border: none !important;
			border-radius: 14px !important;
			box-shadow: 0 4px 14px -2px rgba(245, 158, 11, 0.4) !important;
			color: #ffffff !important;
			font-size: 16px !important;
			font-weight: 700 !important;
			font-family: 'Outfit', sans-serif !important;
			padding: 14px 28px !important;
			height: auto !important;
			line-height: 1.4 !important;
			text-shadow: none !important;
			transition: all 0.25s ease !important;
			width: 100% !important;
			margin-top: 15px !important;
			cursor: pointer !important;
		}

		.login .button-primary:hover,
		#wp-submit:hover {
			background: linear-gradient(135deg, #D97706 0%, #CA8A04 100%) !important;
			transform: translateY(-2px) !important;
			box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.5) !important;
		}

		.login .button-primary:active,
		#wp-submit:active {
			transform: scale(0.98) !important;
		}

		/* Generate Password Button (Reset Page) */
		.wp-generate-pw {
			background-color: #F3F4F6 !important;
			border: 1px solid #D1D5DB !important;
			border-radius: 10px !important;
			color: #4B5563 !important;
			font-weight: 600 !important;
			padding: 8px 16px !important;
			font-size: 13px !important;
			transition: all 0.2s ease !important;
			box-shadow: none !important;
			text-shadow: none !important;
			margin-bottom: 20px !important;
		}

		.wp-generate-pw:hover {
			background-color: #E5E7EB !important;
			color: #1F2937 !important;
			border-color: #9CA3AF !important;
		}

		/* Remember Me Checkbox */
		.login .forgetmenot {
			float: none !important;
			margin: 20px 0 !important;
		}
		
		.login .forgetmenot label {
			display: flex !important;
			align-items: center !important;
			gap: 10px !important;
			text-transform: none !important;
			font-weight: 500 !important;
			font-size: 14px !important;
			color: #6B7280 !important;
			cursor: pointer !important;
		}

		.login input[type="checkbox"] {
			width: 18px !important;
			height: 18px !important;
			border-radius: 4px !important;
			accent-color: #F59E0B !important;
		}

		/* Links (Lost Password, Back to Site) */
		.login #nav,
		.login #backtoblog {
			text-align: center !important;
			margin-top: 24px !important;
			padding: 0 !important;
			font-size: 14px !important;
		}

		.login #nav a,
		.login #backtoblog a {
			color: #9CA3AF !important;
			text-decoration: none !important;
			transition: color 0.2s ease !important;
		}

		.login #nav a:hover,
		.login #backtoblog a:hover {
			color: #D97706 !important;
		}

		/* Error and Success Messages */
		.login .message,
		.login .success,
		.login #login_error {
			border-radius: 12px !important;
			border-left: 4px solid #F59E0B !important;
			box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05) !important;
			background-color: #FFFBEB !important;
			color: #92400E !important;
			padding: 12px 16px !important;
			margin-bottom: 20px !important;
		}

		/* Hide language switcher */
		.wp-core-ui .language-switcher,
		.language-switcher {
			display: none !important;
		}

		/* Turnstile Widget Spacing */
		.turnstile-wrapper,
		.cf-turnstile {
			margin: 20px 0 !important;
		}

		/* Privacy Policy Link */
		.login .privacy-policy-page-link {
			text-align: center !important;
			margin-top: 20px !important;
		}

		.login .privacy-policy-page-link a {
			color: #9CA3AF !important;
			font-size: 12px !important;
		}
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'honeyscroop_login_styling', 99 );

/**
 * Change the logo URL from wordpress.org to our home URL.
 */
function honeyscroop_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'honeyscroop_login_logo_url' );

/**
 * Change the logo title attribute.
 */
function honeyscroop_login_logo_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'honeyscroop_login_logo_title' );

/**
 * Add Magic Link to WP Login Form.
 */
function honeyscroop_hook_magic_link_wp_login() {
	if ( function_exists( 'honeyscroop_add_magic_link_form' ) ) {
		honeyscroop_add_magic_link_form();
	}
}
add_action( 'login_form', 'honeyscroop_hook_magic_link_wp_login' );
