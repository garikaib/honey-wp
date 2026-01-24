</main><!-- #primary -->

<footer id="colophon" class="site-footer bg-honey-900 text-honey-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div>
                <h3 class="text-xl font-heading font-bold text-white mb-4">Honeyscroop</h3>
                <p class="text-honey-200">Pure, organic honey from the heart of Zimbabwe. Sustainably sourced, premium quality.</p>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-4">Quick Links</h4>
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'space-y-2',
                    )
                );
                ?>
            </div>
            <div>
                <h4 class="text-lg font-bold text-white mb-4">Contact Us</h4>
                <p class="text-honey-200">Email: info@honeyscroop.co.zw</p>
                <p class="text-honey-200">Phone: +263 123 456 789</p>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-honey-800 text-center text-honey-400 text-sm">
            <p>&copy; <?php echo date( 'Y' ); ?> Honeyscroop. All rights reserved.</p>
        </div>
    </div>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
