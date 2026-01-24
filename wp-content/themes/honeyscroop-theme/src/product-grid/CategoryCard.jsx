import React from 'react';
import { createRoot } from 'react-dom/client';

const CategoryCard = ({ title, imageSrc, link, isLeftColumn, className = '' }) => {
    // Left Column (Index 0, 2) -> Text aligns to the RIGHT side (inner edge) -> justify-end
    // Right Column (Index 1, 3) -> Text aligns to the LEFT side (inner edge) -> justify-start
    const alignmentClass = isLeftColumn ? 'justify-end pr-12' : 'justify-start pl-12';

    return (
        <a
            href={link}
            className={`card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-[1.01] transition-all duration-500 overflow-hidden group h-full ${className}`}
        >
            <figure className="relative h-full w-full">
                <img
                    src={imageSrc}
                    alt={title}
                    className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                />

                {/* Dark Overlay */}
                <div className="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors duration-500 z-10"></div>

                {/* Content Overlay - Absolute Positioned */}
                <div className={`absolute inset-0 flex items-center ${alignmentClass} z-20`}>
                    <div className="relative overflow-hidden group-hover:-translate-y-1 transition-transform duration-300">
                        <span className="inline-block bg-white/95 text-honey-900 border-b-4 border-honey-500 text-lg md:text-xl font-heading font-medium px-8 py-4 shadow-lg uppercase tracking-[0.15em] backdrop-blur-sm">
                            {title}
                        </span>
                    </div>
                </div>
            </figure>
        </a>
    );
};

export default CategoryCard;
