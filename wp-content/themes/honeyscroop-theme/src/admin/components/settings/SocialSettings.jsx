import React from 'react';
import { Facebook, Instagram, Twitter } from 'lucide-react';

// Custom TikTok icon since Lucide might not have it or it's named differently
const TikTok = ({ size = 24, className }) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width={size}
        height={size}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        className={className}
    >
        <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
    </svg>
);

const SocialSettings = ({ settings, handleChange, isLocked }) => {
    return (
        <div className={`transition-all duration-300 ${isLocked ? 'grayscale-[0.8] opacity-60 pointer-events-none' : ''}`}>
            <div className="flex items-center gap-2 text-amber-800 font-bold text-sm uppercase tracking-widest mb-6">
                <Twitter className="w-4 h-4" />
                <span>Social Media</span>
            </div>

            <div className="space-y-6">
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Facebook URL</label>
                    <div className="relative">
                        <div className="absolute left-4 top-3.5 flex items-center justify-center p-1 bg-blue-50 rounded-md">
                            <Facebook className="text-blue-600 w-4 h-4" />
                        </div>
                        <input
                            type="url"
                            name="social_facebook"
                            value={settings.social?.facebook || ''}
                            onChange={handleChange}
                            placeholder="https://facebook.com/..."
                            className="w-full pl-14 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300"
                        />
                    </div>
                </div>
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Instagram URL</label>
                    <div className="relative">
                        <div className="absolute left-4 top-3.5 flex items-center justify-center p-1 bg-pink-50 rounded-md">
                            <Instagram className="text-pink-600 w-4 h-4" />
                        </div>
                        <input
                            type="url"
                            name="social_instagram"
                            value={settings.social?.instagram || ''}
                            onChange={handleChange}
                            placeholder="https://instagram.com/..."
                            className="w-full pl-14 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300"
                        />
                    </div>
                </div>
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">TikTok URL</label>
                    <div className="relative">
                        <div className="absolute left-4 top-3.5 flex items-center justify-center p-1 bg-gray-100 rounded-md">
                            <TikTok className="text-black w-4 h-4" />
                        </div>
                        <input
                            type="url"
                            name="social_tiktok"
                            value={settings.social?.tiktok || ''}
                            onChange={handleChange}
                            placeholder="https://tiktok.com/..."
                            className="w-full pl-14 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300"
                        />
                    </div>
                </div>
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">X (Twitter) URL</label>
                    <div className="relative">
                        <div className="absolute left-4 top-3.5 flex items-center justify-center p-1 bg-gray-100 rounded-md">
                            <Twitter className="text-gray-800 w-4 h-4" />
                        </div>
                        <input
                            type="url"
                            name="social_x"
                            value={settings.social?.x || ''}
                            onChange={handleChange}
                            placeholder="https://x.com/..."
                            className="w-full pl-14 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300"
                        />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SocialSettings;
