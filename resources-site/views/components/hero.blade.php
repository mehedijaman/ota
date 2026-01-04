<!-- HERO -->
<section class="relative min-h-[650px] flex items-center pt-32 pb-20 overflow-hidden isolate">
    <!-- Background Image -->
    <div class="absolute inset-0 -z-20">
        <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Travel Background" class="w-full h-full object-cover">
    </div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 tracking-tight drop-shadow-xl">
                Discover Your Next <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-300 to-cyan-200">Adventure</span>
            </h1>
            <p class="text-lg md:text-xl text-blue-50 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md">
                Find the best deals on flights, hotels, tours, and more. Experience the world with Triptonic.
            </p>
        </div>

        <!-- SEARCH CARD -->
        <div class="rounded-3xl shadow-2xl overflow-hidden max-w-5xl mx-auto backdrop-blur-md bg-white/95 border border-white/20 ring-1 ring-black/5">
            <div class="flex flex-col h-auto">
                
                <!-- Mobile Tabs (Horizontal Scroll Strip) -->
                <div class="md:hidden w-full bg-slate-50/80 border-b border-gray-200/50 overflow-x-auto">
                    <div class="flex whitespace-nowrap p-3 gap-2" id="mobile-tabs">
                         <button onclick="switchTab('flight')" class="tab-btn-mobile active shrink-0 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all text-blue-600 bg-blue-100/50 ring-1 ring-blue-200 flex items-center justify-center gap-2 min-w-[90px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Flight
                        </button>
                        <button onclick="switchTab('hotel')" class="tab-btn-mobile shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all flex items-center justify-center gap-2 min-w-[90px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                             Hotel
                        </button>
                        <button onclick="switchTab('tour')" class="tab-btn-mobile shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all flex items-center justify-center gap-2 min-w-[90px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                             Tour
                        </button>
                        <button onclick="switchTab('visa')" class="tab-btn-mobile shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all flex items-center justify-center gap-2 min-w-[90px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                             Visa
                        </button>
                    </div>
                </div>

                <!-- Desktop Tabs (Top) -->
                <div class="hidden md:flex w-full border-b border-gray-200/50 bg-white/50">
                    <div class="flex flex-1 overflow-x-auto" id="desktop-tabs">
                        <button onclick="switchTab('flight')" class="tab-btn-desktop flex-1 py-5 px-6 text-sm font-bold text-blue-600 border-b-2 border-blue-600 hover:bg-blue-50/50 transition-all flex items-center justify-center gap-2.5">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                           Flight
                        </button>
                        <button onclick="switchTab('hotel')" class="tab-btn-desktop flex-1 py-5 px-6 text-sm font-medium text-slate-500 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-50/50 transition-all flex items-center justify-center gap-2.5">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                           Hotel
                        </button>
                        <button onclick="switchTab('tour')" class="tab-btn-desktop flex-1 py-5 px-6 text-sm font-medium text-slate-500 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-50/50 transition-all flex items-center justify-center gap-2.5">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                           Tour
                        </button>
                        <button onclick="switchTab('visa')" class="tab-btn-desktop flex-1 py-5 px-6 text-sm font-medium text-slate-500 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-50/50 transition-all flex items-center justify-center gap-2.5">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                           Visa
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6 md:p-10 bg-white/80">
                <!-- Flight Form -->
                <div id="tab-flight" class="tab-content block space-y-8 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="space-y-1.5 relative">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">From</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700" placeholder="City or Airport">
                            </div>
                        </div>
                        <div class="space-y-1.5 relative">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">To</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700" placeholder="City or Airport">
                            </div>
                        </div>
                        <div class="space-y-1.5 relative">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Departure</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700">
                            </div>
                        </div>
                        <div class="space-y-1.5 relative">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Return</label>
                             <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-end justify-between gap-5 pt-2 border-t border-slate-100">
                        <div class="w-full md:w-auto">
                             <div class="space-y-1.5 w-full md:w-56">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Passengers</label>
                                <div class="relative group">
                                     <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700">
                                        <option>1 Passenger</option>
                                        <option>2 Passengers</option>
                                        <option>3+ Passengers</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="w-full md:w-auto bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Search Flights
                        </button>
                    </div>
                </div>

                <!-- Hotel Form -->
                <div id="tab-hotel" class="tab-content hidden space-y-8 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="space-y-1.5 md:col-span-4 lg:col-span-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Destination</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700" placeholder="Where are you going?">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Check-in</label>
                            <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium">
                        </div>
                         <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Check-out</label>
                            <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium">
                        </div>
                         <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Guests</label>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium">
                                <option>2 Adults, 1 Room</option>
                                <option>1 Adult, 1 Room</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-slate-100">
                         <button class="w-full md:w-auto bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Search Hotels
                        </button>
                    </div>
                </div>

                <!-- Tour Form -->
                <div id="tab-tour" class="tab-content hidden space-y-6 animate-fade-in">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5 md:col-span-2">
                             <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Destination</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium" placeholder="Where do you want to go?">
                        </div>
                         <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Date</label>
                            <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium">
                        </div>
                         <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Duration</label>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium">
                                <option>Any</option>
                                <option>1-3 Days</option>
                                <option>4-7 Days</option>
                                <option>7+ Days</option>
                            </select>
                        </div>
                     </div>
                     <div class="flex justify-end pt-4 border-t border-slate-100">
                         <button class="w-full md:w-auto bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Search Tours
                        </button>
                    </div>
                </div>

                <!-- Visa Form -->
                <div id="tab-visa" class="tab-content hidden space-y-6 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">I am a citizen of</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium" placeholder="Your Country">
                        </div>
                         <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Travelling to</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 text-slate-700 font-medium" placeholder="Destination Country">
                        </div>
                    </div>
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                         <button class="w-full md:w-auto bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Check Visa Requirements
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function switchTab(tabName) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        // Show selected content
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        // Update Mobile Tabs
        document.querySelectorAll('.tab-btn-mobile').forEach(btn => {
            btn.classList.remove('text-blue-600', 'bg-blue-100/50', 'ring-1', 'ring-blue-200');
            btn.classList.add('text-slate-600');
            if(btn.getAttribute('onclick').includes(tabName)) {
                 btn.classList.remove('text-slate-600');
                 btn.classList.add('text-blue-600', 'bg-blue-100/50', 'ring-1', 'ring-blue-200');
            }
        });

        // Update Desktop Tabs
        document.querySelectorAll('.tab-btn-desktop').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-blue-600');
            btn.classList.add('text-slate-500', 'border-transparent');
             if(btn.getAttribute('onclick').includes(tabName)) {
                 btn.classList.remove('text-slate-500', 'border-transparent');
                 btn.classList.add('text-blue-600', 'border-blue-600');
            }
        });
    }
</script>