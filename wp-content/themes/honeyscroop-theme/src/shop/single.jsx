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

import StarRating from './components/StarRating';

const ProductTabs = ({ productId, description, avgRating = 0, reviewsCount = 0 }) => {
    const [activeTab, setActiveTab] = useState('description');
    const [reviews, setReviews] = useState([]);
    const [loadingReviews, setLoadingReviews] = useState(false);
    const [hasReviewed, setHasReviewed] = useState(false);
    const [showForm, setShowForm] = useState(false);

    const [formStatus, setFormStatus] = useState({ loading: false, success: false, error: null });
    const [formData, setFormData] = useState({ rating: 5, name: '', email: '', comment: '' });

    const fetchReviews = async () => {
        setLoadingReviews(true);
        try {
            const res = await fetch(`${window.honeyShopData.restUrl}honeyscroop/v1/product-reviews/${productId}`);
            const data = await res.json();
            setReviews(data.reviews || []);
            setHasReviewed(data.has_reviewed);
        } catch (err) {
            console.error(err);
        } finally {
            setLoadingReviews(false);
        }
    };

    useEffect(() => {
        if (activeTab === 'reviews') {
            fetchReviews();
        }
    }, [activeTab, productId]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setFormStatus({ loading: true, success: false, error: null });

        try {
            const res = await fetch(`${window.honeyShopData.restUrl}honeyscroop/v1/submit-review`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ...formData, product_id: productId })
            });

            const data = await res.json();

            if (data.success) {
                setFormStatus({ loading: false, success: true, error: null });
                setHasReviewed(true);
                setShowForm(false);
                fetchReviews(); // Refresh
                // Optional: Update parent avgRating if needed via callback
            } else {
                setFormStatus({ loading: false, success: false, error: data.message });
            }
        } catch (err) {
            setFormStatus({ loading: false, success: false, error: 'Connection failed.' });
        }
    };

    return (
        <div className="mt-16">
            <div className="flex border-b border-gray-200 dark:border-white/10 mb-8 overflow-x-auto">
                <button
                    onClick={() => setActiveTab('description')}
                    className={`pb-4 px-1 text-sm uppercase font-bold tracking-widest transition-all whitespace-nowrap relative ${activeTab === 'description'
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
                    className={`pb-4 px-1 ml-8 text-sm uppercase font-bold tracking-widest transition-all whitespace-nowrap relative ${activeTab === 'reviews'
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
                    className={`pb-4 px-1 ml-8 text-sm uppercase font-bold tracking-widest transition-all whitespace-nowrap relative ${activeTab === 'info'
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
                        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                            <div>
                                <h4 className="text-3xl font-serif text-gray-900 dark:text-white mb-2">{avgRating} <span className="text-xl text-gray-400">/ 5</span></h4>
                                <StarRating rating={avgRating} size={20} />
                                <p className="text-sm text-gray-500 mt-2">Based on {reviewsCount} reviews</p>
                            </div>
                            {!hasReviewed && !showForm && (
                                <button
                                    onClick={() => setShowForm(true)}
                                    className="px-8 py-3 bg-gray-900 dark:bg-honey-600 text-white text-sm font-bold uppercase tracking-widest rounded-xl hover:bg-honey-600 transition-all shadow-lg"
                                >
                                    Write a Review
                                </button>
                            )}
                        </div>

                        {showForm && (
                            <div className="bg-gray-50 dark:bg-white/5 p-8 rounded-[2rem] mb-12 border border-gray-100 dark:border-white/10">
                                <h5 className="text-xl font-bold text-gray-800 dark:text-white mb-6">Your Review</h5>
                                <form onSubmit={handleSubmit} className="space-y-6">
                                    <div className="flex items-center gap-4 mb-4">
                                        <span className="text-sm font-bold uppercase tracking-widest opacity-60">Your Rating:</span>
                                        <StarRating
                                            rating={formData.rating}
                                            interactive
                                            size={24}
                                            onRatingChange={(r) => setFormData({ ...formData, rating: r })}
                                        />
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label className="block text-xs font-bold uppercase tracking-widest mb-2 opacity-60">Name</label>
                                            <input
                                                type="text" required
                                                value={formData.name}
                                                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                                className="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-white/10 rounded-xl px-4 py-3"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-bold uppercase tracking-widest mb-2 opacity-60">Email</label>
                                            <input
                                                type="email" required
                                                value={formData.email}
                                                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                                className="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-white/10 rounded-xl px-4 py-3"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-bold uppercase tracking-widest mb-2 opacity-60">Your Thoughts</label>
                                        <textarea
                                            rows="4" required
                                            value={formData.comment}
                                            onChange={(e) => setFormData({ ...formData, comment: e.target.value })}
                                            className="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-white/10 rounded-xl px-4 py-3"
                                        ></textarea>
                                    </div>

                                    {formStatus.error && <p className="text-red-500 text-sm font-bold">{formStatus.error}</p>}

                                    <div className="flex justify-end gap-4">
                                        <button
                                            type="button"
                                            onClick={() => setShowForm(false)}
                                            className="px-6 py-3 text-gray-500 font-bold uppercase tracking-widest text-xs hover:text-gray-800"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={formStatus.loading}
                                            className="px-10 py-3 bg-honey-600 text-white font-bold uppercase tracking-widest text-xs rounded-xl shadow-xl hover:bg-honey-700 disabled:opacity-50"
                                        >
                                            {formStatus.loading ? 'Submitting...' : 'Post Review'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        )}

                        {loadingReviews ? (
                            <div className="flex justify-center py-12"><div className="w-8 h-8 border-2 border-honey-200 border-t-honey-600 rounded-full animate-spin"></div></div>
                        ) : reviews.length > 0 ? (
                            <div className="space-y-8">
                                {reviews.map(review => (
                                    <div key={review.id} className="pb-8 border-b border-gray-100 dark:border-white/5 last:border-0">
                                        <div className="flex justify-between items-start mb-4">
                                            <div>
                                                <h6 className="font-bold text-gray-900 dark:text-white">{review.author}</h6>
                                                <span className="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{review.date}</span>
                                            </div>
                                            <StarRating rating={review.rating} size={14} />
                                        </div>
                                        <p className="text-gray-600 dark:text-gray-300 italic">"{review.content}"</p>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-gray-500 italic text-center py-12 border-2 border-dashed border-gray-100 dark:border-white/5 rounded-3xl">No reviews yet. Be the first to share your experience!</p>
                        )}
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

                        <div className="flex items-center justify-between mb-8 pb-8 border-b border-gray-100 dark:border-white/10">
                            <div className="flex items-center gap-4">
                                <div className="text-4xl font-bold text-honey-600 dark:text-honey-400 transition-colors">
                                    {formatPrice(price / 100)}
                                </div>
                                {!isOutOfStock && (
                                    <span className="text-[10px] text-green-600 bg-green-50 dark:bg-green-900/20 px-3 py-1.5 font-bold uppercase tracking-wider rounded-full border border-green-100 dark:border-green-900/30">In Stock</span>
                                )}
                            </div>

                            <div className="flex flex-col items-end gap-1">
                                <StarRating rating={details.average_rating || 0} size={16} />
                                <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{details.review_count || 0} Reviews</span>
                            </div>
                        </div>

                        <div className="prose prose-lg text-gray-600 dark:text-gray-300 mb-8 max-w-none line-clamp-3" dangerouslySetInnerHTML={{ __html: product.excerpt?.rendered || product.content.rendered.substring(0, 150) + "..." }} />

                        {/* Add to Cart Section */}
                        <div className="py-8 mb-8">
                            {!isOutOfStock ? (
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <div className="flex items-center border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 rounded-xl overflow-hidden h-14">
                                        <button
                                            className="px-5 h-full bg-transparent hover:bg-gray-100 dark:hover:bg-white/10 transition text-gray-400 hover:text-gray-700 dark:hover:text-white"
                                            onClick={() => setQty(q => Math.max(1, q - 1))}
                                        >-</button>
                                        <div className="w-12 h-full flex items-center justify-center font-bold text-gray-900 dark:text-white border-x border-gray-100 dark:border-white/10">{qty}</div>
                                        <button
                                            className="px-5 h-full bg-transparent hover:bg-gray-100 dark:hover:bg-white/10 transition text-gray-400 hover:text-gray-700 dark:hover:text-white"
                                            onClick={() => setQty(q => q + 1)}
                                        >+</button>
                                    </div>

                                    <button
                                        onClick={handleAddToCart}
                                        className="flex-1 h-14 bg-gray-900 dark:bg-honey-600 text-white font-bold uppercase tracking-[0.2em] text-xs hover:bg-honey-600 dark:hover:bg-honey-500 transition-all shadow-xl shadow-gray-900/10 dark:shadow-none active:scale-95 rounded-xl"
                                    >
                                        Add to curation
                                    </button>
                                </div>
                            ) : (
                                <div className="p-5 bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/20 rounded-xl text-center font-bold text-sm tracking-wide">
                                    Currently Out of Stock
                                </div>
                            )}
                        </div>

                        <div className="flex items-center gap-6 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">
                            <span className="flex items-center gap-2"><span className="w-1.5 h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></span> SKU: {sku || 'N/A'}</span>
                            <span className="flex items-center gap-2"><span className="w-1.5 h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></span> Category: <a href="#" className="text-honey-600 hover:underline">Honey</a></span>
                        </div>

                        {/* Added Notification */}
                        {showAdded && (
                            <div className="mt-8 p-5 bg-green-500 text-white rounded-2xl animate-fade-in-up flex items-center justify-between shadow-lg shadow-green-500/20">
                                <span className="font-bold text-sm flex items-center gap-3 tracking-wide">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Added to your curation!
                                </span>
                                <a href="/cart" className="text-xs font-black uppercase tracking-widest bg-white/20 px-4 py-2 rounded-lg hover:bg-white/30 transition-colors">Checkout</a>
                            </div>
                        )}
                    </div>

                    {/* Tabs Section */}
                    <ProductTabs
                        productId={product.id}
                        description={product.content.rendered}
                        avgRating={details.average_rating || 0}
                        reviewsCount={details.review_count || 0}
                    />

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
