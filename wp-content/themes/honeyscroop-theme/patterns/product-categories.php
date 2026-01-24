<?php
/**
 * Title: Product Categories Grid
 * Slug: honeyscroop/product-categories
 * Categories: featured, gallery
 * Description: 2x2 category grid with pink/magenta gradient backgrounds matching reference.
 */
?>
<section class="py-12 bg-white">
    <div class="max-w-[1500px] mx-auto px-6">
        <h3 class="text-center text-3xl md:text-4xl text-gray-900 mb-12 font-heading font-normal">Choose By Product Category</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category 1: Raw Honey -->
            <a href="/shop/raw-honey" class="card image-full bg-base-100 shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 aspect-[16/9] before:!bg-black/10 before:!opacity-100 group">
                <figure>
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/DSC_9644-scaled.jpg' ) ); ?>" alt="Raw Honey" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                </figure>
                <div class="card-body justify-center items-end p-0 pr-6 md:pr-10">
                    <span class="inline-block bg-[#FFC107] text-gray-900 text-lg md:text-2xl font-heading font-semibold px-4 py-2 shadow-lg uppercase tracking-wider backdrop-blur-sm bg-opacity-95 z-20">
                        Raw Honey
                    </span>
                </div>
            </a>

            <!-- Category 2: Infused Honey -->
            <a href="/shop/infused-honey" class="card image-full bg-base-100 shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 aspect-[16/9] before:!bg-black/10 before:!opacity-100 group">
                <figure>
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/DSC_9622-scaled.jpg' ) ); ?>" alt="Infused Honey" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                </figure>
                <div class="card-body justify-center items-end p-0 pr-6 md:pr-10">
                    <span class="inline-block bg-[#FFC107] text-gray-900 text-lg md:text-2xl font-heading font-semibold px-4 py-2 shadow-lg uppercase tracking-wider backdrop-blur-sm bg-opacity-95 z-20">
                        Infused Honey
                    </span>
                </div>
            </a>

            <!-- Category 3: Other Products -->
            <a href="/shop/other-products" class="card image-full bg-base-100 shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 aspect-[16/9] before:!bg-black/10 before:!opacity-100 group">
                <figure>
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/IMG-20241121-WA0042.jpg' ) ); ?>" alt="Other Products" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                </figure>
                <div class="card-body justify-center items-end p-0 pr-6 md:pr-10">
                    <span class="inline-block bg-[#FFC107] text-gray-900 text-lg md:text-2xl font-heading font-semibold px-4 py-2 shadow-lg uppercase tracking-wider backdrop-blur-sm bg-opacity-95 z-20">
                        Other Products
                    </span>
                </div>
            </a>

            <!-- Category 4: Honey Products -->
            <a href="/shop/honey-products" class="card image-full bg-base-100 shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 aspect-[16/9] before:!bg-black/10 before:!opacity-100 group">
                <figure>
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/IMG-20241121-WA0041.jpg' ) ); ?>" alt="Honey Products" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                </figure>
                <div class="card-body justify-center items-end p-0 pr-6 md:pr-10">
                    <span class="inline-block bg-[#FFC107] text-gray-900 text-lg md:text-2xl font-heading font-semibold px-4 py-2 shadow-lg uppercase tracking-wider backdrop-blur-sm bg-opacity-95 z-20">
                        Honey Products
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>
