import React from 'react';
import { useCurrency } from '../context/CurrencyContext';

const ProductCard = ({ product }) => {
    // Check if product_details exists, if not fall back (should be there from REST)
    const price = product.product_details?.price || 0;
    const imageUrl = product._embedded?.['wp:featuredmedia']?.[0]?.source_url || 'https://via.placeholder.com/300x300?text=No+Image';
    const isOutOfStock = product.product_details?.availability === 'out_of_stock';

    const { formatPrice } = useCurrency();
    // Helper to divide by 100 since WP stores in cents
    const displayPrice = formatPrice(price / 100);

    return (
        <div className="group relative bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-honey-100">
            <div className="aspect-square w-full overflow-hidden bg-gray-100">
                <img
                    src={imageUrl}
                    alt={product.title.rendered}
                    className="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                />
                {isOutOfStock && (
                    <div className="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                        OUT OF STOCK
                    </div>
                )}
            </div>
            <div className="p-4">
                <h3 className="text-lg font-serif font-bold text-honey-900">
                    <a href={product.link}>
                        <span aria-hidden="true" className="absolute inset-0" />
                        <span dangerouslySetInnerHTML={{ __html: product.title.rendered }} />
                    </a>
                </h3>
                <p className="mt-1 text-sm text-gray-500 line-clamp-2" dangerouslySetInnerHTML={{ __html: product.excerpt.rendered }} />
                <div className="mt-4 flex justify-between items-center">
                    <p className="text-lg font-medium text-honey-700">
                        {displayPrice}
                    </p>
                    {isOutOfStock ? (
                        <span className="text-gray-400 text-sm">Unavailable</span>
                    ) : (
                        <span className="text-honey-600 font-bold text-sm group-hover:underline">View Details &rarr;</span>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ProductCard;
