<?php
/**
 * Title: Product Categories Grid
 * Slug: honeyscroop/product-categories
 * Categories: featured, gallery
 * Description: 2x2 category grid with pink/magenta gradient backgrounds matching reference.
 */
?>
<section class="py-12 bg-white">
    <div class="max-w-[1200px] mx-auto px-4">
        <h3 class="text-center text-lg md:text-xl text-gray-800 mb-10">Choose By Product Category</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Category 1: Honey -->
            <a href="/shop/honey" class="group block relative overflow-hidden rounded-lg aspect-[16/9] bg-gradient-to-br from-pink-500 to-rose-600">
                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/VJ70756.jpg' ) ); ?>" alt="Pure Honey" class="absolute inset-0 w-full h-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500/80 to-transparent"></div>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-right">
                    <span class="text-4xl md:text-5xl font-light text-white tracking-widest uppercase">Honey</span>
                </div>
            </a>

            <!-- Category 2: Gift -->
            <a href="/shop/gifts" class="group block relative overflow-hidden rounded-lg aspect-[16/9] bg-gradient-to-br from-pink-500 to-rose-600">
                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/IMG-20241109-WA0034.jpg' ) ); ?>" alt="Gifting" class="absolute inset-0 w-full h-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500/80 to-transparent"></div>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-right">
                    <span class="text-4xl md:text-5xl font-light text-white tracking-widest uppercase">Gift</span>
                </div>
            </a>

            <!-- Category 3: Peanut Butter -->
            <a href="/shop/peanut-butter" class="group block relative overflow-hidden rounded-lg aspect-[16/9] bg-gradient-to-br from-pink-500 to-rose-600">
                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/20250510_084203-768x1024.jpg' ) ); ?>" alt="Butlerscoop Peanut Butter" class="absolute inset-0 w-full h-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500/80 to-transparent"></div>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-right">
                    <span class="text-4xl md:text-5xl font-light text-white tracking-widest uppercase">Butter</span>
                </div>
            </a>

            <!-- Category 4: Honeycomb -->
            <a href="/shop/honeycomb" class="group block relative overflow-hidden rounded-lg aspect-[16/9] bg-gradient-to-br from-pink-500 to-rose-600">
                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/IMG-20250402-WA0016-768x1024.jpg' ) ); ?>" alt="Honeycomb" class="absolute inset-0 w-full h-full object-cover opacity-90 transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500/80 to-transparent"></div>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-right">
                    <span class="text-4xl md:text-5xl font-light text-white tracking-widest uppercase">Comb</span>
                </div>
            </a>
        </div>
    </div>
</section>
