import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import useCartStore from './store';
import { CurrencyProvider, useCurrency } from './context/CurrencyContext';

// Icons as components
const WhatsAppIcon = () => (
    <svg viewBox="0 0 24 24" fill="currentColor" className="w-6 h-6">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
    </svg>
);

const EmailIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-6 h-6">
        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
);

const TrashIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-5 h-5">
        <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
    </svg>
);

const ShoppingBagIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-16 h-16">
        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" /><line x1="3" y1="6" x2="21" y2="6" /><path d="M16 10a4 4 0 01-8 0" />
    </svg>
);

const BeeAnimation = () => (
    <div className="relative h-32 flex items-center justify-center overflow-hidden">
        <div className="absolute animate-bounce" style={{ animationDuration: '2s' }}>
            <span className="text-6xl">🐝</span>
        </div>
        <div className="absolute bottom-0 flex gap-4">
            <span className="text-3xl animate-pulse" style={{ animationDelay: '0.2s' }}>🌻</span>
            <span className="text-3xl animate-pulse" style={{ animationDelay: '0.5s' }}>🌼</span>
            <span className="text-3xl animate-pulse" style={{ animationDelay: '0.8s' }}>🌷</span>
        </div>
    </div>
);

const OrderForm = ({ items, total, onCancel }) => {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        method: 'whatsapp',
        createAccount: false
    });
    const [submitting, setSubmitting] = useState(false);
    const [step, setStep] = useState(1);
    const [generatedPassword, setGeneratedPassword] = useState('');
    const [copied, setCopied] = useState(false);
    const { orderUrl, nonce } = window.cartData || {};
    const globalSettings = window.honeyShopData?.settings || {};
    const whatsappNumber = globalSettings.whatsappNumber || '263772123456';
    const clearCart = useCartStore(state => state.clearCart);
    const setLastOrderId = useCartStore(state => state.setLastOrderId);
    const { formatPrice } = useCurrency();

    const copyPassword = () => {
        navigator.clipboard.writeText(generatedPassword);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);

        const payload = {
            name: formData.name,
            email: formData.email,
            phone: formData.phone,
            items: items.map(i => ({ name: i.title, price: i.price, quantity: i.quantity, id: i.id })),
            total: total,
            type: formData.method,
            createAccount: formData.createAccount
        };

        try {
            const res = await fetch(orderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                setLastOrderId(data.order_id);
                clearCart();
                if (data.password) {
                    setGeneratedPassword(data.password);
                }
                if (formData.method === 'whatsapp') {
                    const itemsList = items.map(i => `• ${i.title} x${i.quantity}`).join('\n');
                    const text = `🍯 *New Order #${data.order_id}*\n\nHi, I'd like to place an order:\n\n${itemsList}\n\n*Total:* ${formatPrice(total / 100)}\n\n*Name:* ${formData.name}\n*Phone:* ${formData.phone}`;
                    window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`, '_blank');
                    setStep(3);
                } else {
                    setStep(3);
                }
            } else {
                alert('Error placing order: ' + data.message);
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred.');
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
            style={{ backgroundColor: 'rgba(0,0,0,0.6)', backdropFilter: 'blur(8px)' }}
        >
            <div
                className="bg-white dark:bg-[#1a1a14] w-full max-w-[650px] overflow-hidden animate-slideUp border dark:border-white/10 shadow-2xl rounded-[32px] relative"
            >
                {/* Premium Gradient Header */}
                <div
                    className="relative px-8 text-center overflow-hidden"
                    style={{
                        background: 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 50%, #F59E0B 100%)',
                        paddingTop: '32px',
                        paddingBottom: '24px'
                    }}
                >
                    <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>

                    <h3 className="relative text-2xl font-serif font-bold text-white drop-shadow-sm mb-1.5">
                        {step === 3 ? 'Order Placed!' : 'Complete Your Order'}
                    </h3>
                    <p className="relative text-white/90 text-xs font-medium max-w-xs mx-auto leading-relaxed">
                        {step === 3 ? 'Thank you for shopping with us' : 'Enter your details below to finalize your sweet purchase'}
                    </p>
                </div>

                {step === 3 ? (
                    <div className="p-10 text-center">
                        <div className="w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <svg className="w-12 h-12 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 className="text-3xl font-serif font-bold text-gray-900 dark:text-honey-50 mb-4">Thank You!</h4>
                        <p className="text-gray-500 dark:text-gray-400 mb-8 text-lg leading-relaxed max-w-sm mx-auto">
                            We've received your order. We'll be in touch shortly to finalize everything.
                        </p>

                        {/* Password Display */}
                        {generatedPassword && (
                            <div className="bg-amber-50 dark:bg-honey-900/20 border-2 border-amber-200 dark:border-honey-500/30 rounded-2xl p-6 mb-8 text-left relative overflow-hidden group">
                                <div className="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-amber-200/50 to-transparent rounded-bl-full"></div>
                                <p className="text-xs font-bold text-amber-700 dark:text-honey-300 mb-3 uppercase tracking-widest flex items-center gap-2">
                                    <span className="text-lg">🔐</span> Account Password
                                </p>
                                <div className="flex gap-3 items-center">
                                    <input
                                        type="text"
                                        readOnly
                                        value={generatedPassword}
                                        className="flex-1 p-3.5 bg-white dark:bg-black/30 border border-amber-200 dark:border-honey-500/20 rounded-xl font-mono text-base font-bold text-gray-800 dark:text-honey-50 outline-none tracking-wide"
                                    />
                                    <button
                                        type="button"
                                        onClick={copyPassword}
                                        className={`px-5 py-3.5 rounded-xl font-bold text-white transition-all shadow-md active:scale-95 ${copied ? 'bg-green-600' : 'bg-honey-600 hover:bg-honey-700'}`}
                                    >
                                        {copied ? '✓' : 'Copy'}
                                    </button>
                                </div>
                                <p className="text-xs text-amber-600/80 dark:text-honey-400/60 mt-3 font-medium">
                                    Save this to log in and track your sweet order!
                                </p>
                            </div>
                        )}

                        <button
                            onClick={() => window.location.href = '/shop'}
                            className="w-full py-4 bg-gradient-to-r from-honey-500 to-honey-600 text-white font-bold text-lg rounded-xl shadow-lg shadow-honey-500/30 hover:shadow-honey-500/50 hover:-translate-y-1 transition-all"
                        >
                            Continue Shopping
                        </button>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="p-8">
                        {/* Order Summary Mini Panel */}
                        <div className="flex justify-between items-center bg-amber-50 dark:bg-honey-900/20 border border-amber-100 dark:border-honey-500/20 p-5 rounded-2xl mb-8">
                            <span className="text-gray-600 dark:text-gray-300 font-medium text-lg">{items.length} item(s) to order</span>
                            <span className="text-2xl font-bold text-honey-700 dark:text-honey-400">{formatPrice(total / 100)}</span>
                        </div>

                        {/* Contact Method Cards */}
                        <div className="mb-8">
                            <label className="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4 ml-1">How would you like to order?</label>
                            <div className="grid grid-cols-2 gap-4">
                                <button
                                    type="button"
                                    onClick={() => setFormData({ ...formData, method: 'whatsapp' })}
                                    className={`relative p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-3 overflow-hidden group ${formData.method === 'whatsapp' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/5 hover:border-gray-300'}`}
                                >
                                    {formData.method === 'whatsapp' && <div className="absolute top-3 right-3 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>}
                                    <div className={`p-3 rounded-full ${formData.method === 'whatsapp' ? 'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400' : 'bg-gray-200 dark:bg-white/10 text-gray-500'}`}>
                                        <WhatsAppIcon />
                                    </div>
                                    <div className="text-center">
                                        <span className={`block font-bold text-base ${formData.method === 'whatsapp' ? 'text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-gray-400'}`}>WhatsApp</span>
                                        <span className={`text-[11px] font-medium ${formData.method === 'whatsapp' ? 'text-green-600/70 dark:text-green-400/70' : 'text-gray-400'}`}>Instant Chat</span>
                                    </div>
                                </button>

                                <button
                                    type="button"
                                    onClick={() => setFormData({ ...formData, method: 'email' })}
                                    className={`relative p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-3 overflow-hidden group ${formData.method === 'email' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/5 hover:border-gray-300'}`}
                                >
                                    {formData.method === 'email' && <div className="absolute top-3 right-3 w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>}
                                    <div className={`p-3 rounded-full ${formData.method === 'email' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : 'bg-gray-200 dark:bg-white/10 text-gray-500'}`}>
                                        <EmailIcon />
                                    </div>
                                    <div className="text-center">
                                        <span className={`block font-bold text-base ${formData.method === 'email' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400'}`}>Email</span>
                                        <span className={`text-[11px] font-medium ${formData.method === 'email' ? 'text-blue-600/70 dark:text-blue-400/70' : 'text-gray-400'}`}>Detailed Quote</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        {/* Interactive Inputs */}
                        <div className="space-y-4 mb-8">
                            <div className="group">
                                <label className="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-honey-600">Full Name</label>
                                <input required type="text" placeholder="John Doe" className="w-full p-4 bg-gray-50 dark:bg-white/5 border-2 border-transparent dark:border-white/10 rounded-xl outline-none focus:bg-white dark:focus:bg-black/20 focus:border-honey-300 dark:focus:border-honey-600 transition-all font-medium text-gray-800 dark:text-gray-100 placeholder-gray-400"
                                    value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} />
                            </div>

                            <div className="group">
                                <label className="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-honey-600">Email Address</label>
                                <input required type="email" placeholder="john@example.com" className="w-full p-4 bg-gray-50 dark:bg-white/5 border-2 border-transparent dark:border-white/10 rounded-xl outline-none focus:bg-white dark:focus:bg-black/20 focus:border-honey-300 dark:focus:border-honey-600 transition-all font-medium text-gray-800 dark:text-gray-100 placeholder-gray-400"
                                    value={formData.email} onChange={e => setFormData({ ...formData, email: e.target.value })} />
                            </div>

                            <div className="group">
                                <label className="block text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-honey-600">Phone Number</label>
                                <input required type="tel" placeholder="+263 77 123 4567" className="w-full p-4 bg-gray-50 dark:bg-white/5 border-2 border-transparent dark:border-white/10 rounded-xl outline-none focus:bg-white dark:focus:bg-black/20 focus:border-honey-300 dark:focus:border-honey-600 transition-all font-medium text-gray-800 dark:text-gray-100 placeholder-gray-400"
                                    value={formData.phone} onChange={e => setFormData({ ...formData, phone: e.target.value })} />
                            </div>

                            {/* Create Account Toggle */}
                            <label className="flex items-center gap-4 p-4 rounded-xl border border-dashed border-gray-300 dark:border-white/20 hover:border-honey-400 dark:hover:border-honey-600 bg-gray-50/50 dark:bg-white/5 cursor-pointer transition-all group select-none">
                                <div className={`w-6 h-6 rounded-md border-2 flex items-center justify-center transition-all ${formData.createAccount ? 'bg-honey-500 border-honey-500' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-transparent'}`}>
                                    {formData.createAccount && <svg className="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" /></svg>}
                                </div>
                                <input type="checkbox" checked={formData.createAccount} onChange={e => setFormData({ ...formData, createAccount: e.target.checked })} className="hidden" />
                                <div>
                                    <span className="block font-bold text-sm text-gray-800 dark:text-honey-50 group-hover:text-honey-600 transition-colors">Create an account</span>
                                    <span className="text-xs text-gray-500 dark:text-gray-400">Save details for faster future checkout</span>
                                </div>
                            </label>
                        </div>

                        {/* Action Buttons */}
                        <div className="flex gap-4">
                            <button
                                type="button"
                                onClick={onCancel}
                                className="px-8 py-4 text-gray-600 dark:text-gray-300 font-bold rounded-xl bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={submitting}
                                style={{
                                    background: formData.method === 'whatsapp'
                                        ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)'
                                        : 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                                    color: '#ffffff',
                                    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
                                }}
                                className={`flex-1 py-4 font-bold text-lg rounded-xl text-white transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0 ${submitting ? 'opacity-70 cursor-not-allowed' : 'hover:shadow-lg'}`}
                            >
                                {submitting ? (
                                    <>
                                        <svg className="w-5 h-5 animate-spin" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                                        Processing...
                                    </>
                                ) : formData.method === 'whatsapp' ? (
                                    <>
                                        <WhatsAppIcon /> Order via WhatsApp
                                    </>
                                ) : (
                                    <>Place Order</>
                                )}
                            </button>
                        </div>
                    </form>
                )}
            </div>
        </div>
    );
};


