import React, { useState, useRef, useEffect } from 'react';
import { createPortal } from 'react-dom';
import { createRoot } from 'react-dom/client';
import { Search, User, ShoppingBag, ChevronDown, Globe, Menu, X, Home, ShoppingCart, Newspaper, Calendar, Heart, Mail, Moon } from 'lucide-react';
import CartWidget from '../shop/components/CartWidget';
import { CurrencyProvider } from '../shop/context/CurrencyContext';
import CurrencySelector from '../shop/components/CurrencySelector';
import DarkModeToggle from '../components/DarkModeToggle';
import gsap from 'gsap';

const getIcon = (label) => {
    switch (label.toLowerCase()) {
        case 'home': return <Home size={20} strokeWidth={1.5} />;
        case 'shop': return <ShoppingCart size={20} strokeWidth={1.5} />;
        case 'news': return <Newspaper size={20} strokeWidth={1.5} />;
        case 'events': return <Calendar size={20} strokeWidth={1.5} />;
        case 'our story': return <Heart size={20} strokeWidth={1.5} />;
        case 'contact': return <Mail size={20} strokeWidth={1.5} />;
        default: return <ChevronDown size={20} strokeWidth={1.5} />;
    }
};

const menuItems = window.honeyscroopHeaderData?.primaryMenu || [];

