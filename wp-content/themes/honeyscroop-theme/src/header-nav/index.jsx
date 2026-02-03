import React, { useState, useRef, useEffect } from 'react';
import { createPortal } from 'react-dom';
import { createRoot } from 'react-dom/client';
import { Search, User, ShoppingBag, ChevronDown, Globe, Menu, X } from 'lucide-react';
import CartWidget from '../shop/components/CartWidget';
import { CurrencyProvider } from '../shop/context/CurrencyContext';
import CurrencySelector from '../shop/components/CurrencySelector';
import DarkModeToggle from '../components/DarkModeToggle';

const menuItems = window.honeyscroopHeaderData?.primaryMenu || [];

const NavItem = ({ item }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [activeCategory, setActiveCategory] = useState(item.children?.[0]?.category || null);
    const timeoutRef = useRef(null);

    const handleMouseEnter = () => {
        if (timeoutRef.current) clearTimeout(timeoutRef.current);
        setIsOpen(true);
    };

    const handleMouseLeave = () => {
        timeoutRef.current = setTimeout(() => setIsOpen(false), 200);
    };

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
                style={{ fontWeight: 700, letterSpacing: '0.2em' }}
            >
                <span className="flex items-center relative">
                    <span>{item.label}</span>
                    {/* Underline animation */}
                    <span className={`absolute left-0 -bottom-1 h-[1.5px] bg-gray-400 transition-all duration-300 ease-out ${isOpen ? 'w-full' : 'w-0 group-hover:w-full'}`}></span>
                </span>
            </a>

            {/* Dropdown Logic */}
            {hasChildren && (
                <div
                    className={`absolute ${isMega ? '-left-10' : 'left-1/2 -translate-x-1/2'} top-[95%] pt-3 z-50 transition-all duration-200 ease-out origin-top-left ${isOpen
                        ? 'opacity-100 scale-100 translate-y-0 visible'
                        : 'opacity-0 scale-95 translate-y-2 invisible'
                        }`}
                >
                    {isMega ? (
                        <div
                            className="shadow-xl rounded-sm flex min-w-[400px] bg-white/90 dark:bg-dark-surface/90 backdrop-blur-md border border-gray-100 dark:border-white/5"
                        >
                            {/* Left Column: Categories */}
                            <div className="w-48 py-4 border-r border-gray-100/50 dark:border-white/10">
                                <ul>
                                    {item.children.map((child, idx) => (
                                        <li key={idx}>
                                            <div
                                                onMouseEnter={() => setActiveCategory(child.category)}
                                                className={`flex items-center justify-between px-6 py-2.5 text-[13px] font-medium cursor-pointer transition-colors
                                                    ${activeCategory === child.category ? 'text-amber-600 bg-amber-50/40 dark:bg-amber-500/10' : 'text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400'}
                                                `}
                                            >
                                                <span>{child.category}</span>
                                                {activeCategory === child.category && (
                                                    <ChevronDown size={14} className="-rotate-90" />
                                                )}
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </div>

                            {/* Right Column: Sub-items */}
                            <div className="w-56 py-6 px-6">
                                <ul className="space-y-3">
                                    {activeSubItems.map((subItem, idx) => (
                                        <li key={idx}>
                                            <a
                                                href={subItem.href}
                                                className="block text-[13px] text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
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
                        <ul
                            className="shadow-xl rounded-sm min-w-[200px] py-4 bg-white/90 dark:bg-dark-surface/90 backdrop-blur-md border border-gray-100 dark:border-white/5"
                        >
                            {item.children.map((child, idx) => (
                                <li key={idx}>
                                    <a
                                        href={child.href}
                                        className="block px-8 py-2.5 text-[13px] text-gray-700 dark:text-gray-300 font-normal hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50/30 dark:hover:bg-amber-500/10 transition-colors duration-150"
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

const MobileDrawer = ({ isOpen, items, onClose }) => {
    // Prevent scrolling when menu is open
    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
        return () => {
            document.body.style.overflow = '';
        };
    }, [isOpen]);

    return createPortal(
        <div className={`mobile-drawer ${isOpen ? 'is-open' : ''}`}>
            <div className="mobile-drawer-overlay" onClick={onClose}></div>
            <div className="mobile-drawer-content">
                <div className="flex justify-between items-center mb-8">
                    <span className="text-[12px] font-bold tracking-widest text-gray-400 uppercase">Menu</span>
                    <button onClick={onClose} className="text-gray-500 hover:text-gray-900">
                        <X size={24} strokeWidth={1.5} />
                    </button>
                </div>
                <nav className="flex-1 overflow-y-auto">
                    <ul className="flex flex-col">
                        {/* Removed duplicate "Home" link */}
                        {items.map((item, idx) => (
                            <li key={idx} className="mobile-nav-item">
                                <a href={item.href} className="block">{item.label}</a>
                            </li>
                        ))}
                    </ul>
                </nav>
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
                </ul>
            </nav>

            {/* Mobile Header Row (Visible only on mobile) */}
            <div className="flex md:hidden items-center justify-between py-2">
                <button
                    onClick={() => setMobileMenuOpen(true)}
                    className="text-gray-500 hover:text-gray-900"
                    aria-label="Open Menu"
                >
                    <Menu size={24} strokeWidth={1.5} />
                </button>

                <a href="/" className="block">
                    <img
                        src="/wp-content/uploads/2026/01/honeyscoop-logo.png"
                        alt="Honeyscoop"
                        className="h-8 w-auto"
                    />
                </a>

                <a href="#" className="text-gray-500 hover:text-gray-900 relative">
                    <ShoppingBag size={24} strokeWidth={1.5} />
                </a>
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
