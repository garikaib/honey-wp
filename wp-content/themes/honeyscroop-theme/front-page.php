<?php
/**
 * The template for displaying the front page.
 *
 * @package Honeyscroop
 */

get_header();
?>

<!-- Single Full-Width Hero Image (Clickable) -->
<a href="/shop" class="block w-full">
    <img 
        src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/VJ70752.jpg' ) ); ?>" 
        alt="Honeyscoop Promotion" 
        class="w-full h-auto object-cover"
    />
</a>

<?php
get_footer();
