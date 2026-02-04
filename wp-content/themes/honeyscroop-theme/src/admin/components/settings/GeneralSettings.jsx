import React from 'react';
import { Globe, Phone, MapPin } from 'lucide-react';

const GeneralSettings = ({ settings, handleChange, isLocked }) => {
    return (
        <div className={`transition-all duration-300 ${isLocked ? 'grayscale-[0.8] opacity-60 pointer-events-none' : ''}`}>
            <div className="flex items-center gap-2 text-amber-800 font-bold text-sm uppercase tracking-widest mb-6">
                <Globe className="w-4 h-4" />
                <span>Contact Details</span>
            </div>

            <div className="space-y-6">
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">WhatsApp Number</label>
                    <input
                        type="text"
                        name="whatsappNumber"
                        value={settings.whatsappNumber}
                        onChange={handleChange}
                        placeholder="263771234567"
                        className="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300"
                    />
                    <p className="text-[10px] text-gray-400 ml-1 font-medium">Format: Country code without + (e.g., 263...)</p>
                </div>
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Phone Number (Display)</label>
                    <input
                        type="text"
                        name="phoneNumber"
                        value={settings.phoneNumber}
                        onChange={handleChange}
                        placeholder="+263 77 123 4567"
                        className="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300"
                    />
                </div>
                <div className="space-y-2">
                    <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Address</label>
                    <textarea
                        name="address"
                        value={settings.address}
                        onChange={handleChange}
                        placeholder="123 Honey Lane..."
                        rows="3"
                        className="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all shadow-sm text-gray-800 font-medium placeholder:text-gray-300 resize-none"
                    />
                </div>
            </div>
        </div>
    );
};

export default GeneralSettings;