const CartItem = ({ item, onUpdateQuantity, onRemove, formatPrice }) => {
    const [isRemoving, setIsRemoving] = useState(false);

    const handleRemove = () => {
        setIsRemoving(true);
        setTimeout(() => onRemove(item.id), 300);
    };

    return (
        <div className={`group flex gap-5 bg-white dark:bg-surface-glass p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 hover:shadow-md dark:hover:shadow-honey-900/20 hover:border-amber-100 dark:hover:border-honey-500/30 transition-all duration-300 ${isRemoving ? 'opacity-0 scale-95 -translate-x-4' : 'opacity-100 scale-100 translate-x-0'}`}>
            {/* Image */}
            <div className="w-28 h-28 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                <img src={item.image} alt={item.title} className="w-full h-full object-cover" />
            </div>

            {/* Details */}
            <div className="flex-1 flex flex-col justify-between">
                <div>
                    <div className="flex justify-between items-start mb-1">
                        <h3 className="text-lg font-bold text-gray-900 dark:text-honey-50 group-hover:text-amber-700 dark:group-hover:text-honey-400 transition-colors" dangerouslySetInnerHTML={{ __html: item.title }} />
                        <span className="text-xl font-bold text-amber-600 dark:text-honey-400">{formatPrice((item.price * item.quantity) / 100)}</span>
                    </div>
                    <p className="text-gray-400 dark:text-gray-500 text-sm">{formatPrice(item.price / 100)} each</p>
                </div>

                <div className="flex items-center justify-between mt-3">
                    {/* Quantity Controls */}
                    <div className="flex items-center bg-gray-50 dark:bg-white/5 rounded-xl overflow-hidden border border-gray-200 dark:border-white/10">
                        <button
                            onClick={() => onUpdateQuantity(item.id, item.quantity - 1)}
                            className="w-10 h-10 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-amber-100 dark:hover:bg-honey-900/30 hover:text-amber-700 dark:hover:text-honey-300 transition-all font-bold text-lg"
                        >
                            −
                        </button>
                        <span className="w-12 text-center font-bold text-gray-800 dark:text-honey-50">{item.quantity}</span>
                        <button
                            onClick={() => onUpdateQuantity(item.id, item.quantity + 1)}
                            className="w-10 h-10 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-amber-100 dark:hover:bg-honey-900/30 hover:text-amber-700 dark:hover:text-honey-300 transition-all font-bold text-lg"
                        >
                            +
                        </button>
                    </div>

                    {/* Remove Button */}
                    <button
                        onClick={handleRemove}
                        className="p-2 text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                        title="Remove item"
                    >
                        <TrashIcon />
                    </button>
                </div>
            </div>
        </div>
    );
};

