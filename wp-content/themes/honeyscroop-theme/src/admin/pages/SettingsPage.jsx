import React, { useState, useEffect, useRef } from 'react';
import {
    Lock,
    Unlock,
    Save,
    Settings,
    CheckCircle2,
    AlertCircle,
    Loader2,
} from 'lucide-react';
import gsap from 'gsap';
import { useAdmin } from '../context/AdminContext';
import GeneralSettings from '../components/settings/GeneralSettings';
import SocialSettings from '../components/settings/SocialSettings';
import ServerSettings from '../components/settings/ServerSettings';

const SettingsPage = () => {
    const { showAdvanced } = useAdmin();

    // Local state for form data
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
    const [toast, setToast] = useState(null); // { type: 'success' | 'error' | 'info', message: '' }
    const [debugLog, setDebugLog] = useState(null);

    const toastRef = useRef(null);
    const formRef = useRef(null);

    // Initial Fetch
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

    // Entry animation
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

    const handleSmtpChange = (e) => {
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

    // Passed down to ServerSettings
    const handleTestEmail = async () => {
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
                setDebugLog(data.debug);
            }
        } catch (err) {
            showToast('error', 'Failed to trigger test email.');
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
                            <p className="text-sm text-gray-500">Manage contact info, social links, and system settings.</p>
                        </div>
                    </div>

                    <div className="flex items-center gap-3">
                        <button
                            onClick={() => setIsLocked(!isLocked)}
                            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all ${isLocked
                                ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                : 'bg-amber-100 text-amber-700 hover:bg-amber-200'
                                }`}
                        >
                            {isLocked ? <Lock className="w-4 h-4" /> : <Unlock className="w-4 h-4" />}
                            {isLocked ? 'Unlock' : 'Lock'}
                        </button>

                        <button
                            onClick={handleSave}
                            disabled={saving || isLocked}
                            className={`flex items-center gap-2 px-6 py-2 bg-amber-500 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition-all transform active:scale-95 disabled:opacity-50 disabled:shadow-none ${!isLocked ? 'hover:bg-amber-600 hover:-translate-y-1' : ''}`}
                        >
                            {saving ? (
                                <Loader2 className="w-4 h-4 animate-spin" />
                            ) : (
                                <Save className="w-4 h-4" />
                            )}
                            {saving ? 'Saving...' : 'Save Changes'}
                        </button>
                    </div>
                </div>

                {/* Form Body */}
                <div className="p-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        {/* Left Column: General & Social */}
                        <div className="space-y-12">
                            <GeneralSettings
                                settings={settings}
                                handleChange={handleSettingsChange}
                                isLocked={isLocked}
                            />
                            <div className="w-full h-px bg-gray-100 lg:hidden"></div>
                            <SocialSettings
                                settings={settings}
                                handleChange={handleSettingsChange}
                                isLocked={isLocked}
                            />
                        </div>

                        {/* Right Column: Server (if advanced) or Empty State/Illustration */}
                        <div className="relative">
                            {showAdvanced ? (
                                <div className="animate-in fade-in slide-in-from-right-4 duration-500">
                                    <ServerSettings
                                        smtp={smtp}
                                        handleChange={handleSmtpChange}
                                        isLocked={isLocked}
                                        onTestEmail={handleTestEmail}
                                    />
                                    {debugLog && (
                                        <div className="mt-8 p-4 bg-gray-900 rounded-xl overflow-hidden animate-in fade-in slide-in-from-bottom-4">
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
                            ) : (
                                <div className="h-full flex flex-col items-center justify-center text-center p-12 bg-amber-50/50 rounded-2xl border-2 border-dashed border-amber-100">
                                    <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                                        <Settings className="w-8 h-8 text-amber-300" />
                                    </div>
                                    <h3 className="text-lg font-bold text-amber-900/70">Simplified Mode</h3>
                                    <p className="text-amber-800/50 text-sm max-w-xs mx-auto mt-2">
                                        Advanced server configurations are hidden to keep things simple. Switch to <strong>Advanced Mode</strong> in the sidebar to configure SMTP.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
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
