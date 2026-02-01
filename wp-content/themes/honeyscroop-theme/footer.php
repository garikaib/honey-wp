</main><!-- #primary -->

<footer id="colophon" class="site-footer">
    <!-- Newsletter Section: Join the Hive -->
    <section class="footer-newsletter">
        <div class="container container-wide">
            <div class="newsletter-grid">
                <div class="newsletter-title">
                    <h4 class="text-2xl font-serif font-bold">Join the Hive</h4>
                    <p class="text-honey-200/60">Subscribe for sweet updates and exclusive drops.</p>
                </div>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" class="newsletter-input" required aria-label="Email for newsletter">
                    <button type="submit" class="newsletter-btn">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <div class="footer-main">
        <div class="container container-wide">
            
            <!-- Main Grid -->
            <div class="footer-grid">
                
                <!-- Column 1: Brand Info -->
                <div class="footer-column lg:col-span-2">
                    <div class="footer-logo mb-6">
                        <div class="inline-flex items-center justify-center bg-honey-50 rounded-full w-20 h-20 p-4 shadow-md">
                            <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/honescoop_cropped_transparent.webp' ) ); ?>" alt="Honeyscoop" class="w-full h-auto">
                        </div>
                    </div>
                    <p class="footer-blurb">
                        Savor the sweetness of nature with Honeyscoop. We are a family dedicated to bringing you the golden richness of pure, ethically sourced honey from Zimbabwe's vibrant landscapes.
                    </p>
                    <div class="footer-contact">
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-honey-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            45 Cunningham Road, Greendale, Harare
                        </p>
                        <p class="flex items-center gap-2 font-bold text-honey-100">
                            <svg class="w-4 h-4 text-honey-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5.983C3 4.888 3.895 4 5 4h.445c.962 0 1.836.586 2.212 1.474l.822 1.944a2.212 2.212 0 01-.482 2.373l-1.505 1.505a1.505 1.505 0 00-.317 1.595 12.015 12.015 0 004.836 4.836 1.505 1.505 0 001.595-.317l1.505-1.505a2.212 2.212 0 012.373-.482l1.944.822c.888.376 1.474 1.252 1.474 2.212v.445c0 1.105-.895 2-2 2a14.152 14.152 0 01-13.633-13.633z"></path></svg>
                            +263 714 239 920
                        </p>
                    </div>
                </div>

                <!-- Column 2: Navigate -->
                <div class="footer-column">
                    <h6 class="footer-title">Navigate</h6>
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer_navigate',
                            'container'      => false,
                            'menu_class'     => 'footer-menu',
                            'fallback_cb'    => false,
                        )
                    );
                    ?>
                </div>

                <!-- Column 3: Shop -->
                <div class="footer-column">
                    <h6 class="footer-title">Our Products</h6>
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer_products',
                            'container'      => false,
                            'menu_class'     => 'footer-menu',
                            'fallback_cb'    => false,
                        )
                    );
                    ?>
                </div>

                <!-- Column 4: Social -->
                <div class="footer-column">
                    <h6 class="footer-title">Connect</h6>
                    <div class="footer-social">
                        <?php
                        $social_networks = array(
                            'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                            'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
                            'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.65-1.58-1.09v8.32c0 2.58-2.09 4.68-4.67 4.68-2.58 0-4.66-2.09-4.66-4.67 0-2.58 2.08-4.67 4.66-4.67.5 0 .99.1 1.45.28v-4.69c-3.79.23-6.77 3.4-6.77 7.23 0 4.01 3.25 7.26 7.26 7.26 4.01 0 7.26-3.25 7.26-7.26V6.63c1.09.52 2.22.86 3.41.97V2.01c-1.89-.13-3.61-.95-4.9-2.31-.13-.13-.25-.28-.37-.42l-.09-.07-.5-.01z"/></svg>',
                            'x'         => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>',
                        );

                        foreach ( $social_networks as $key => $icon_svg ) {
                            $url = get_theme_mod( "social_{$key}_url" );
                            if ( ! empty( $url ) ) {
                                echo '<a href="' . esc_url( $url ) . '" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( ucfirst( $key ) ) . '">' . $icon_svg . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <div class="footer-bottom-left">
                    <p>&copy; <?php echo date( 'Y' ); ?> Honeyscoop (Pvt) Ltd. Taste the Sweetness.</p>
                </div>
                
                <div class="footer-bottom-right">
                    <div class="currency-selector flex items-center gap-2 text-xs opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        Zimbabwe (USD)
                    </div>
                    <div class="payment-icons flex items-center gap-4">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/EcoCash-App-Image.png' ) ); ?>" alt="EcoCash" class="h-6 w-auto">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/INNBUCKS_WHITE_LOGO_B.png' ) ); ?>" alt="InnBucks" class="h-6 w-auto">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/unnamed.webp' ) ); ?>" alt="Omari" class="h-6 w-auto">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
