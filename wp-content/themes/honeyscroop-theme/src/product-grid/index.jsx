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
            link: '/shop/raw-honey',
            imageSrc: '/wp-content/uploads/2026/01/DSC_9644-scaled.jpg'
        },
        {
            id: 2,
            title: 'Infused Honey',
            link: '/shop/infused-honey',
            imageSrc: '/wp-content/uploads/2026/01/DSC_9622-scaled.jpg'
        },
        {
            id: 3,
            title: 'Other Products',
            link: '/shop/other-products',
            imageSrc: '/wp-content/uploads/2026/01/IMG-20241121-WA0042.jpg'
        },
        {
            id: 4,
            title: 'Honey Products',
            link: '/shop/honey-products',
            imageSrc: '/wp-content/uploads/2026/01/IMG-20241121-WA0041.jpg'
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
