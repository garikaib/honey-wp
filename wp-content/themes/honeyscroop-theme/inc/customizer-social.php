<?php
/**
 * Social Media Customizer Settings
 *
 * @package Honeyscroop
 */

function honeyscroop_social_customize_register( $wp_customize ) {
	// Add Section
	$wp_customize->add_section(
		'honeyscroop_social',
		array(
			'title'       => __( 'Social Media', 'honeyscroop' ),
			'description' => __( 'Add your social media links here. Icons will only appear for networks with a link set. Use # for a placeholder.', 'honeyscroop' ),
			'priority'    => 30, // After Announcement Bar
		)
	);

	$social_networks = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'tiktok'    => 'TikTok',
		'x'         => 'X (Twitter)',
		'linkedin'  => 'LinkedIn',
	);

	foreach ( $social_networks as $id => $label ) {
		// Add Setting
		$wp_customize->add_setting(
			"social_{$id}_url",
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		// Add Control
		$wp_customize->add_control(
			"social_{$id}_url",
			array(
				'label'   => $label,
				'section' => 'honeyscroop_social',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'honeyscroop_social_customize_register' );
