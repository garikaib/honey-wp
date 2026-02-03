import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';

const CurrencyContext = createContext();
const CURRENCY_STORAGE_KEY = 'honeyscroop_selected_currency';
const CURRENCY_CHANGE_EVENT = 'honeyscroop:currency_changed';

export const useCurrency = () => {
    return useContext(CurrencyContext);
};

export const CurrencyProvider = ({ children }) => {
    // Load initial currency from localStorage or default to USD
    const [currency, setCurrencyState] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem(CURRENCY_STORAGE_KEY) || 'USD';
        }
        return 'USD';
    });
    const [rates, setRates] = useState({ USD: 1 });
    const [activeCurrencies, setActiveCurrencies] = useState(['USD']);

    // Load settings from WP
    useEffect(() => {
        const shopData = window.honeyShopData || {};
        const settings = shopData.currencySettings || {};

        if (settings.rates) {
            setRates({ USD: 1, ...settings.rates });
        }
        if (settings.activeCurrencies) {
            setActiveCurrencies(settings.activeCurrencies);
        }
    }, []);

    // Listen for currency changes from other React roots
    useEffect(() => {
        const handleCurrencyChange = (event) => {
            const newCurrency = event.detail?.currency;
            if (newCurrency && newCurrency !== currency) {
                setCurrencyState(newCurrency);
            }
        };

        window.addEventListener(CURRENCY_CHANGE_EVENT, handleCurrencyChange);
        return () => window.removeEventListener(CURRENCY_CHANGE_EVENT, handleCurrencyChange);
    }, [currency]);

    // Custom setCurrency that broadcasts to all React roots
    const setCurrency = useCallback((newCurrency) => {
        setCurrencyState(newCurrency);

        // Persist to localStorage
        localStorage.setItem(CURRENCY_STORAGE_KEY, newCurrency);

        // Broadcast to other React roots via custom event
        window.dispatchEvent(new CustomEvent(CURRENCY_CHANGE_EVENT, {
            detail: { currency: newCurrency }
        }));
    }, []);

    const formatPrice = useCallback((priceInUsageCurrency) => {
        const rate = rates[currency] || 1;
        const converted = priceInUsageCurrency * rate;

        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2
        }).format(converted);
    }, [currency, rates]);

    const getPriceValue = useCallback((basePrice) => {
        const rate = rates[currency] || 1;
        return basePrice * rate;
    }, [currency, rates]);

    const value = {
        currency,
        setCurrency,
        rates,
        activeCurrencies,
        formatPrice,
        getPriceValue
    };

    return (
        <CurrencyContext.Provider value={value}>
            {children}
        </CurrencyContext.Provider>
    );
};
