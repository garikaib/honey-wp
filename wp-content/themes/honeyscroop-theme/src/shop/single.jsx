import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import useCartStore from './store';
import { CurrencyProvider, useCurrency } from './context/CurrencyContext';

const ShopSingle = () => {
    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [qty, setQty] = useState(1);
    const { productId, restUrl, orderUrl, nonce } = window.productData || {};
    const addToCart = useCartStore((state) => state.addToCart);
    const [showAdded, setShowAdded] = useState(false);
    const [showFloating, setShowFloating] = useState(false);
    const mainBtnRef = React.useRef(null);
    const { formatPrice } = useCurrency();

    useEffect(() => {
        const handleScroll = () => {
            if (mainBtnRef.current) {
                const rect = mainBtnRef.current.getBoundingClientRect();
                setShowFloating(rect.bottom < 0);
            }
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

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

    if (loading) return <div className="container mx-auto py-20 text-center">Loading...</div>;
    if (!product) return <div className="container mx-auto py-20 text-center">Product not found.</div>;

    const price = product.product_details?.price || 0;
    const imageUrl = product._embedded?.['wp:featuredmedia']?.[0]?.source_url || 'https://via.placeholder.com/600x600?text=No+Image';
    const isOutOfStock = product.product_details?.availability === 'out_of_stock';
    const sku = product.product_details?.sku;

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
        <>
            <div className="container mx-auto px-4 py-12 md:py-20">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20">
                    {/* Image Side */}
                    <div className="relative">
                        <div className="rounded-xl overflow-hidden shadow-xl bg-gray-50 border border-honey-100">
                            <img src={imageUrl} alt={product.title.rendered} className="w-full h-full object-cover" />
                        </div>
                    </div>

                    {/* Details Side */}
                    <div>
                        <h1 className="text-4xl md:text-5xl font-serif font-bold text-honey-900 mb-4" dangerouslySetInnerHTML={{ __html: product.title.rendered }} />

                        <div className="text-2xl font-medium text-honey-600 mb-6">
                            {formatPrice(price / 100)}
                        </div>

                        <div className="prose prose-lg text-gray-600 mb-8" dangerouslySetInnerHTML={{ __html: product.content.rendered }} />

                        <div className="mb-8">
                            <span className="text-sm font-bold text-gray-400 uppercase tracking-widest">SKU: {sku}</span>
                        </div>

                        {!isOutOfStock ? (
                            <div className="flex flex-col sm:flex-row gap-4 items-center mb-6">
                                {/* Qty Selector */}
                                <div className="flex items-center border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                                    <button
                                        className="px-5 py-3 bg-gray-50 hover:bg-gray-100 transition text-gray-600"
                                        onClick={() => setQty(q => Math.max(1, q - 1))}
                                    >-</button>
                                    <div className="px-5 py-3 min-w-[3.5rem] text-center font-bold bg-white text-lg">{qty}</div>
                                    <button
                                        className="px-5 py-3 bg-gray-50 hover:bg-gray-100 transition text-gray-600"
                                        onClick={() => setQty(q => q + 1)}
                                    >+</button>
                                </div>
                                <div className="text-sm font-medium text-gray-500">
                                    Quantity
                                </div>
                            </div>
                        ) : (
                            <div className="inline-block px-6 py-3 bg-red-100 text-red-600 font-bold rounded-lg border border-red-200">
                                Currently Out of Stock
                            </div>
                        )}

                        {/* Added Notification */}
                        {showAdded && (
                            <div className="mt-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg animate-fade-in-up flex items-center gap-2">
                                <span className="text-xl">🍯</span>
                                <div>
                                    <strong>Sweet choice!</strong> Added to your cart.
                                    <a href="/cart" className="ml-2 underline font-bold hover:text-green-800">View Cart &rarr;</a>
                                </div>
                            </div>
                        )}

                        {/* Action Buttons */}
                        {!isOutOfStock && (
                            <div className="flex flex-col gap-3 mt-8">
                                <button
                                    onClick={handleAddToCart}
                                    className="group relative w-full flex items-center justify-center gap-3 px-8 py-4 bg-honey-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-honey-200 hover:shadow-xl hover:shadow-honey-300 hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                >
                                    <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer" />
                                    <span className="relative z-10">Add to Cart</span>
                                    <svg className="w-5 h-5 relative z-10 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </button>

                                <a
                                    href={`https://wa.me/${window.honeyShopData?.settings?.whatsappNumber || '263772123456'}?text=${encodeURIComponent(`Hi, I'm interested in ${product.title.rendered}.`)}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="w-full flex items-center justify-center gap-3 px-8 py-4 bg-white text-green-600 border-2 border-green-100 font-bold text-lg rounded-2xl shadow-sm hover:shadow-md hover:border-green-200 hover:-translate-y-1 transition-all duration-300"
                                >
                                    <span>Order via WhatsApp</span>
                                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-6.912-11.967-1.396-26.657 12.353-29.419C28.53.826 39.543 11.231 39.293 24c-.266 13.6-11.272 24.238-25.044 24.238-4.22 0-8.232-1.09-11.777-3.003L0 48.057zM20.246 3.737C8.988 3.737-.123 12.863-.123 24.088c0 3.633.958 7.086 2.628 10.07l-2.07 7.556 7.77-2.036c2.916 1.586 6.273 2.508 9.877 2.508 11.196 0 20.354-9.123 20.354-20.384 0-11.238-9.192-20.36-20.19-20.065zm12.33 27.63c-1.35.666-2.522 1.258-3.414 1.4-1.385.22-4.145.456-9.67-1.745-6.96-2.77-11.666-9.916-11.944-10.3-.29-.4-2.846-3.793-2.846-7.24 0-3.447 1.832-5.15 2.484-5.84.664-.7 1.436-.88 1.914-.88.47 0 .942.016 1.354.032.44.02 1.05.096 1.65.114.7.02 1.15-.36 1.815-1.92.518-1.218 1.522-3.66.69-4.52-.83-.86-1.52-1.12-2.146.12-.44.88-1.737 2.064-2.164 2.56-1.36 1.59-1.3 3.69-.17 6.07 2.76 5.86 7.6 9.61 11.63 11.18 2.65 1.03 4.67 1.01 6.24.49 1.34-.44 3.79-2.04 4.58-4.04.59-1.5.06-2.56-.25-2.9-.3-.34-.72-.51-1.39-.83-.67-.32-3.95-1.91-4.55-2.13-.59-.22-1.03-.33-1.46.33-.43.66-1.68 2.1-2.05 2.54-.38.42-.76.48-1.43.16z" /></svg>
                                </a>
                            </div>
                        )}

                        {/* Additional Info / Accordions could go here */}
                    </div>
                </div>
            </div>
        </>
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
