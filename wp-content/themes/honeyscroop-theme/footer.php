</main><!-- #primary -->

<footer id="colophon" class="site-footer">
    <div class="container container-wide">
        
        <!-- Top Section: 5 Columns -->
        <div class="footer-grid">
            
            <!-- Column 1: Navigate -->
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

            <!-- Column 2: Our Products -->
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

            <!-- Column 3: Company -->
            <div class="footer-column">
                <h6 class="footer-title">Company</h6>
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer_company',
                        'container'      => false,
                        'menu_class'     => 'footer-menu',
                        'fallback_cb'    => false,
                    )
                );
                ?>
            </div>

            <!-- Column 4: Brand Blurb -->
            <div class="footer-column">
                <h6 class="footer-title">Honeyscoop</h6>
                <p class="footer-blurb">
                    We are a family dedicated to bringing you pure, ethically sourced honey from the landscapes of Zimbabwe to your home. Partnering with rural producers to deliver wellness, joy, and purpose in every drop.
                </p>
                <div class="footer-contact">
                    <p>45 Cunningham Road, Greendale</p>
                    <p>Harare, Zimbabwe</p>
                    <p style="margin-top: 0.5rem; font-weight: bold;">+263 714 239 920</p>
                </div>
            </div>

            <!-- Column 5: Social -->
            <div class="footer-column footer-social-column">
                <h6 class="footer-title">Social</h6>
                <div class="footer-social">
                    <?php
                    $social_networks = array(
                        'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
                        'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.65-1.58-1.09v8.32c0 2.58-2.09 4.68-4.67 4.68-2.58 0-4.66-2.09-4.66-4.67 0-2.58 2.08-4.67 4.66-4.67.5 0 .99.1 1.45.28v-4.69c-3.79.23-6.77 3.4-6.77 7.23 0 4.01 3.25 7.26 7.26 7.26 4.01 0 7.26-3.25 7.26-7.26V6.63c1.09.52 2.22.86 3.41.97V2.01c-1.89-.13-3.61-.95-4.9-2.31-.13-.13-.25-.28-.37-.42l-.09-.07-.5-.01z"/></svg>',
                        'x'         => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>',
                        'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
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

        <!-- Bottom Section: Copy + Currency + Payment -->
        <div class="footer-bottom">
            <!-- Copy -->
            <div class="footer-bottom-left">
                <p>&copy; 2025 Honeyscoop (Pvt) Ltd. All Rights Reserved.</p>
                <div style="margin-top: 0.25rem;">
                    <a href="#" class="hover-underline">Privacy Policy</a>
                    <span style="margin: 0 0.5rem;">•</span>
                    <a href="#" class="hover-underline">Terms of Service</a>
                </div>
            </div>
            
            <!-- Currency & Payment -->
            <div class="footer-bottom-right">
                 <div class="flex items-center" style="gap: 0.25rem; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>Zimbabwe (USD)</span>
                 </div>
                 
                 <div class="payment-icons">
                    <div class="payment-icon-placeholder"></div>
                    <div class="payment-icon-placeholder"></div>
                    <div class="payment-icon-placeholder"></div>
                 </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
