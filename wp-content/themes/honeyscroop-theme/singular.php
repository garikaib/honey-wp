<?php
/**
 * The template for displaying all pages, single posts and attachments
 *
 * @package Honeyscroop
 */

get_header();
?>

<div class="singular-content">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php body_class(); ?>>
            <?php the_content(); ?>
        </article>
        <?php
    endwhile;
    ?>
    
    <?php if ( is_front_page() ) : ?>
        <div id="honey-finder-root"></div>
    <?php endif; ?>
</div>

<?php
get_footer();
