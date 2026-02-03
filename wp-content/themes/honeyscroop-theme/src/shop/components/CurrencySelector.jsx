import React, { useState } from 'react';
import { useCurrency } from '../context/CurrencyContext';
import { ChevronDown, Globe } from 'lucide-react';

const FLAGS = {
    'USD': '🇺🇸',
    'ZWG': '🇿🇼',
    'ZAR': '🇿🇦',
    'GBP': '🇬🇧',
    'BWP': '🇧🇼',
    'AUD': '🇦🇺',
    'NZD': '🇳🇿',
    'EUR': '🇪🇺'
};

const CurrencySelector = () => {
    const { currency, setCurrency, activeCurrencies } = useCurrency();
    const [isOpen, setIsOpen] = useState(false);

    if (!activeCurrencies || activeCurrencies.length <= 1) return null;

    return (
        <div className="relative z-50">
            <button
                onClick={() => setIsOpen(!isOpen)}
                className={`
                    flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all duration-200
                    ${isOpen
                        ? 'bg-black/5 border-black/10 shadow-inner'
                        : 'bg-white border-gray-200 hover:border-honey-300 hover:shadow-sm'
                    }
                `}
                aria-label="Select Currency"
            >
                <span className="text-lg leading-none" role="img" aria-label={currency}>
                    {FLAGS[currency] || <Globe size={14} className="text-gray-400" />}
                </span>
                <span className="font-bold text-xs text-gray-700 tracking-wide">{currency}</span>
                <ChevronDown size={10} className={`text-gray-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`} />
            </button>

            {isOpen && (
                <>
                    <div className="fixed inset-0 z-40" onClick={() => setIsOpen(false)}></div>
                    <div className="absolute top-full right-0 mt-2 w-40 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 animate-fade-in-up">
                        <div className="py-1">
                            {activeCurrencies.map(code => (
                                <button
                                    key={code}
                                    onClick={() => {
                                        setCurrency(code);
                                        setIsOpen(false);
                                    }}
                                    className={`
                                        w-full text-left px-4 py-3 text-xs font-medium flex items-center gap-3 transition-colors
                                        ${currency === code ? 'bg-honey-50 text-honey-700' : 'text-gray-600 hover:bg-gray-50'}
                                    `}
                                >
                                    <span className="text-lg leading-none">{FLAGS[code] || '🌐'}</span>
                                    <span className="flex-1">{code}</span>
                                    {currency === code && (
                                        <div className="w-1.5 h-1.5 rounded-full bg-honey-500"></div>
                                    )}
                                </button>
                            ))}
                        </div>
                    </div>
                </>
            )}
        </div>
    );
};

export default CurrencySelector;
