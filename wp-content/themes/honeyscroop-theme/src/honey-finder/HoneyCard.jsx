import React from 'react';
import { Leaf, Droplets, MapPin } from 'lucide-react';

const HoneyCard = ({ honey }) => {
    const meta = honey.honey_details || {};
    const price = meta.price ? (meta.price / 100).toFixed(2) : 'N/A';

    return (
        <div className="bg-white rounded-lg shadow-lg overflow-hidden border border-honey-100 hover:border-honey-300 transition-colors duration-300">
            <div className="h-48 bg-gradient-to-br from-honey-100 to-honey-300 flex items-center justify-center">
                {/* Placeholder for honey jar image */}
                <Droplets className="w-16 h-16 text-honey-600 opacity-50" />
            </div>

            <div className="p-6">
                <h3 className="text-xl font-heading font-bold text-honey-900 mb-2" dangerouslySetInnerHTML={{ __html: honey.title.rendered }} />

                <div className="space-y-2 mb-4">
                    <div className="flex items-center text-honey-700 text-sm">
                        <Leaf className="w-4 h-4 mr-2" />
                        <span>{meta.nectar_source || 'Unknown Source'}</span>
                    </div>
                    <div className="flex items-center text-honey-700 text-sm">
                        <MapPin className="w-4 h-4 mr-2" />
                        <span>{meta.region || 'Unknown Region'}</span>
                    </div>
                </div>

                <div className="flex justify-between items-center mt-4 pt-4 border-t border-honey-50">
                    <span className="text-2xl font-bold text-honey-600">${price}</span>
                    <button className="bg-honey-500 hover:bg-honey-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    );
};

export default HoneyCard;
