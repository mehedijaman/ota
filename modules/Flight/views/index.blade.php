@extends('site-layout')

@section('meta-title', 'Find Cheap Flights | Triptonic')

@section('meta-description', 'Book cheap flights to top destinations. Compare airlines and save on your next trip with Triptonic.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Flight Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1542296332-2e44a02176cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Flight Background" class="w-full h-full object-cover">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Fly Anywhere, <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-300 to-cyan-200">Anytime</span>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Unbeatable deals on domestic and international flights.
            </p>
            
            <!-- Flight Search Form -->
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-4xl mx-auto text-left ring-1 ring-white/20">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">From</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700" placeholder="Source">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">To</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700" placeholder="Destination">
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
                <div class="mt-6 flex justify-end">
                    <button class="w-full md:w-auto bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Search Flights
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Features / Popular Routes -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Popular Flight Routes</h2>
            <p class="text-slate-500 mt-2">Trending destinations from Dhaka</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Route 1 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-48 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1548013146-72479768bada?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cox's Bazar" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute bottom-3 left-3 bg-black/50 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">Domestic</div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold text-lg text-slate-800">Dhaka to Cox's Bazar</h3>
                        <span class="text-blue-600 font-bold">from 3,500 BDT</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">Daily flights available • 1h 0m</p>
                    <button class="w-full py-2.5 rounded-xl border border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition-colors">Book Now</button>
                </div>
            </div>

            <!-- Route 2 -->
             <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-48 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Bali" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                     <div class="absolute bottom-3 left-3 bg-blue-600/80 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">International</div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold text-lg text-slate-800">Dhaka to Bali</h3>
                        <span class="text-blue-600 font-bold">from 55,000 BDT</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">Via Transit • 8h 30m</p>
                    <button class="w-full py-2.5 rounded-xl border border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition-colors">Book Now</button>
                </div>
            </div>

             <!-- Route 3 -->
             <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-48 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1512453979798-5ea90b792950?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Dubai" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                     <div class="absolute bottom-3 left-3 bg-blue-600/80 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">International</div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold text-lg text-slate-800">Dhaka to Dubai</h3>
                        <span class="text-blue-600 font-bold">from 45,000 BDT</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">Direct Flights • 5h 15m</p>
                    <button class="w-full py-2.5 rounded-xl border border-blue-600 text-blue-600 font-semibold hover:bg-blue-50 transition-colors">Book Now</button>
                </div>
            </div>
        </div>
    </section>
@endsection
