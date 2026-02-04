<?php
/**
 * Template Name: Our Story
 *
 * @package Honeyscroop
 * @since   1.0.0
 */

get_header();
?>

<div class="our-story-page bg-page py-20 overflow-hidden relative transition-colors">
    
    <!-- Hero Section: Adventure Begins -->
    <div class="container mx-auto px-4 mb-32 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="font-handwritten text-6xl md:text-8xl text-honey-600 dark:text-honey-400 mb-6 transform -rotate-2 inline-block relative transition-colors">
                The Adventure Begins
                <div class="doodle-svg absolute -top-8 -right-12 w-16 h-16 text-honey-400 dark:text-honey-600 transition-colors">
                   <!-- Sun Doodle -->
                   <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="4">
                        <circle cx="50" cy="50" r="20"></circle>
                        <path d="M50 10V25 M50 75V90 M10 50H25 M75 50H90 M22 22L32 32 M68 68L78 78 M22 78L32 68 M68 32L78 22"></path>
                   </svg>
                </div>
            </h1>
            <p class="font-note text-2xl text-gray-700 dark:text-gray-300 max-w-2xl mx-auto rotate-1 tracking-wide transition-colors">
                "It started with a sneeze, a jar, and a dream..."
            </p>
        </div>
    </div>

    <!-- Section 1: Curiosity Begins (Polaroid) -->
    <div class="container mx-auto px-4 mb-32 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-center gap-12">
            
            <!-- Text Side -->
            <div class="md:w-1/2 max-w-md text-center md:text-right">
                <h2 class="font-handwritten text-5xl text-gray-800 dark:text-honey-50 mb-6 relative inline-block transition-colors">
                    Curiosity Begins
                    <span class="absolute -bottom-2 left-0 w-full h-1 bg-honey-300 dark:bg-honey-600 rounded transform -rotate-1 transition-colors"></span>
                </h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg mb-6 transition-colors">
                    Savour the Sweetness of Nature with Honeyscoop. Our journey began not in a boardroom, but in the wild, lush landscapes of Zimbabwe. What started as a quest for the purest sweetness turned into a mission to bottle nature's finest moments.
                </p>
                <p class="font-note text-2xl text-honey-600 dark:text-honey-400 transform -rotate-1 transition-colors">
                    "From the jungle to your jar!"
                </p>
            </div>

            <!-- Polaroid Side (Video Embed) -->
            <div class="md:w-1/2 flex justify-center md:justify-start">
                <div class="polaroid-frame bg-white dark:bg-white/5 w-full max-w-md transform rotate-3 relative border dark:border-white/10 transition-colors">
                    <div class="tape-strip tape-top-center"></div>
                    <div class="aspect-video w-full mb-4 overflow-hidden rounded shadow-inner bg-black">
                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/oE6KzMA4HJo" title="Vincent's Story" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                    <div class="font-handwritten text-3xl text-center text-gray-800 dark:text-honey-100 transition-colors">
                        A Pure, Sweet Taste
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Section 2: Founder Story (Vincent) -->
    <div class="container mx-auto px-4 mb-32 relative z-10">
        <div class="relative bg-white dark:bg-dark-surface rounded-lg shadow-xl p-8 md:p-12 max-w-5xl mx-auto transform -rotate-1 border border-gray-100 dark:border-white/10 transition-colors">
            <!-- Tape corners -->
            <div class="tape-strip tape-corner-tl"></div>
            <div class="tape-strip tape-corner-tr"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Image -->
                <div class="md:w-2/5">
                     <div class="polaroid-frame bg-white dark:bg-white/5 transform -rotate-3 w-full border dark:border-white/10 transition-colors">
                         <div class="aspect-[3/4] overflow-hidden rounded mb-4">
                            <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/20250510_084203-1-scaled.jpg' ) ); ?>" alt="Vincent Mutimbanyoka" class="w-full h-full object-cover">
                         </div>
                         <div class="font-handwritten text-2xl text-center text-gray-900 dark:text-honey-50 transition-colors">Vincent Mutimbanyoka</div>
                         <div class="font-note text-lg text-center text-gray-500 dark:text-gray-400 transition-colors">Founder & Chief Taster</div>
                     </div>
                </div>

                <!-- Content -->
                <div class="md:w-3/5">
                    <h2 class="font-handwritten text-5xl text-honey-600 dark:text-honey-400 mb-6 transition-colors">A Sweet Discovery</h2>
                    <div class="prose prose-lg text-gray-600 dark:text-gray-300 transition-colors">
                        <p class="italic mb-4 text-xl border-l-4 border-honey-300 dark:border-honey-600 pl-4 bg-honey-50 dark:bg-honey-900/20 py-2 pr-2 transition-colors">
                            "I used to have severe pimples on my face whenever I consumed sugar. A doctor advised me to switch to honey—and that changed everything."
                        </p>
                        <p class="mb-4">
                            For Vincent Mutimbanyoka, HoneyScoop was born from a personal search for health. Struggling with sugar allergies that affected his skin and wellbeing, he followed medical advice to seek natural alternatives. This led him to the heart of Zimbabwe's wild jungles, where he discovered the pure, healing power of raw honey.
                        </p>
                        <p class="mb-4">
                            His journey from consumer to producer hasn't gone unnoticed. Vincent was a **Finalist in the Old Mutual VCC4 (2024)** and is a proud participant in the **Eagles Nest Young Export Incubator 4** run by ZimTrade.
                        </p>
                        <p>
                            Today, HoneyScoop stands as a testament to that discovery—offering 100% natural, organic honey that bridges the gap between traditional wisdom and modern wellness.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: The Why (Gallery) -->
    <div class="container mx-auto px-4 mb-32">
        <h2 class="font-handwritten text-6xl text-center text-gray-800 dark:text-honey-50 mb-16 transition-colors">The Why</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="polaroid-frame bg-white dark:bg-white/5 transform rotate-2 h-auto border dark:border-white/10 transition-colors">
                <div class="h-64 mb-4 overflow-hidden rounded">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/DSC_9617-scaled.jpg' ) ); ?>" alt="Zimbabwean Roots" class="w-full h-full object-cover">
                </div>
                <h3 class="font-handwritten text-2xl mb-2 text-center text-gray-900 dark:text-honey-50 transition-colors">Zimbabwean Roots</h3>
                <p class="font-note text-lg text-center text-gray-600 dark:text-gray-400 transition-colors">Proudly local produce, harvested from our vibrant landscapes.</p>
            </div>

            <!-- Card 2 -->
            <div class="polaroid-frame bg-white dark:bg-white/5 transform -rotate-1 mt-12 md:mt-0 border dark:border-white/10 transition-colors">
                <div class="h-64 mb-4 overflow-hidden rounded">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/Trade-Center-April_-61.jpg' ) ); ?>" alt="Empowerment" class="w-full h-full object-cover">
                </div>
                <h3 class="font-handwritten text-2xl mb-2 text-center text-gray-900 dark:text-honey-50 transition-colors">Empowerment</h3>
                <p class="font-note text-lg text-center text-gray-600 dark:text-gray-400 transition-colors">Uplifting rural communities and Zimbabwean exporters.</p>
            </div>

            <!-- Card 3 -->
            <div class="polaroid-frame bg-white dark:bg-white/5 transform rotate-3 border dark:border-white/10 transition-colors">
                 <div class="tape-strip tape-top-center transform rotate-45" style="top: -10px;"></div>
                <div class="h-64 mb-4 overflow-hidden rounded">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/DSC_9625-scaled.jpg' ) ); ?>" alt="Pure Quality" class="w-full h-full object-cover">
                </div>
                <h3 class="font-handwritten text-2xl mb-2 text-center text-gray-900 dark:text-honey-50 transition-colors">Pure Quality</h3>
                <p class="font-note text-lg text-center text-gray-600 dark:text-gray-400 transition-colors">Testing against the highest global standards.</p>
            </div>
        </div>
    </div>
    
    <!-- Parallax / Passion Found -->
    <div class="w-full relative py-20 bg-honey-100 dark:bg-honey-900/20 overflow-hidden mb-20 group transition-colors">
         <!-- Abstract Bee BG -->
         <div class="absolute inset-0 opacity-10">
             <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                 <path d="M0 100 L100 0" stroke="orange" stroke-width="2" />
                 <path d="M0 0 L100 100" stroke="orange" stroke-width="2" />
             </svg>
         </div>
         
         <div class="container mx-auto px-4 relative z-10 text-center">
             <h2 class="font-handwritten text-6xl text-honey-800 dark:text-honey-400 mb-8 transition-colors">Passion Found</h2>
             <p class="text-xl md:text-3xl font-light text-honey-900 dark:text-honey-100 max-w-4xl mx-auto leading-relaxed transition-colors">
                 "They roamed about the savannahs, the jungles, the vleis, and the delicate ecosystems of our elements here in home-sweet-home Africa."
             </p>
         </div>
         
         <!-- Floating Bee Elements (GSAP could animate these later) -->
         <div class="absolute top-10 left-10 text-honey-300 w-20 h-20 animate-bounce delay-700">
             <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15 8L21 9L17 14L18 20L12 17L6 20L7 14L3 9L9 8L12 2Z"/></svg>
         </div>
    </div>


     <!-- Sustainability / Mission -->
    <div class="container mx-auto px-4 mb-20">
         <div class="flex flex-col-reverse md:flex-row items-center gap-12 bg-white dark:bg-dark-surface p-8 rounded-3xl border-4 border-dashed border-honey-200 dark:border-honey-500/30 transition-colors">
             
             <!-- Content -->
             <div class="md:w-1/2">
                 <h2 class="font-handwritten text-5xl text-gray-800 dark:text-honey-50 mb-6 transition-colors">Sustainability</h2>
                 <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed transition-colors">
                     At Honeyscoop, we believe in a future where nature thrives. Our mission extends beyond the jar—we are dedicated to creating a sustainable ecosystem that protects our bees and supports the rural farmers who care for them.
                 </p>
                 <a href="/shop" class="inline-block mt-4 px-8 py-3 bg-honey-500 text-white font-bold rounded-full hover:bg-honey-600 transition-colors transform hover:-translate-y-1 shadow-md">
                     Taste the Difference
                 </a>
             </div>
             
             <!-- Image -->
             <div class="md:w-1/2">
                 <div class="rounded-2xl overflow-hidden shadow-lg transform rotate-2 aspect-video">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/DSC_9625-scaled.jpg' ) ); ?>" alt="Sustainability" class="w-full h-full object-cover">
                 </div>
             </div>
         </div>
    </div>

</div>

<?php
get_footer();
