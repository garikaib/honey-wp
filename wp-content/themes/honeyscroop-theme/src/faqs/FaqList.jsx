import React, { useState, useEffect } from 'react';
import { ChevronDown, Plus, Minus } from 'lucide-react';

const FaqList = () => {
    const [faqs, setFaqs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [openIndex, setOpenIndex] = useState(null);

    useEffect(() => {
        const fetchFaqs = async () => {
            try {
                // Use localized data if available, otherwise fallback (mostly for dev)
                const apiUrl = window.faqData?.restUrl || '/wp-json/wp/v2/faq';
                const res = await fetch(apiUrl + '?per_page=100');
                const data = await res.json();
                setFaqs(data);
            } catch (error) {
                console.error('Error fetching FAQs:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchFaqs();
    }, []);

    const toggleFaq = (index) => {
        setOpenIndex(openIndex === index ? null : index);
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center min-h-[50vh]">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500"></div>
            </div>
        );
    }

    if (!faqs.length) {
        return (
            <div className="text-center py-20">
                <p className="text-gray-500 text-lg">No questions found right now. Check back later!</p>
            </div>
        );
    }

    return (
        <div className="max-w-3xl mx-auto px-4 py-20">
            {/* Header */}
            <div className="text-center mb-16 space-y-4">
                <span className="text-amber-600 font-bold uppercase tracking-wider text-sm">Got Questions?</span>
                <h1 className="text-5xl font-serif text-gray-900">Frequently Asked Questions</h1>
                <p className="text-gray-600 text-lg max-w-xl mx-auto">
                    Everything you need to know about our premium honey and delivery services.
                </p>
            </div>

            {/* Accordion */}
            <div className="space-y-4">
                {faqs.map((faq, index) => {
                    const isOpen = openIndex === index;

                    return (
                        <div
                            key={faq.id}
                            className={`bg-white rounded-2xl overflow-hidden transition-all duration-300 border ${isOpen ? 'border-amber-200 shadow-xl shadow-amber-900/5' : 'border-transparent shadow-sm hover:shadow-md'}`}
                        >
                            <button
                                onClick={() => toggleFaq(index)}
                                className="w-full text-left px-8 py-6 flex items-center justify-between group"
                            >
                                <span className={`font-serif text-xl transition-colors ${isOpen ? 'text-amber-800' : 'text-gray-800 group-hover:text-amber-700'}`}>
                                    {faq.title.rendered}
                                </span>
                                <div className={`p-2 rounded-full transition-all duration-300 ${isOpen ? 'bg-amber-100 text-amber-700 rotate-180' : 'bg-gray-50 text-gray-400 group-hover:bg-amber-50 group-hover:text-amber-600'}`}>
                                    <ChevronDown className="w-5 h-5" />
                                </div>
                            </button>

                            <div
                                className={`transition-all duration-500 ease-in-out overflow-hidden ${isOpen ? 'max-h-96 opacity-100' : 'max-h-0 opacity-0'}`}
                            >
                                <div
                                    className="px-8 pb-8 text-gray-600 leading-relaxed prose prose-amber max-w-none"
                                    dangerouslySetInnerHTML={{ __html: faq.content.rendered }}
                                />
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

export default FaqList;
