import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

const PartnerTicker = () => {
    const [partners, setPartners] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchPartners = async () => {
            try {
                // Fetch partners with embedded featured media
                const response = await fetch('/wp-json/wp/v2/partner?_embed&per_page=20');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                const formattedPartners = data.map(item => ({
                    id: item.id,
                    title: item.title.rendered,
                    logoUrl: item._embedded?.['wp:featuredmedia']?.[0]?.source_url || null
                })).filter(p => p.logoUrl); // Only show partners with logos

                setPartners(formattedPartners);
            } catch (error) {
                console.error('Error fetching partners:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchPartners();
    }, []);

    if (loading) return null;

    // Fallback demo partners if none are uploaded yet
    let displayPartners = partners.length > 0 ? partners : [
        { id: 'd1', title: 'Demo 1', isDemo: true },
        { id: 'd2', title: 'Demo 2', isDemo: true },
        { id: 'd3', title: 'Demo 3', isDemo: true },
        { id: 'd4', title: 'Demo 4', isDemo: true },
    ];

    // Ensure we have enough items for a smooth loop on wide screens (min 12 items)
    // This prevents the "sudden increase/gap" issue if the list is too short
    while (displayPartners.length < 12) {
        displayPartners = [...displayPartners, ...displayPartners.map(p => ({ ...p, id: p.id + '-dup-' + Math.random() }))];
    }

    return (
        <div className="w-full inline-flex flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]">
            <ul className="flex items-center justify-center md:justify-start [&_li]:mx-8 [&_img]:max-w-none animate-infinite-scroll">
                {/* First Set */}
                {displayPartners.map((partner) => (
                    <li key={partner.id}>
                        {partner.isDemo ? (
                            <div className="h-16 w-32 bg-gray-100 rounded flex items-center justify-center opacity-40 grayscale">
                                <span className="text-[10px] font-bold uppercase tracking-widest text-gray-400">Partner Placeholder</span>
                            </div>
                        ) : (
                            <img
                                src={partner.logoUrl}
                                alt={partner.title}
                                className="h-16 w-auto object-contain opacity-100 transition-all duration-300 hover:opacity-80 hover:grayscale"
                            />
                        )}
                    </li>
                ))}
            </ul>
            {/* Duplicate Set for Infinite Scroll */}
            <ul className="flex items-center justify-center md:justify-start [&_li]:mx-8 [&_img]:max-w-none animate-infinite-scroll" aria-hidden="true">
                {displayPartners.map((partner) => (
                    <li key={`${partner.id}-clone`}>
                        {partner.isDemo ? (
                            <div className="h-16 w-32 bg-gray-100 rounded flex items-center justify-center opacity-40 grayscale">
                                <span className="text-[10px] font-bold uppercase tracking-widest text-gray-400">Partner Placeholder</span>
                            </div>
                        ) : (
                            <img
                                src={partner.logoUrl}
                                alt={partner.title}
                                className="h-16 w-auto object-contain opacity-100 transition-all duration-300 hover:opacity-80 hover:grayscale"
                            />
                        )}
                    </li>
                ))}
            </ul>
        </div>
    );
};

const rootElement = document.getElementById('partner-ticker-root');
if (rootElement) {
    createRoot(rootElement).render(
        <React.StrictMode>
            <PartnerTicker />
        </React.StrictMode>
    );
}
