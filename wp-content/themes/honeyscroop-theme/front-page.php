<?php
/**
 * The template for displaying the front page.
 *
 * @package Honeyscroop
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- 1. Hero Section (Single Clickable Image) -->
    <?php get_template_part( 'patterns/honey-hero' ); ?>

    <!-- 2. Brand Introduction (All Things Honey) -->
    <?php get_template_part( 'patterns/all-things-honey' ); ?>

    <!-- 3. Product Categories Grid -->
    <?php get_template_part( 'patterns/product-categories' ); ?>

    <!-- 4. Partner Belt -->
    <?php get_template_part( 'patterns/partner-belt' ); ?>

</main>

<?php
get_footer();
