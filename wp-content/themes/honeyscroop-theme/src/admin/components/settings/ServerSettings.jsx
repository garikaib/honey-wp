import React, { useState } from 'react';
import { Server, ShieldCheck, Info, Send, Loader2 } from 'lucide-react';

const ServerSettings = ({ smtp, handleChange, isLocked, onTestEmail }) => {
    const [testing, setTesting] = useState(false);

    const handleTest = async () => {
        setTesting(true);
        await onTestEmail();
        setTesting(false);
    };

    return (
        <div className={`transition-all duration-300 ${isLocked ? 'grayscale-[0.8] opacity-60 pointer-events-none' : ''}`}>
            <div className="flex items-center justify-between mb-6">
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

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {/* Server Config */}
                <div className="space-y-6">
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
            </div>

            {/* Sender Info - Full Width */}
            <div className="mt-8 pt-6 border-t border-gray-100 flex flex-col md:flex-row gap-8">
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
                        placeholder="info@..."
                        className="w-full px-5 py-3 bg-gray-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all"
                    />
                    <p className="text-[10px] text-amber-600 font-medium flex items-center gap-1 mt-1">
                        <Info className="w-3 h-3" />
                        Ensure this email is authorized on your SMTP server.
                    </p>
                </div>
            </div>

            <div className="mt-8 flex justify-end">
                <button
                    onClick={handleTest}
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
            </div>
        </div>
    );
};

export default ServerSettings;
