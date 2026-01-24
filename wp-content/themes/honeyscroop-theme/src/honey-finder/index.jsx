import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Search, Loader2 } from 'lucide-react';
import HoneyCard from './HoneyCard';

const HoneyFinder = () => {
    const [honeys, setHoneys] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [searchTerm, setSearchTerm] = useState('');

    useEffect(() => {
        // Data provided by wp_localize_script
        const { restUrl, nonce } = window.honeyscroopData || {};

        if (!restUrl) {
            setError('API configuration missing');
            setLoading(false);
            return;
        }

        const fetchHoneys = async () => {
            try {
                const response = await fetch(restUrl, {
                    headers: {
                        'X-WP-Nonce': nonce
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch honey varieties');

                const data = await response.json();
                setHoneys(data);
            } catch (err) {
                console.error(err);
                setError('Unable to load our honey collection. Please try again later.');
            } finally {
                setLoading(false);
            }
        };

        fetchHoneys();
    }, []);

    const filteredHoneys = honeys.filter(honey => {
        const title = honey.title.rendered.toLowerCase();
        const source = (honey.honey_details?.nectar_source || '').toLowerCase();
        const term = searchTerm.toLowerCase();
        return title.includes(term) || source.includes(term);
    });

    if (error) {
        return (
            <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-center">
                {error}
            </div>
        );
    }

    return (
        <div className="max-w-6xl mx-auto px-4 py-12">
            <div className="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
                <div>
                    <h2 className="text-3xl font-heading font-bold text-honey-900">Find Your Honey</h2>
                    <p className="text-honey-600 mt-2">Explore our collection of pure, organic honey.</p>
                </div>

                <div className="relative w-full md:w-72">
                    <input
                        type="text"
                        placeholder="Search varieties..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="w-full pl-10 pr-4 py-2 border border-honey-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-honey-400 focus:border-transparent"
                    />
                    <Search className="absolute left-3 top-2.5 text-honey-400 w-5 h-5" />
                </div>
            </div>

            {loading ? (
                <div className="flex justify-center items-center py-20">
                    <Loader2 className="w-10 h-10 text-honey-500 animate-spin" />
                </div>
            ) : (
                <>
                    {filteredHoneys.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {filteredHoneys.map(honey => (
                                <HoneyCard key={honey.id} honey={honey} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-honey-50 rounded-lg border border-honey-100">
                            <p className="text-honey-800 text-lg">No honey varieties found matching your search.</p>
                        </div>
                    )}
                </>
            )}
        </div>
    );
};

// Mount the app
const container = document.getElementById('honey-finder-root');
if (container) {
    const root = createRoot(container);
    root.render(<HoneyFinder />);
}
