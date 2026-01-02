@extends('site-layout')

@section('meta-title', 'Hotel')

@section('meta-description', 'Hotel')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@extends('site-layout')

@section('meta-title', 'Book Top Hotels | Travelly')

@section('meta-description', 'Find the best deals on hotels, resorts, and vacation rentals. Book your perfect stay with Travelly.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Hotel Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Hotel Background" class="w-full h-full object-cover">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Stay at the <span class="text-transparent bg-clip-text bg-linear-to-r from-teal-300 to-emerald-200">Best Hotels</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-50 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Luxury resorts, cozy cottages, and city apartments—all in one place.
            </p>
            
            <!-- Hotel Search Form -->
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-4xl mx-auto text-left ring-1 ring-white/20">
                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="space-y-1.5 relative md:col-span-4 lg:col-span-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Destination</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all font-medium text-slate-700" placeholder="Where are you going?">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Check-in</label>
                        <div class="relative group">
                            <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-4 pr-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all font-medium text-slate-700">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Check-out</label>
                        <div class="relative group">
                            <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-4 pr-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all font-medium text-slate-700">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Guests</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all font-medium text-slate-700">
                                <option>2 Adults, 1 Room</option>
                                <option>1 Adult, 1 Room</option>
                                <option>Family (2A + 2C)</option>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="mt-6 flex justify-end">
                    <button class="w-full md:w-auto bg-linear-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-teal-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Find Hotels
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Trending Destinations -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Trending Destinations</h2>
            <p class="text-slate-500 mt-2">Most booked hotels this month</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Dest 1 -->
            <div class="group relative rounded-2xl overflow-hidden aspect-4/5 shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Royal Tulip" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 text-white transform transition-transform duration-300 group-hover:-translate-y-2">
                    <h3 class="text-2xl font-bold mb-1">Cox's Bazar</h3>
                    <p class="text-slate-200 text-sm">250+ Hotels Available</p>
                </div>
            </div>
             <!-- Dest 2 -->
             <div class="group relative rounded-2xl overflow-hidden aspect-4/5 shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1596436889106-be35e843f974?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Habiganj" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 text-white transform transition-transform duration-300 group-hover:-translate-y-2">
                    <h3 class="text-2xl font-bold mb-1">Sylhet</h3>
                    <p class="text-slate-200 text-sm">120+ Resorts Available</p>
                </div>
            </div>
             <!-- Dest 3 -->
             <div class="group relative rounded-2xl overflow-hidden aspect-4/5 shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1566863473556-2e8c14771d2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Sajek" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 text-white transform transition-transform duration-300 group-hover:-translate-y-2">
                    <h3 class="text-2xl font-bold mb-1">Chittagong</h3>
                    <p class="text-slate-200 text-sm">80+ Hotels Available</p>
                </div>
            </div>
        </div>
    </section>
@endsection
