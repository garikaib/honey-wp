import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Calendar, MapPin, Clock, ArrowRight } from 'lucide-react';
import MonthView from './MonthView';

const EventsCalendar = () => {
    const [events, setEvents] = useState([]);
    const [loading, setLoading] = useState(true);
    const [viewMode, setViewMode] = useState('list'); // 'list' or 'month'
    const [currentDate, setCurrentDate] = useState(new Date());

    useEffect(() => {
        const fetchEvents = async () => {
            try {
                const response = await fetch('/wp-json/wp/v2/event?_embed&per_page=100');
                if (!response.ok) throw new Error('Failed to fetch events');
                const data = await response.json();
                setEvents(data);
            } catch (error) {
                console.error("Error loading events:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchEvents();
    }, []);

    // Format Date Helper
    const formatDate = (dateString) => {
        if (!dateString) return { day: '01', month: 'JAN', time: '10:00 AM' };
        const date = new Date(dateString);
        return {
            day: date.getDate().toString().padStart(2, '0'),
            month: date.toLocaleString('default', { month: 'short' }).toUpperCase(),
            time: date.toLocaleString('default', { hour: 'numeric', minute: '2-digit' })
        };
    };

    const handleMonthNavigate = (direction) => {
        const newDate = new Date(currentDate);
        if (direction === 'prev') {
            newDate.setMonth(newDate.getMonth() - 1);
        } else {
            newDate.setMonth(newDate.getMonth() + 1);
        }
        setCurrentDate(newDate);
    };

    if (loading) return null; // Let the PHP skeleton show

    return (
        <div className="container py-16 md:py-24">

            {/* Controls Bar */}
            <div class="flex flex-col md:flex-row justify-between items-center mb-12 pb-8 border-b border-gray-100 dark:border-white/10">
                <h2 class="text-3xl font-serif font-bold text-gray-800 dark:text-honey-50 mb-6 md:mb-0 transition-colors">Upcoming Gatherings</h2>

                <div className="flex gap-4">
                    <button
                        onClick={() => setViewMode('list')}
                        className={`px-6 py-2 rounded-full font-bold transition-all ${viewMode === 'list' ? 'bg-honey-100 dark:bg-honey-900/40 text-honey-900 dark:text-honey-100' : 'bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:border-honey-200 dark:hover:border-honey-500/50 hover:text-honey-600 dark:hover:text-honey-300'}`}
                    >
                        List View
                    </button>
                    <button
                        onClick={() => setViewMode('month')}
                        className={`px-6 py-2 rounded-full font-bold transition-all ${viewMode === 'month' ? 'bg-honey-100 dark:bg-honey-900/40 text-honey-900 dark:text-honey-100' : 'bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:border-honey-200 dark:hover:border-honey-500/50 hover:text-honey-600 dark:hover:text-honey-300'}`}
                    >
                        Month View
                    </button>
                </div>
            </div>

            {/* View Content */}
            {viewMode === 'list' ? (
                /* List View */
                <div className="grid grid-cols-1 gap-8">
                    {events.map((event) => {
                        const startDate = event.meta?.event_start_date ? formatDate(event.meta.event_start_date) : formatDate(event.date);
                        const location = event.meta?.event_location || 'Honeyscoop HQ';
                        const price = event.meta?.event_price || 'Free';
                        const imageUrl = event._embedded?.['wp:featuredmedia']?.[0]?.source_url || null;

                        return (
                            <div key={event.id} className="group bg-white dark:bg-surface-glass rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden hover:shadow-xl hover:shadow-honey-900/5 transition-all duration-300 md:flex">

                                {/* Date Badge (Mobile: Top, Desktop: Left) */}
                                <div class="md:w-32 bg-honey-50/50 dark:bg-amber-900/10 flex flex-col items-center justify-center p-6 border-b md:border-b-0 md:border-r border-honey-100 dark:border-white/10 transition-colors">
                                    <span class="text-honey-600 dark:text-honey-400 font-bold text-5xl font-serif transition-colors">{startDate.day}</span>
                                    <span class="text-gray-500 dark:text-gray-400 font-bold tracking-widest text-sm mt-1 transition-colors">{startDate.month}</span>
                                </div>

                                {/* Image (Hidden on mobile if small, or keep it) */}
                                {imageUrl && (
                                    <div className="md:w-72 h-48 md:h-auto relative overflow-hidden">
                                        <img
                                            src={imageUrl}
                                            alt={event.title.rendered}
                                            className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                                        />
                                        <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-honey-800 uppercase tracking-wide shadow-sm">
                                            {price}
                                        </div>
                                    </div>
                                )}

                                {/* Content */}
                                <div className="p-8 flex-1 flex flex-col justify-center">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-honey-50 mb-3 group-hover:text-honey-600 dark:group-hover:text-honey-400 transition-colors" dangerouslySetInnerHTML={{ __html: event.title.rendered }} />

                                    <div className="flex flex-wrap gap-4 text-sm text-gray-500 mb-6 font-medium">
                                        <div class="flex items-center gap-2">
                                            <Clock className="w-4 h-4 text-honey-400 dark:text-honey-300 transition-colors" />
                                            {startDate.time}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <MapPin className="w-4 h-4 text-honey-400 dark:text-honey-300 transition-colors" />
                                            {location}
                                        </div>
                                    </div>

                                    <div class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-2 transition-colors" dangerouslySetInnerHTML={{ __html: event.excerpt.rendered }} />

                                    <a href={event.link} className="inline-flex items-center gap-2 text-honey-600 font-bold hover:gap-3 transition-all uppercase text-sm tracking-wide">
                                        Event Details <ArrowRight className="w-4 h-4" />
                                    </a>
                                </div>

                            </div>
                        );
                    })}

                    {events.length === 0 && !loading && (
                        <div class="text-center py-20 transition-colors">
                            <Calendar class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-4" />
                            <p class="text-gray-500 dark:text-gray-400 text-lg">No upcoming events found.</p>
                        </div>
                    )}
                </div>
            ) : (
                /* Month View */
                <MonthView
                    events={events}
                    currentDate={currentDate}
                    onNavigate={handleMonthNavigate}
                    onEventClick={(link) => window.location.href = link}
                />
            )}
        </div>
    );
};

const root = document.getElementById('events-calendar-root');
if (root) {
    createRoot(root).render(<EventsCalendar />);
}
