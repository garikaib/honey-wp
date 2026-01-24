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
        
        <!-- Dynamic React Ticker -->
        <div id="partner-ticker-root" class="w-full flex justify-center mt-12"></div>
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
