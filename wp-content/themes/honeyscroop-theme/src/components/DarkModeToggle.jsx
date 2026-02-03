import React, { useEffect, useState, useRef } from 'react';
import { Sun, Moon } from 'lucide-react';
import gsap from 'gsap';

const DarkModeToggle = () => {
    const [isDark, setIsDark] = useState(false);
    const sunRef = useRef(null);
    const moonRef = useRef(null);
    const [hasMounted, setHasMounted] = useState(false);

    useEffect(() => {
        const isDarkMode = document.documentElement.classList.contains('dark');
        setIsDark(isDarkMode);
        setHasMounted(true);

        // Listen for system changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = (e) => {
            if (!localStorage.getItem('honeyscroop-theme')) {
                setIsDark(e.matches);
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        };

        mediaQuery.addEventListener('change', handleChange);
        return () => mediaQuery.removeEventListener('change', handleChange);
    }, []);

    const toggleDarkMode = () => {
        const newDark = !isDark;
        setIsDark(newDark);

        if (newDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('honeyscroop-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('honeyscroop-theme', 'light');
        }

        // Premium GSAP Animation
        const ctx = gsap.context(() => {
            if (newDark) {
                // Sun out, Moon in
                gsap.to(sunRef.current, {
                    rotate: 180,
                    scale: 0,
                    opacity: 0,
                    duration: 0.5,
                    ease: "power2.inOut"
                });
                gsap.fromTo(moonRef.current,
                    { rotate: -180, scale: 0, opacity: 0 },
                    { rotate: 0, scale: 1, opacity: 1, duration: 0.5, ease: "back.out(1.7)" }
                );
            } else {
                // Moon out, Sun in
                gsap.to(moonRef.current, {
                    rotate: 180,
                    scale: 0,
                    opacity: 0,
                    duration: 0.5,
                    ease: "power2.inOut"
                });
                gsap.fromTo(sunRef.current,
                    { rotate: -180, scale: 0, opacity: 0 },
                    { rotate: 0, scale: 1, opacity: 1, duration: 0.5, ease: "back.out(1.7)" }
                );
            }
        });

        return () => ctx.revert();
    };

    if (!hasMounted) return <div className="w-8 h-8" />;

    return (
        <button
            onClick={toggleDarkMode}
            className="relative w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 border border-gray-200 dark:bg-surface-glass dark:hover:bg-surface dark:border-white/10 backdrop-blur-md shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group"
            aria-label="Toggle Dark Mode"
        >
            <div ref={sunRef} className={`absolute ${isDark ? 'opacity-0 scale-0' : 'opacity-100 scale-100'}`}>
                <Sun size={20} className="text-amber-500 group-hover:rotate-12 transition-transform duration-300" strokeWidth={1.8} />
            </div>
            <div ref={moonRef} className={`absolute ${isDark ? 'opacity-100 scale-100' : 'opacity-0 scale-0'}`}>
                <Moon size={20} className="text-honey-300 group-hover:-rotate-12 transition-transform duration-300" strokeWidth={1.8} />
            </div>
        </button>
    );
};

export default DarkModeToggle;
