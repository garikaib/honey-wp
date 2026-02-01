<?php
/**
 * The template for displaying all single event posts
 *
 * @package HoneyScroop_Theme
 */

get_header();

while ( have_posts() ) :
    the_post();

    // Get Meta Data
    $start_date = get_post_meta( get_the_ID(), 'event_start_date', true );
    $end_date   = get_post_meta( get_the_ID(), 'event_end_date', true );
    $location   = get_post_meta( get_the_ID(), 'event_location', true );
    $price      = get_post_meta( get_the_ID(), 'event_price', true );

    // Format Date
    $formatted_date = $start_date ? date( 'F j, Y', strtotime( $start_date ) ) : '';
    $formatted_time = $start_date ? date( 'g:i a', strtotime( $start_date ) ) : '';
    ?>

    <main id="primary" class="site-main bg-honey-50/20 min-h-screen pb-20">

        <!-- Hero Section (No Full Overlay - Text Underlays Only) -->
        <div class="relative h-[65vh] min-h-[550px] flex items-end overflow-hidden group">
            <!-- Background Image (Full, Untinted) -->
            <div class="absolute inset-0 z-0">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'full', array( 'class' => 'w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105' ) ); ?>
                <?php else : ?>
                    <div class="w-full h-full bg-gradient-to-br from-honey-100 to-honey-200"></div>
                <?php endif; ?>
            </div>

            <!-- Content Container -->
            <div class="container container-wide relative z-20 pb-12">
                
                <!-- Back Button (Pill Underlay) -->
                <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="inline-flex items-center gap-2 text-white hover:text-honey-300 transition-all mb-6 text-xs font-bold uppercase tracking-widest bg-black/50 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/20 hover:border-honey-400 hover:bg-black/60 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Events
                </a>
                
                <!-- Title (Card Underlay) -->
                <div class="inline-block bg-black/50 backdrop-blur-md px-8 py-6 rounded-2xl border border-white/20 mb-6 shadow-2xl">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold leading-tight text-white max-w-3xl">
                        <?php the_title(); ?>
                    </h1>
                </div>

                <!-- Meta Pills Row -->
                <div class="flex flex-wrap gap-3">
                    
                    <!-- Date Pill -->
                    <div class="inline-flex items-center gap-3 bg-black/50 backdrop-blur-md px-5 py-3 rounded-full border border-white/20 shadow-lg">
                        <div class="w-9 h-9 rounded-full bg-honey-500 text-white flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase text-honey-300 tracking-widest font-bold">Date</span>
                            <span class="text-sm font-semibold text-white"><?php echo esc_html( $formatted_date ); ?></span>
                        </div>
                    </div>

                    <!-- Location Pill -->
                    <div class="inline-flex items-center gap-3 bg-black/50 backdrop-blur-md px-5 py-3 rounded-full border border-white/20 shadow-lg">
                        <div class="w-9 h-9 rounded-full bg-honey-500 text-white flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase text-honey-300 tracking-widest font-bold">Location</span>
                            <span class="text-sm font-semibold text-white"><?php echo esc_html( $location ); ?></span>
                        </div>
                    </div>

                    <!-- Price Pill -->
                    <div class="inline-flex items-center gap-3 bg-honey-500 px-5 py-3 rounded-full shadow-lg shadow-honey-600/30 border border-honey-400">
                        <div class="w-9 h-9 rounded-full bg-white text-honey-600 flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase text-honey-900/70 tracking-widest font-bold">Price</span>
                            <span class="text-sm font-bold text-white"><?php echo esc_html( $price ); ?></span>
                        </div>
                    </div>

                    <!-- Time Pill -->
                    <?php if ( $formatted_time ) : ?>
                    <div class="inline-flex items-center gap-3 bg-black/50 backdrop-blur-md px-5 py-3 rounded-full border border-white/20 shadow-lg">
                        <div class="w-9 h-9 rounded-full bg-honey-500 text-white flex items-center justify-center shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-[9px] uppercase text-honey-300 tracking-widest font-bold">Time</span>
                            <span class="text-sm font-semibold text-white"><?php echo esc_html( $formatted_time ); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Left: Content -->
                <div class="lg:col-span-8">
                    <div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-honey-100">
                        <h2 class="text-2xl font-serif font-bold text-gray-900 mb-6">About This Event</h2>
                        <div class="prose prose-lg prose-headings:font-serif prose-a:text-honey-600 max-w-none text-gray-600">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Details Sidebar -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <!-- Details Card -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-honey-100 sticky top-24">
                        <h3 class="text-xl font-serif font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <span class="w-1 h-6 bg-honey-500 rounded-full"></span>
                            Event Details
                        </h3>
                        
                        <div class="space-y-6">
                            <?php if ( $start_date ) : ?>
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-honey-50/50 hover:bg-honey-50 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-white text-honey-600 flex items-center justify-center flex-shrink-0 shadow-sm border border-honey-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm mb-1 uppercase tracking-wider">Date & Time</h4>
                                    <p class="text-gray-600 text-sm font-medium"><?php echo esc_html( $formatted_date ); ?></p>
                                    <p class="text-honey-600 text-xs mt-1 font-bold">Starts at <?php echo esc_html( $formatted_time ); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ( $location ) : ?>
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-honey-50/50 hover:bg-honey-50 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-white text-honey-600 flex items-center justify-center flex-shrink-0 shadow-sm border border-honey-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm mb-1 uppercase tracking-wider">Location</h4>
                                    <p class="text-gray-600 text-sm font-medium"><?php echo esc_html( $location ); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-8 pt-8 border-t border-honey-100">
                             <a href="#" class="group block w-full text-center bg-gradient-to-r from-honey-500 to-honey-600 text-white font-bold py-4 rounded-xl hover:shadow-lg hover:shadow-honey-500/30 transition-all transform hover:-translate-y-1">
                                <span class="flex items-center justify-center gap-2">
                                    Register Now 
                                    <span class="bg-white/20 px-2 py-0.5 rounded text-xs"><?php echo esc_html( $price ); ?></span>
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Share Widget -->
                    <div class="bg-gray-50 p-6 rounded-2xl text-center">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Share this event</p>
                        <div class="flex justify-center gap-4">
                            <!-- Social Icons (Dummy) -->
                            <button class="w-10 h-10 rounded-full bg-white text-gray-400 hover:text-honey-600 hover:shadow-md transition-all flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </button>
                            <button class="w-10 h-10 rounded-full bg-white text-gray-400 hover:text-honey-600 hover:shadow-md transition-all flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <?php
endwhile;

get_footer();
