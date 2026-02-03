import React from 'react';
import { createRoot } from 'react-dom/client';
import CategoryCard from './CategoryCard';

const ProductGrid = () => {
    // Data encoded from PHP or static for now (static per request context)
    // In a real dynamic scenario, we'd use window.honeyscroopData or fetch WP API
    const categories = [
        {
            id: 1,
            title: 'Raw Honey',
            link: '/product-category/raw-honey/',
            imageSrc: '/wp-content/uploads/2026/01/DSC_9644.webp'
        },
        {
            id: 2,
            title: 'Honey Products',
            link: '/product-category/honey-products/',
            imageSrc: '/wp-content/uploads/2026/01/IMG-20241121-WA0041.webp'
        },
        {
            id: 3,
            title: 'Infused Honey',
            link: '/product-category/infused-honey/',
            imageSrc: '/wp-content/uploads/2026/01/DSC_9622.webp'
        },
        {
            id: 4,
            title: 'Other Products',
            link: '/product-category/other-products/',
            imageSrc: '/wp-content/uploads/2026/01/IMG-20241121-WA0042.webp'
        }
    ];

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {categories.map((cat, index) => (
                <CategoryCard
                    key={cat.id}
                    title={cat.title}
                    link={cat.link}
                    imageSrc={cat.imageSrc}
                    isLeftColumn={index % 2 === 0}
                />
            ))}
        </div>
    );
};

const rootElement = document.getElementById('product-grid-root');
if (rootElement) {
    createRoot(rootElement).render(
        <React.StrictMode>
            <ProductGrid />
        </React.StrictMode>
    );
}
