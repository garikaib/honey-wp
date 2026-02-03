import React, { useState, useMemo } from 'react';
import { ChevronLeft, ChevronRight, Clock, MapPin } from 'lucide-react';

const MonthView = ({ events, currentDate, onNavigate, onEventClick }) => {

    // Helper: Get days in month
    const getDaysInMonth = (year, month) => {
        return new Date(year, month + 1, 0).getDate();
    };

    // Helper: Get day of week for the 1st of the month (0 = Sunday)
    const getFirstDayOfMonth = (year, month) => {
        return new Date(year, month, 1).getDay();
    };

    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth();

    const daysInMonth = getDaysInMonth(currentYear, currentMonth);
    const firstDay = getFirstDayOfMonth(currentYear, currentMonth);

    // Generate calendar grid
    // We need 'firstDay' number of empty slots before the actual days
    // Then 'daysInMonth' slots for the days
    // Then maybe some padding at the end to complete the row (optional, but looks better)

    const calendarDays = useMemo(() => {
        const days = [];

        // Previous month padding
        for (let i = 0; i < firstDay; i++) {
            days.push({ type: 'padding', id: `pad-prev-${i}` });
        }

        // Current month days
        for (let i = 1; i <= daysInMonth; i++) {
            // Find events for this day
            // We need to parse event dates carefully
            // Assuming event.meta.event_start_date is YYYY-MM-DD or ISO

            const daysEvents = events.filter(event => {
                const dateStr = event.meta?.event_start_date || event.date;
                if (!dateStr) return false;
                const eventDate = new Date(dateStr);
                return eventDate.getDate() === i &&
                    eventDate.getMonth() === currentMonth &&
                    eventDate.getFullYear() === currentYear;
            });

            days.push({
                type: 'day',
                day: i,
                events: daysEvents,
                id: `day-${i}`
            });
        }

        return days;
    }, [currentYear, currentMonth, events]);

    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    const weekDays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    return (
        <div className="bg-white dark:bg-dark-surface rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm overflow-hidden animate-fade-in-up transition-colors">
            {/* Calendar Header */}
            <div className="flex items-center justify-between p-6 border-b border-gray-100 dark:border-white/10 bg-honey-50/30 dark:bg-white/5 transition-colors">
                <button
                    onClick={() => onNavigate('prev')}
                    className="p-2 hover:bg-honey-100 dark:hover:bg-honey-900/40 rounded-full transition-colors text-honey-800 dark:text-honey-300"
                    aria-label="Previous Month"
                >
                    <ChevronLeft className="w-6 h-6" />
                </button>

                <h3 className="text-2xl font-serif font-bold text-gray-800 dark:text-honey-50 transition-colors">
                    {monthNames[currentMonth]} <span className="text-honey-600 dark:text-honey-400">{currentYear}</span>
                </h3>

                <button
                    onClick={() => onNavigate('next')}
                    className="p-2 hover:bg-honey-100 dark:hover:bg-honey-900/40 rounded-full transition-colors text-honey-800 dark:text-honey-300"
                    aria-label="Next Month"
                >
                    <ChevronRight className="w-6 h-6" />
                </button>
            </div>

            {/* Weekday Headers */}
            <div className="grid grid-cols-7 border-b border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 transition-colors">
                {weekDays.map(day => (
                    <div key={day} className="py-3 text-center text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        {day}
                    </div>
                ))}
            </div>

            {/* Calendar Grid */}
            <div className="grid grid-cols-7 auto-rows-fr bg-gray-100 dark:bg-white/5 gap-px border-b border-gray-100 dark:border-white/10 transition-colors">
                {calendarDays.map((cell) => {
                    if (cell.type === 'padding') {
                        return <div key={cell.id} className="bg-gray-50/30 dark:bg-white/5 min-h-[120px] transition-colors" />;
                    }

                    const hasEvents = cell.events.length > 0;
                    const isToday = new Date().getDate() === cell.day &&
                        new Date().getMonth() === currentMonth &&
                        new Date().getFullYear() === currentYear;

                    return (
                        <div key={cell.id} className={`bg-white dark:bg-dark-surface min-h-[120px] p-2 hover:bg-honey-50/10 dark:hover:bg-honey-900/10 transition-colors relative group ${isToday ? 'bg-honey-50/30 dark:bg-honey-900/20' : ''}`}>
                            <span className={`inline-block w-7 h-7 text-center leading-7 rounded-full text-sm font-medium mb-1 transition-colors ${isToday ? 'bg-honey-500 text-white' : 'text-gray-500 dark:text-gray-400'}`}>
                                {cell.day}
                            </span>

                            <div className="space-y-1">
                                {cell.events.map(event => (
                                    <div
                                        key={event.id}
                                        onClick={() => onEventClick && onEventClick(event.link)}
                                        className="text-xs p-1.5 rounded bg-honey-100/50 dark:bg-honey-900/40 text-honey-900 dark:text-honey-100 border-l-2 border-honey-400 cursor-pointer hover:bg-honey-200 dark:hover:bg-honey-800/60 transition-colors truncate"
                                        title={event.title.rendered}
                                    >
                                        <div className="font-bold truncate" dangerouslySetInnerHTML={{ __html: event.title.rendered }} />
                                        <div className="text-[10px] text-honey-700 dark:text-honey-300 flex items-center gap-1">
                                            <Clock className="w-3 h-3" />
                                            {new Date(event.meta?.event_start_date || event.date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                })}
            </div>

            {/* Mobile Helper Text */}
            <div className="p-4 text-center text-xs text-gray-400 md:hidden">
                Tap an event to view details
            </div>
        </div>
    );
};

export default MonthView;
