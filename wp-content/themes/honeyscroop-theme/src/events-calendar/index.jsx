import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Calendar, MapPin, Clock, ArrowRight } from 'lucide-react';

const EventsCalendar = () => {
    const [events, setEvents] = useState([]);
    const [loading, setLoading] = useState(true);

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

    if (loading) return null; // Let the PHP skeleton show

    return (
        <div className="container py-16 md:py-24">

            {/* Controls Bar */}
            <div className="flex flex-col md:flex-row justify-between items-center mb-12 pb-8 border-b border-gray-100">
                <h2 className="text-3xl font-serif font-bold text-gray-800 mb-6 md:mb-0">Upcoming Gatherings</h2>

                <div className="flex gap-4">
                    <button className="px-6 py-2 bg-honey-50 text-honey-900 font-bold rounded-full hover:bg-honey-100 transition-colors">
                        List View
                    </button>
                    <button className="px-6 py-2 bg-white border border-gray-200 text-gray-500 font-bold rounded-full hover:border-honey-200 hover:text-honey-600 transition-colors">
                        Month View
                    </button>
                </div>
            </div>

            {/* Events Grid */}
            <div className="grid grid-cols-1 gap-8">
                {events.map((event) => {
                    const startDate = event.meta?.event_start_date ? formatDate(event.meta.event_start_date) : formatDate(event.date);
                    const location = event.meta?.event_location || 'Honeyscoop HQ';
                    const price = event.meta?.event_price || 'Free';
                    const imageUrl = event._embedded?.['wp:featuredmedia']?.[0]?.source_url || null;

                    return (
                        <div key={event.id} className="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-honey-900/5 transition-all duration-300 md:flex">

                            {/* Date Badge (Mobile: Top, Desktop: Left) */}
                            <div className="md:w-32 bg-honey-50/50 flex flex-col items-center justify-center p-6 border-b md:border-b-0 md:border-r border-honey-100">
                                <span className="text-honey-600 font-bold text-5xl font-serif">{startDate.day}</span>
                                <span className="text-gray-500 font-bold tracking-widest text-sm mt-1">{startDate.month}</span>
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
                                <h3 className="text-2xl font-bold text-gray-900 mb-3 group-hover:text-honey-600 transition-colors" dangerouslySetInnerHTML={{ __html: event.title.rendered }} />

                                <div className="flex flex-wrap gap-4 text-sm text-gray-500 mb-6 font-medium">
                                    <div className="flex items-center gap-2">
                                        <Clock className="w-4 h-4 text-honey-400" />
                                        {startDate.time}
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <MapPin className="w-4 h-4 text-honey-400" />
                                        {location}
                                    </div>
                                </div>

                                <div className="text-gray-600 leading-relaxed mb-6 line-clamp-2" dangerouslySetInnerHTML={{ __html: event.excerpt.rendered }} />

                                <a href={event.link} className="inline-flex items-center gap-2 text-honey-600 font-bold hover:gap-3 transition-all uppercase text-sm tracking-wide">
                                    Event Details <ArrowRight className="w-4 h-4" />
                                </a>
                            </div>

                        </div>
                    );
                })}
            </div>

            {events.length === 0 && !loading && (
                <div className="text-center py-20">
                    <Calendar className="w-12 h-12 text-gray-300 mx-auto mb-4" />
                    <p className="text-gray-500 text-lg">No upcoming events found.</p>
                </div>
            )}
        </div>
    );
};

const root = document.getElementById('events-calendar-root');
if (root) {
    createRoot(root).render(<EventsCalendar />);
}
