<?php
/**
 * Honeyscroop Email Templates
 *
 * Provides branded HTML email templates matching site design.
 *
 * @package Honeyscroop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get base email template wrapper.
 *
 * @param string $content The email body content.
 * @param string $subject The email subject for the header.
 * @return string Complete HTML email.
 */
function honeyscroop_email_template( $content, $subject = '' ) {
	$site_name = get_bloginfo( 'name' );
	$site_url = home_url();
	$logo_url = home_url( '/wp-content/uploads/2026/01/honescoop_cropped_transparent.webp' );
	$year = date( 'Y' );

	return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$subject}</title>
    <!--[if mso]>
    <style type="text/css">
        table { border-collapse: collapse; }
        td { padding: 0; }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #FFFEF7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #FFFEF7;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #FFFFFF; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 40px rgba(245, 158, 11, 0.1);">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 50%, #F59E0B 100%); padding: 32px 40px; text-align: center;">
                            <img src="{$logo_url}" alt="{$site_name}" width="180" style="display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px;">
                            {$content}
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #FEF3C7; padding: 24px 40px; text-align: center; border-top: 1px solid #FDE68A;">
                            <p style="margin: 0 0 8px 0; color: #92400E; font-size: 14px;">
                                Thank you for choosing {$site_name}!
                            </p>
                            <p style="margin: 0; color: #B45309; font-size: 12px;">
                                <a href="{$site_url}" style="color: #D97706; text-decoration: none;">Visit our website</a> | 
                                <a href="{$site_url}/shop" style="color: #D97706; text-decoration: none;">Shop Now</a>
                            </p>
                            <p style="margin: 16px 0 0 0; color: #9CA3AF; font-size: 11px;">
                                © {$year} {$site_name}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

/**
 * Send a branded email.
 *
 * @param string $to Recipient email.
 * @param string $subject Email subject.
 * @param string $body_content HTML content for the body.
 * @return bool Whether the email was sent successfully.
 */
function honeyscroop_send_email( $to, $subject, $body_content ) {
	$site_name = get_bloginfo( 'name' );
	$html = honeyscroop_email_template( $body_content, $subject );
	
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . $site_name . ' <noreply@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
	);
	
	return wp_mail( $to, $subject, $html, $headers );
}

/**
 * Send welcome email with account credentials.
 *
 * @param string $email User email.
 * @param string $name User display name.
 * @param string $username Username.
 * @param string $password Generated password.
 * @return bool Success.
 */
function honeyscroop_send_welcome_email( $email, $name, $username, $password ) {
	$site_name = get_bloginfo( 'name' );
	$login_url = home_url( '/my-account/' );
	
	$content = <<<HTML
<h1 style="margin: 0 0 20px 0; color: #1F2937; font-size: 28px; font-weight: 700;">Welcome to {$site_name}! 🍯</h1>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    Hi {$name},
</p>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    Your account has been created successfully! Here are your login details:
</p>

<div style="background-color: #FEF3C7; border: 2px solid #FCD34D; border-radius: 16px; padding: 24px; margin: 0 0 24px 0;">
    <p style="margin: 0 0 12px 0; color: #92400E; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        Your Login Credentials
    </p>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding: 8px 0; color: #78716C; font-size: 14px; width: 100px;">Email:</td>
            <td style="padding: 8px 0; color: #1F2937; font-size: 14px; font-weight: 600;">{$email}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #78716C; font-size: 14px;">Password:</td>
            <td style="padding: 8px 0; color: #1F2937; font-size: 16px; font-weight: 700; font-family: monospace; background-color: #FFFFFF; padding: 8px 12px; border-radius: 8px; display: inline-block;">{$password}</td>
        </tr>
    </table>
</div>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    You can now log in to your account to track orders and manage your profile.
</p>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
    <tr>
        <td style="background: linear-gradient(135deg, #F59E0B 0%, #EAB308 100%); border-radius: 14px;">
            <a href="{$login_url}" style="display: inline-block; padding: 16px 40px; color: #FFFFFF; font-size: 16px; font-weight: 700; text-decoration: none;">
                Log In to Your Account
            </a>
        </td>
    </tr>
</table>

<p style="margin: 32px 0 0 0; color: #9CA3AF; font-size: 13px; line-height: 1.5;">
    <strong>Security Tip:</strong> We recommend changing your password after your first login.
</p>
HTML;

	return honeyscroop_send_email( $email, "Welcome to {$site_name}!", $content );
}

