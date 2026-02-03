<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
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
        <div class="header-row hidden-mobile">
            
            <!-- Left: Logo -->
            <div class="header-left">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/honescoop_logo.webp' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="logo-img">
                </a>
            </div>

            <!-- Right: Generic Column -->
            <div class="header-right-column">
                <!-- Top Row: Tools -->
                <div class="header-top-row header-tools">
                <!-- Currency Selector -->
                  <div id="currency-selector-root"></div>

                 <!-- Account -->
                 <a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>" class="icon-btn" aria-label="Account">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                 </a>

                 <!-- Cart -->
                 <div id="cart-widget-root" class="flex items-center"></div>
                </div>
                <!-- Bottom Row: Navigation -->
                <div class="header-bottom-row">
                    <div id="header-nav-root"></div>
                </div>
            </div>
        </div>
    </div>
</header>
<main id="primary" class="site-main">
