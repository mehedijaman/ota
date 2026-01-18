<!-- HERO -->
<section class="relative min-h-[400px] md:min-h-[500px] flex items-center pt-20 md:pt-24 pb-8 overflow-hidden isolate">
    <!-- Background Image -->
    <div class="absolute inset-0 -z-20">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
            alt="Travel Background" class="w-full h-full object-cover">
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 -z-10 bg-linear-to-b from-slate-900/40 via-slate-900/30 to-slate-900/60"></div>

    <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header Text -->
        <div class="text-center mb-4 md:mb-6">
            <h1 class="text-2xl md:text-4xl font-light text-white mb-1 md:mb-2">
                Welcome to <span class="font-bold">TriptonicBD!</span>
            </h1>
            <p class="text-xs md:text-sm text-white/80">
                Find Flights, Hotels, Visa & Holidays
            </p>
        </div>

        <!-- SEARCH CARD -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">

            <!-- Tabs with scroll indicator -->
            <div class="border-b border-slate-200 relative">
                <div class="overflow-x-auto scrollbar-hide" id="tabs-container">
                    <div class="flex min-w-max" id="service-tabs">
                        @php
                            $tabs = [
                                ['id' => 'flight', 'label' => 'Flight', 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                                ['id' => 'hotel', 'label' => 'Hotel', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                ['id' => 'tour', 'label' => 'Holiday', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['id' => 'visa', 'label' => 'Visa', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                                ['id' => 'medical', 'label' => 'Medical', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                ['id' => 'transport', 'label' => 'Transport', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                                ['id' => 'recharge', 'label' => 'Recharge', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                ['id' => 'bill', 'label' => 'Pay Bill', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
                            ];
                        @endphp
                        @foreach($tabs as $idx => $tab)
                            <button onclick="switchTab('{{ $tab['id'] }}')"
                                class="service-tab shrink-0 py-2.5 md:py-3 px-3 md:px-4 text-xs md:text-sm font-medium transition-all flex flex-col md:flex-row items-center gap-1 md:gap-2 border-b-2 whitespace-nowrap
                                {{ $idx === 0 ? 'text-orange-600 border-orange-500' : 'text-slate-600 border-transparent hover:text-slate-800' }}"
                                data-tab="{{ $tab['id'] }}">
                                <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $tab['icon'] }}"></path>
                                </svg>
                                <span>{{ $tab['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                <!-- Scroll indicator arrow for mobile -->
                <button
                    class="md:hidden absolute right-0 top-0 bottom-0 w-8 bg-linear-to-l from-white via-white to-transparent flex items-center justify-center text-slate-400"
                    onclick="scrollTabsRight()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-4 md:p-5">
                <!-- Flight Form -->
                <div id="tab-flight" class="tab-content block">
                    <!-- Trip Type - Mobile: horizontal scroll, Desktop: inline -->
                    <div class="flex items-center gap-3 md:gap-2 mb-4 overflow-x-auto scrollbar-hide">
                        <label class="flex items-center gap-1.5 text-sm text-slate-600 cursor-pointer shrink-0">
                            <input type="radio" name="trip-type" class="w-4 h-4 text-orange-500 accent-orange-500"> One
                            Way
                        </label>
                        <label
                            class="flex items-center gap-1.5 px-3 py-1 bg-orange-500 text-white text-sm rounded-full cursor-pointer shrink-0">
                            <input type="radio" name="trip-type" checked class="hidden"> Round Trip
                        </label>
                        <label class="flex items-center gap-1.5 text-sm text-slate-600 cursor-pointer shrink-0">
                            <input type="radio" name="trip-type" class="w-4 h-4 text-orange-500 accent-orange-500">
                            Multi City
                        </label>
                        <!-- Traveller and Economy buttons - hidden on mobile, shown later -->
                        <div class="hidden md:flex ml-auto gap-2">
                            <button
                                class="px-3 py-1.5 border border-teal-500 text-teal-600 text-sm rounded-md flex items-center gap-1">
                                1 Traveller <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <button
                                class="px-3 py-1.5 border border-teal-500 text-teal-600 text-sm rounded-md flex items-center gap-1">
                                Economy <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Search Fields - Stacked layout -->
                    <div class="md:hidden space-y-0 mb-4">
                        <!-- From Field -->
                        <div class="relative">
                            <div class="p-3 border border-slate-200 rounded-t-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">From</p>
                                <div class="flex items-baseline gap-2">
                                    <p class="text-base font-bold text-slate-800">DAC</p>
                                    <p class="text-lg font-semibold text-slate-800">Dhaka</p>
                                </div>
                                <p class="text-xs text-slate-500">Bangladesh, Hazrat Shahjalal International Airport</p>
                            </div>
                            <!-- Swap Button - Positioned at center between From/To -->
                            <div class="absolute left-1/2 -translate-x-1/2 -bottom-4 z-10">
                                <button
                                    class="w-9 h-9 bg-orange-500 hover:bg-orange-600 rounded-full flex items-center justify-center shadow-md transition-colors">
                                    <svg class="w-4 h-4 text-white rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- To Field -->
                        <div class="p-3 border border-t-0 border-slate-200 rounded-b-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">To</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-base font-bold text-slate-800">CXB</p>
                                <p class="text-lg font-semibold text-slate-800">Cox's Bazar</p>
                            </div>
                            <p class="text-xs text-slate-500">Bangladesh, Hazrat Shahjalal International Airport</p>
                        </div>

                        <!-- Dates Row -->
                        <div class="flex gap-0 mt-3">
                            <div class="flex-1 p-3 border border-slate-200 rounded-l-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Departure</p>
                                <div class="flex items-baseline gap-1.5">
                                    <p class="text-xl font-bold text-slate-800">06</p>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700">January</p>
                                        <p class="text-xs text-slate-400">Tue, 2026</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 p-3 border border-l-0 border-slate-200 rounded-r-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Return</p>
                                <div class="flex items-baseline gap-1.5">
                                    <p class="text-xl font-bold text-slate-800">08</p>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700">January</p>
                                        <p class="text-xs text-slate-400">Thu, 2026</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Traveller & Class Row -->
                        <div class="flex gap-0 mt-3">
                            <div class="flex-1 p-3 border border-slate-200 rounded-l-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Traveller</p>
                                <div class="flex items-baseline gap-1.5">
                                    <p class="text-xl font-bold text-slate-800">01</p>
                                    <p class="text-sm text-slate-500">1 Adult</p>
                                </div>
                            </div>
                            <div
                                class="flex-1 p-3 border border-l-0 border-slate-200 rounded-r-lg bg-white flex items-center justify-end">
                                <p class="text-sm font-medium text-slate-700">Economy</p>
                            </div>
                        </div>

                        <!-- Search Button - Full Width -->
                        <a href="{{ route('flight.results') }}"
                            class="block w-full mt-4 py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Search Flight
                        </a>
                    </div>

                    <!-- Desktop Search Fields - Original horizontal layout -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">From</p>
                            <p class="text-lg font-bold text-slate-800">DAC</p>
                            <p class="text-xs text-slate-500 truncate">Dhaka, Hazrat Shahjalal...</p>
                        </div>
                        <div class="flex items-center justify-center px-3 bg-slate-50">
                            <div
                                class="w-8 h-8 bg-white border border-slate-200 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">To</p>
                            <p class="text-lg font-bold text-slate-800">CXB</p>
                            <p class="text-xs text-slate-500 truncate">Cox's Bazar, Cox's Bazar Al...</p>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Departure</p>
                            <p class="text-lg font-bold text-slate-800">06</p>
                            <p class="text-xs text-slate-500">January, Tuesday</p>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Return</p>
                            <p class="text-lg font-bold text-slate-800">08</p>
                            <p class="text-xs text-slate-500">January, Thursday</p>
                        </div>
                        <div class="flex items-center px-3">
                            <a href="{{ route('flight.results') }}"
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Fare Type -->
                    <div class="flex flex-wrap items-center gap-3 md:gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="fare-type" checked
                                class="w-4 h-4 text-teal-500 border-teal-500 accent-teal-500">
                            <span class="text-teal-600 font-medium">Regular Fare</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer">
                            <input type="radio" name="fare-type" class="w-4 h-4 accent-teal-500">
                            Student Fare
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer">
                            <input type="radio" name="fare-type" class="w-4 h-4 accent-teal-500">
                            Umrah Fare
                        </label>
                    </div>
                </div>

                <!-- Hotel Form -->
                <div id="tab-hotel" class="tab-content hidden">
                    <!-- Mobile Hotel Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Destination</p>
                            <input class="w-full text-lg font-semibold text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Where are you going?">
                        </div>
                        <div class="flex gap-0">
                            <div class="flex-1 p-3 border border-slate-200 rounded-l-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Check-in</p>
                                <input type="date"
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                            </div>
                            <div class="flex-1 p-3 border border-l-0 border-slate-200 rounded-r-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Check-out</p>
                                <input type="date"
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                            </div>
                        </div>
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Guests</p>
                            <p class="text-sm font-medium text-slate-800">2 Adults, 1 Room</p>
                        </div>
                        <a href="{{ route('site.hotel.search') }}"
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Search Hotel
                        </a>
                    </div>
                    <!-- Desktop Hotel Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Destination</p>
                            <input class="w-full text-lg font-bold text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Where are you going?">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Check-in</p>
                            <input type="date"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Check-out</p>
                            <input type="date"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Guests</p>
                            <p class="text-sm font-medium text-slate-800">2 Adults, 1 Room</p>
                        </div>
                        <div class="flex items-center px-3">
                            <a href="{{ route('site.hotel.search') }}"
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tour Form -->
                <div id="tab-tour" class="tab-content hidden">
                    <!-- Mobile Tour Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Destination</p>
                            <input class="w-full text-lg font-semibold text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Where to?">
                        </div>
                        <div class="flex gap-0">
                            <div class="flex-1 p-3 border border-slate-200 rounded-l-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Date</p>
                                <input type="date"
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                            </div>
                            <div class="flex-1 p-3 border border-l-0 border-slate-200 rounded-r-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Duration</p>
                                <select
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                    <option>Any Duration</option>
                                    <option>1-3 Days</option>
                                    <option>4-7 Days</option>
                                </select>
                            </div>
                        </div>
                        <a href="{{ route('site.tour.search') }}"
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Search Holiday
                        </a>
                    </div>
                    <!-- Desktop Tour Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Destination</p>
                            <input class="w-full text-lg font-bold text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Where to?">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Date</p>
                            <input type="date"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Duration</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Any Duration</option>
                                <option>1-3 Days</option>
                                <option>4-7 Days</option>
                            </select>
                        </div>
                        <div class="flex items-center px-3">
                            <a href="{{ route('site.tour.search') }}"
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Visa Form -->
                <div id="tab-visa" class="tab-content hidden">
                    <!-- Mobile Visa Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Citizen of</p>
                            <select
                                class="w-full text-lg font-semibold text-slate-800 bg-transparent focus:outline-none">
                                <option>Bangladesh</option>
                                <option>India</option>
                            </select>
                        </div>
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Travelling to</p>
                            <select id="visa-destination-mobile"
                                class="w-full text-lg font-semibold text-slate-800 bg-transparent focus:outline-none">
                                <option value="thailand">Thailand</option>
                                <option value="malaysia">Malaysia</option>
                                <option value="singapore">Singapore</option>
                                <option value="uae">UAE</option>
                            </select>
                        </div>
                        <a href="{{ route('site.visa.requirements', 'thailand') }}" id="visa-link-mobile"
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Check Requirements
                        </a>
                    </div>
                    <!-- Desktop Visa Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Citizen of</p>
                            <select class="w-full text-lg font-bold text-slate-800 bg-transparent focus:outline-none">
                                <option>Bangladesh</option>
                                <option>India</option>
                            </select>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Travelling to</p>
                            <select id="visa-destination"
                                class="w-full text-lg font-bold text-slate-800 bg-transparent focus:outline-none">
                                <option value="thailand">Thailand</option>
                                <option value="malaysia">Malaysia</option>
                                <option value="singapore">Singapore</option>
                                <option value="uae">UAE</option>
                            </select>
                        </div>
                        <div class="flex items-center px-3">
                            <a href="{{ route('site.visa.requirements', 'thailand') }}" id="visa-link"
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Medical Form -->
                <div id="tab-medical" class="tab-content hidden">
                    <!-- Mobile Medical Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Service</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Medical Tourism</option>
                                <option>Hospital Appointment</option>
                            </select>
                        </div>
                        <div class="flex gap-0">
                            <div class="flex-1 p-3 border border-slate-200 rounded-l-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Country</p>
                                <select
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                    <option>India</option>
                                    <option>Thailand</option>
                                </select>
                            </div>
                            <div class="flex-1 p-3 border border-l-0 border-slate-200 rounded-r-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Date</p>
                                <input type="date"
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                            </div>
                        </div>
                        <button
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Search Medical
                        </button>
                    </div>
                    <!-- Desktop Medical Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Service</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Medical Tourism</option>
                                <option>Hospital Appointment</option>
                            </select>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Country</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>India</option>
                                <option>Thailand</option>
                            </select>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Date</p>
                            <input type="date"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                        </div>
                        <div class="flex items-center px-3">
                            <button
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Transport Form -->
                <div id="tab-transport" class="tab-content hidden">
                    <!-- Mobile Transport Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="relative">
                            <div class="p-3 border border-slate-200 rounded-t-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">From</p>
                                <input
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                    placeholder="Pickup location">
                            </div>
                            <div class="absolute left-1/2 -translate-x-1/2 -bottom-4 z-10">
                                <button
                                    class="w-9 h-9 bg-orange-500 hover:bg-orange-600 rounded-full flex items-center justify-center shadow-md transition-colors">
                                    <svg class="w-4 h-4 text-white rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-3 border border-t-0 border-slate-200 rounded-b-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">To</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Drop location">
                        </div>
                        <div class="flex gap-0">
                            <div class="flex-1 p-3 border border-slate-200 rounded-l-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Date</p>
                                <input type="date"
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                            </div>
                            <div class="flex-1 p-3 border border-l-0 border-slate-200 rounded-r-lg bg-white">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Vehicle</p>
                                <select
                                    class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                    <option>Sedan</option>
                                    <option>SUV</option>
                                </select>
                            </div>
                        </div>
                        <button
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Search Transport
                        </button>
                    </div>
                    <!-- Desktop Transport Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">From</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Pickup">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">To</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Drop">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Date</p>
                            <input type="date"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Vehicle</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Sedan</option>
                                <option>SUV</option>
                            </select>
                        </div>
                        <div class="flex items-center px-3">
                            <button
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recharge Form -->
                <div id="tab-recharge" class="tab-content hidden">
                    <!-- Mobile Recharge Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Operator</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Grameenphone</option>
                                <option>Robi</option>
                                <option>Banglalink</option>
                            </select>
                        </div>
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Mobile Number</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="01XXXXXXXXX">
                        </div>
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Amount</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>৳100</option>
                                <option>৳200</option>
                                <option>৳500</option>
                            </select>
                        </div>
                        <button
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Recharge Now
                        </button>
                    </div>
                    <!-- Desktop Recharge Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Operator</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Grameenphone</option>
                                <option>Robi</option>
                                <option>Banglalink</option>
                            </select>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Mobile Number</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="01XXXXXXXXX">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Amount</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>৳100</option>
                                <option>৳200</option>
                                <option>৳500</option>
                            </select>
                        </div>
                        <div class="flex items-center px-3">
                            <button
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bill Form -->
                <div id="tab-bill" class="tab-content hidden">
                    <!-- Mobile Bill Form -->
                    <div class="md:hidden space-y-3 mb-4">
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Bill Type</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Electricity</option>
                                <option>Gas</option>
                                <option>Water</option>
                            </select>
                        </div>
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Account No.</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Enter account">
                        </div>
                        <div class="p-3 border border-slate-200 rounded-lg bg-white">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Amount</p>
                            <input type="number"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="৳ Amount">
                        </div>
                        <button
                            class="block w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white text-center font-semibold rounded-lg transition-colors shadow-sm">
                            Pay Bill
                        </button>
                    </div>
                    <!-- Desktop Bill Form -->
                    <div
                        class="hidden md:flex items-stretch gap-0 border border-slate-200 rounded-lg overflow-hidden mb-4">
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Bill Type</p>
                            <select class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none">
                                <option>Electricity</option>
                                <option>Gas</option>
                                <option>Water</option>
                            </select>
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Account No.</p>
                            <input class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="Enter account">
                        </div>
                        <div class="flex-1 p-3 border-r border-slate-200">
                            <p class="text-xs text-slate-400 mb-1">Amount</p>
                            <input type="number"
                                class="w-full text-sm font-medium text-slate-800 bg-transparent focus:outline-none"
                                placeholder="৳ Amount">
                        </div>
                        <div class="flex items-center px-3">
                            <button
                                class="w-12 h-12 bg-orange-500 hover:bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        document.querySelectorAll('.service-tab').forEach(btn => {
            const isActive = btn.getAttribute('data-tab') === tabName;
            btn.classList.toggle('text-orange-600', isActive);
            btn.classList.toggle('border-orange-500', isActive);
            btn.classList.toggle('text-slate-600', !isActive);
            btn.classList.toggle('border-transparent', !isActive);
        });
    }

    function scrollTabsRight() {
        const container = document.getElementById('tabs-container');
        if (container) {
            container.scrollBy({ left: 100, behavior: 'smooth' });
        }
    }

    document.getElementById('visa-destination')?.addEventListener('change', function () {
        document.getElementById('visa-link').href = '/visa/requirements/' + this.value;
    });

    document.getElementById('visa-destination-mobile')?.addEventListener('change', function () {
        document.getElementById('visa-link-mobile').href = '/visa/requirements/' + this.value;
    });
</script>