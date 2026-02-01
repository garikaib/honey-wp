<?php
/**
 * Template Name: Contact Page
 *
 * @package HoneyScroop_Theme
 */

get_header();
?>

<main id="primary" class="site-main bg-honey-50/30 min-h-screen">
    
    <!-- Hero Section -->
    <div class="contact-hero py-20 text-center relative overflow-hidden">
        <!-- Bee Swarm Container -->
        <div id="bee-swarm" class="absolute inset-0 z-0 pointer-events-none"></div>

        <div class="container relative z-10">
            <span class="inline-block py-1 px-3 rounded-full bg-honey-100 text-honey-800 text-xs font-bold tracking-widest uppercase mb-4">Get In Touch</span>
            <h1 class="text-5xl md:text-6xl font-serif font-bold text-honey-900 mb-6">Let's Create Some Buzz</h1>
            <p class="text-xl text-gray-800 max-w-2xl mx-auto">Whether you're looking for distribution, support, or just want to talk about bees, we're all ears.</p>
        </div>
        <!-- Decorative Background Element -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-yellow-400/5 rounded-full blur-3xl -z-10"></div>
    </div>

    <!-- Contact Grid -->
    <div class="container pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left: Contact Form (Glassmorphism) -->
            <div class="lg:col-span-7 bg-white/80 backdrop-blur-md p-8 md:p-10 rounded-2xl shadow-xl shadow-honey-900/5 border border-white">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Bee Mail</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Name</label>
                            <input type="text" id="name" class="w-full px-4 py-3 rounded-lg bg-gray-50 border-transparent focus:border-honey-500 focus:bg-white focus:ring-2 focus:ring-honey-500/20 transition duration-200 placeholder-gray-500" placeholder="Your Name">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" id="email" class="w-full px-4 py-3 rounded-lg bg-gray-50 border-transparent focus:border-honey-500 focus:bg-white focus:ring-2 focus:ring-honey-500/20 transition duration-200 placeholder-gray-500" placeholder="you@example.com">
                        </div>
                    </div>
                    <div>
                        <label for="subject-trigger" class="block text-sm font-semibold text-gray-900 mb-2">Subject</label>
                        <div class="relative" id="custom-subject-dropdown" x-data="{ open: false, selected: 'General Inquiry' }">
                            <!-- Hidden input for form submission -->
                            <input type="hidden" name="subject" :value="selected">
                            
                            <!-- Trigger Button -->
                            <button 
                                type="button" 
                                id="subject-trigger"
                                @click="open = !open"
                                @click.away="open = false"
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border-transparent focus:border-honey-500 focus:bg-white focus:ring-2 focus:ring-honey-500/20 transition duration-200 text-gray-900 flex items-center justify-between text-left"
                                aria-haspopup="listbox"
                                :aria-expanded="open">
                                <span x-text="selected">General Inquiry</span>
                                <svg class="w-5 h-5 text-honey-600 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Options Listbox -->
                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-honey-100 overflow-hidden"
                                role="listbox">
                                <ul class="py-1">
                                    <template x-for="option in ['General Inquiry', 'Wholesale / Distribution', 'Product Support', 'Just Saying Hi']">
                                        <li 
                                            @click="selected = option; open = false"
                                            class="px-4 py-3 text-sm text-gray-700 cursor-pointer hover:bg-honey-50 hover:text-honey-900 transition-colors duration-150 flex items-center justify-between"
                                            :class="{ 'bg-honey-100 text-honey-900 font-semibold': selected === option }"
                                            role="option"
                                            :aria-selected="selected === option">
                                            <span x-text="option"></span>
                                            <svg x-show="selected === option" class="w-4 h-4 text-honey-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">Your Message</label>
                        <textarea id="message" rows="5" class="w-full px-4 py-3 rounded-lg bg-gray-50 border-transparent focus:border-honey-500 focus:bg-white focus:ring-2 focus:ring-honey-500/20 transition duration-200 placeholder-gray-500" placeholder="Tell us what's on your mind..."></textarea>
                    </div>
                    <button type="button" class="w-full bg-honey-700 hover:bg-honey-800 text-white font-bold py-4 rounded-lg shadow-lg shadow-honey-900/20 transition duration-300 transform hover:-translate-y-0.5 focus:ring-4 focus:ring-honey-500/30 outline-none">
                        Fly Message to the Hive
                    </button>
                </form>
            </div>

            <!-- Right: Contact Info Cards -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Card 1: Address -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-start space-x-5 transition hover:shadow-md">
                    <div class="p-3 bg-honey-100 text-honey-700 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">The Hive HQ</h4>
                        <p class="text-gray-800 leading-relaxed">123 Apiary Lane<br>Golden Valley, CA 90210</p>
                    </div>
                </div>

                <!-- Card 2: Email -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-start space-x-5 transition hover:shadow-md">
                    <div class="p-3 bg-honey-100 text-honey-700 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">Digital Honey</h4>
                        <p class="text-gray-800">hello@honeyscoop.com</p>
                        <p class="text-gray-600 text-sm mt-1">We reply within 24 hours.</p>
                    </div>
                </div>

                <!-- Card 3: Phone -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-start space-x-5 transition hover:shadow-md">
                    <div class="p-3 bg-honey-100 text-honey-700 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">Buzz Line</h4>
                        <p class="text-gray-800">+1 (555) 123-4567</p>
                        <p class="text-gray-600 text-sm mt-1">Mon-Fri, 9am - 5pm EST</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php
get_footer();
