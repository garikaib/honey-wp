import React, { useState, useEffect, useRef } from 'react';
import { RefreshCw, Save, Check, AlertCircle, Coins, Info } from 'lucide-react';
import gsap from 'gsap';

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
    const [contentLoading, setContentLoading] = useState(true);
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

    const pageRef = useRef(null);

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
            } finally {
                setTimeout(() => setContentLoading(false), 200);
            }
        };

        fetchSettings();
    }, []);

    // Entry Animation
    useEffect(() => {
        if (!contentLoading && pageRef.current) {
            gsap.fromTo(pageRef.current,
                { opacity: 0, y: 15 },
                { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }
            );
        }
    }, [contentLoading]);

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

    if (contentLoading) return null; // Or a spinner if preferred, but existing wrapper has spinner?

    return (
        <div ref={pageRef} className="space-y-8 opacity-0">
            <div className="bg-white rounded-2xl shadow-xl shadow-amber-900/5 border border-amber-100/50 overflow-hidden">
                <div className="px-8 py-6 bg-gradient-to-r from-amber-50 to-white border-b border-amber-100 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-emerald-500/20">
                            <Coins className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 className="text-2xl font-bold text-gray-800">Currency Settings</h2>
                            <p className="text-sm text-gray-500">Manage rates, active currencies, and auto-updates.</p>
                        </div>
                    </div>
                </div>

                <div className="p-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {/* Active Currencies */}
                    <div>
                        <h3 className="text-xs font-bold uppercase tracking-widest text-amber-800 mb-6 flex items-center gap-2">
                            <span>Active Currencies</span>
                            <div className="h-px bg-amber-100 flex-1"></div>
                        </h3>
                        <div className="space-y-3">
                            {availableCurrencies.map(currency => (
                                <div key={currency.code} className="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-amber-400 hover:border-2 hover:shadow-lg hover:shadow-amber-500/10 transition-all bg-gray-50/50 group">
                                    <div className="flex items-center gap-4">
                                        <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors ${settings.activeCurrencies.includes(currency.code) ? 'bg-amber-100 text-amber-800' : 'bg-white text-gray-300'}`}>
                                            {currency.symbol}
                                        </div>
                                        <div>
                                            <p className={`font-bold transition-colors ${settings.activeCurrencies.includes(currency.code) ? 'text-gray-900' : 'text-gray-400'}`}>{currency.name}</p>
                                            <p className="text-xs font-mono text-gray-400">{currency.code}</p>
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
                                        <div className="w-12 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Exchange Rates */}
                    <div>
                        <div className="flex items-center justify-between mb-6">
                            <h3 className="text-xs font-bold uppercase tracking-widest text-amber-800">Exchange Rates</h3>
                            <span className="text-[10px] font-bold px-3 py-1 bg-blue-50 text-blue-600 rounded-full border border-blue-100 tracking-wide uppercase">Base: 1 USD</span>
                        </div>

                        <div className="bg-gray-50 rounded-2xl border border-gray-100 p-6 space-y-4">
                            {settings.activeCurrencies.map(code => {
                                if (code === 'USD') return null;
                                return (
                                    <div key={code} className="flex items-center gap-4">
                                        <div className="w-16 font-bold text-gray-500 text-sm">{code}</div>
                                        <div className="flex-1 relative group">
                                            <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">rate</span>
                                            <input
                                                type="number"
                                                step="0.01"
                                                value={settings.rates[code] || ''}
                                                onChange={(e) => updateRate(code, e.target.value)}
                                                className="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-400 transition-all font-mono font-medium text-gray-800"
                                                placeholder="0.00"
                                            />
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        <div className="mt-8 flex gap-4 pt-8 border-t border-gray-100">
                            <button
                                onClick={fetchLiveRates}
                                disabled={loading}
                                className="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-gray-100 text-gray-600 font-bold rounded-xl hover:border-gray-300 hover:text-gray-800 transition-all text-sm disabled:opacity-50"
                            >
                                <RefreshCw size={18} className={loading ? "animate-spin" : ""} />
                                {loading ? 'Syncing...' : 'Sync Live Rates'}
                            </button>
                            <button
                                onClick={saveSettings}
                                disabled={loading}
                                className="flex-[2] flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition-all shadow-lg hover:shadow-xl text-sm disabled:opacity-50"
                            >
                                <Save size={18} />
                                {loading ? 'Saving...' : 'Save Configuration'}
                            </button>
                        </div>

                        <div className="mt-6 flex flex-col md:flex-row gap-3 opacity-60 hover:opacity-100 transition-opacity">
                            <Info size={16} className="text-gray-400 flex-shrink-0 mt-0.5" />
                            <p className="text-xs text-gray-500 leading-relaxed">
                                <strong>Rate Sources:</strong> ZWG rates are sourced from Zimpricecheck (Black Market - Buy Rate). Major currencies come from OpenExchangeRates. Rates are cached for performance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CurrencyPage;
