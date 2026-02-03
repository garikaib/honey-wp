import React, { useState, useEffect } from 'react';
import { RefreshCw, Save, Check, AlertCircle } from 'lucide-react';

const availableCurrencies = [
    { code: 'USD', name: 'US Dollar', symbol: '$' },
    { code: 'ZWG', name: 'Zimbabwe Gold', symbol: 'ZiG' },
    { code: 'ZAR', name: 'South African Rand', symbol: 'R' },
    { code: 'GBP', name: 'British Pound', symbol: '£' },
    { code: 'BWP', name: 'Botswana Pula', symbol: 'P' },
    { code: 'AUD', name: 'Australian Dollar', symbol: 'A$' },
    { code: 'NZD', name: 'New Zealand Dollar', symbol: 'NZ$' }
];

const CurrencyPage = () => {
    const [loading, setLoading] = useState(false);
    const [rates, setRates] = useState({});
    const [settings, setSettings] = useState({
        activeCurrencies: ['USD', 'ZWG', 'ZAR'],
        baseCurrency: 'USD',
        autoUpdate: true,
        rates: {
            ZWG: 15.0, // Manual Fallback
            ZAR: 19.5,
            GBP: 0.79,
            BWP: 13.5,
            AUD: 1.52,
            NZD: 1.63
        }
    });

    // Load saved settings from WP Options API on mount
    useEffect(() => {
        const fetchSettings = async () => {
            try {
                const response = await fetch(`${window.honeyAdmin?.apiUrl || '/wp-json/honeyscroop/v1/'}options`, {
                    headers: {
                        'X-WP-Nonce': window.honeyAdmin?.nonce || ''
                    }
                });
                const data = await response.json();

                if (data.currency && Object.keys(data.currency).length > 0) {
                    setSettings(prev => ({
                        ...prev,
                        activeCurrencies: data.currency.activeCurrencies || prev.activeCurrencies,
                        baseCurrency: data.currency.baseCurrency || prev.baseCurrency,
                        autoUpdate: data.currency.autoUpdate ?? prev.autoUpdate,
                        rates: data.currency.rates || prev.rates
                    }));
                }
            } catch (err) {
                console.error('Failed to load currency settings:', err);
            }
        };

        fetchSettings();
    }, []);

    const toggleCurrency = (code) => {
        if (code === 'USD') return; // Cannot disable base
        setSettings(prev => ({
            ...prev,
            activeCurrencies: prev.activeCurrencies.includes(code)
                ? prev.activeCurrencies.filter(c => c !== code)
                : [...prev.activeCurrencies, code]
        }));
    };

    const updateRate = (code, value) => {
        setSettings(prev => ({
            ...prev,
            rates: { ...prev.rates, [code]: value }
        }));
    };

    const fetchLiveRates = async () => {
        setLoading(true);
        let newRates = { ...settings.rates }; // Start with current rates as fallback
        try {
            // Note: In a real scenario, this fetch should happen via a WP AJAX proxy to avoid CORS and hide keys if any
            // For now simulating success based on user provided endpoints

            // 1. Fetch ZWG Rate
            try {
                const res = await fetch('https://api.clientemails.xyz/api/rates/fx-rates');
                const data = await res.json();
                if (data.success && data.rates && data.rates.ZiG_BMBuy) {
                    newRates.ZWG = parseFloat(data.rates.ZiG_BMBuy);
                }
            } catch (e) {
                console.error("Failed to fetch ZWG", e);
            }

            // 2. Fetch Other Rates
            try {
                const res = await fetch('https://api.clientemails.xyz/api/rates/oe-rates/raw');
                const data = await res.json();
                if (data.success && data.rates && Array.isArray(data.rates.rates)) {
                    // rates.rates is an array of objects: [{ "AED": 3.67 }, { "ZAR": 18.5 }, ...]
                    // Convert to a map for easier lookup
                    const remoteRatesMap = {};
                    data.rates.rates.forEach(rateObj => {
                        const key = Object.keys(rateObj)[0];
                        if (key) remoteRatesMap[key] = rateObj[key];
                    });

                    // Update supported currencies (excluding ZWG and USD which is base)
                    ['ZAR', 'GBP', 'BWP', 'AUD', 'NZD'].forEach(code => {
                        if (remoteRatesMap[code]) {
                            newRates[code] = parseFloat(remoteRatesMap[code]);
                        }
                    });
                }
            } catch (e) {
                console.error("Failed to fetch OE rates", e);
            }

            setSettings(prev => ({
                ...prev,
                rates: newRates
            }));

        } catch (err) {
            console.error("Failed to fetch rates", err);
            alert("Failed to fetch live rates. Please check console.");
        } finally {
            setLoading(false);
        }
    };

    const saveSettings = async () => {
        setLoading(true);
        try {
            const response = await fetch(`${window.honeyAdmin?.apiUrl || '/wp-json/honeyscroop/v1/'}options`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.honeyAdmin?.nonce || ''
                },
                body: JSON.stringify({
                    group: 'currency',
                    data: settings
                })
            });

            const result = await response.json();
            if (result.success) {
                alert('Settings saved successfully!');
            } else {
                throw new Error(result.message || 'Save failed');
            }
        } catch (err) {
            console.error('Failed to save settings:', err);
            alert('Failed to save settings. Check console for details.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="space-y-8 animate-fade-in-up">
            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h2 className="text-xl font-bold text-gray-800">Currency Configuration</h2>
                        <p className="text-sm text-gray-500">Manage active currencies and exchange rates.</p>
                    </div>
                    <div className="flex gap-3">
                        <button
                            onClick={fetchLiveRates}
                            disabled={loading}
                            className="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
                        >
                            <RefreshCw size={16} className={loading ? "animate-spin" : ""} />
                            Sync Rates
                        </button>
                        <button
                            onClick={saveSettings}
                            disabled={loading}
                            className="flex items-center gap-2 px-6 py-2 bg-honey-600 text-white rounded-lg hover:bg-honey-700 transition-colors shadow-sm text-sm font-medium"
                        >
                            <Save size={16} />
                            Save Changes
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    {/* Active Currencies */}
                    <div>
                        <h3 className="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Active Currencies</h3>
                        <div className="space-y-3">
                            {availableCurrencies.map(currency => (
                                <div key={currency.code} className="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:border-honey-200 transition-colors">
                                    <div className="flex items-center gap-3">
                                        <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm ${settings.activeCurrencies.includes(currency.code) ? 'bg-honey-100 text-honey-700' : 'bg-gray-100 text-gray-400'}`}>
                                            {currency.symbol}
                                        </div>
                                        <div>
                                            <p className="font-semibold text-gray-800">{currency.name}</p>
                                            <p className="text-xs text-gray-400">{currency.code}</p>
                                        </div>
                                    </div>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            className="sr-only peer"
                                            checked={settings.activeCurrencies.includes(currency.code)}
                                            onChange={() => toggleCurrency(currency.code)}
                                            disabled={currency.code === 'USD'}
                                        />
                                        <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-honey-600"></div>
                                    </label>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Exchange Rates */}
                    <div>
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-sm font-bold uppercase tracking-wider text-gray-400">Exchange Rates (vs USD)</h3>
                            <span className="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded border border-blue-100">Base: 1 USD</span>
                        </div>

                        <div className="space-y-4">
                            {settings.activeCurrencies.map(code => {
                                if (code === 'USD') return null;
                                return (
                                    <div key={code} className="flex items-center gap-4">
                                        <div className="w-16 font-bold text-gray-700">{code}</div>
                                        <div className="flex-1 relative">
                                            <input
                                                type="number"
                                                step="0.01"
                                                value={settings.rates[code] || ''}
                                                onChange={(e) => updateRate(code, e.target.value)}
                                                className="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-honey-500 focus:border-transparent transition-all"
                                                placeholder="0.00"
                                            />
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        <div className="mt-8 p-4 bg-amber-50 rounded-lg border border-amber-100 flex gap-3">
                            <AlertCircle size={20} className="text-amber-600 flex-shrink-0 mt-0.5" />
                            <div className="text-sm text-amber-800">
                                <p className="font-semibold mb-1">Rate Sources</p>
                                <ul className="list-disc pl-4 space-y-1 opacity-90">
                                    <li><strong>ZWG</strong>: Synced from Zimpricecheck (Buy Rate).</li>
                                    <li><strong>Others</strong>: Synced from OpenExchangeRates.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CurrencyPage;
