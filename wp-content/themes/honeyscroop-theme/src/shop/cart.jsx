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
    // Use dynamic settings or fallback
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

    // Input style object for consistency
    const inputStyle = {
        width: '100%',
        padding: '14px 18px',
        backgroundColor: '#F9FAFB',
        border: '2px solid #E5E7EB',
        borderRadius: '14px',
        fontSize: '16px',
        fontFamily: 'Outfit, sans-serif',
        outline: 'none',
        transition: 'all 0.2s ease'
    };

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
            style={{ backgroundColor: 'rgba(0,0,0,0.6)', backdropFilter: 'blur(4px)' }}
        >
            <div
                className="bg-white w-full overflow-hidden animate-slideUp"
                style={{
                    maxWidth: '480px',
                    borderRadius: '28px',
                    boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)'
                }}
            >
                {/* Header */}
                <div
                    style={{
                        background: 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 50%, #F59E0B 100%)',
                        padding: '28px 24px',
                        textAlign: 'center'
                    }}
                >
                    <h3 style={{
                        fontSize: '24px',
                        fontFamily: 'Cormorant Garamond, serif',
                        fontWeight: '700',
                        color: '#FFFFFF',
                        margin: 0,
                        textShadow: '0 2px 4px rgba(0,0,0,0.1)'
                    }}>
                        {step === 3 ? '🎉 Order Placed!' : '🍯 Complete Your Order'}
                    </h3>
                </div>

                {step === 3 ? (
                    <div style={{ padding: '40px 32px', textAlign: 'center' }}>
                        <div style={{
                            width: '80px',
                            height: '80px',
                            backgroundColor: '#D1FAE5',
                            borderRadius: '50%',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            margin: '0 auto 24px auto'
                        }}>
                            <svg style={{ width: '40px', height: '40px', color: '#10B981' }} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 style={{ fontSize: '22px', fontWeight: '700', color: '#1F2937', marginBottom: '8px' }}>Thank You!</h4>
                        <p style={{ color: '#6B7280', marginBottom: '24px', lineHeight: '1.6' }}>
                            We've received your order and will contact you shortly via email.
                        </p>

                        {/* Password Display if account created */}
                        {generatedPassword && (
                            <div style={{
                                backgroundColor: '#FEF3C7',
                                border: '2px solid #FCD34D',
                                borderRadius: '16px',
                                padding: '20px',
                                marginBottom: '24px',
                                textAlign: 'left'
                            }}>
                                <p style={{ fontSize: '13px', fontWeight: '700', color: '#92400E', marginBottom: '12px', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                                    🔐 Your Account Password
                                </p>
                                <div style={{
                                    display: 'flex',
                                    gap: '8px',
                                    alignItems: 'center'
                                }}>
                                    <input
                                        type="text"
                                        readOnly
                                        value={generatedPassword}
                                        style={{
                                            flex: 1,
                                            padding: '12px 16px',
                                            backgroundColor: '#FFFFFF',
                                            border: '1px solid #FCD34D',
                                            borderRadius: '10px',
                                            fontFamily: 'monospace',
                                            fontSize: '15px',
                                            fontWeight: '600'
                                        }}
                                    />
                                    <button
                                        type="button"
                                        onClick={copyPassword}
                                        style={{
                                            padding: '12px 16px',
                                            backgroundColor: copied ? '#10B981' : '#F59E0B',
                                            color: '#FFFFFF',
                                            border: 'none',
                                            borderRadius: '10px',
                                            fontWeight: '700',
                                            cursor: 'pointer',
                                            transition: 'all 0.2s',
                                            whiteSpace: 'nowrap'
                                        }}
                                    >
                                        {copied ? '✓ Copied!' : 'Copy'}
                                    </button>
                                </div>
                                <p style={{ fontSize: '12px', color: '#B45309', marginTop: '12px' }}>
                                    Save this password! You can use it to log in to your account.
                                </p>
                            </div>
                        )}

                        <button
                            onClick={() => window.location.href = '/shop'}
                            style={{
                                padding: '16px 32px',
                                background: 'linear-gradient(135deg, #F59E0B 0%, #EAB308 100%)',
                                color: '#FFFFFF',
                                border: 'none',
                                borderRadius: '14px',
                                fontWeight: '700',
                                fontSize: '16px',
                                cursor: 'pointer',
                                boxShadow: '0 4px 14px -2px rgba(245, 158, 11, 0.4)'
                            }}
                        >
                            Continue Shopping
                        </button>
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} style={{ padding: '28px 32px 32px' }}>
                        {/* Order Summary Mini */}
                        <div style={{
                            backgroundColor: '#FFFBEB',
                            borderRadius: '14px',
                            padding: '16px 20px',
                            marginBottom: '24px',
                            border: '1px solid #FDE68A',
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center'
                        }}>
                            <span style={{ color: '#78716C', fontWeight: '500' }}>{items.length} item(s)</span>
                            <span style={{ fontSize: '20px', fontWeight: '700', color: '#B45309' }}>{formatPrice(total / 100)}</span>
                        </div>

                        {/* Contact Method Selection */}
                        <div style={{ marginBottom: '24px' }}>
                            <label style={{
                                display: 'block',
                                fontSize: '11px',
                                fontWeight: '700',
                                color: '#9CA3AF',
                                textTransform: 'uppercase',
                                letterSpacing: '0.1em',
                                marginBottom: '12px'
                            }}>How would you like to order?</label>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }}>
                                <button
                                    type="button"
                                    onClick={() => setFormData({ ...formData, method: 'whatsapp' })}
                                    style={{
                                        padding: '18px 16px',
                                        borderRadius: '14px',
                                        border: formData.method === 'whatsapp' ? '2px solid #22C55E' : '2px solid #E5E7EB',
                                        backgroundColor: formData.method === 'whatsapp' ? '#F0FDF4' : '#FFFFFF',
                                        color: formData.method === 'whatsapp' ? '#16A34A' : '#6B7280',
                                        cursor: 'pointer',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '8px',
                                        transition: 'all 0.2s'
                                    }}
                                >
                                    <WhatsAppIcon />
                                    <span style={{ fontWeight: '700', fontSize: '14px' }}>WhatsApp</span>
                                    <span style={{ fontSize: '11px', opacity: 0.7 }}>Instant chat</span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setFormData({ ...formData, method: 'email' })}
                                    style={{
                                        padding: '18px 16px',
                                        borderRadius: '14px',
                                        border: formData.method === 'email' ? '2px solid #3B82F6' : '2px solid #E5E7EB',
                                        backgroundColor: formData.method === 'email' ? '#EFF6FF' : '#FFFFFF',
                                        color: formData.method === 'email' ? '#2563EB' : '#6B7280',
                                        cursor: 'pointer',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '8px',
                                        transition: 'all 0.2s'
                                    }}
                                >
                                    <EmailIcon />
                                    <span style={{ fontWeight: '700', fontSize: '14px' }}>Email</span>
                                    <span style={{ fontSize: '11px', opacity: 0.7 }}>We'll call you</span>
                                </button>
                            </div>
                        </div>

                        {/* Form Fields */}
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '18px' }}>
                            <div>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: '700', color: '#9CA3AF', textTransform: 'uppercase', letterSpacing: '0.1em', marginBottom: '8px' }}>Full Name</label>
                                <input
                                    required
                                    type="text"
                                    placeholder="John Doe"
                                    style={inputStyle}
                                    value={formData.name}
                                    onChange={e => setFormData({ ...formData, name: e.target.value })}
                                    onFocus={e => { e.target.style.borderColor = '#FBBF24'; e.target.style.boxShadow = '0 0 0 4px rgba(251, 191, 36, 0.15)'; }}
                                    onBlur={e => { e.target.style.borderColor = '#E5E7EB'; e.target.style.boxShadow = 'none'; }}
                                />
                            </div>
                            <div>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: '700', color: '#9CA3AF', textTransform: 'uppercase', letterSpacing: '0.1em', marginBottom: '8px' }}>Email Address</label>
                                <input
                                    required
                                    type="email"
                                    placeholder="john@example.com"
                                    style={inputStyle}
                                    value={formData.email}
                                    onChange={e => setFormData({ ...formData, email: e.target.value })}
                                    onFocus={e => { e.target.style.borderColor = '#FBBF24'; e.target.style.boxShadow = '0 0 0 4px rgba(251, 191, 36, 0.15)'; }}
                                    onBlur={e => { e.target.style.borderColor = '#E5E7EB'; e.target.style.boxShadow = 'none'; }}
                                />
                            </div>
                            <div>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: '700', color: '#9CA3AF', textTransform: 'uppercase', letterSpacing: '0.1em', marginBottom: '8px' }}>Phone Number</label>
                                <input
                                    required
                                    type="tel"
                                    placeholder="+263 77 123 4567"
                                    style={inputStyle}
                                    value={formData.phone}
                                    onChange={e => setFormData({ ...formData, phone: e.target.value })}
                                    onFocus={e => { e.target.style.borderColor = '#FBBF24'; e.target.style.boxShadow = '0 0 0 4px rgba(251, 191, 36, 0.15)'; }}
                                    onBlur={e => { e.target.style.borderColor = '#E5E7EB'; e.target.style.boxShadow = 'none'; }}
                                />
                            </div>

                            {/* Create Account Checkbox */}
                            <div style={{
                                backgroundColor: '#F0FDF4',
                                border: '1px solid #BBF7D0',
                                borderRadius: '14px',
                                padding: '16px 18px',
                                display: 'flex',
                                alignItems: 'flex-start',
                                gap: '14px',
                                cursor: 'pointer'
                            }} onClick={() => setFormData({ ...formData, createAccount: !formData.createAccount })}>
                                <input
                                    type="checkbox"
                                    checked={formData.createAccount}
                                    onChange={e => setFormData({ ...formData, createAccount: e.target.checked })}
                                    style={{
                                        width: '22px',
                                        height: '22px',
                                        accentColor: '#22C55E',
                                        marginTop: '2px',
                                        cursor: 'pointer'
                                    }}
                                />
                                <div>
                                    <span style={{ fontWeight: '600', color: '#166534', fontSize: '15px', display: 'block', marginBottom: '4px' }}>
                                        Create an account
                                    </span>
                                    <span style={{ fontSize: '13px', color: '#4ADE80', lineHeight: '1.4' }}>
                                        Track orders and checkout faster next time
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Buttons */}
                        <div style={{ display: 'flex', gap: '12px', marginTop: '28px' }}>
                            <button
                                type="button"
                                onClick={onCancel}
                                style={{
                                    flex: 1,
                                    padding: '18px',
                                    color: '#6B7280',
                                    fontWeight: '700',
                                    fontSize: '16px',
                                    backgroundColor: '#F3F4F6',
                                    border: 'none',
                                    borderRadius: '14px',
                                    cursor: 'pointer',
                                    transition: 'all 0.2s'
                                }}
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={submitting}
                                style={{
                                    flex: 1.5,
                                    padding: '18px',
                                    fontWeight: '700',
                                    fontSize: '16px',
                                    border: 'none',
                                    borderRadius: '14px',
                                    cursor: submitting ? 'not-allowed' : 'pointer',
                                    transition: 'all 0.2s',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    gap: '10px',
                                    background: formData.method === 'whatsapp'
                                        ? 'linear-gradient(135deg, #22C55E 0%, #16A34A 100%)'
                                        : 'linear-gradient(135deg, #F59E0B 0%, #EAB308 100%)',
                                    color: '#FFFFFF',
                                    boxShadow: formData.method === 'whatsapp'
                                        ? '0 4px 14px -2px rgba(34, 197, 94, 0.4)'
                                        : '0 4px 14px -2px rgba(245, 158, 11, 0.4)'
                                }}
                            >
                                {submitting ? (
                                    <>
                                        <svg style={{ width: '20px', height: '20px', animation: 'spin 1s linear infinite' }} viewBox="0 0 24 24">
                                            <circle style={{ opacity: 0.25 }} cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
                                            <path style={{ opacity: 0.75 }} fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Processing...
                                    </>
                                ) : formData.method === 'whatsapp' ? (
                                    <>
                                        <WhatsAppIcon /> Order via WhatsApp
                                    </>
                                ) : (
                                    'Place Order'
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
        <div className={`group flex gap-5 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-100 transition-all duration-300 ${isRemoving ? 'opacity-0 scale-95 -translate-x-4' : 'opacity-100 scale-100 translate-x-0'}`}>
            {/* Image */}
            <div className="w-28 h-28 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                <img src={item.image} alt={item.title} className="w-full h-full object-cover" />
            </div>

            {/* Details */}
            <div className="flex-1 flex flex-col justify-between">
                <div>
                    <div className="flex justify-between items-start mb-1">
                        <h3 className="text-lg font-bold text-gray-900 group-hover:text-amber-700 transition-colors" dangerouslySetInnerHTML={{ __html: item.title }} />
                        <span className="text-xl font-bold text-amber-600">{formatPrice((item.price * item.quantity) / 100)}</span>
                    </div>
                    <p className="text-gray-400 text-sm">{formatPrice(item.price / 100)} each</p>
                </div>

                <div className="flex items-center justify-between mt-3">
                    {/* Quantity Controls */}
                    <div className="flex items-center bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                        <button
                            onClick={() => onUpdateQuantity(item.id, item.quantity - 1)}
                            className="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-amber-100 hover:text-amber-700 transition-all font-bold text-lg"
                        >
                            −
                        </button>
                        <span className="w-12 text-center font-bold text-gray-800">{item.quantity}</span>
                        <button
                            onClick={() => onUpdateQuantity(item.id, item.quantity + 1)}
                            className="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-amber-100 hover:text-amber-700 transition-all font-bold text-lg"
                        >
                            +
                        </button>
                    </div>

                    {/* Remove Button */}
                    <button
                        onClick={handleRemove}
                        className="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
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
                            <h1 className="text-4xl font-serif text-gray-800 mb-4 mt-6">Your Cart is Empty</h1>
                            <p className="text-gray-500 text-lg mb-8 max-w-md mx-auto">
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
        <div className="bg-gradient-to-b from-amber-50/30 to-white min-h-screen">
            <div className="container mx-auto px-4 py-12 lg:py-16">
                {/* Header */}
                <div className={`mb-10 transition-all duration-700 ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    <h1 className="text-4xl lg:text-5xl font-serif text-gray-900 mb-2">Your Cart</h1>
                    <p className="text-gray-500 text-lg">{items.length} item{items.length !== 1 ? 's' : ''} • Ready to order</p>
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
                        <div className={`bg-white p-8 rounded-3xl border border-gray-100 shadow-xl sticky top-8 transition-all duration-700 ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`} style={{ transitionDelay: '300ms' }}>
                            {/* Decorative Element */}
                            <div className="absolute top-0 left-8 right-8 h-1 bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-400 rounded-full"></div>

                            <h3 className="text-2xl font-serif font-bold text-gray-900 mb-6 pt-2">Order Summary</h3>

                            {/* Items List Mini */}
                            <div className="space-y-3 mb-6 max-h-48 overflow-y-auto">
                                {items.map(item => (
                                    <div key={item.id} className="flex justify-between text-sm">
                                        <span className="text-gray-600 truncate flex-1 mr-2" dangerouslySetInnerHTML={{ __html: `${item.title} × ${item.quantity}` }} />
                                        <span className="text-gray-900 font-medium">{formatPrice((item.price * item.quantity) / 100)}</span>
                                    </div>
                                ))}
                            </div>

                            <div className="border-t border-dashed border-gray-200 pt-4 mb-6">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="text-gray-500">Subtotal</span>
                                    <span className="font-medium">{formatPrice(total / 100)}</span>
                                </div>
                                <div className="flex justify-between items-center text-sm text-gray-400">
                                    <span>Shipping</span>
                                    <span>Calculated at checkout</span>
                                </div>
                            </div>

                            <div className="border-t border-gray-200 pt-4 mb-8">
                                <div className="flex justify-between items-center">
                                    <span className="text-xl font-bold text-gray-900">Total</span>
                                    <span className="text-2xl font-bold text-amber-600">{formatPrice(total / 100)}</span>
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
