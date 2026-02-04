import React from 'react';
import { useCurrency } from '../context/CurrencyContext';

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

const MobileCurrencySelector = () => {
    const { currency, setCurrency, activeCurrencies } = useCurrency();

    if (!activeCurrencies || activeCurrencies.length <= 1) return null;

    return (
        <div className="flex flex-wrap gap-2">
            {activeCurrencies.map(code => {
                const isSelected = currency === code;
                return (
                    <button
                        key={code}
                        onClick={() => setCurrency(code)}
                        className={`
                            flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all duration-300
                            ${isSelected
                                ? 'bg-honey-600 border-honey-600 text-white shadow-lg shadow-honey-600/20'
                                : 'bg-white dark:bg-white/5 border-gray-100 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-honey-300'
                            }
                        `}
                    >
                        <span className="text-xl leading-none">{FLAGS[code] || '🌐'}</span>
                        <span className={`text-sm font-bold tracking-wide ${isSelected ? 'text-white' : ''}`}>
                            {code}
                        </span>
                        {isSelected && (
                            <div className="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        )}
                    </button>
                );
            })}
        </div>
    );
};

export default MobileCurrencySelector;
