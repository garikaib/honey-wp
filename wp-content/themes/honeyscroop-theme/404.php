<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package HoneyScroop_Theme
 */

get_header();
?>

<main id="primary" class="site-main flex items-center justify-center min-h-[60vh] bg-honey-50/50">
    <div class="container text-center py-20">
        <div class="error-404 not-found max-w-2xl mx-auto">
            
            <!-- Hive Scene Container -->
            <div id="hive-scene" class="relative w-full h-[300px] mb-8 flex justify-center items-center overflow-hidden">
                
                <!-- SVG: Tree Branch + Hanging Hive -->
                <svg width="300" height="300" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="relative z-10">
                    <!-- Tree Branch -->
                    <path d="M-20 40C20 40 50 60 80 50C110 40 140 20 180 30" stroke="#78350F" stroke-width="8" stroke-linecap="round"/>
                    <path d="M80 50V70" stroke="#78350F" stroke-width="4"/>
                    
                    <!-- Hanging Hive Group -->
                    <g id="hive-group">
                        <!-- Hive Body -->
                        <path d="M60 70H100L110 90H50L60 70Z" fill="#F59E0B"/>
                        <path d="M50 90H110L120 110H40L50 90Z" fill="#FBBF24"/>
                        <path d="M40 110H120L110 130H50L40 110Z" fill="#F59E0B"/>
                        <path d="M50 130H110L100 150H60L50 130Z" fill="#FBBF24"/>
                        <!-- Entrance -->
                        <circle cx="80" cy="120" r="12" fill="#3D2912"/>
                    </g>
                    
                    <!-- Buzzing Swarm Particles Target -->
                    <g id="hive-swarm-group" transform="translate(80, 120)"></g>
                </svg>

            </div>

            <header class="page-header mb-6">
                <h1 class="text-5xl font-serif font-bold text-honey-900 mb-4">Oh! You've strayed from the hive.</h1>
            </header>

            <div class="page-content">
                <p class="text-xl text-gray-600 mb-10">It seems this page has flown away or doesn't exist. Let's get you back to the sweet stuff.</p>
                
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary inline-flex items-center px-8 py-3 bg-honey-500 text-white font-bold rounded-full hover:bg-honey-600 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Return to Hive
                </a>
            </div>
            
        </div>
    </div>
</main>

<?php
get_footer();