const ShopCart = () => {
    const { items, updateQuantity, removeFromCart, getTotalPrice, lastOrderId, clearLastOrderId } = useCartStore();
    const [showCheckout, setShowCheckout] = useState(false);
    const [mounted, setMounted] = useState(false);
    const total = getTotalPrice();
    const { formatPrice } = useCurrency();

    useEffect(() => {
        setMounted(true);
    }, []);

    if (items.length === 0) {
        return (
            <div className="min-h-[60vh] flex flex-col items-center justify-center py-20 px-4">
                <div className={`text-center transition-all duration-700 ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    {lastOrderId ? (
                        <>
                            <div className="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg className="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h1 className="text-4xl font-serif text-gray-800 mb-4">Sweet Success!</h1>
                            <p className="text-gray-500 text-lg mb-8 max-w-md mx-auto">
                                Thank you for your order <strong>#{lastOrderId}</strong>! We've received it and the bees are already busy preparing your sweetness.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <a
                                    href="/shop"
                                    onClick={() => clearLastOrderId()}
                                    className="inline-flex items-center justify-center gap-3 px-8 py-4 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-all shadow-lg"
                                >
                                    Continue Shopping
                                </a>
                                <button
                                    onClick={() => clearLastOrderId()}
                                    className="inline-flex items-center justify-center px-8 py-4 text-gray-400 font-bold rounded-xl hover:text-gray-600 transition-all"
                                >
                                    Dismiss
                                </button>
                            </div>
                        </>
                    ) : (
                        <>
                            <BeeAnimation />
                            <h1 className="text-4xl font-serif text-gray-800 dark:text-honey-50 mb-4 mt-6">Your Cart is Empty</h1>
                            <p className="text-gray-500 dark:text-gray-400 text-lg mb-8 max-w-md mx-auto">
                                Looks like you haven't added any sweetness yet. Let's fix that!
                            </p>
                            <a
                                href="/shop"
                                style={{
                                    background: 'linear-gradient(to right, #F59E0B, #EAB308)',
                                    color: '#FFFFFF',
                                    boxShadow: '0 10px 15px -3px rgba(245, 158, 11, 0.3)'
                                }}
                                className="inline-flex items-center gap-3 px-8 py-4 font-bold rounded-xl hover:opacity-90 transition-all transform hover:-translate-y-1"
                            >
                                <ShoppingBagIcon className="w-5 h-5" />
                                Explore Our Honey
                            </a>
                        </>
                    )}
                </div>
            </div>
        );
    }

    return (
        <div className="bg-gradient-to-b from-amber-50/30 to-white dark:from-dark-bg dark:to-dark-bg min-h-screen">
            <div className="container mx-auto px-4 py-12 lg:py-16">
                {/* Header */}
                <div className={`mb-10 transition-all duration-700 ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    <h1 className="text-4xl lg:text-5xl font-serif text-gray-900 dark:text-honey-50 mb-2">Your Cart</h1>
                    <p className="text-gray-500 dark:text-gray-400 text-lg">{items.length} item{items.length !== 1 ? 's' : ''} • Ready to order</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                    {/* Cart Items */}
                    <div className="lg:col-span-2 space-y-4">
                        {items.map((item, index) => (
                            <div
                                key={item.id}
                                className={`transition-all duration-500 ${mounted ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'}`}
                                style={{ transitionDelay: `${index * 100}ms` }}
                            >
                                <CartItem
                                    item={item}
                                    onUpdateQuantity={updateQuantity}
                                    onRemove={removeFromCart}
                                    formatPrice={formatPrice}
                                />
                            </div>
                        ))}
                    </div>

                    {/* Summary Sidebar */}
                    <div className="lg:col-span-1">
                        <div className={`bg-white dark:bg-surface-glass p-8 rounded-3xl border border-gray-100 dark:border-white/10 shadow-xl sticky top-8 transition-all duration-700 ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`} style={{ transitionDelay: '300ms' }}>
                            {/* Decorative Element */}
                            <div className="absolute top-0 left-8 right-8 h-1 bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-400 rounded-full"></div>

                            <h3 className="text-2xl font-serif font-bold text-gray-900 dark:text-honey-50 mb-6 pt-2">Order Summary</h3>

                            {/* Items List Mini */}
                            <div className="space-y-3 mb-6 max-h-48 overflow-y-auto">
                                {items.map(item => (
                                    <div key={item.id} className="flex justify-between text-sm">
                                        <span className="text-gray-600 dark:text-gray-400 truncate flex-1 mr-2" dangerouslySetInnerHTML={{ __html: `${item.title} × ${item.quantity}` }} />
                                        <span className="text-gray-900 dark:text-honey-100 font-medium">{formatPrice((item.price * item.quantity) / 100)}</span>
                                    </div>
                                ))}
                            </div>

                            <div className="border-t border-dashed border-gray-200 dark:border-white/10 pt-4 mb-6">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="text-gray-500 dark:text-gray-400">Subtotal</span>
                                    <span className="font-medium dark:text-honey-100">{formatPrice(total / 100)}</span>
                                </div>
                                <div className="flex justify-between items-center text-sm text-gray-400 dark:text-gray-500">
                                    <span>Shipping</span>
                                    <span>Calculated at checkout</span>
                                </div>
                            </div>

                            <div className="border-t border-gray-200 dark:border-white/10 pt-4 mb-8">
                                <div className="flex justify-between items-center">
                                    <span className="text-xl font-bold text-gray-900 dark:text-honey-50">Total</span>
                                    <span className="text-2xl font-bold text-amber-600 dark:text-honey-400">{formatPrice(total / 100)}</span>
                                </div>
                            </div>

                            {/* CTA Button */}
                            <button
                                onClick={() => setShowCheckout(true)}
                                style={{
                                    background: 'linear-gradient(to right, #F59E0B, #EAB308)',
                                    boxShadow: '0 10px 15px -3px rgba(245, 158, 11, 0.3)'
                                }}
                                className="w-full py-5 text-white font-bold text-lg rounded-2xl hover:opacity-90 transition-all transform hover:-translate-y-1 active:scale-95"
                            >
                                Proceed to Checkout
                            </button>

                            {/* Trust Badges */}
                            <div className="mt-6 flex justify-center gap-6 text-gray-400 text-xs">
                                <span className="flex items-center gap-1">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    Secure
                                </span>
                                <span className="flex items-center gap-1">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" /></svg>
                                    Quality
                                </span>
                                <span className="flex items-center gap-1">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Fast
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {showCheckout && <OrderForm items={items} total={total} onCancel={() => setShowCheckout(false)} />}
            </div>
        </div>
    );
};

// Add custom animations via style tag
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
    .animate-slideUp { animation: slideUp 0.4s ease-out; }
`;
document.head.appendChild(styleSheet);

const root = document.getElementById('shop-cart-root');
if (root) {
    createRoot(root).render(
        <CurrencyProvider>
            <ShopCart />
        </CurrencyProvider>
    );
}
