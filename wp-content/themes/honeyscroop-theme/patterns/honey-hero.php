<?php
/**
 * Title: Honey Hero
 * Slug: honeyscroop/honey-hero
 * Categories: featured, banner
 * Description: A full-width clickable hero section for promotions.
 */
?>
<!-- Full Page Clickable Hero -->
<div class="relative w-full h-[calc(100vh-140px)] md:h-[calc(100vh-180px)] overflow-hidden group">
    <!-- Clickable Overlay Link -->
    <a href="/shop" class="absolute inset-0 z-20 block w-full h-full cursor-pointer" aria-label="Shop Chinese New Year Package">
        <span class="sr-only">Shop Chinese New Year Package</span>
    </a>

    <!-- Background Image -->
    <div class="absolute inset-0 w-full h-full">
        <img 
            src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/VJ70752.jpg' ) ); ?>" 
            alt="Honey Scoop Promotion" 
            class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
        />
        <!-- Optional Overlay if text contrast is needed, currently transparent as requested to "cover whole page" -->
        <div class="absolute inset-0 bg-black/10 transition-opacity duration-300 group-hover:bg-black/0"></div>
    </div>
</div>
