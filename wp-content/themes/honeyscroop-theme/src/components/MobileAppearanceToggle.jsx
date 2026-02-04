import React, { useEffect, useState, useRef } from 'react';
import { Sun, Moon } from 'lucide-react';
import gsap from 'gsap';

const MobileAppearanceToggle = () => {
    const [isDark, setIsDark] = useState(false);
    const [hasMounted, setHasMounted] = useState(false);
    const sliderRef = useRef(null);

    useEffect(() => {
        const isDarkMode = document.documentElement.classList.contains('dark');
        setIsDark(isDarkMode);
        setHasMounted(true);

        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = (e) => {
            if (!localStorage.getItem('honeyscroop-theme')) {
                setIsDark(e.matches);
                updateTheme(e.matches);
            }
        };

        mediaQuery.addEventListener('change', handleChange);
        return () => mediaQuery.removeEventListener('change', handleChange);
    }, []);

    const updateTheme = (dark) => {
        if (dark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('honeyscroop-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('honeyscroop-theme', 'light');
        }
    };

    const handleToggle = (dark) => {
        if (isDark === dark) return;
        setIsDark(dark);
        updateTheme(dark);

        // Animate slider background
        gsap.to(sliderRef.current, {
            x: dark ? '100%' : '0%',
            duration: 0.4,
            ease: "expo.out"
        });
    };

    useEffect(() => {
        if (hasMounted) {
            gsap.set(sliderRef.current, { x: isDark ? '100%' : '0%' });
        }
    }, [hasMounted, isDark]);

    if (!hasMounted) return <div className="h-12 w-full bg-gray-100 dark:bg-white/5 rounded-2xl animate-pulse" />;

    return (
        <div className="relative w-full h-14 bg-gray-100 dark:bg-white/5 rounded-2xl p-1.5 flex items-center border border-gray-200 dark:border-white/10">
            {/* Animated Slider Background */}
            <div
                ref={sliderRef}
                className="absolute top-1.5 bottom-1.5 left-1.5 w-[calc(50%-6px)] bg-white dark:bg-honey-600 rounded-xl shadow-md z-0"
            />

            {/* Light Option */}
            <button
                onClick={() => handleToggle(false)}
                className={`relative z-10 flex-1 h-full flex items-center justify-center gap-2 transition-colors duration-300 ${!isDark ? 'text-gray-900' : 'text-gray-400 hover:text-gray-200'}`}
            >
                <Sun size={18} strokeWidth={2} />
                <span className="text-sm font-bold tracking-wide">LIGHT</span>
            </button>

            {/* Dark Option */}
            <button
                onClick={() => handleToggle(true)}
                className={`relative z-10 flex-1 h-full flex items-center justify-center gap-2 transition-colors duration-300 ${isDark ? 'text-white' : 'text-gray-400 hover:text-gray-900'}`}
            >
                <Moon size={18} strokeWidth={2} />
                <span className="text-sm font-bold tracking-wide">DARK</span>
            </button>
        </div>
    );
};

export default MobileAppearanceToggle;
