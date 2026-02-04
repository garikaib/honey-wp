import React, { useState, useEffect, useRef } from 'react';
import { Package, Clock, CheckCircle, Smartphone, Mail, User, Search, Filter, ChevronRight, X, AlertCircle } from 'lucide-react';
import gsap from 'gsap';

const OrderStatusBadge = ({ status }) => {
    const styles = {
        lead: 'bg-blue-50 text-blue-700 border-blue-100',
        processing: 'bg-amber-50 text-amber-700 border-amber-100', // "Processing" usually means paid/in-progress
        completed: 'bg-emerald-50 text-emerald-700 border-emerald-100',
        cancelled: 'bg-red-50 text-red-700 border-red-100',
        trash: 'bg-gray-50 text-gray-500 border-gray-100'
    };

    // Default to gray if unknown
    const activeStyle = styles[status] || styles.trash;

    return (
        <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${activeStyle}`}>
            {status}
        </span>
    );
};

const OrdersPage = () => {
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeStatus, setActiveStatus] = useState('all');
    const [selectedOrder, setSelectedOrder] = useState(null);
    const [updating, setUpdating] = useState(false);

    // Filter Tabs
    const tabs = [
        { id: 'all', label: 'All Orders' },
        { id: 'lead', label: 'Leads' },
        { id: 'processing', label: 'Processing' },
        { id: 'completed', label: 'Completed' },
        { id: 'cancelled', label: 'Cancelled' }
    ];

    useEffect(() => {
        fetchOrders();
    }, [activeStatus]);

    const fetchOrders = async () => {
        setLoading(true);
        try {
            const apiUrl = window.honeyAdmin?.apiUrl || '/wp-json/honeyscroop/v1/';
            const nonce = window.honeyAdmin?.nonce || '';
            const statusParam = activeStatus !== 'all' ? `&status=${activeStatus}` : '';

            const response = await fetch(`${apiUrl}orders?page=1${statusParam}`, {
                headers: { 'X-WP-Nonce': nonce }
            });

            if (response.ok) {
                const data = await response.json();
                setOrders(data.orders || []);
            }
        } catch (err) {
            console.error('Failed to fetch orders:', err);
        } finally {
            setLoading(false);
        }
    };

    const updateOrderStatus = async (orderId, newStatus) => {
        if (!confirm(`Are you sure you want to mark this order as ${newStatus}?`)) return;

        setUpdating(true);
        try {
            const apiUrl = window.honeyAdmin?.apiUrl || '/wp-json/honeyscroop/v1/';
            const nonce = window.honeyAdmin?.nonce || '';

            const response = await fetch(`${apiUrl}orders/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (response.ok) {
                // Update local state
                setOrders(prev => prev.map(o => o.id === orderId ? { ...o, status: newStatus } : o));
                if (selectedOrder?.id === orderId) {
                    setSelectedOrder(prev => ({ ...prev, status: newStatus }));
                }
                alert('Order updated successfully');
            } else {
                alert('Failed to update order');
            }
        } catch (err) {
            console.error('Failed to update order:', err);
            alert('Error updating order');
        } finally {
            setUpdating(false);
        }
    };

    // GSAP Entry
    const containerRef = useRef(null);
    useEffect(() => {
        if (!loading && containerRef.current) {
            gsap.fromTo(containerRef.current.children,
                { opacity: 0, y: 10 },
                { opacity: 1, y: 0, duration: 0.4, stagger: 0.05, ease: 'power2.out' }
            );
        }
    }, [loading, activeStatus]);

    return (
        <div className="space-y-6 h-full flex flex-col">
            {/* Header */}
            <div>
                <h1 className="text-2xl font-black text-gray-900 mb-2">Order Management</h1>
                <p className="text-gray-500 text-sm">Track and manage customer orders.</p>
            </div>

            {/* Filter Tabs */}
            <div className="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                {tabs.map(tab => (
                    <button
                        key={tab.id}
                        onClick={() => setActiveStatus(tab.id)}
                        className={`px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all whitespace-nowrap ${activeStatus === tab.id
                                ? 'bg-gray-900 text-white shadow-md'
                                : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-100'
                            }`}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            {/* Orders List */}
            <div className="flex-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col relative">
                {loading ? (
                    <div className="flex-1 flex items-center justify-center p-12">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
                    </div>
                ) : orders.length === 0 ? (
                    <div className="flex-1 flex flex-col items-center justify-center p-12 text-center">
                        <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <Package className="text-gray-300" size={32} />
                        </div>
                        <h3 className="text-lg font-bold text-gray-800">No orders found</h3>
                        <p className="text-gray-500 text-sm">There are no orders with this status.</p>
                    </div>
                ) : (
                    <div className="overflow-y-auto flex-1">
                        <table className="w-full text-left" ref={containerRef}>
                            <thead className="bg-gray-50 sticky top-0 z-10 border-b border-gray-100">
                                <tr>
                                    <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Order</th>
                                    <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                    <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                    <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Total</th>
                                    <th className="px-6 py-4 w-10"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {orders.map(order => (
                                    <tr
                                        key={order.id}
                                        onClick={() => setSelectedOrder(order)}
                                        className="group hover:bg-amber-50/30 transition-colors cursor-pointer"
                                    >
                                        <td className="px-6 py-4">
                                            <span className="font-bold text-gray-900">#{order.id}</span>
                                            <div className="text-[10px] text-gray-400 uppercase mt-0.5 font-bold tracking-wider">{order.type}</div>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-600 font-medium">
                                            {new Date(order.date).toLocaleDateString()}
                                            <div className="text-xs text-gray-400">{new Date(order.date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <OrderStatusBadge status={order.status} />
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                                    {order.customer.name.charAt(0)}
                                                </div>
                                                <div>
                                                    <div className="text-sm font-bold text-gray-800">{order.customer.name}</div>
                                                    <div className="text-xs text-gray-400">{order.customer.email}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-right font-mono font-bold text-gray-800">
                                            ${(order.total / 100).toFixed(2)}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <ChevronRight size={16} className="text-gray-300 group-hover:text-amber-500 transition-colors" />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Order Details Slide-over Modal */}
            {selectedOrder && (
                <div className="fixed inset-0 z-50 flex justify-end">
                    <div className="absolute inset-0 bg-black/20 backdrop-blur-sm" onClick={() => setSelectedOrder(null)}></div>
                    <div className="relative w-full max-w-md bg-white shadow-2xl h-full flex flex-col animate-slide-in-right">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <div>
                                <h2 className="text-xl font-black text-gray-900">Order #{selectedOrder.id}</h2>
                                <p className="text-xs text-gray-500 mt-1 font-medium">{new Date(selectedOrder.date).toLocaleString()}</p>
                            </div>
                            <button onClick={() => setSelectedOrder(null)} className="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <X size={20} className="text-gray-500" />
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto p-6 space-y-8">

                            {/* Status Actions */}
                            <div className="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                <div className="flex items-center justify-between mb-4">
                                    <span className="text-xs font-bold uppercase tracking-wider text-amber-800">Current Status</span>
                                    <OrderStatusBadge status={selectedOrder.status} />
                                </div>
                                <div className="grid grid-cols-2 gap-2">
                                    {selectedOrder.status !== 'processing' && (
                                        <button
                                            onClick={() => updateOrderStatus(selectedOrder.id, 'processing')}
                                            disabled={updating}
                                            className="px-3 py-2 bg-white border border-amber-200 text-amber-700 rounded-lg text-xs font-bold hover:bg-amber-100 transition-colors"
                                        >
                                            Mark Processing
                                        </button>
                                    )}
                                    {selectedOrder.status !== 'completed' && (
                                        <button
                                            onClick={() => updateOrderStatus(selectedOrder.id, 'completed')}
                                            disabled={updating}
                                            className="px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-colors shadow-sm"
                                        >
                                            Mark Completed
                                        </button>
                                    )}
                                    {selectedOrder.status !== 'cancelled' && (
                                        <button
                                            onClick={() => updateOrderStatus(selectedOrder.id, 'cancelled')}
                                            disabled={updating}
                                            className="px-3 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-xs font-bold hover:bg-red-50 transition-colors col-span-2"
                                        >
                                            Cancel Order
                                        </button>
                                    )}
                                </div>
                            </div>

                            {/* Customer Info */}
                            <div>
                                <h3 className="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-2">
                                    <User size={14} /> Customer Details
                                </h3>
                                <div className="space-y-4">
                                    <div className="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <div className="w-10 h-10 rounded-full bg-white flex items-center justify-center font-bold text-gray-400 border border-gray-100">
                                            {selectedOrder.customer.name.charAt(0)}
                                        </div>
                                        <div>
                                            <p className="font-bold text-gray-800">{selectedOrder.customer.name}</p>
                                            <p className="text-xs text-gray-500">Customer</p>
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <a href={`mailto:${selectedOrder.customer.email}`} className="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-gray-300 transition-colors">
                                            <Mail size={16} className="text-gray-400" />
                                            <div className="overflow-hidden">
                                                <p className="text-xs text-gray-400 font-bold uppercase">Email</p>
                                                <p className="text-sm font-medium text-gray-800 truncate">{selectedOrder.customer.email}</p>
                                            </div>
                                        </a>
                                        <a href={`tel:${selectedOrder.customer.phone}`} className="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-gray-300 transition-colors">
                                            <Smartphone size={16} className="text-gray-400" />
                                            <div>
                                                <p className="text-xs text-gray-400 font-bold uppercase">Phone</p>
                                                <p className="text-sm font-medium text-gray-800">{selectedOrder.customer.phone}</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {/* Items */}
                            <div>
                                <h3 className="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-2">
                                    <Package size={14} /> Order Items
                                </h3>
                                <div className="bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden">
                                    {selectedOrder.items?.map((item, i) => (
                                        <div key={i} className="flex items-center justify-between p-4 border-b border-gray-100 last:border-0">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-xs font-bold text-gray-400">
                                                    {item.quantity}x
                                                </div>
                                                <span className="font-medium text-gray-800">{item.name}</span>
                                            </div>
                                            <span className="font-mono font-bold text-gray-600">
                                                ${((item.price * item.quantity) / 100).toFixed(2)}
                                            </span>
                                        </div>
                                    ))}
                                    <div className="p-4 bg-gray-100/50 flex justify-between items-center border-t border-gray-200/50">
                                        <span className="font-bold text-gray-600 text-sm uppercase tracking-wide">Total</span>
                                        <span className="font-mono font-black text-xl text-gray-900">${(selectedOrder.total / 100).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default OrdersPage;
