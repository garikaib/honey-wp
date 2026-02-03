<?php
/**
 * Template Name: Events Page
 *
 * @package HoneyScroop_Theme
 */

get_header();
?>

<main id="primary" class="site-main bg-page min-h-screen transition-colors">
    
    <!-- Hero Section -->
    <div class="events-hero py-24 text-center relative overflow-hidden bg-honey-900 border-b border-honey-100/10">
        <!-- Bee Swarm Container -->
        <div id="bee-swarm" class="absolute inset-0 z-0 pointer-events-none"></div>

        <div class="container relative z-10">
            <h1 class="text-5xl md:text-7xl font-serif font-bold text-honey-50 mb-6 drop-shadow-md">
                Our Hive <span class="text-honey-500">Events</span>
            </h1>
            <p class="text-xl text-honey-100/80 font-light max-w-2xl mx-auto leading-relaxed">
                Join the Honeyscoop community. From harvesting workshops to tasting festivals, discover where the sweetness happens next.
            </p>
        </div>
        
        <!-- Decorative Tape -->
        <div class="tape-strip tape-top-right"></div>
    </div>

    <!-- React Calendar Root -->
    <div id="events-calendar-root" class="bg-page min-h-[600px] relative transition-colors">
        <!-- Loading State (SSR Mimic) -->
        <div class="container py-20 flex justify-center">
             <div class="flex items-center gap-3 text-honey-600 animate-pulse">
                <svg class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="3" stroke-dasharray="30 60"></circle></svg>
                <span class="font-bold tracking-widest uppercase text-sm">Loading Events...</span>
             </div>
        </div>
    </div>

</main>

<style>
/* Events Page Specific Styles */
.tape-top-right {
    top: -15px;
    right: 10%;
    transform: rotate(3deg);
}
</style>

<?php
get_footer();
