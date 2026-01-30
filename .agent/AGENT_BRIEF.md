# Honeyscroop WordPress Theme - Agent Brief

## Project Overview
- **Type**: WordPress child theme of Twenty Twenty-Six
- **Purpose**: E-commerce honey shop website
- **Stack**: PHP 8.3, Tailwind CSS, React (for blocks), Vite

## Directory Structure
```
wp-content/themes/honeyscroop-theme/
├── dist/              # Compiled assets (Vite output)
├── inc/               # PHP includes
│   ├── assets.php     # Asset enqueueing
│   ├── cpt-*.php      # Custom Post Types
│   └── customizer-*.php # Customizer settings
├── patterns/          # Block patterns
├── src/               # Source files (React blocks, CSS)
├── functions.php      # Main theme functions
├── header.php         # Custom header template
├── footer.php         # Custom footer template
├── front-page.php     # Homepage template
├── theme.json         # Block theme settings
├── tailwind.config.js
└── vite.config.js
```

## Key Files

### functions.php
- Parent theme style enqueueing
- Menu registration (primary, footer_navigate, footer_products, footer_company)
- Menu seeding logic
- Customizer hook loading

### inc/customizer-social.php
- Social media URL settings (Facebook, Instagram, TikTok, X, LinkedIn)

### footer.php
- Dynamic menus via `wp_nav_menu()`
- Dynamic social icons via `get_theme_mod()`

### header-nav (React)
- Dynamic menu via `window.honeyscroopHeaderData.primaryMenu` (localized from PHP)

## Build Commands
```bash
// turbo
npm run dev     # Development with hot reload
npm run build   # Production build
```

## Common Patterns

### Adding Customizer Settings
1. Create `inc/customizer-{feature}.php`
2. Register section and settings via `customize_register` hook
3. Include in `functions.php`
4. Use `get_theme_mod()` in templates

### Adding Menu Locations
1. Register in `register_nav_menus()` in functions.php
2. Optionally seed with `honeyscroop_seed_menus()`
3. Use `wp_nav_menu()` in template

### Creating React Blocks
1. Create folder in `src/{block-name}/`
2. Add `index.jsx` and `style.css`
3. Register in PHP via `register_block_type()`
4. Run `npm run build`

## Parent Theme
- **Name**: Twenty Twenty-Six
- **Template**: twentytwentysix
