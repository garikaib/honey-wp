<?php
/**
 * Title: Promotional Section
 * Slug: honeyscroop/promo-section
 * Categories: featured
 * Description: A promotional section with decorative headers and product highlights.
 */
?>
<section class="py-16 md:py-24 bg-[#FFF5EE]"> <!-- Light peach/pink tint background similar to reference -->
    <div class="max-w-[1400px] mx-auto px-4 text-center">
        
        <!-- Decorative Header Icons (Honeycomb style) -->
        <div class="flex justify-center items-center gap-4 md:gap-8 mb-6">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-[#FFD700] rounded-2xl rotate-45 flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl md:text-2xl -rotate-45">蜜</span>
            </div>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-[#FF1493] rounded-2xl rotate-45 flex items-center justify-center shadow-lg"> <!-- Hot pink -->
                <span class="text-white font-bold text-xl md:text-2xl -rotate-45">意</span>
            </div>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-[#FF1493] rounded-2xl rotate-45 flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl md:text-2xl -rotate-45">满</span>
            </div>
             <div class="w-16 h-16 md:w-20 md:h-20 bg-[#FF1493] rounded-2xl rotate-45 flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl md:text-2xl -rotate-45">屋</span>
            </div>
        </div>

        <h2 class="text-3xl md:text-4xl text-gray-800 font-medium mb-12 tracking-wide">Bring Home the Blooming Joy of Honey</h2>

        <!-- Product/Collection Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <!-- Item 1 -->
            <a href="#" class="group block relative">
                 <div class="aspect-[3/4] overflow-hidden rounded-lg shadow-md mb-4">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/Trade-Center-June_-172-2-scaled.jpg' ) ); ?>" alt="CNY Hamper" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                 </div>
                 <h3 class="text-lg font-medium text-gray-800 group-hover:text-honey-600 transition-colors">CNY Hamper</h3>
            </a>

            <!-- Item 2 -->
             <a href="#" class="group block relative">
                 <div class="aspect-[3/4] overflow-hidden rounded-lg shadow-md mb-4">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/Trade-Center-June_-173-2-scaled.jpg' ) ); ?>" alt="Premium Gift Sets" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                 </div>
                 <h3 class="text-lg font-medium text-gray-800 group-hover:text-honey-600 transition-colors">Premium Gift Sets</h3>
            </a>

            <!-- Item 3 -->
             <a href="#" class="group block relative">
                 <div class="aspect-[3/4] overflow-hidden rounded-lg shadow-md mb-4">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/Trade-Center-June_-174-2-scaled.jpg' ) ); ?>" alt="Daily Series" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                 </div>
                 <h3 class="text-lg font-medium text-gray-800 group-hover:text-honey-600 transition-colors">Daily Series</h3>
            </a>

            <!-- Item 4 -->
             <a href="#" class="group block relative">
                 <div class="aspect-[3/4] overflow-hidden rounded-lg shadow-md mb-4">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/Trade-Center-June_-176-2-scaled.jpg' ) ); ?>" alt="Squeezy Packs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                 </div>
                 <h3 class="text-lg font-medium text-gray-800 group-hover:text-honey-600 transition-colors">Squeezy Packs</h3>
            </a>
        </div>
    </div>
</section>
