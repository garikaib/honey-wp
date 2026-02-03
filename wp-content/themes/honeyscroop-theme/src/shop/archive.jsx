import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import ProductCard from './components/ProductCard';

const ShopArchive = () => {
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);
    const { restUrl, categories, currentTerm } = window.shopData || {};
    const [activeCategory, setActiveCategory] = useState(currentTerm || 0);

    useEffect(() => {
        const fetchProducts = async () => {
            setLoading(true);
            let url = `${restUrl}?_embed&per_page=20`;

            if (activeCategory !== 0) {
                url += `&product_category=${activeCategory}`;
            }

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Failed to fetch');
                const data = await response.json();
                setProducts(data);
            } catch (error) {
                console.error('Error fetching products:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchProducts();
    }, [activeCategory, restUrl]);

    return (
        <div className="container mx-auto px-4 py-12">
            <header className="text-center mb-12">
                <h1 className="text-5xl font-serif font-bold text-honey-900 mb-4">Our Collection</h1>
                <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover our range of premium raw honeys, infused varieties, and pantry staples.
                </p>
            </header>

            {/* Category Filter */}
            {categories && categories.length > 0 && (
                <div className="flex flex-wrap justify-center mb-12 gap-4">
                    <button
                        onClick={() => setActiveCategory(0)}
                        className={`px-6 py-2 rounded-full border transition-all ${activeCategory === 0
                            ? 'bg-honey-500 text-white border-honey-500 shadow-lg'
                            : 'bg-white text-honey-900 border-honey-200 hover:border-honey-500'
                            }`}
                    >
                        All Products
                    </button>
                    {categories.map(cat => (
                        <button
                            key={cat.term_id}
                            onClick={() => setActiveCategory(cat.term_id)}
                            className={`px-6 py-2 rounded-full border transition-all ${activeCategory === cat.term_id
                                ? 'bg-honey-500 text-white border-honey-500 shadow-lg'
                                : 'bg-white text-honey-900 border-honey-200 hover:border-honey-500'
                                }`}
                        >
                            {cat.name}
                        </button>
                    ))}
                </div>
            )}

            {loading ? (
                <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8 animate-pulse">
                    {[1, 2, 3, 4].map(i => (
                        <div key={i} className="h-80 bg-gray-200 rounded-lg"></div>
                    ))}
                </div>
            ) : (
                <>
                    {products.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            {products.map((product) => (
                                <ProductCard key={product.id} product={product} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20">
                            <h3 className="text-2xl font-serif text-gray-400">No products found in this category.</h3>
                        </div>
                    )}
                </>
            )}
        </div>
    );
};

import { CurrencyProvider } from './context/CurrencyContext';

// ... existing code ...

const root = document.getElementById('shop-archive-root');
if (root) {
    createRoot(root).render(
        <CurrencyProvider>
            <ShopArchive />
        </CurrencyProvider>
    );
}