const NavItem = ({ item }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [activeCategory, setActiveCategory] = useState(item.children?.[0]?.category || null);
    const timeoutRef = useRef(null);
    const dropdownRef = useRef(null);
    const itemsRef = useRef([]);

    const handleMouseEnter = () => {
        if (timeoutRef.current) clearTimeout(timeoutRef.current);
        setIsOpen(true);
    };

    const handleMouseLeave = () => {
        timeoutRef.current = setTimeout(() => setIsOpen(false), 200);
    };

    useEffect(() => {
        if (isOpen && dropdownRef.current) {
            // Dropdown Entrance
            gsap.fromTo(dropdownRef.current,
                { opacity: 0, y: 10, scale: 0.98, display: 'block' },
                { opacity: 1, y: 0, scale: 1, duration: 0.4, ease: "power4.out" }
            );

            // Staggered Items Entrance
            const subItems = dropdownRef.current.querySelectorAll('.stagger-item');
            if (subItems.length > 0) {
                gsap.fromTo(subItems,
                    { opacity: 0, y: 5 },
                    { opacity: 1, y: 0, duration: 0.4, stagger: 0.04, ease: "power2.out", delay: 0.1 }
                );
            }
        } else if (dropdownRef.current) {
            // Dropdown Exit
            gsap.to(dropdownRef.current, {
                opacity: 0,
                y: 8,
                scale: 0.98,
                duration: 0.3,
                ease: "power2.in",
                onComplete: () => {
                    if (dropdownRef.current) dropdownRef.current.style.display = 'none';
                }
            });
        }
    }, [isOpen]);

    const hasChildren = item.children && item.children.length > 0;
    const isMega = item.type === 'mega';

    // Get active sub-items for mega menu
    const activeSubItems = isMega && activeCategory
        ? item.children.find(c => c?.category === activeCategory)?.items || []
        : [];

    return (
        <li
            className="relative"
            onMouseEnter={hasChildren ? handleMouseEnter : undefined}
            onMouseLeave={hasChildren ? handleMouseLeave : undefined}
        >
            <a
                href={item.href}
                className={`group flex items-center py-2 text-gray-800 dark:text-honey-50 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 ${isOpen ? 'text-gray-900 dark:text-white' : ''}`}
                style={{ fontWeight: 700, letterSpacing: '0.2em', position: 'relative' }}
            >
                <span className="flex items-center relative">
                    <span>{item.label}</span>
                    <span className={`absolute left-0 -bottom-1 h-[2px] bg-honey-600 transition-all duration-300 ease-out ${isOpen ? 'w-full' : 'w-0 group-hover:w-full'}`}></span>
                </span>
            </a>

            {/* Dropdown Logic */}
            {hasChildren && (
                <div
                    ref={dropdownRef}
                    className={`absolute ${isMega ? '-left-20' : 'left-1/2 -translate-x-1/2'} top-[90%] pt-4 z-50 desktop-dropdown overflow-hidden`}
                    style={{ display: 'none' }}
                >
                    {isMega ? (
                        <div className="flex min-w-[500px]">
                            {/* Left Column: Categories */}
                            <div className="w-52 py-4 border-r border-gray-100 dark:border-white/10">
                                <ul>
                                    {item.children.map((child, idx) => (
                                        <li key={idx} className="stagger-item">
                                            <div
                                                onMouseEnter={() => setActiveCategory(child.category)}
                                                className={`flex items-center justify-between px-6 py-2.5 text-[12px] font-bold tracking-widest cursor-pointer transition-all duration-300 uppercase
                                                    ${activeCategory === child.category
                                                        ? 'text-honey-600 bg-honey-500/5 dark:bg-honey-500/10'
                                                        : 'text-gray-400 hover:text-honey-600 hover:bg-honey-500/5'}
                                                `}
                                            >
                                                <span>{child.category}</span>
                                                {activeCategory === child.category && (
                                                    <ChevronDown size={12} className="-rotate-90 text-honey-600" />
                                                )}
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </div>

                            {/* Right Column: Sub-items */}
                            <div className="flex-1 py-8 px-8 min-w-[280px]">
                                <ul className="grid grid-cols-1 gap-4">
                                    {activeSubItems.map((subItem, idx) => (
                                        <li key={idx} className="stagger-item">
                                            <a
                                                href={subItem.href}
                                                className="mega-menu-link text-[14px] font-medium text-gray-600 dark:text-gray-300 hover:text-honey-600 block transition-all"
                                            >
                                                {subItem.label}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    ) : (
                        /* Standard Dropdown */
                        <ul className="min-w-[220px] py-4">
                            {item.children.map((child, idx) => (
                                <li key={idx} className="stagger-item">
                                    <a
                                        href={child.href}
                                        className="block px-8 py-3 text-[13px] font-medium text-gray-600 dark:text-gray-300 hover:text-honey-600 hover:bg-honey-500/5 transition-all duration-200"
                                    >
                                        {child.label}
                                    </a>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            )}
        </li>
    );
};

import MobileCurrencySelector from '../shop/components/MobileCurrencySelector';
import MobileAppearanceToggle from '../components/MobileAppearanceToggle';

const MobileDrawer = ({ isOpen, items, onClose }) => {
    const drawerRef = useRef(null);
    const overlayRef = useRef(null);
    const contentRef = useRef(null);
    const itemsRef = useRef([]);

    // Prevent scrolling when menu is open
    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';

            // GSAP Entrance
            const tl = gsap.timeline();
            tl.to(drawerRef.current, { visibility: 'visible', duration: 0 });
            tl.to(overlayRef.current, { opacity: 1, duration: 0.4, ease: "power2.out" }, 0);
            tl.to(contentRef.current, { x: 0, duration: 0.6, ease: "expo.out" }, 0);
            tl.fromTo(itemsRef.current,
                { x: -20, opacity: 0 },
                { x: 0, opacity: 1, duration: 0.5, stagger: 0.08, ease: "power3.out" },
                "-=0.3"
            );
        } else {
            document.body.style.overflow = '';

            // GSAP Exit
            const tl = gsap.timeline({
                onComplete: () => {
                    if (drawerRef.current) drawerRef.current.style.visibility = 'hidden';
                }
            });
            tl.to(contentRef.current, { x: '-100%', duration: 0.4, ease: "power2.in" });
            tl.to(overlayRef.current, { opacity: 0, duration: 0.3, ease: "power2.in" }, "-=0.2");
        }
        return () => {
            document.body.style.overflow = '';
        };
    }, [isOpen]);

    return createPortal(
        <div ref={drawerRef} className={`mobile-drawer ${isOpen ? 'is-open' : ''}`} style={{ visibility: 'hidden' }}>
            <div ref={overlayRef} className="mobile-drawer-overlay" onClick={onClose} style={{ opacity: 0 }}></div>
            <div ref={contentRef} className="mobile-drawer-content" style={{ transform: 'translateX(-100%)' }}>
                <div className="flex justify-between items-center mb-10">
                    <span className="text-[11px] font-bold tracking-[0.25em] text-muted uppercase">Boutique Menu</span>
                    <button
                        onClick={onClose}
                        className="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"
                    >
                        <X size={20} strokeWidth={1.5} className="text-muted" />
                    </button>
                </div>

                <nav className="flex-1 overflow-y-auto overflow-x-hidden">
                    <ul className="flex flex-col">
                        {items.map((item, idx) => {
                            const isActive = window.location.pathname === item.href || (window.location.pathname === '/' && item.href === '/');
                            return (
                                <li
                                    key={idx}
                                    className={`mobile-nav-item ${isActive ? 'is-active' : ''}`}
                                    ref={el => itemsRef.current[idx] = el}
                                >
                                    <a href={item.href}>
                                        <span className={`mobile-nav-icon block ${isActive ? 'text-honey-600 opacity-100' : 'text-honey-600'}`}>
                                            {getIcon(item.label)}
                                        </span>
                                        <span className={isActive ? 'font-bold' : ''}>{item.label}</span>
                                    </a>
                                </li>
                            );
                        })}
                    </ul>
                </nav>

                {/* Mobile Drawer Footer: Settings */}
                <div className="mobile-drawer-footer">
                    <div className="mb-8">
                        <div className="flex items-center gap-2 mb-6">
                            <Globe size={14} className="text-honey-600" />
                            <span className="text-[10px] font-bold tracking-[0.15em] text-muted uppercase">Currency Selection</span>
                        </div>
                        <div className="pl-0">
                            <MobileCurrencySelector />
                        </div>
                    </div>

                    <div>
                        <div className="flex items-center gap-2 mb-6">
                            <Moon size={14} className="text-honey-600" />
                            <span className="text-[10px] font-bold tracking-[0.15em] text-muted uppercase">Appearance</span>
                        </div>
                        <div className="w-full">
                            <MobileAppearanceToggle />
                        </div>
                    </div>
                </div>
            </div>
        </div>,
        document.body
    );
};

const HeaderNav = () => {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const siteData = window.honeyscroopData || {};

    // Handle scroll detection for sticky header
    useEffect(() => {
        const header = document.getElementById('masthead');
        if (!header) return;

        // Initialize Spacer
        const spacer = document.createElement('div');
        spacer.className = 'header-spacer';
        if (header.parentNode) {
            header.parentNode.insertBefore(spacer, header.nextSibling); // Insert AFTER header (since header is fixed/top)
            // Actually, if header is fixed at top, spacer should push content down.
            // If inserted after, it pushes main content. 
        }

        const updateSpacer = () => {
            // Spacer should always match the full height of the header (expanded)
            // unless we want it to shrink. Expanding it once and keeping it helps prevent layout shift.
            spacer.style.height = `${header.offsetHeight}px`;
        };

        // Initial set
        updateSpacer();
        window.addEventListener('resize', updateSpacer);

        const handleScroll = () => {
            const currentScrollY = window.scrollY;

            // At the top: Show full header
            if (currentScrollY <= 50) {
                if (header.classList.contains('smart-compact')) {
                    header.classList.remove('smart-compact');
                }
            }
            // Scrolled: Show compact header
            else {
                if (!header.classList.contains('smart-compact')) {
                    header.classList.add('smart-compact');
                }
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });

        return () => {
            window.removeEventListener('scroll', handleScroll);
            window.removeEventListener('resize', updateSpacer);
            if (spacer.parentNode) spacer.parentNode.removeChild(spacer);
        };
    }, []);

    return (
        <CurrencyProvider>
            {/* Desktop Navigation */}
            <nav className="hidden md:flex justify-center ml-24">
                <ul className="flex items-center space-x-16 text-[12px] font-bold tracking-[0.2em] uppercase text-gray-800 dark:text-honey-50">
                    {menuItems.map((item, idx) => (
                        <NavItem key={idx} item={item} />
                    ))}
                    {/* Integrated Account Icon - Aligned with other items */}
                    <li className="relative flex items-center">
                        <a
                            href="/my-account/"
                            className="text-gray-500 dark:text-honey-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                            aria-label="Account"
                        >
                            <User size={20} strokeWidth={1.5} />
                        </a>
                    </li>
                </ul>
            </nav>

            {/* Mobile Header Row (Visible only on mobile) */}
            <div className="flex md:hidden items-center justify-between py-2 w-full">
                {/* Left: Menu */}
                <div className="flex items-center gap-4">
                    <button
                        onClick={() => setMobileMenuOpen(true)}
                        className="text-gray-500 dark:text-honey-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                        aria-label="Open Menu"
                    >
                        <Menu size={24} strokeWidth={1.5} />
                    </button>
                </div>

                {/* Right: Account & Cart */}
                <div className="flex items-center gap-5">
                    <a
                        href="/my-account/"
                        className="text-gray-500 dark:text-honey-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                        aria-label="Account"
                    >
                        <User size={22} strokeWidth={1.5} />
                    </a>

                    <a href="/cart" className="text-gray-500 dark:text-honey-300 hover:text-gray-900 dark:hover:text-white transition-colors relative">
                        <ShoppingBag size={22} strokeWidth={1.5} />
                    </a>
                </div>
            </div>

            {/* Mobile Drawer */}
            <MobileDrawer
                isOpen={mobileMenuOpen}
                items={menuItems}
                onClose={() => setMobileMenuOpen(false)}
            />

            {/* Inject Cart Widget into Header Tools */}
            {document.getElementById('cart-widget-root') && createPortal(
                <CartWidget />,
                document.getElementById('cart-widget-root')
            )}

            {/* Inject Currency Selector */}
            {document.getElementById('currency-selector-root') && createPortal(
                <div className="flex items-center gap-4">
                    <CurrencySelector />
                    <DarkModeToggle />
                </div>,
                document.getElementById('currency-selector-root')
            )}
        </CurrencyProvider>
    );
};

// Mount
const navContainer = document.getElementById('header-nav-root');
if (navContainer) {
    const root = createRoot(navContainer);
    root.render(<HeaderNav />);
}
