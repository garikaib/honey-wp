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

    <style>
        #site-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
        }
        
        .dark #site-loader {
            background: #1a0f05; /* Deep Premium Cocoa consistent with footer */
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .infinity-loader {
            width: 120px;
            height: auto;
        }

        .infinity-path {
            fill: none;
            stroke: #d97706; /* amber-600 */
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dasharray: 200;
            stroke-dashoffset: 200;
            animation: dash 2.5s ease-in-out infinite, color-shift 5s infinite;
        }

        .dark .infinity-path {
            stroke: #fbbf24; /* amber-400 */
        }

        @keyframes dash {
            0% { stroke-dashoffset: 200; }
            50% { stroke-dashoffset: 0; }
            100% { stroke-dashoffset: -200; }
        }

        @keyframes color-shift {
            0%, 100% { stroke: #d97706; }
            50% { stroke: #f59e0b; }
        }

        .loader-text {
            font-family: 'Outfit', sans-serif;
            font-size: 0.875rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #4b5563;
            font-weight: 500;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .dark .loader-text {
            color: #94a3b8;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        #site-loader.loader-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        
        /* Prevent scroll while loading */
        body.loading {
            overflow: hidden;
        }
    </style>
</head>
<body <?php body_class('loading'); ?>>
<?php wp_body_open(); ?>

<!-- Full Page Premium Loader -->
<div id="site-loader">
    <div class="loader-content">
        <svg class="infinity-loader" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
            <path class="infinity-path" d="M30 20 C 30 10, 45 10, 50 20 C 55 30, 70 30, 70 20 C 70 10, 55 10, 50 20 C 45 30, 30 30, 30 20 Z" />
        </svg>
        <span class="loader-text">Taste The Sweetness</span>
    </div>
</div>

<script>
    (function() {
        const hideLoader = () => {
            const loader = document.getElementById('site-loader');
            if (loader && !loader.classList.contains('loader-hidden')) {
                loader.classList.add('loader-hidden');
                document.body.classList.remove('loading');
            }
        };
        window.addEventListener('load', hideLoader);
        setTimeout(hideLoader, 3000); // 3s max wait fallback
    })();
</script>

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
