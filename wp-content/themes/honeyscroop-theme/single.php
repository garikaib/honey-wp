<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<div class="bg-white">
    <!-- Hero Header -->
    <div class="relative h-[75vh] min-h-[600px] w-full overflow-hidden">
        <?php 
        $thumb_id = get_post_thumbnail_id();
        $img_url = wp_get_attachment_image_url($thumb_id, 'full'); // Use full size for hero
        if (!$img_url) $img_url = 'https://placehold.co/1600x900';
        ?>
        <img src="<?php echo esc_url($img_url); ?>" class="absolute inset-0 w-full h-full object-cover animate-scale-slow" alt="<?php the_title_attribute(); ?>">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/10"></div>
        
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
            <div class="max-w-5xl animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-6 py-2 mb-8 text-xs font-bold tracking-[0.2em] text-white uppercase bg-amber-600/90 backdrop-blur-sm rounded-full border border-amber-500/30 shadow-xl">
                    <?php 
                    $cats = get_the_category();
                    if ($cats) echo esc_html($cats[0]->name);
                    ?>
                </div>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif text-white mb-8 leading-[1.1] drop-shadow-2xl font-medium tracking-tight">
                    <?php the_title(); ?>
                </h1>
                <div class="flex flex-wrap items-center justify-center gap-8 text-white/90 text-sm md:text-base font-light tracking-wide bg-black/20 backdrop-blur-md px-8 py-3 rounded-full inline-flex">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <?php echo get_the_date(); ?>
                    </span>
                    <span class="w-1 h-1 rounded-full bg-white/50"></span>
                    <span class="flex items-center gap-2">
                         <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        By <span class="font-medium"><?php the_author(); ?></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Torn Edge Separator (CSS-based or SVG) -->
        <div class="absolute bottom-0 left-0 w-full leading-none text-bg-page overflow-hidden">
            <svg class="relative block w-[calc(100%+1.3px)] h-[50px] transform rotate-180" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="currentColor"></path>
                <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="currentColor"></path>
                <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="currentColor"></path>
            </svg>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-page pb-20 transition-colors">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto -mt-20 relative z-10 bg-white dark:bg-dark-surface p-8 md:p-16 rounded-t-3xl shadow-xl transition-colors border-x border-t border-transparent dark:border-white/5">
                <!-- Drop Cap & Prose -->
                <div class="prose prose-lg md:prose-xl prose-stone dark:prose-invert prose-headings:font-serif prose-headings:text-secondary-900 dark:prose-headings:text-honey-50 prose-p:font-light prose-p:leading-relaxed prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline prose-blockquote:border-l-4 prose-blockquote:border-amber-400 prose-blockquote:bg-amber-50/50 dark:prose-blockquote:bg-amber-900/10 prose-blockquote:py-4 prose-blockquote:px-8 prose-blockquote:rounded-r-lg prose-blockquote:not-italic prose-blockquote:font-serif prose-img:rounded-2xl prose-img:shadow-lg first-letter:float-left first-letter:text-7xl first-letter:pr-4 first-letter:font-serif first-letter:text-amber-600 font-serif-content">
                    <?php the_content(); ?>
                </div>
                
                <!-- Tags & Share -->
                <div class="mt-16 pt-10 border-t border-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                         <div class="flex flex-wrap gap-2">
                            <?php 
                            $tags = get_the_tags();
                            if ($tags) {
                                foreach($tags as $tag) {
                                    echo '<a href="' . get_tag_link($tag->term_id) . '" class="px-4 py-1.5 bg-gray-50 border border-gray-200 text-gray-600 rounded-full text-xs font-medium uppercase tracking-wide hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700 transition-all duration-300">#' . $tag->name . '</a>';
                                }
                            }
                            ?>
                        </div>
                        <div class="flex gap-4">
                            <!-- Social Share Buttons (Placeholder) -->
                            <span class="text-xs uppercase tracking-widest font-bold text-gray-400 self-center">Share</span>
                            <button class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></button>
                            <button class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-blue-50 hover:text-blue-800 transition-all"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Posts -->
            <div class="max-w-6xl mx-auto mt-20">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-3xl font-serif text-secondary-900 dark:text-honey-50">You Might Also Like</h3>
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="text-sm font-bold tracking-widest text-amber-600 dark:text-amber-500 uppercase hover:text-amber-700 transition-colors">View All Stories</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php
                    $related = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'rand'
                    ));
                    
                    if ($related->have_posts()) :
                        while ($related->have_posts()) : $related->the_post();
                            $thumb_id = get_post_thumbnail_id();
                            $img_url = wp_get_attachment_image_url($thumb_id, 'blog-card');
                            if (!$img_url) $img_url = 'https://placehold.co/600x400';
                    ?>
                    <article class="group bg-white dark:bg-surface-glass rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-honey-100 dark:border-white/10">
                        <a href="<?php the_permalink(); ?>" class="block relative overflow-hidden aspect-[3/2]">
                             <img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        </a>
                        <div class="p-6">
                            <div class="text-xs font-bold text-amber-600 dark:text-amber-500 uppercase tracking-wider mb-2">
                                <?php $cats = get_the_category(); if($cats) echo esc_html($cats[0]->name); ?>
                            </div>
                            <h4 class="text-xl font-serif text-secondary-900 dark:text-honey-100 mb-2 leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>
                            <span class="text-gray-400 dark:text-gray-500 text-xs"><?php echo get_the_date(); ?></span>
                        </div>
                    </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.animate-scale-slow { animation: scaleSlow 25s linear infinite alternate; }
.animate-fade-in-up { animation: fadeInUp 1s ease-out forwards; }
@keyframes scaleSlow { from { transform: scale(1); } to { transform: scale(1.1); } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

/* Drop Cap Specific Fixes */
.prose p:first-of-type::first-letter {
    float: left;
    font-size: 4.5rem;
    line-height: 0.8;
    padding-right: 0.15em;
    padding-top: 0.1em;
    font-family: 'Cormorant Garamond', serif;
    color: #d97706; /* amber-600 */
}
</style>
<?php endwhile; ?>
<?php get_footer(); ?>