/**
 * Send magic link login email.
 *
 * @param string $email User email.
 * @param string $name User display name.
 * @param string $magic_link The magic login URL.
 * @return bool Success.
 */
function honeyscroop_send_magic_link_email( $email, $name, $magic_link ) {
	$site_name = get_bloginfo( 'name' );
	
	$content = <<<HTML
<h1 style="margin: 0 0 20px 0; color: #1F2937; font-size: 28px; font-weight: 700;">Your Magic Login Link 🔮</h1>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    Hi {$name},
</p>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    Click the button below to log in to your {$site_name} account. This link will expire in 24 hours.
</p>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto 24px auto;">
    <tr>
        <td style="background: linear-gradient(135deg, #F59E0B 0%, #EAB308 100%); border-radius: 14px;">
            <a href="{$magic_link}" style="display: inline-block; padding: 18px 48px; color: #FFFFFF; font-size: 18px; font-weight: 700; text-decoration: none;">
                ✨ Log In Now
            </a>
        </td>
    </tr>
</table>

<p style="margin: 0 0 16px 0; color: #6B7280; font-size: 14px; line-height: 1.5;">
    Or copy and paste this link into your browser:
</p>

<div style="background-color: #F3F4F6; border-radius: 12px; padding: 16px; word-break: break-all; margin: 0 0 24px 0;">
    <a href="{$magic_link}" style="color: #6366F1; font-size: 13px; text-decoration: none;">{$magic_link}</a>
</div>

<p style="margin: 0; color: #9CA3AF; font-size: 13px; line-height: 1.5;">
    If you didn't request this email, you can safely ignore it. Someone may have entered your email by mistake.
</p>
HTML;

	return honeyscroop_send_email( $email, "Your Magic Login Link - {$site_name}", $content );
}

/**
 * Send order confirmation email.
 *
 * @param string $email Customer email.
 * @param string $name Customer name.
 * @param int $order_id Order ID.
 * @param array $items Order items.
 * @param int $total Total in cents.
 * @return bool Success.
 */
function honeyscroop_send_order_confirmation_email( $email, $name, $order_id, $items, $total ) {
	$site_name = get_bloginfo( 'name' );
	
	// Build items table
	$items_html = '';
	foreach ( $items as $item ) {
		$item_total = ( $item['price'] * $item['quantity'] ) / 100;
		$items_html .= sprintf(
			'<tr><td style="padding: 12px 0; border-bottom: 1px solid #E5E7EB; color: #1F2937;">%s × %d</td><td style="padding: 12px 0; border-bottom: 1px solid #E5E7EB; color: #1F2937; text-align: right; font-weight: 600;">$%.2f</td></tr>',
			esc_html( $item['name'] ),
			$item['quantity'],
			$item_total
		);
	}
	
	$total_formatted = number_format( $total / 100, 2 );
	
	$content = <<<HTML
<h1 style="margin: 0 0 20px 0; color: #1F2937; font-size: 28px; font-weight: 700;">Order Confirmed! 🎉</h1>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    Hi {$name},
</p>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    Thank you for your order! We've received it and will process it shortly.
</p>

<div style="background-color: #F0FDF4; border: 2px solid #BBF7D0; border-radius: 16px; padding: 20px; margin: 0 0 24px 0;">
    <p style="margin: 0; color: #166534; font-size: 14px; font-weight: 700;">
        Order #{$order_id}
    </p>
</div>

<h3 style="margin: 0 0 16px 0; color: #1F2937; font-size: 18px;">Order Summary</h3>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 24px 0;">
    {$items_html}
    <tr>
        <td style="padding: 16px 0 0 0; color: #1F2937; font-size: 18px; font-weight: 700;">Total</td>
        <td style="padding: 16px 0 0 0; color: #F59E0B; font-size: 20px; font-weight: 700; text-align: right;">\${$total_formatted}</td>
    </tr>
</table>

<p style="margin: 0 0 24px 0; color: #4B5563; font-size: 16px; line-height: 1.6;">
    We'll contact you soon to arrange delivery or pickup. If you have any questions, feel free to reply to this email.
</p>
HTML;

	return honeyscroop_send_email( $email, "Order #{$order_id} Confirmed - {$site_name}", $content );
}
