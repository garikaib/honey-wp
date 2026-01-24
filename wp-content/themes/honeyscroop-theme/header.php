<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> style="font-family: 'Outfit', sans-serif;">
<?php wp_body_open(); ?>

<!-- Announcement Bar / Ticker -->
    <div class="announcement-bar bg-[#FFD700] text-honey-900 text-center py-2.5 text-[11px] font-semibold tracking-wider uppercase">
        <div class="flex items-center justify-center gap-1">
            <span>Providing AI Agents for</span>
            <span class="text-rotate inline-flex align-bottom h-[1.3em] overflow-hidden">
                <span class="text-rotate-inner block text-left">
                    <span class="block px-2 bg-teal-400 text-teal-800 rounded mx-1">Designers</span>
                    <span class="block px-2 bg-red-400 text-red-800 rounded mx-1">Developers</span>
                    <span class="block px-2 bg-blue-400 text-blue-800 rounded mx-1">Managers</span>
                </span>
            </span>
        </div>
    </div>

<!-- Main Header -->
<header id="masthead" class="site-header bg-white border-b border-gray-100 z-40 relative">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Row: Currency (Left) | Logo (Center) | Icons (Right) -->
        <div class="hidden md:grid grid-cols-3 items-center py-5">
            
            <!-- Left: Currency Selector -->
            <div class="justify-self-start">
                <button class="text-[11px] font-semibold text-gray-800 flex items-center hover:text-honey-600 transition-colors tracking-widest uppercase">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="mr-2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
                    <span>UNITED STATES (USD$)</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-1 opacity-50"><path d="m6 9 6 6 6-6"/></svg>
                </button>
            </div>

            <!-- Center: Logo -->
            <div class="justify-self-center">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block">
                    <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/honeyscoop-logo.png' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-14 w-auto object-contain">
                </a>
            </div>

            <!-- Right: Search, Account, Cart -->
            <div class="justify-self-end flex items-center space-x-5">
                 <!-- Search -->
                 <button class="text-gray-700 hover:text-honey-600 transition-colors" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                 </button>

                 <!-- Account -->
                 <a href="#" class="text-gray-700 hover:text-honey-600 transition-colors" aria-label="Account">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                 </a>

                 <!-- Cart -->
                 <a href="#" class="text-gray-700 hover:text-honey-600 transition-colors relative" aria-label="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                 </a>
            </div>
        </div>

        <!-- Desktop Navigation (React-powered with animations) -->
        <div id="header-nav-root" class="hidden md:block"></div>

        <!-- Mobile Header -->
        <div class="md:hidden flex justify-between items-center py-3">
             <!-- Hamburger -->
             <button class="text-gray-700 p-2" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
             </button>

             <!-- Logo (Smaller) -->
             <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block">
                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/honeyscoop-logo.png' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-8 w-auto">
             </a>

             <!-- Cart -->
             <a href="#" class="text-gray-700 p-2 relative" aria-label="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
             </a>
        </div>
    </div>
</header>
<main id="primary" class="site-main">
