import React, { useEffect, useState } from 'react';
import useCartStore from '../store';
import { ShoppingBag } from 'lucide-react';

const CartWidget = () => {
    const totalItems = useCartStore(state => state.getTotalItems());
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        setMounted(true);
    }, []);

    if (!mounted) return null;

    return (
        <a href="/cart" className="icon-btn relative group" aria-label="Cart">
            <ShoppingBag size={20} strokeWidth={1.5} className="text-gray-900 dark:text-honey-300 group-hover:text-honey-600 dark:group-hover:text-honey-200 transition-colors" />
            {totalItems > 0 && (
                <span className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-honey-600 text-[10px] font-bold text-white shadow-sm animate-fade-in">
                    {totalItems}
                </span>
            )}
        </a>
    );
};

export default CartWidget;
