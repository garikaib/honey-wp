#!/bin/bash
# HoneyScroop Site Setup Script
# Seeds the site with core pages, menus, and sample honey data.

echo "🍯 Starting HoneyScroop Site Setup..."

# 1. Create Core Pages
echo "Creating core pages..."
HOME_ID=$(wp post create --post_type=page --post_title='Home' --post_status=publish --porcelain)
ABOUT_ID=$(wp post create --post_type=page --post_title='Our Story' --post_status=publish --porcelain)
SHOP_ID=$(wp post create --post_type=page --post_title='Shop Honey' --post_status=publish --porcelain)

# 2. Set Front Page
echo "Setting static front page..."
wp option update show_on_front 'page'
wp option update page_on_front $HOME_ID
wp option update page_for_posts $SHOP_ID

# 3. Create Navigation Menus
echo "Creating menus..."
wp menu create "Primary Menu"
wp menu create "Footer Menu"

# Assign locations
wp menu location assign "Primary Menu" primary
wp menu location assign "Footer Menu" footer

# Add items to Primary Menu
wp menu item add-post "Primary Menu" $HOME_ID
wp menu item add-post "Primary Menu" $SHOP_ID
wp menu item add-post "Primary Menu" $ABOUT_ID

# 4. Seed Honey Varieties
echo "Seeding Honey Varieties..."

# Honey 1: Acacia
wp post create --post_type=honey_variety --post_title='Acacia Gold' --post_status=publish --meta_input='{"_honey_price":"1500","_honey_nectar_source":"Acacia Trees","_honey_region":"Eastern Highlands"}'

# Honey 2: Miombo
wp post create --post_type=honey_variety --post_title='Miombo Woodland' --post_status=publish --meta_input='{"_honey_price":"1200","_honey_nectar_source":"Miombo Woodlands","_honey_region":"Mashonaland Central"}'

# Honey 3: Mountain Wildflower
wp post create --post_type=honey_variety --post_title='Mountain Wildflower' --post_status=publish --meta_input='{"_honey_price":"1800","_honey_nectar_source":"Wildflowers","_honey_region":"Nyanga Mountains"}'

echo "✅ Site setup complete! Visit your new HoneyScroop site."
