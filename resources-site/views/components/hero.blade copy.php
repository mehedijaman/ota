<!-- HERO -->
<section class="relative bg-slate-900 min-h-[600px] flex items-center pt-24 pb-12 overflow-hidden isolate">
    <!-- Abstract Shapes/Gradient -->
    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-288.75" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-linear-to-t from-white to-transparent"></div>
    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-288.75" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 tracking-tight drop-shadow-lg">
                Discover Your Next <span class="text-cyan-400">Adventure</span>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto font-light">
                Find the best deals on flights, hotels, tours, and more. Experience the world with Triptonic.
            </p>
        </div>

        <!-- SEARCH CARD -->
        <div class="rounded-2xl shadow-2xl overflow-hidden max-w-5xl mx-auto ring-1 ring-white/20 backdrop-blur-sm bg-white/95">
            <div class="flex flex-col h-auto">
                <!-- Tabs (Mobile: Horizontal Scroll / Desktop: Top Horizontal) -->
                
                <!-- Mobile Tabs (Horizontal Scroll Strip) -->
                <div class="md:hidden w-full bg-slate-50 border-b border-gray-100 overflow-x-auto">
                    <div class="flex whitespace-nowrap p-2 gap-2" id="mobile-tabs">
                         <button onclick="switchTab('flight')" class="tab-btn-mobile active shrink-0 px-4 py-2 rounded-lg text-xs font-medium transition-colors text-blue-600 bg-blue-50 flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">✈️</span> Flight
                        </button>
                        <button onclick="switchTab('hotel')" class="tab-btn-mobile shrink-0 px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">🏨</span> Hotel
                        </button>
                        <button onclick="switchTab('tour')" class="tab-btn-mobile shrink-0 px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">🗺️</span> Tour
                        </button>
                        <button onclick="switchTab('visa')" class="tab-btn-mobile shrink-0 px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">🎫</span> Visa
                        </button>
                        <button onclick="switchTab('bill')" class="tab-btn-mobile shrink-0 px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">🧾</span> Bill
                        </button>
                        <button onclick="switchTab('medical')" class="tab-btn-mobile shrink-0 px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">🏥</span> Medical
                        </button>
                        <a href="https://12go.asia/en" target="_blank" class="shrink-0 px-4 py-2 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors flex flex-col items-center justify-center gap-1 min-w-[70px]">
                            <span class="text-xl">🚆</span> Transport
                        </a>
                    </div>
                </div>

                <!-- Desktop Tabs (Top) -->
                <div class="hidden md:flex w-full border-b border-gray-100 bg-slate-50">
                    <div class="flex flex-1 overflow-x-auto" id="desktop-tabs">
                        <button onclick="switchTab('flight')" class="tab-btn-desktop flex-1 py-4 px-6 text-sm font-medium text-blue-600 border-b-2 border-blue-600 hover:bg-blue-50 transition-colors flex items-center justify-center gap-2">
                           ✈️ Flight
                        </button>
                        <button onclick="switchTab('hotel')" class="tab-btn-desktop flex-1 py-4 px-6 text-sm font-medium text-slate-600 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                           🏨 Hotel
                        </button>
                        <button onclick="switchTab('tour')" class="tab-btn-desktop flex-1 py-4 px-6 text-sm font-medium text-slate-600 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                           🗺️ Tour
                        </button>
                        <button onclick="switchTab('visa')" class="tab-btn-desktop flex-1 py-4 px-6 text-sm font-medium text-slate-600 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                           🎫 Visa
                        </button>
                         <button onclick="switchTab('bill')" class="tab-btn-desktop flex-1 py-4 px-6 text-sm font-medium text-slate-600 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                           🧾 Bill
                        </button>
                        <button onclick="switchTab('medical')" class="tab-btn-desktop flex-1 py-4 px-6 text-sm font-medium text-slate-600 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                           🏥 Medical
                        </button>
                         <a href="https://12go.asia/en" target="_blank" class="flex-1 py-4 px-6 text-sm font-medium text-slate-600 border-b-2 border-transparent hover:text-blue-600 hover:bg-slate-100 transition-colors flex items-center justify-center gap-2">
                           🚆 Transport
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6 md:p-8 bg-white flex-1 relative">
                <!-- Flight Form -->
                <div id="tab-flight" class="tab-content block space-y-6 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">From</label>
                            <input class="w-full border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="City or Airport">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">To</label>
                            <input class="w-full border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="City or Airport">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Departure</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow text-gray-600">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Return</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow text-gray-600">
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-4">
                        <div class="w-full md:w-auto">
                             <div class="space-y-1 w-full md:w-48">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Passengers</label>
                                <select class="w-full border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-600">
                                    <option>1 Passenger</option>
                                    <option>2 Passengers</option>
                                    <option>3+ Passengers</option>
                                </select>
                            </div>
                        </div>
                        <button class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white py-3 px-8 rounded-lg font-bold shadow-lg shadow-orange-200 transition-transform hover:-translate-y-0.5 active:translate-y-0">
                            Search Flights
                        </button>
                    </div>
                </div>

                <!-- Hotel Form -->
                <div id="tab-hotel" class="tab-content hidden space-y-6 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1 md:col-span-3">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Destination</label>
                            <input class="w-full border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow" placeholder="Where are you going?">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Check-in</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg p-3 text-gray-600">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Check-out</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg p-3 text-gray-600">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Guests</label>
                            <select class="w-full border border-gray-200 rounded-lg p-3 bg-white text-gray-600">
                                <option>2 Adults, 1 Room</option>
                                <option>1 Adult, 1 Room</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                         <button class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white py-3 px-8 rounded-lg font-bold shadow-lg shadow-orange-200 transition-transform hover:-translate-y-0.5">
                            Search Hotels
                        </button>
                    </div>
                </div>

                <!-- Tour Form -->
                <div id="tab-tour" class="tab-content hidden space-y-6 animate-fade-in">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1 md:col-span-2">
                             <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Destination</label>
                            <input class="w-full border border-gray-200 rounded-lg p-3" placeholder="Where do you want to go?">
                        </div>
                         <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</label>
                            <input type="date" class="w-full border border-gray-200 rounded-lg p-3 text-gray-600">
                        </div>
                         <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</label>
                            <select class="w-full border border-gray-200 rounded-lg p-3 bg-white text-gray-600">
                                <option>Any</option>
                                <option>1-3 Days</option>
                                <option>4-7 Days</option>
                                <option>7+ Days</option>
                            </select>
                        </div>
                     </div>
                     <div class="flex justify-end mt-4">
                         <button class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white py-3 px-8 rounded-lg font-bold shadow-lg shadow-orange-200 transition-transform hover:-translate-y-0.5">
                            Search Tours
                        </button>
                    </div>
                </div>

                <!-- Visa Form -->
                <div id="tab-visa" class="tab-content hidden space-y-6 animate-fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">I am a citizen of</label>
                            <input class="w-full border border-gray-200 rounded-lg p-3" placeholder="Your Country">
                        </div>
                         <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Travelling to</label>
                            <input class="w-full border border-gray-200 rounded-lg p-3" placeholder="Destination Country">
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                         <button class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white py-3 px-8 rounded-lg font-bold shadow-lg shadow-orange-200 transition-transform hover:-translate-y-0.5">
                            Check Visa Requirements
                        </button>
                    </div>
                </div>
                
                 <!-- Bill Form -->
                <div id="tab-bill" class="tab-content hidden space-y-6 animate-fade-in">
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Pay your utility bills easily.</p>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-8 rounded-lg font-bold shadow-lg transition-transform hover:-translate-y-0.5">
                            Go to Bill Pay
                        </button>
                    </div>
                </div>
                
                <!-- Medical Form -->
                <div id="tab-medical" class="tab-content hidden space-y-6 animate-fade-in">
                    <div class="text-center py-8">
                         <p class="text-gray-500 mb-4">Find medical tourism packages.</p>
                         <button class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-8 rounded-lg font-bold shadow-lg transition-transform hover:-translate-y-0.5">
                            Explore Medical Services
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
            btn.classList.remove('text-blue-600', 'bg-blue-50');
            btn.classList.add('text-slate-600');
            // Check if this button activates the current tab (simple check by onclick attribute or we can add data attributes)
            if(btn.getAttribute('onclick').includes(tabName)) {
                 btn.classList.remove('text-slate-600');
                 btn.classList.add('text-blue-600', 'bg-blue-50');
            }
        });

        // Update Desktop Tabs
        document.querySelectorAll('.tab-btn-desktop').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
            btn.classList.add('text-slate-600', 'border-transparent');
             if(btn.getAttribute('onclick').includes(tabName)) {
                 btn.classList.remove('text-slate-600', 'border-transparent');
                 btn.classList.add('text-blue-600', 'border-blue-600');
            }
        });
    }
</script>