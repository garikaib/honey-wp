<?php
/**
 * Title: Partner Belt
 * Slug: honeyscroop/partner-belt
 * Categories: featured
 * Description: A scrolling belt of partner logos using Tailwind animation.
 */
?>
<!-- Partner Belt Section -->
<div class="partner-belt py-16 bg-cream border-t border-honey-100 overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-12 text-center">
        <h3 class="text-2xl md:text-3xl font-heading text-gray-900 mb-3 font-normal">Our Trusted Partners</h3>
        <p class="text-gray-500 text-base font-light">Working together to bring you nature's finest.</p>
    </div>

    <!-- Scrolling Container -->
    <div class="relative flex overflow-x-hidden group">
        <!-- Marquee Animation Track (First Set) -->
        <div class="animate-marquee whitespace-nowrap flex space-x-16 items-center">
            <?php for ( $i = 0; $i < 8; $i++ ) : ?>
                <div class="w-32 h-16 bg-gray-100 rounded flex items-center justify-center opacity-60 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                    <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Partner <?php echo $i + 1; ?></span>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Marquee Animation Track (Second Set for smooth loop) -->
        <div class="animate-marquee whitespace-nowrap flex space-x-16 items-center absolute top-0 left-full pl-16">
            <?php for ( $i = 0; $i < 8; $i++ ) : ?>
                <div class="w-32 h-16 bg-gray-100 rounded flex items-center justify-center opacity-60 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                    <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Partner <?php echo $i + 1; ?></span>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<style>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}
.animate-marquee {
    animation: marquee 30s linear infinite;
}
.group:hover .animate-marquee {
    animation-play-state: paused;
}
</style>
