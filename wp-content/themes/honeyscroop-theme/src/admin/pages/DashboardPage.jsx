import React, { useState, useEffect, useRef } from 'react';
import { ShoppingBag, Users, FileText, ArrowRight, Loader } from 'lucide-react';
import gsap from 'gsap';

const StatCard = ({ label, value, icon: Icon, colorClass, borderClass, loading, delay }) => {
    const cardRef = useRef(null);

    useEffect(() => {
        if (!loading && cardRef.current) {
            gsap.fromTo(cardRef.current,
                { opacity: 0, y: 20 },
                { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out', delay: delay }
            );
        }
    }, [loading, delay]);

    return (
        <div ref={cardRef} className={`bg-white p-6 rounded-2xl shadow-sm border ${borderClass} flex items-center justify-between hover:shadow-md transition-shadow opacity-0`}>
            <div>
                <p className="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{label}</p>
                {loading ? (
                    <div className="h-8 w-16 bg-gray-100 rounded animate-pulse"></div>
                ) : (
                    <p className="text-3xl font-black text-gray-800 tracking-tight">{value}</p>
                )}
            </div>
            <div className={`p-4 rounded-xl ${colorClass}`}>
                <Icon size={24} className="opacity-90" />
            </div>
        </div>
    );
};

const ActionCard = ({ title, desc, onClick, delay }) => {
    const buttonRef = useRef(null);

    useEffect(() => {
        if (buttonRef.current) {
            gsap.fromTo(buttonRef.current,
                { opacity: 0, x: -20 },
                { opacity: 1, x: 0, duration: 0.5, ease: 'power2.out', delay: delay }
            );
        }
    }, [delay]);

    return (
        <button
            ref={buttonRef}
            onClick={onClick}
            className="w-full text-left bg-white hover:bg-amber-50/50 hover:border-amber-200 border border-gray-100 p-6 rounded-2xl transition-all duration-300 group opacity-0 shadow-sm hover:shadow-md"
        >
            <div className="flex justify-between items-center mb-2">
                <h4 className="font-bold text-gray-800 text-lg group-hover:text-amber-700 transition-colors">{title}</h4>
                <div className="bg-gray-50 p-2 rounded-full group-hover:bg-amber-100 transition-colors">
                    <ArrowRight size={18} className="text-gray-400 group-hover:text-amber-600 transform group-hover:translate-x-1 transition-all" />
                </div>
            </div>
            <p className="text-sm text-gray-500 leading-relaxed group-hover:text-gray-600">{desc}</p>
        </button>
    );
};

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
                // Artificial delay for smoother animation entry
                setTimeout(() => setLoading(false), 300);
            }
        };

        fetchStats();
    }, []);

    return (
        <div className="space-y-12 pb-12">
            <div>
                <h1 className="text-3xl font-black text-gray-900 mb-2">Hive Overview</h1>
                <p className="text-gray-500">Welcome back. Here's what's happening in your shop today.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard
                    label="Total Orders"
                    value={stats.orders}
                    icon={ShoppingBag}
                    colorClass="bg-blue-50 text-blue-600"
                    borderClass="border-blue-100/50"
                    loading={loading}
                    delay={0.1}
                />
                <StatCard
                    label="Active Leads"
                    value={stats.leads}
                    icon={Users}
                    colorClass="bg-amber-50 text-amber-600"
                    borderClass="border-amber-100/50"
                    loading={loading}
                    delay={0.2}
                />
                <StatCard
                    label="Total Products"
                    value={stats.products}
                    icon={FileText}
                    colorClass="bg-emerald-50 text-emerald-600"
                    borderClass="border-emerald-100/50"
                    loading={loading}
                    delay={0.3}
                />
            </div>

            <div className="pt-4">
                <h3 className="text-sm font-bold uppercase tracking-wider text-gray-400 mb-6 flex items-center gap-4">
                    <span>Quick Actions</span>
                    <div className="h-px bg-gray-100 flex-1"></div>
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <ActionCard
                        title="Manage Currencies"
                        desc="Configure exchange rates, set base currency, and toggle display options."
                        onClick={() => setActiveTab('currency')}
                        delay={0.4}
                    />
                    <ActionCard
                        title="Shop Settings"
                        desc="Update payment methods, checkout flow, contact info, and system integrations."
                        onClick={() => setActiveTab('settings')}
                        delay={0.5}
                    />
                </div>
            </div>
        </div>
    );
};

export default DashboardPage;
