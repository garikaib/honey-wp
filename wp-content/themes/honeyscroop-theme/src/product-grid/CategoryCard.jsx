import React, { useRef } from 'react';
import { createRoot } from 'react-dom/client';

const CategoryCard = ({ title, imageSrc, link, isLeftColumn, className = '' }) => {
    // Left Column (Index 0, 2) -> Text aligns to the RIGHT side (inner edge) -> justify-end
    // Right Column (Index 1, 3) -> Text aligns to the LEFT side (inner edge) -> justify-start
    const alignmentClass = isLeftColumn ? 'justify-end pr-12' : 'justify-start pl-12';
    
    const cardRef = useRef(null);
    const contentRef = useRef(null);

    const handleMouseMove = (e) => {
        if (!cardRef.current) return;

        const card = cardRef.current;
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        // Rotate X (up/down tilt) - inverted: slight movement
        const rotateX = ((y - centerY) / centerY) * -5; // Max -5 to 5 deg
        // Rotate Y (left/right tilt)
        const rotateY = ((x - centerX) / centerX) * 5; // Max -5 to 5 deg

        // Apply transform to the card figure (image container)
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        
        // Parallax effect for content
        if (contentRef.current) {
            contentRef.current.style.transform = `translateZ(20px) translateX(${rotateY * 1.5}px) translateY(${rotateX * 1.5}px)`;
        }
    };

    const handleMouseLeave = () => {
        if (!cardRef.current) return;
        
        // Reset transform
        cardRef.current.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        
        if (contentRef.current) {
            contentRef.current.style.transform = 'translateZ(0) translateX(0) translateY(0)';
        }
    };

    return (
        <a 
            href={link} 
            className={`card bg-transparent h-full perspective-container group ${className}`}
            style={{ perspective: '1000px' }}
        >
            <figure 
                ref={cardRef}
                onMouseMove={handleMouseMove}
                onMouseLeave={handleMouseLeave}
                className="relative h-full w-full shadow-xl group-hover:shadow-2xl transition-all duration-200 ease-out transform-gpu overflow-hidden rounded-box"
                style={{ transformStyle: 'preserve-3d' }}
            >
                <img 
                    src={imageSrc} 
                    alt={title} 
                    className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" 
                />
                
                {/* Dark Overlay */}
                <div className="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors duration-500 z-10 pointer-events-none"></div>
                
                {/* Content Overlay - Absolute Positioned */}
                <div 
                    className={`absolute inset-0 flex items-center ${alignmentClass} z-20 pointer-events-none`}
                    style={{ transformStyle: 'preserve-3d' }}
                >
                    <div 
                        ref={contentRef}
                        className="relative overflow-hidden transition-transform duration-200 ease-out"
                    >
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
