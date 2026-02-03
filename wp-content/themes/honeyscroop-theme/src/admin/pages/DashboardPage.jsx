import React, { useState, useEffect } from 'react';
import { ShoppingBag, Users, FileText, ArrowRight, Loader } from 'lucide-react';

const StatCard = ({ label, value, icon: Icon, colorClass, loading }) => (
    <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p className="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">{label}</p>
            {loading ? (
                <div className="h-8 w-16 bg-gray-100 rounded animate-pulse"></div>
            ) : (
                <p className="text-2xl font-bold text-gray-800">{value}</p>
            )}
        </div>
        <div className={`p-3 rounded-lg ${colorClass}`}>
            <Icon size={24} className="opacity-80" />
        </div>
    </div>
);

const ActionCard = ({ title, desc, onClick }) => (
    <button
        onClick={onClick}
        className="w-full text-left bg-white hover:bg-amber-50/50 hover:border-amber-200 border border-gray-100 p-5 rounded-xl transition-all duration-200 group"
    >
        <div className="flex justify-between items-center mb-2">
            <h4 className="font-semibold text-gray-800 group-hover:text-amber-700">{title}</h4>
            <ArrowRight size={16} className="text-gray-300 group-hover:text-amber-500 transform group-hover:translate-x-1 transition-all" />
        </div>
        <p className="text-sm text-gray-500">{desc}</p>
    </button>
);

const DashboardPage = ({ setActiveTab }) => {
    const [stats, setStats] = useState({ orders: 0, leads: 0, products: 0 });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const apiUrl = window.honeyAdmin?.apiUrl || '/wp-json/honeyscroop/v1/';
                const nonce = window.honeyAdmin?.nonce || '';

                const response = await fetch(`${apiUrl}stats`, {
                    headers: { 'X-WP-Nonce': nonce }
                });

                if (response.ok) {
                    const data = await response.json();
                    setStats({
                        orders: data.orders || 0,
                        leads: data.leads || 0,
                        products: data.products || 0
                    });
                }
            } catch (err) {
                console.error('Failed to fetch stats:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchStats();
    }, []);

    return (
        <div className="space-y-8 animate-fade-in-up">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard label="Total Orders" value={stats.orders} icon={ShoppingBag} colorClass="bg-blue-50 text-blue-600" loading={loading} />
                <StatCard label="Active Leads" value={stats.leads} icon={Users} colorClass="bg-amber-50 text-amber-600" loading={loading} />
                <StatCard label="Total Products" value={stats.products} icon={FileText} colorClass="bg-emerald-50 text-emerald-600" loading={loading} />
            </div>

            <div>
                <h3 className="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <ActionCard
                        title="Manage Currencies"
                        desc="Configure exchange rates and display options."
                        onClick={() => setActiveTab('currency')}
                    />
                    <ActionCard
                        title="Shop Settings"
                        desc="Update payment methods and checkout flow."
                        onClick={() => setActiveTab('settings')}
                    />
                </div>
            </div>
        </div>
    );
};

export default DashboardPage;
