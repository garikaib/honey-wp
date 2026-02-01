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

        <!-- Hero Section -->
        <div class="relative h-[50vh] min-h-[400px] flex items-end pb-16 overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0 bg-honey-900">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'full', array( 'class' => 'w-full h-full object-cover opacity-60 mix-blend-overlay' ) ); ?>
                <?php else : ?>
                    <div class="w-full h-full opacity-10 pattern-honeycomb"></div>
                <?php endif; ?>
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent z-10"></div>

            <div class="container container-wide relative z-20 text-white">
                <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="inline-flex items-center gap-2 text-honey-200 hover:text-white transition-colors mb-6 text-sm font-bold uppercase tracking-widest">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Events
                </a>
                
                <h1 class="text-4xl md:text-6xl font-serif font-bold leading-tight mb-6">
                    <?php the_title(); ?>
                </h1>

                <!-- Quick Meta Bar -->
                <div class="flex flex-wrap gap-6 md:gap-12 text-lg font-medium border-t border-white/20 pt-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-honey-500/20 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-honey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs uppercase text-white/50 tracking-wider">Date</span>
                            <?php echo esc_html( $formatted_date ); ?>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-honey-500/20 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-honey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs uppercase text-white/50 tracking-wider">Location</span>
                            <?php echo esc_html( $location ); ?>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-honey-500/20 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-honey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs uppercase text-white/50 tracking-wider">Price</span>
                            <?php echo esc_html( $price ); ?>
                        </div>
                    </div>
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
                    <div class="bg-white p-8 rounded-2xl shadow-sm border-t-4 border-honey-500">
                        <h3 class="text-xl font-serif font-bold text-gray-900 mb-6">Event Details</h3>
                        
                        <div class="space-y-6">
                            <?php if ( $start_date ) : ?>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-honey-50 text-honey-600 flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm mb-1">Date & Time</h4>
                                    <p class="text-gray-600 text-sm"><?php echo esc_html( $formatted_date ); ?></p>
                                    <p class="text-gray-500 text-xs mt-1">Starts at <?php echo esc_html( $formatted_time ); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ( $location ) : ?>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-lg bg-honey-50 text-honey-600 flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm mb-1">Location</h4>
                                    <p class="text-gray-600 text-sm"><?php echo esc_html( $location ); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100">
                             <a href="#" class="block w-full text-center bg-honey-600 text-white font-bold py-4 rounded-xl hover:bg-honey-700 transition-colors shadow-lg shadow-honey-500/30">
                                Register Now — <?php echo esc_html( $price ); ?>
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
