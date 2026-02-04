import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import useCartStore from './store';
import { CurrencyProvider, useCurrency } from './context/CurrencyContext';

// --- Sub-components ---

const Breadcrumbs = ({ title }) => (
    <nav className="text-sm text-gray-500 mb-6 font-medium tracking-wide">
        <a href="/" className="hover:text-honey-600 transition-colors">Home</a>
        <span className="mx-2 text-gray-300">/</span>
        <a href="/shop" className="hover:text-honey-600 transition-colors">Shop</a>
        <span className="mx-2 text-gray-300">/</span>
        <span className="text-gray-900 dark:text-gray-100">{title}</span>
    </nav>
);

const ProductBadges = ({ isBestseller, isFeatured, isNew }) => (
    <div className="absolute top-4 left-4 z-10 flex flex-col gap-2">
        {isBestseller && (
            <span className="bg-honey-600 text-white text-[10px] uppercase font-bold tracking-[0.15em] px-3 py-1.5 rounded-sm shadow-md">
                Best Seller
            </span>
        )}
        {isFeatured && (
            <span className="bg-gray-900 dark:bg-white text-white dark:text-black text-[10px] uppercase font-bold tracking-[0.15em] px-3 py-1.5 rounded-sm shadow-md">
                Featured
            </span>
        )}
        {isNew && (
            <span className="bg-green-600 text-white text-[10px] uppercase font-bold tracking-[0.15em] px-3 py-1.5 rounded-sm shadow-md">
                New
            </span>
        )}
    </div>
);

