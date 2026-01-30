---
description: Add new settings to WordPress Customizer
---

# Add Customizer Settings

## Steps

1. **Create settings file**
   Create `inc/customizer-{feature}.php`:
   ```php
   <?php
   function honeyscroop_{feature}_customize_register( $wp_customize ) {
       // Add Section
       $wp_customize->add_section(
           'honeyscroop_{feature}',
           array(
               'title'    => __( 'Feature Name', 'honeyscroop' ),
               'priority' => 30,
           )
       );

       // Add Setting
       $wp_customize->add_setting(
           '{setting_id}',
           array(
               'default'           => '',
               'sanitize_callback' => 'sanitize_text_field',
               'transport'         => 'refresh',
           )
       );

       // Add Control
       $wp_customize->add_control(
           '{setting_id}',
           array(
               'label'   => __( 'Setting Label', 'honeyscroop' ),
               'section' => 'honeyscroop_{feature}',
               'type'    => 'text',
           )
       );
   }
   add_action( 'customize_register', 'honeyscroop_{feature}_customize_register' );
   ```

2. **Include in functions.php**
   Add after existing includes:
   ```php
   require_once get_template_directory() . '/inc/customizer-{feature}.php';
   ```

3. **Use in templates**
   ```php
   $value = get_theme_mod( '{setting_id}', 'default_value' );
   ```

## Available Control Types
- `text` - Single line text
- `textarea` - Multi-line text
- `url` - URL input
- `email` - Email input
- `checkbox` - Boolean toggle
- `select` - Dropdown
- `radio` - Radio buttons
- `color` - Color picker
- `image` - Media uploader

## Sanitize Callbacks
- `sanitize_text_field` - Plain text
- `esc_url_raw` - URLs
- `absint` - Positive integers
- `wp_kses_post` - HTML content
