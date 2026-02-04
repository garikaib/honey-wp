import React from 'react';
import { Star } from 'lucide-react';

const StarRating = ({ rating, max = 5, size = 16, interactive = false, onRatingChange }) => {
    return (
        <div className="flex items-center gap-0.5">
            {[...Array(max)].map((_, i) => {
                const starValue = i + 1;
                const isFilled = starValue <= rating;
                const isHalf = !isFilled && starValue - 0.5 <= rating;

                return (
                    <button
                        key={i}
                        type="button"
                        disabled={!interactive}
                        onClick={() => interactive && onRatingChange(starValue)}
                        className={`transition-all duration-200 ${interactive ? 'hover:scale-110 active:scale-95' : 'cursor-default'}`}
                    >
                        <Star
                            size={size}
                            className={`
                                ${isFilled ? 'fill-honey-500 text-honey-500' : isHalf ? 'fill-honey-500/50 text-honey-500' : 'text-gray-300 dark:text-gray-600'}
                                ${interactive && !isFilled ? 'hover:text-honey-400' : ''}
                            `}
                            strokeWidth={isFilled || isHalf ? 0 : 1.5}
                        />
                    </button>
                );
            })}
        </div>
    );
};

export default StarRating;