const ProductTabs = ({ description, reviewsCount = 0 }) => {
    const [activeTab, setActiveTab] = useState('description');

    return (
        <div className="mt-16">
            <div className="flex border-b border-gray-200 dark:border-white/10 mb-8">
                <button
                    onClick={() => setActiveTab('description')}
                    className={`pb-4 px-1 text-sm uppercase font-bold tracking-widest transition-all relative ${activeTab === 'description'
                        ? 'text-gray-900 dark:text-white'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'
                        }`}
                >
                    Description
                    {activeTab === 'description' && (
                        <span className="absolute bottom-0 left-0 w-full h-[2px] bg-honey-600"></span>
                    )}
                </button>
                <button
                    onClick={() => setActiveTab('reviews')}
                    className={`pb-4 px-1 ml-8 text-sm uppercase font-bold tracking-widest transition-all relative ${activeTab === 'reviews'
                        ? 'text-gray-900 dark:text-white'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'
                        }`}
                >
                    Reviews ({reviewsCount})
                    {activeTab === 'reviews' && (
                        <span className="absolute bottom-0 left-0 w-full h-[2px] bg-honey-600"></span>
                    )}
                </button>
                <button
                    onClick={() => setActiveTab('info')}
                    className={`pb-4 px-1 ml-8 text-sm uppercase font-bold tracking-widest transition-all relative ${activeTab === 'info'
                        ? 'text-gray-900 dark:text-white'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'
                        }`}
                >
                    Additional Info
                    {activeTab === 'info' && (
                        <span className="absolute bottom-0 left-0 w-full h-[2px] bg-honey-600"></span>
                    )}
                </button>
            </div>

            <div className="prose prose-lg text-gray-600 dark:text-gray-300 max-w-none transition-all duration-300">
                {activeTab === 'description' && (
                    <div className="animate-fade-in" dangerouslySetInnerHTML={{ __html: description }} />
                )}
                {activeTab === 'reviews' && (
                    <div className="animate-fade-in">
                        <p className="text-gray-500 italic">No reviews yet. Be the first to review this product!</p>
                        <button className="mt-4 text-honey-600 text-sm font-bold uppercase tracking-widest border-b border-honey-600 pb-0.5 hover:text-honey-700">Write a Review</button>
                    </div>
                )}
                {activeTab === 'info' && (
                    <div className="animate-fade-in">
                        <table className="w-full text-sm text-left">
                            <tbody>
                                <tr className="border-b border-gray-100 dark:border-white/5">
                                    <th className="py-2 text-gray-900 dark:text-white font-medium w-1/4">Weight</th>
                                    <td className="py-2 text-gray-600 dark:text-gray-400">0.5 kg</td>
                                </tr>
                                <tr className="border-b border-gray-100 dark:border-white/5">
                                    <th className="py-2 text-gray-900 dark:text-white font-medium">Dimensions</th>
                                    <td className="py-2 text-gray-600 dark:text-gray-400">10 × 10 × 15 cm</td>
                                </tr>
                                <tr>
                                    <th className="py-2 text-gray-900 dark:text-white font-medium">Origin</th>
                                    <td className="py-2 text-gray-600 dark:text-gray-400">Zimbabwe</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

const RelatedProducts = ({ productIds, currentProductId }) => {
    const [related, setRelated] = useState([]);
    const { collectionUrl } = window.productData || {};
    const { formatPrice } = useCurrency();

    useEffect(() => {
        if (!productIds || productIds.length === 0 || !collectionUrl) return;

        const fetchRelated = async () => {
            try {
                // Fetch specific IDs using the collection endpoint
                const idsString = productIds.join(',');
                const res = await fetch(`${collectionUrl}?include=${idsString}&_embed`);
                const data = await res.json();

                // Ensure data is array
                if (Array.isArray(data)) {
                    setRelated(data);
                } else {
                    console.warn("Related products API returned non-array:", data);
                    setRelated([]);
                }
            } catch (err) {
                console.error("Failed to load related products", err);
                setRelated([]);
            }
        };

        fetchRelated();
    }, [productIds, collectionUrl]);

    if (!Array.isArray(related) || related.length === 0) return null;

    return (
        <div className="mt-20 pt-12 border-t border-gray-100 dark:border-white/5">
            <h3 className="text-2xl font-serif font-bold text-gray-900 dark:text-white mb-8">You May Also Like</h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {related.map(prod => {
                    const price = prod.product_details?.price || 0;
                    const img = prod._embedded?.['wp:featuredmedia']?.[0]?.source_url || 'https://via.placeholder.com/400';

                    return (
                        <a href={prod.link} key={prod.id} className="group block">
                            <div className="relative aspect-[4/5] overflow-hidden rounded-lg bg-gray-100 dark:bg-white/5 mb-4">
                                <img
                                    src={img}
                                    alt={prod.title.rendered}
                                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                />
                                {prod.product_details?.is_bestseller && (
                                    <span className="absolute top-2 left-2 bg-honey-600 text-white text-[9px] uppercase font-bold px-2 py-1 rounded-sm">Best Seller</span>
                                )}
                            </div>
                            <h4 className="font-serif text-lg text-gray-900 dark:text-white group-hover:text-honey-600 transition-colors" dangerouslySetInnerHTML={{ __html: prod.title.rendered }} />
                            <div className="text-honey-600 dark:text-honey-400 font-medium mt-1">{formatPrice(price / 100)}</div>
                        </a>
                    );
                })}
            </div>
        </div>
    );
};


// --- Main Page Component ---

const ShopSingle = () => {
    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [qty, setQty] = useState(1);
    const { productId, restUrl, orderUrl, nonce } = window.productData || {};
    const addToCart = useCartStore((state) => state.addToCart);
    const [showAdded, setShowAdded] = useState(false);
    const { formatPrice } = useCurrency();

    useEffect(() => {
        if (!restUrl) return;
        fetch(`${restUrl}?_embed`)
            .then(res => res.json())
            .then(data => {
                setProduct(data);
                setLoading(false);
            })
            .catch(err => console.error(err));
    }, [restUrl]);

    if (loading) return (
        <div className="min-h-screen flex items-center justify-center">
            <div className="animate-pulse flex flex-col items-center">
                <div className="w-12 h-12 border-4 border-honey-200 border-t-honey-600 rounded-full animate-spin mb-4"></div>
                <div className="text-gray-400 font-medium tracking-widest text-sm uppercase">Loading Product...</div>
            </div>
        </div>
    );

    if (!product) return <div className="container mx-auto py-20 text-center text-gray-500">Product not found.</div>;

    const details = product.product_details || {};
    const price = details.price || 0;
    const imageUrl = product._embedded?.['wp:featuredmedia']?.[0]?.source_url || 'https://via.placeholder.com/600x600?text=No+Image';
    const isOutOfStock = details.availability === 'out_of_stock';
    const sku = details.sku;

    // Derived from API (or mocked in backend)
    const isBestseller = details.is_bestseller;
    const isFeatured = details.is_featured;
    const relatedIds = details.related_ids || [];

    const handleAddToCart = () => {
        if (isOutOfStock) return;

        addToCart({
            id: product.id,
            title: product.title.rendered,
            price: price,
            image: imageUrl,
            sku: sku
        }, qty);

        setShowAdded(true);
        setTimeout(() => setShowAdded(false), 3000);
    };

    return (
        <div className="container mx-auto px-4 py-12 md:py-20 animate-fade-in">
            {/* Breadcrumbs */}
            <Breadcrumbs title={product.title.rendered} />

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-24">
                {/* Left: Gallery (Sticky) */}
                <div className="relative">
                    <div className="lg:sticky lg:top-24">
                        <div className="relative rounded-sm overflow-hidden shadow-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/5 group">
                            <ProductBadges isBestseller={isBestseller} isFeatured={isFeatured} isNew={false} />

                            <img
                                src={imageUrl}
                                alt={product.title.rendered}
                                className="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105"
                            />

                            {/* Zoom Icon Overlay */}
                            <div className="absolute bottom-4 right-4 bg-white/90 dark:bg-black/60 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg className="w-5 h-5 text-gray-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right: Details (Glassmorphism Card) */}
                <div>
                    <div className="bg-white/90 dark:bg-white/5 backdrop-blur-xl border border-gray-100 dark:border-white/5 rounded-2xl p-8 lg:p-12 shadow-xl">
                        <h1 className="text-4xl lg:text-5xl font-serif font-bold text-gray-900 dark:text-white mb-4 leading-tight" dangerouslySetInnerHTML={{ __html: product.title.rendered }} />

                        <div className="flex items-center gap-4 mb-8">
                            <div className="text-3xl font-medium text-honey-600 dark:text-honey-400">
                                {formatPrice(price / 100)}
                            </div>
                            {!isOutOfStock && (
                                <span className="text-green-600 bg-green-50 dark:bg-green-900/20 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full">In Stock</span>
                            )}
                        </div>

                        <div className="prose prose-lg text-gray-600 dark:text-gray-300 mb-8 max-w-none" dangerouslySetInnerHTML={{ __html: product.excerpt?.rendered || product.content.rendered.substring(0, 150) + "..." }} />

                        {/* Add to Cart Section */}
                        <div className="py-8 border-y border-gray-100 dark:border-white/10 mb-8">
                            {!isOutOfStock ? (
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <div className="flex items-center border border-gray-300 dark:border-white/20 rounded-lg overflow-hidden h-14">
                                        <button
                                            className="px-4 h-full bg-transparent hover:bg-gray-100 dark:hover:bg-white/10 transition text-gray-500"
                                            onClick={() => setQty(q => Math.max(1, q - 1))}
                                        >-</button>
                                        <div className="w-12 h-full flex items-center justify-center font-bold text-gray-900 dark:text-white border-x border-gray-200 dark:border-white/10">{qty}</div>
                                        <button
                                            className="px-4 h-full bg-transparent hover:bg-gray-100 dark:hover:bg-white/10 transition text-gray-500"
                                            onClick={() => setQty(q => q + 1)}
                                        >+</button>
                                    </div>

                                    <button
                                        onClick={handleAddToCart}
                                        className="flex-1 h-14 bg-gray-900 dark:bg-honey-600 text-white font-bold uppercase tracking-widest text-sm hover:bg-honey-600 dark:hover:bg-honey-500 transition-colors shadow-lg shadow-gray-900/10 dark:shadow-none rounded-lg"
                                    >
                                        Add to cart
                                    </button>
                                </div>
                            ) : (
                                <div className="p-4 bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/20 rounded-lg text-center font-bold">
                                    Currently Out of Stock
                                </div>
                            )}
                        </div>

                        <div className="flex items-center gap-6 text-xs font-bold uppercase tracking-widest text-gray-400">
                            <span>SKU: {sku || 'N/A'}</span>
                            <span>Category: <a href="#" className="hover:text-honey-600 transition-colors">Honey</a></span>
                        </div>

                        {/* Added Notification */}
                        {showAdded && (
                            <div className="mt-6 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-900/30 rounded-lg animate-fade-in-up flex items-center justify-between">
                                <span className="font-medium flex items-center gap-2">
                                    <span className="text-xl">✨</span> Added to cart!
                                </span>
                                <a href="/cart" className="text-sm font-bold underline hover:text-green-800 dark:hover:text-white">View Cart</a>
                            </div>
                        )}
                    </div>

                    {/* Tabs Section */}
                    <ProductTabs description={product.content.rendered} />

                </div>
            </div>

            {/* Related Products */}
            <RelatedProducts productIds={relatedIds} currentProductId={product.id} />
        </div>
    );
};

const root = document.getElementById('shop-single-root');
if (root) {
    createRoot(root).render(
        <CurrencyProvider>
            <ShopSingle />
        </CurrencyProvider>
    );
}
