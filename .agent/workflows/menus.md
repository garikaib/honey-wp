---
description: Create new WordPress nav menu locations
---

# Add Menu Locations

## Steps

1. **Register menu location**
   In `functions.php` inside `honeyscroop_setup()`:
   ```php
   register_nav_menus(
       array(
           'menu_slug' => esc_html__( 'Menu Display Name', 'honeyscroop' ),
       )
   );
   ```

2. **Display in template**
   ```php
   wp_nav_menu(
       array(
           'theme_location' => 'menu_slug',
           'container'      => false,
           'menu_class'     => 'your-css-classes',
           'fallback_cb'    => false,
       )
   );
   ```

3. **(Optional) Seed with default items**
   Add to `honeyscroop_seed_menus()` in functions.php:
   ```php
   $menus['Menu Name'] = array(
       'location' => 'menu_slug',
       'items'    => array(
           'Link Text' => '/url',
       ),
   );
   ```

## Existing Menu Locations
- `primary` - Main header navigation
- `footer_navigate` - Footer: Navigate column
- `footer_products` - Footer: Products column
- `footer_company` - Footer: Company column
