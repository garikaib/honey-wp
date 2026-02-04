import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import { LayoutDashboard, Settings, Coins, Palette, Zap, ZapOff, ShoppingBag } from 'lucide-react';
import DashboardPage from './pages/DashboardPage';
import CurrencyPage from './pages/CurrencyPage';
import SettingsPage from './pages/SettingsPage';
import ThemeOptionsPage from './pages/ThemeOptionsPage';
import OrdersPage from './pages/OrdersPage';
import { AdminProvider, useAdmin } from './context/AdminContext';

const AdminLayout = () => {
    const { activeTab, setActiveTab, showAdvanced, setShowAdvanced } = useAdmin();

    const tabs = [
        { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard },
        { id: 'orders', label: 'Orders', icon: ShoppingBag },
        { id: 'currency', label: 'Currencies', icon: Coins },
        { id: 'settings', label: 'Shop Settings', icon: Settings },
        { id: 'theme', label: 'Theme Options', icon: Palette },
    ];

    const renderContent = () => {
        switch (activeTab) {
            case 'dashboard': return <DashboardPage setActiveTab={setActiveTab} />;
            case 'orders': return <OrdersPage />;
            case 'currency': return <CurrencyPage />;
            case 'settings': return <SettingsPage />;
            case 'theme': return <ThemeOptionsPage />;
            default: return <DashboardPage />;
        }
    };

    return (
        <div className="honey-admin-wrap bg-gray-50 min-h-screen font-sans text-gray-800">
            {/* Header */}
            <div className="bg-white border-b border-gray-200 px-8 py-5 flex items-center justify-between sticky top-0 z-10 shadow-sm glass-header">
                <div className="flex items-center gap-3">
                    <img src="/wp-content/uploads/2026/01/honeyscoop-logo.png" alt="Logo" className="h-8" />
                    <span className="text-sm font-bold text-honey-600 tracking-wider uppercase border-l border-gray-300 pl-3 ml-1">
                        Control Center
                    </span>
                </div>
                <div className="flex items-center gap-4">
                    {/* Potential User Profile or status indicators here */}
                </div>
            </div>

            <div className="flex max-w-7xl mx-auto mt-8 gap-8 px-4">
                {/* Sidebar */}
                <aside className="w-64 flex-shrink-0">
                    <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24 flex flex-col">
                        <nav className="flex flex-col p-2 space-y-1">
                            {tabs.map(tab => {
                                const Icon = tab.icon;
                                const isActive = activeTab === tab.id;
                                return (
                                    <button
                                        key={tab.id}
                                        onClick={() => setActiveTab(tab.id)}
                                        className={`flex items-center gap-3 px-4 py-3 rounded-lg text-[13px] font-medium transition-all duration-200 ${isActive
                                            ? 'bg-amber-50 text-amber-700 shadow-sm translate-x-1'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1'
                                            }`}
                                    >
                                        <Icon size={18} strokeWidth={isActive ? 2 : 1.5} className={isActive ? 'text-amber-600' : 'text-gray-400'} />
                                        {tab.label}
                                    </button>
                                );
                            })}
                        </nav>

                        <div className="mt-auto p-4 border-t border-gray-100 bg-gray-50/50">
                            <div className="bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                        {showAdvanced ? <Zap size={12} className="text-amber-500 fill-amber-500" /> : <ZapOff size={12} className="text-gray-400" />}
                                        {showAdvanced ? 'Pro Mode' : 'Lite Mode'}
                                    </span>
                                    <div
                                        onClick={() => setShowAdvanced(!showAdvanced)}
                                        className={`w-10 h-6 rounded-full p-1 cursor-pointer transition-colors duration-300 ${showAdvanced ? 'bg-amber-500' : 'bg-gray-200'}`}
                                    >
                                        <div className={`w-4 h-4 rounded-full bg-white shadow-sm transform transition-transform duration-300 ${showAdvanced ? 'translate-x-4' : 'translate-x-0'}`} />
                                    </div>
                                </div>
                                <p className="text-[10px] text-gray-400 leading-tight">
                                    {showAdvanced ? 'Full control enabled.' : 'Essential settings only.'}
                                </p>
                            </div>
                        </div>
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

const AdminApp = () => {
    return (
        <AdminProvider>
            <AdminLayout />
        </AdminProvider>
    );
};

const root = document.getElementById('honey-admin-root');
if (root) {
    createRoot(root).render(<AdminApp />);
}
