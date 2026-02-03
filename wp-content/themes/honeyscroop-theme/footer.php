</main><!-- #primary -->

<footer id="colophon" class="site-footer">
    <!-- Newsletter Section: Join the Hive -->
    <section class="relative py-20 overflow-hidden bg-gradient-to-br from-amber-900 via-amber-800 to-amber-950 text-white">
        <!-- Decor: Honeycomb Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M30 0l25.98 15v30L30 60 4.02 45V15z\' fill-opacity=\'0.4\' fill=\'%23ffffff\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
        
        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-block mb-4">
                    <span class="px-4 py-1.5 rounded-full border border-amber-400/30 bg-amber-900/50 backdrop-blur-sm text-xs font-bold tracking-[0.2em] uppercase text-amber-200">Exclusive Updates</span>
                </div>
                <h4 class="text-4xl md:text-5xl font-serif font-medium mb-6 leading-tight">Join the Hive</h4>
                <p class="text-lg md:text-xl text-amber-100/80 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                    Subscribe for sweet updates, exclusive drops, and a taste of the wilderness delivered straight to your inbox.
                </p>
                
                <form id="newsletter-form" class="relative max-w-2xl mx-auto w-full px-4">
                    <div class="flex flex-col sm:flex-row items-center p-1.5 rounded-2xl sm:rounded-full bg-white/5 backdrop-blur-xl border border-white/10 shadow-2xl transition-all duration-500 hover:border-amber-400/30">
                        <div class="relative flex-grow w-full">
                            <input type="email" name="email" placeholder="Enter your email to join the hive" required 
                                   class="bg-transparent border-none w-full h-12 md:h-14 pl-8 pr-4 text-amber-50 placeholder-amber-100/40 focus:outline-none text-lg">
                        </div>
                        
                        <button type="submit" class="w-full sm:w-auto h-12 md:h-14 px-10 rounded-xl sm:rounded-full bg-white/10 hover:bg-white/20 text-white font-bold tracking-[0.1em] uppercase text-sm border border-white/40 hover:border-white/80 transition-all duration-300 whitespace-nowrap flex items-center justify-center gap-3 shadow-lg">
                            <span class="btn-text">Subscribe</span>
                            <span class="loading-spinner hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <svg class="success-icon hidden w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                    <div id="newsletter-message" class="absolute top-full left-0 w-full mt-4 text-sm font-medium opacity-0 transition-opacity duration-300 text-center tracking-wide space-y-2"></div>
                </form>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('newsletter-form');
        const message = document.getElementById('newsletter-message');
        const btn = form.querySelector('button');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.loading-spinner');
        const successIcon = btn.querySelector('.success-icon');
        const input = form.querySelector('input');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = input.value;
            
            // Loading State
            btn.disabled = true;
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
            message.style.opacity = '0';
            input.disabled = true;
            
            const formData = new FormData();
            formData.append('action', 'honeyscroop_subscribe');
            const nonce = window.honeyShopData ? window.honeyShopData.nonce : ''; 
            formData.append('nonce', nonce);
            formData.append('email', email);

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('hidden');
                
                if (data.success) {
                    successIcon.classList.remove('hidden');
                    // Update for outline style success
                    btn.classList.remove('bg-white/10', 'border-white/40');
                    btn.classList.add('bg-green-600/20', 'border-green-400');
                    
                    message.textContent = data.data.message || 'Welcome to the Hive!';
                    message.className = 'absolute top-full left-0 w-full mt-4 text-sm font-bold text-center tracking-wide text-green-400 drop-shadow-md';
                    message.style.opacity = '1';
                } else {
                    btn.disabled = false;
                    input.disabled = false;
                    btnText.classList.remove('hidden');
                    
                    message.textContent = data.data.message || 'Something went wrong. Please try again.';
                    message.className = 'absolute top-full left-0 w-full mt-4 text-sm font-bold text-center tracking-wide text-red-100 drop-shadow-md';
                    message.style.opacity = '1';
                }
            })
            .catch(error => {
                btn.disabled = false;
                input.disabled = false;
                btnText.classList.remove('hidden');
                spinner.classList.add('hidden');
                
                message.textContent = 'Connection error. Please try again.';
                message.className = 'absolute top-full left-0 w-full mt-4 text-sm font-bold text-center tracking-wide text-red-100 drop-shadow-md';
                message.style.opacity = '1';
            });
        });
    });
    </script>

    <div class="footer-main">
        <div class="container container-wide">
            
            <!-- Main Grid -->
            <div class="footer-grid">
                
                <!-- Column 1: Brand Info -->
                <div class="footer-column lg:col-span-2">
                    <div class="footer-logo mb-6">
                        <div class="inline-flex items-center justify-center rounded-full w-24 h-24 p-4 bg-white dark:bg-white/[0.075] transition-colors shadow-lg">
                            <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/honescoop_logo.webp' ) ); ?>" 
                                 alt="Honeyscoop" 
                                 width="240" 
                                 height="auto"
                                 class="w-full h-auto">
                        </div>
                    </div>
                    <p class="footer-blurb">
                        Savor the sweetness of nature with Honeyscoop. We are a family dedicated to bringing you the golden richness of pure, ethically sourced honey from Zimbabwe's vibrant landscapes.
                    </p>
                    <div class="footer-contact">
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-honey-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <?php echo esc_html( $settings['address'] ?? '45 Cunningham Road, Greendale, Harare' ); ?>
                        </p>
                        <p class="flex items-center gap-2 font-bold text-honey-100">
                            <svg class="w-4 h-4 text-honey-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5.983C3 4.888 3.895 4 5 4h.445c.962 0 1.836.586 2.212 1.474l.822 1.944a2.212 2.212 0 01-.482 2.373l-1.505 1.505a1.505 1.505 0 00-.317 1.595 12.015 12.015 0 004.836 4.836 1.505 1.505 0 001.595-.317l1.505-1.505a2.212 2.212 0 012.373-.482l1.944.822c.888.376 1.474 1.252 1.474 2.212v.445c0 1.105-.895 2-2 2a14.152 14.152 0 01-13.633-13.633z"></path></svg>
                            <?php echo esc_html( $settings['phoneNumber'] ?? '+263 714 239 920' ); ?>
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
                        $settings = get_option( 'honeyscroop_option_settings', array() );
                        $social_networks = array(
                            'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                            'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
                            'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.04-1.04-.79-.49-1.47-1.1-2.02-1.78v-3.95c1.39-.01 2.8-.32 4.03-.92.57-.27 1.08-.6 1.58-.97.01-2.93-.01-5.86.02-8.79.08-1.4-.54-2.79-1.35-3.94-1.31-1.92-3.58-3.17-5.91-3.21-1.43-.08-2.86.31-4.04 1.04-.79.49-1.47 1.1-2.02 1.78V.02h.01z"/></svg>',
                            'x'         => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>',
                        );

                        foreach ( $social_networks as $key => $icon_svg ) {
                            // Check 'social' array in settings
                            $url = $settings['social'][ $key ] ?? '';
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
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/EcoCash-App-Image.png' ) ); ?>" alt="EcoCash" class="h-6 w-auto" width="480" height="480">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/INNBUCKS_WHITE_LOGO_B.png' ) ); ?>" alt="InnBucks" class="h-6 w-auto" width="564" height="136">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/unnamed.webp' ) ); ?>" alt="Omari" class="h-6 w-auto" width="240" height="240">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
