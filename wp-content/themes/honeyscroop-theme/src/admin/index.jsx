import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import { LayoutDashboard, Settings, Coins, Palette } from 'lucide-react';
import DashboardPage from './pages/DashboardPage';
import CurrencyPage from './pages/CurrencyPage';
import SettingsPage from './pages/SettingsPage';
import ThemeOptionsPage from './pages/ThemeOptionsPage';

const AdminApp = () => {
    const [activeTab, setActiveTab] = useState('dashboard');

    const tabs = [
        { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { id: 'currency', label: 'Currencies', icon: Coins },
        { id: 'settings', label: 'Shop Settings', icon: Settings },
        { id: 'theme', label: 'Theme Options', icon: Palette },
    ];

    const renderContent = () => {
        switch (activeTab) {
            case 'dashboard': return <DashboardPage setActiveTab={setActiveTab} />;
            case 'currency': return <CurrencyPage />;
            case 'settings': return <SettingsPage />;
            case 'theme': return <ThemeOptionsPage />;
            default: return <DashboardPage />;
        }
    };

    return (
        <div className="honey-admin-wrap bg-gray-50 min-h-screen font-sans text-gray-800">
            {/* Header */}
            <div className="bg-white border-b border-gray-200 px-8 py-5 flex items-center justify-between sticky top-0 z-10">
                <div className="flex items-center gap-3">
                    <img src="/wp-content/uploads/2026/01/honeyscoop-logo.png" alt="Logo" className="h-8" />
                    <span className="text-sm font-bold text-honey-600 tracking-wider uppercase border-l border-gray-300 pl-3 ml-1">
                        Control Center
                    </span>
                </div>
            </div>

            <div className="flex max-w-7xl mx-auto mt-8 gap-8 px-4">
                {/* Sidebar */}
                <aside className="w-64 flex-shrink-0">
                    <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <nav className="flex flex-col p-2">
                            {tabs.map(tab => {
                                const Icon = tab.icon;
                                const isActive = activeTab === tab.id;
                                return (
                                    <button
                                        key={tab.id}
                                        onClick={() => setActiveTab(tab.id)}
                                        className={`flex items-center gap-3 px-4 py-3 rounded-lg text-[13px] font-medium transition-all duration-200 ${isActive
                                            ? 'bg-amber-50 text-amber-700 shadow-sm'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                            }`}
                                    >
                                        <Icon size={18} strokeWidth={isActive ? 2 : 1.5} />
                                        {tab.label}
                                    </button>
                                );
                            })}
                        </nav>
                    </div>
                </aside>

                {/* Main Content */}
                <main className="flex-1 min-w-0 pb-20">
                    <div className="transform transition-all duration-300 ease-out">
                        {renderContent()}
                    </div>
                </main>
            </div>
        </div>
    );
};

const root = document.getElementById('honey-admin-root');
if (root) {
    createRoot(root).render(<AdminApp />);
}
