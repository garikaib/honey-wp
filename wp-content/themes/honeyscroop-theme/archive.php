<?php
get_header();
?>
<div class="bg-cream-50 min-h-screen py-20 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-amber-50/50 to-transparent pointer-events-none"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-16 animate-fade-in-up">
            <h1 class="text-5xl md:text-6xl font-serif text-secondary-900 mb-4"><?php the_archive_title(); ?></h1>
            <div class="text-lg text-secondary-600 font-light"><?php the_archive_description(); ?></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ( have_posts() ) :
                $delay = 0;
                while ( have_posts() ) : the_post();
                    $thumb_id = get_post_thumbnail_id();
                    $img_url = wp_get_attachment_image_url($thumb_id, 'blog-card');
                    if (!$img_url) $img_url = 'https://placehold.co/600x400';
                    $delay += 100; // Increment delay for staggered effect
            ?>
            <article class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 blog-card" style="animation-delay: <?php echo $delay; ?>ms">
                <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[3/2]">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors duration-300"></div>
                </a>
                <div class="p-8">
                    <div class="flex items-center gap-3 mb-4 text-xs font-medium tracking-wider uppercase text-amber-600">
                        <?php
                        $cats = get_the_category();
                        if ($cats) echo esc_html($cats[0]->name);
                        ?>
                        <span class="w-1 h-1 rounded-full bg-amber-300"></span>
                        <span class="text-secondary-400"><?php echo get_the_date('M d, Y'); ?></span>
                    </div>
                    <h2 class="text-2xl font-serif text-secondary-900 mb-3 leading-tight group-hover:text-amber-600 transition-colors">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <p class="text-secondary-600 line-clamp-3 mb-6 font-light">
                        <?php echo get_the_excerpt(); ?>
                    </p>
                    <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-sm font-semibold text-secondary-900 group-hover:text-amber-600 transition-colors">
                        Read Story
                        <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </article>
            <?php endwhile;
            else :
                echo '<p class="text-center col-span-3 text-secondary-500">No stories found yet. Check back soon!</p>';
            endif;
            ?>
        </div>
        
        <!-- Pagination -->
        <div class="mt-16 flex justify-center">
            <?php
            the_posts_pagination(array(
                'prev_text' => '<span class="sr-only">Previous</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>',
                'next_text' => '<span class="sr-only">Next</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                'class' => 'flex gap-2' // Note: This might need more specific targeting for TW styling of the pagination links output by WP
            ));
            ?>
        </div>
    </div>
</div>

<style>
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.blog-card {
    opacity: 0;
    animation: fadeInUp 0.8s ease-out forwards;
}
/* Pagination Styling Override */
.nav-links { display: flex; gap: 0.5rem; align-items: center; justify-content: center; }
.nav-links a, .nav-links span {
    display: inline-flex; height: 2.5rem; width: 2.5rem; align-items: center; justify-content: center;
    border-radius: 9999px; border: 1px solid #e5e7eb; color: #4b5563; font-weight: 500;
    transition: all 0.2s;
    text-decoration: none;
}
.nav-links a:hover, .nav-links span.current { border-color: #d97706; color: #d97706; background-color: #fffbeb; }
.nav-links svg { width: 1.25rem; height: 1.25rem; }
</style>

<?php get_footer(); ?>
