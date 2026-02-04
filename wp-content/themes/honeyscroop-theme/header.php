<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>

    <!-- Preload LCP Image -->
    <link rel="preload" as="image" href="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/VJ70752.webp' ) ); ?>" fetchpriority="high">

    <script id="dark-mode-init">
        (function() {
            try {
                const theme = localStorage.getItem('honeyscroop-theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && systemTheme)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {
                console.error('Dark mode init failed:', e);
            }
        })();
    </script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Main Header -->
<header id="masthead" class="site-header">
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="flex items-center justify-center gap-md">
            <span>Perfect for</span>
            <div class="text-rotate">
                <ul class="text-rotate-inner">
                    <li class="text-rotate-item"><span class="badge badge-yellow">Honey Lovers</span></li>
                    <li class="text-rotate-item"><span class="badge badge-amber">Healthy Living</span></li>
                    <li class="text-rotate-item"><span class="badge badge-orange">Sweet Moments</span></li>
                    <!-- Duplicate first item for seamless loop -->
                    <li class="text-rotate-item"><span class="badge badge-yellow">Honey Lovers</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        
        <!-- Header Row: Logo (Left) | Tools (Right) -->
        <div class="header-row">
            
            <!-- Left: Logo (Desktop Only) -->
            <div class="header-left hidden md:block">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block group mt-2">
                    <div class="relative p-2 rounded-xl transition-all duration-300 dark:bg-white/40">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/honescoop_logo.webp' ) ); ?>" 
                             alt="Honeyscoop" 
                             width="300" 
                             height="209"
                             class="h-14 w-auto dark:brightness-0 dark:invert transition-[filter] duration-300">
                    </div>
                </a>
            </div>

            <!-- Right: Generic Column -->
            <div class="header-right-column">
                <!-- Top Row: Tools (Desktop Only) -->
                <div class="header-top-row header-tools hidden md:flex">
                <!-- Currency Selector -->
                  <div id="currency-selector-root"></div>

                 <!-- Cart -->
                 <div id="cart-widget-root" class="flex items-center"></div>
                </div>
                <!-- Bottom Row: Navigation (Includes Mobile Header & Menu) -->
                <div class="header-bottom-row w-full md:w-auto">
                    <div id="header-nav-root" class="w-full"></div>
                </div>
            </div>
        </div>
    </div>
</header>
<main id="primary" class="site-main">
