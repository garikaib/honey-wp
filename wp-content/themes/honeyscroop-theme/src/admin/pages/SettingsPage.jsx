import React, { useState, useEffect, useRef } from 'react';
import {
    Lock,
    Unlock,
    Save,
    Settings,
    Send,
    CheckCircle2,
    AlertCircle,
    Loader2,
    Mail,
    Server,
    ShieldCheck,
    Info,
    Phone,
    MapPin,
    Globe,
    Facebook,
    Instagram,
    Twitter
} from 'lucide-react';
import gsap from 'gsap';

const SettingsPage = () => {
    const [smtp, setSmtp] = useState({
        enabled: false,
        host: '',
        port: 587,
        username: '',
        password: '',
        encryption: 'tls',
        from_name: '',
        from_email: ''
    });
    const [settings, setSettings] = useState({
        whatsappNumber: '',
        phoneNumber: '',
        address: '',
        social: {
            facebook: '',
            instagram: '',
            tiktok: '',
            x: ''
        }
    });
    const [isLocked, setIsLocked] = useState(true);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [testing, setTesting] = useState(false);
    const [debugLog, setDebugLog] = useState(null);
    const [toast, setToast] = useState(null); // { type: 'success' | 'error' | 'info', message: '' }

    const toastRef = useRef(null);
    const formRef = useRef(null);

    useEffect(() => {
        if (!window.honeyAdmin) return;

        fetch(window.honeyAdmin.apiUrl + 'options', {
            headers: { 'X-WP-Nonce': window.honeyAdmin.nonce }
        })
            .then(res => res.json())
            .then(data => {
                if (data.smtp) {
                    setSmtp(prev => ({ ...prev, ...data.smtp }));
                }
                if (data.settings) {
                    setSettings(prev => ({ ...prev, ...data.settings }));
                }
                setLoading(false);
            })
            .catch(err => {
                console.error(err);
                setLoading(false);
                showToast('error', 'Failed to load settings.');
            });
    }, []);

    // Entry animation when loading finishes
    useEffect(() => {
        if (!loading && formRef.current) {
            gsap.fromTo(formRef.current,
                { opacity: 0, y: 20 },
                { opacity: 1, y: 0, duration: 0.6, ease: 'power2.out', delay: 0.1 }
            );
        }
    }, [loading]);

    // Toast animation
    useEffect(() => {
        if (toast && toastRef.current) {
            gsap.fromTo(toastRef.current,
                { opacity: 0, y: 50, scale: 0.9 },
                { opacity: 1, y: 0, scale: 1, duration: 0.4, ease: 'back.out(1.7)' }
            );

            const timer = setTimeout(() => {
                if (toastRef.current) {
                    gsap.to(toastRef.current, {
                        opacity: 0,
                        y: 20,
                        duration: 0.3,
                        onComplete: () => setToast(null)
                    });
                }
            }, 4000);

            return () => clearTimeout(timer);
        }
    }, [toast]);

    const showToast = (type, message) => {
        setToast({ type, message });
    };

    const handleChange = (e) => {
        if (isLocked) return;
        const { name, value, type, checked } = e.target;
        setSmtp(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value
        }));
    };

    const handleSettingsChange = (e) => {
        if (isLocked) return;
        const { name, value } = e.target;
        if (name.startsWith('social_')) {
            const socialKey = name.replace('social_', '');
            setSettings(prev => ({
                ...prev,
                social: { ...prev.social, [socialKey]: value }
            }));
        } else {
            setSettings(prev => ({ ...prev, [name]: value }));
        }
    };
    const handleSave = async () => {
        if (isLocked) {
            showToast('info', 'Please unlock settings to make changes.');
            return;
        }

        setSaving(true);
        try {
            // Save SMTP
            await fetch(window.honeyAdmin.apiUrl + 'options', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.honeyAdmin.nonce
                },
                body: JSON.stringify({
                    group: 'smtp',
                    data: smtp
                })
            });

            // Save General Settings
            const res = await fetch(window.honeyAdmin.apiUrl + 'options', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.honeyAdmin.nonce
                },
                body: JSON.stringify({
                    group: 'settings',
                    data: settings
                })
            });

            const data = await res.json();
            if (data.success) {
                showToast('success', 'Settings saved successfully!');
            } else {
                showToast('error', 'Error saving settings.');
            }
        } catch (err) {
            console.error(err);
            showToast('error', 'Network error while saving.');
        } finally {
            setSaving(false);
        }
    };

    const handleTestEmail = async () => {
        setTesting(true);
        try {
            // Test current state (save first if unlocked)
            if (!isLocked) {
                await fetch(window.honeyAdmin.apiUrl + 'options', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.honeyAdmin.nonce },
                    body: JSON.stringify({ group: 'smtp', data: smtp })
                });
            }

            const res = await fetch(window.honeyAdmin.apiUrl + 'test-smtp', {
                method: 'POST',
                headers: { 'X-WP-Nonce': window.honeyAdmin.nonce }
            });
            const data = await res.json();

            if (data.success) {
                showToast('success', data.message);
            } else {
                showToast('error', data.message || 'Test email failed.');
            }

            if (data.debug) {
                console.log('SMTP Debug:', data.debug);
                setDebugLog(data.debug);
            }
        } catch (err) {
            showToast('error', 'Failed to trigger test email.');
        } finally {
            setTesting(false);
        }
    };

    if (loading) return (
        <div className="flex flex-col items-center justify-center p-20 space-y-4">
            <Loader2 className="w-10 h-10 text-amber-500 animate-spin" />
            <p className="text-gray-400 font-medium">Preparing the Hive...</p>
        </div>
    );

    return (
        <div ref={formRef} className="space-y-8 pb-12 opacity-0">

            <div className="bg-white rounded-2xl shadow-xl shadow-amber-900/5 border border-amber-100/50 overflow-hidden">
                {/* Header Section */}
                <div className="px-8 py-6 bg-gradient-to-r from-amber-50 to-white border-b border-amber-100 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-amber-500 rounded-xl shadow-lg shadow-amber-500/20">
                            <Settings className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 className="text-2xl font-bold text-gray-800">Shop Settings</h2>
                            <p className="text-sm text-gray-500">Manage contact info, social links, and email systems.</p>
                        </div>
                    </div>

                    <button
                        onClick={() => setIsLocked(!isLocked)}
                        className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all ${isLocked
                            ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            : 'bg-amber-100 text-amber-700 hover:bg-amber-200'
                            }`}
                    >
                        {isLocked ? <Lock className="w-4 h-4" /> : <Unlock className="w-4 h-4" />}
                        {isLocked ? 'Unlock to Edit' : 'Lock Settings'}
                    </button>
                </div>

                {/* Form Body */}
                <div className="p-8 space-y-12">
                    {/* General Settings Section */}
                    <div className={`transition-all duration-300 ${isLocked ? 'grayscale-[0.8] opacity-60 pointer-events-none' : ''}`}>
                        <div className="flex items-center gap-2 text-amber-800 font-bold text-sm uppercase tracking-widest mb-6">
                            <Globe className="w-4 h-4" />
                            <span>Contact & Social</span>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {/* Contact Info */}
                            <div className="space-y-6">
                                <h4 className="font-bold text-gray-900 border-b border-gray-100 pb-2">Contact Details</h4>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">WhatsApp Number</label>
                                    <input
                                        type="text"
                                        name="whatsappNumber"
                                        value={settings.whatsappNumber}
                                        onChange={handleSettingsChange}
                                        placeholder="263771234567"
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                    <p className="text-xs text-gray-400 ml-1">Format: Country code without + (e.g., 263...)</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">Phone Number (Display)</label>
                                    <input
                                        type="text"
                                        name="phoneNumber"
                                        value={settings.phoneNumber}
                                        onChange={handleSettingsChange}
                                        placeholder="+263 77 123 4567"
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">Address</label>
                                    <textarea
                                        name="address"
                                        value={settings.address}
                                        onChange={handleSettingsChange}
                                        placeholder="123 Honey Lane..."
                                        rows="3"
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                            </div>

                            {/* Social Links */}
                            <div className="space-y-6">
                                <h4 className="font-bold text-gray-900 border-b border-gray-100 pb-2">Social Media Links</h4>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">Facebook URL</label>
                                    <input
                                        type="url"
                                        name="social_facebook"
                                        value={settings.social?.facebook || ''}
                                        onChange={handleSettingsChange}
                                        placeholder="https://facebook.com/..."
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">Instagram URL</label>
                                    <input
                                        type="url"
                                        name="social_instagram"
                                        value={settings.social?.instagram || ''}
                                        onChange={handleSettingsChange}
                                        placeholder="https://instagram.com/..."
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">TikTok URL</label>
                                    <input
                                        type="url"
                                        name="social_tiktok"
                                        value={settings.social?.tiktok || ''}
                                        onChange={handleSettingsChange}
                                        placeholder="https://tiktok.com/..."
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">X (Twitter) URL</label>
                                    <input
                                        type="url"
                                        name="social_x"
                                        value={settings.social?.x || ''}
                                        onChange={handleSettingsChange}
                                        placeholder="https://x.com/..."
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className={`grid grid-cols-1 md:grid-cols-2 gap-8 transition-all duration-300 ${isLocked ? 'grayscale-[0.8] opacity-60 pointer-events-none' : ''}`}>

                        {/* Server Config */}
                        <div className="space-y-6">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2 text-amber-800 font-bold text-sm uppercase tracking-widest">
                                    <Server className="w-4 h-4" />
                                    <span>Server Configuration</span>
                                </div>
                                <label className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="enabled"
                                        className="sr-only peer"
                                        checked={smtp.enabled}
                                        onChange={handleChange}
                                        disabled={isLocked}
                                    />
                                    <div className="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                    <span className="ml-2 text-xs font-bold text-gray-500">{smtp.enabled ? 'Active' : 'Off'}</span>
                                </label>
                            </div>

                            <div className="space-y-2">
                                <label className="block text-sm font-bold text-gray-700 ml-1">SMTP Host</label>
                                <input
                                    type="text"
                                    name="host"
                                    value={smtp.host}
                                    onChange={handleChange}
                                    placeholder="e.g., smtp-pulse.com"
                                    className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-gray-300"
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">Port</label>
                                    <input
                                        type="number"
                                        name="port"
                                        value={smtp.port}
                                        onChange={handleChange}
                                        placeholder="587"
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <label className="block text-sm font-bold text-gray-700 ml-1">Encryption</label>
                                    <select
                                        name="encryption"
                                        value={smtp.encryption}
                                        onChange={handleChange}
                                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all bg-white"
                                    >
                                        <option value="tls">TLS (Recommended)</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {/* Authentication */}
                        <div className="space-y-6">
                            <div className="flex items-center gap-2 text-amber-800 font-bold text-sm uppercase tracking-widest">
                                <ShieldCheck className="w-4 h-4" />
                                <span>Authentication</span>
                            </div>

                            <div className="space-y-2">
                                <label className="block text-sm font-bold text-gray-700 ml-1">Username</label>
                                <input
                                    type="text"
                                    name="username"
                                    value={smtp.username}
                                    onChange={handleChange}
                                    className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                />
                            </div>

                            <div className="space-y-2">
                                <label className="block text-sm font-bold text-gray-700 ml-1">Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    value={smtp.password}
                                    onChange={handleChange}
                                    placeholder="••••••••••••"
                                    className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                />
                            </div>
                        </div>

                        {/* Sender Info */}
                        <div className="md:col-span-2 pt-6 border-t border-gray-100 flex flex-col md:flex-row gap-8">
                            <div className="flex-1 space-y-2">
                                <label className="block text-sm font-bold text-gray-700 ml-1">Sender Display Name</label>
                                <input
                                    type="text"
                                    name="from_name"
                                    value={smtp.from_name}
                                    onChange={handleChange}
                                    placeholder="HoneyScoop"
                                    className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                />
                            </div>
                            <div className="flex-1 space-y-2">
                                <label className="block text-sm font-bold text-gray-700 ml-1">Sender Email Address</label>
                                <input
                                    type="email"
                                    name="from_email"
                                    value={smtp.from_email}
                                    onChange={handleChange}
                                    placeholder="info@crystalcred.co.zw"
                                    className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                                />
                                <p className="text-[10px] text-amber-600 font-medium flex items-center gap-1 mt-1">
                                    <Info className="w-3 h-3" />
                                    Ensure this email is authorized on your SMTP server to avoid delivery failure.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Footer Actions */}
                    <div className="mt-12 pt-8 border-t border-gray-100 flex items-center justify-between">
                        <button
                            onClick={handleTestEmail}
                            disabled={testing || (!smtp.enabled && isLocked)}
                            className="flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:border-amber-200 hover:text-amber-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed group"
                        >
                            {testing ? (
                                <Loader2 className="w-5 h-5 animate-spin" />
                            ) : (
                                <Send className="w-5 h-5 text-amber-500 group-hover:scale-110 transition-transform" />
                            )}
                            {testing ? 'Firing Test...' : 'Send Test Email'}
                        </button>

                        <button
                            onClick={handleSave}
                            disabled={saving || isLocked}
                            className={`flex items-center gap-2 px-10 py-4 bg-amber-500 text-white font-black rounded-2xl shadow-xl shadow-amber-500/30 transition-all transform active:scale-95 disabled:opacity-50 disabled:shadow-none ${!isLocked ? 'hover:bg-amber-600 hover:-translate-y-1' : ''}`}
                        >
                            {saving ? (
                                <Loader2 className="w-5 h-5 animate-spin" />
                            ) : (
                                <Save className="w-5 h-5" />
                            )}
                            {saving ? 'Sealing...' : 'Save Settings'}
                        </button>
                    </div>

                    {debugLog && (
                        <div className="mt-8 p-4 bg-gray-900 rounded-xl overflow-hidden">
                            <h4 className="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">SMTP Debug Log</h4>
                            <pre className="text-xs text-green-400 font-mono overflow-x-auto whitespace-pre-wrap max-h-64 overflow-y-auto">
                                {debugLog}
                            </pre>
                            <button
                                onClick={() => setDebugLog(null)}
                                className="mt-2 text-xs text-gray-500 hover:text-white"
                            >
                                Clear Log
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Custom Toast System */}
            {
                toast && (
                    <div
                        ref={toastRef}
                        className="fixed bottom-10 right-10 z-[9999] flex items-center gap-4 px-6 py-4 rounded-2xl shadow-2xl border-2 backdrop-blur-md"
                        style={{
                            backgroundColor:
                                toast.type === 'success' ? 'rgba(16, 185, 129, 0.95)' :
                                    toast.type === 'error' ? 'rgba(239, 68, 68, 0.95)' :
                                        'rgba(59, 130, 246, 0.95)',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            color: 'white'
                        }}
                    >
                        {toast.type === 'success' && <CheckCircle2 className="w-6 h-6" />}
                        {toast.type === 'error' && <AlertCircle className="w-6 h-6" />}
                        {toast.type === 'info' && <Lock className="w-6 h-6" />}

                        <div className="flex flex-col">
                            <span className="font-black text-sm uppercase tracking-wider">
                                {toast.type === 'success' ? 'Great Success!' : toast.type === 'error' ? 'Oh No!' : 'Hold Up'}
                            </span>
                            <span className="text-sm font-medium opacity-90">{toast.message}</span>
                        </div>

                        <button
                            onClick={() => setToast(null)}
                            className="ml-4 p-1 hover:bg-white/10 rounded-lg transition-colors"
                        >
                            <Loader2 className="w-4 h-4 rotate-45" />
                        </button>
                    </div>
                )
            }
        </div >
    );
};

export default SettingsPage;
