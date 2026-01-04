@extends('site-layout')

@section('meta-title', 'Explore World Tours | TriptonicBD')

@section('meta-description', 'Discover amazing tour packages to the world\'s most beautiful destinations. Adventure awaits with TriptonicBD.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Tour Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Tour Background" class="w-full h-full object-cover">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Explore the <span class="text-transparent bg-clip-text bg-linear-to-r from-orange-300 to-amber-200">World</span>
            </h1>
            <p class="text-lg md:text-xl text-orange-50 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Unforgettable journeys, curated just for you.
            </p>
            
            <!-- Tour Search Form -->
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-4xl mx-auto text-left ring-1 ring-white/20">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="space-y-1.5 relative md:col-span-3 lg:col-span-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Destination</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700" placeholder="Where do you want to go?">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Date</label>
                        <div class="relative group">
                            <input type="date" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-4 pr-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Duration</label>
                         <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                                <option>Any Duration</option>
                                <option>1-3 Days</option>
                                <option>4-7 Days</option>
                                <option>7+ Days</option>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="mt-6 flex justify-end">
                    <button class="w-full md:w-auto bg-linear-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Search Tours
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Packages -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Popular Tour Packages</h2>
            <p class="text-slate-500 mt-2">Curated experiences for every traveler</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pkg 1 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-56 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1548013146-72479768bada?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cox's Bazar" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">3 Days</div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-1 mb-2">
                         <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                         <span class="text-sm font-medium text-slate-600">4.8 (120 Reviews)</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800 mb-1">Cox's Bazar Tour</h3>
                    <p class="text-slate-500 text-sm mb-4 line-clamp-2">Experience the longest sea beach in the world with luxury accommodation.</p>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-medium uppercase">Starts from</span>
                            <span class="text-lg font-bold text-orange-600">৳ 5,500</span>
                        </div>
                        <button class="bg-orange-50 text-orange-600 p-2 rounded-lg hover:bg-orange-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </div>

             <!-- Pkg 2 -->
             <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-56 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1596436889106-be35e843f974?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Sylhet" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">2 Days</div>
                </div>
                 <div class="p-5">
                    <div class="flex items-center gap-1 mb-2">
                         <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                         <span class="text-sm font-medium text-slate-600">4.9 (85 Reviews)</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800 mb-1">Sylhet Tea Garden</h3>
                    <p class="text-slate-500 text-sm mb-4 line-clamp-2">Enjoy the lush green tea gardens and waterfalls of Sylhet.</p>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-medium uppercase">Starts from</span>
                            <span class="text-lg font-bold text-orange-600">৳ 4,200</span>
                        </div>
                         <button class="bg-orange-50 text-orange-600 p-2 rounded-lg hover:bg-orange-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </div>

             <!-- Pkg 3 -->
             <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-56 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1566863473556-2e8c14771d2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Sajek" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">3 Days</div>
                </div>
                 <div class="p-5">
                    <div class="flex items-center gap-1 mb-2">
                         <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                         <span class="text-sm font-medium text-slate-600">4.7 (200 Reviews)</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800 mb-1">Sajek Valley</h3>
                    <p class="text-slate-500 text-sm mb-4 line-clamp-2">Touch the clouds at the queen of hills. A magical experience.</p>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-medium uppercase">Starts from</span>
                            <span class="text-lg font-bold text-orange-600">৳ 6,500</span>
                        </div>
                         <button class="bg-orange-50 text-orange-600 p-2 rounded-lg hover:bg-orange-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pkg 4 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-56 overflow-hidden relative">
                    <img src="https://images.unsplash.com/photo-1578559115206-a83d69c6f2a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Sundarbans" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">4 Days</div>
                </div>
                 <div class="p-5">
                    <div class="flex items-center gap-1 mb-2">
                         <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                         <span class="text-sm font-medium text-slate-600">4.6 (95 Reviews)</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800 mb-1">Sundarbans</h3>
                    <p class="text-slate-500 text-sm mb-4 line-clamp-2">Explore the largest mangrove forest and see the Royal Bengal Tiger.</p>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 font-medium uppercase">Starts from</span>
                            <span class="text-lg font-bold text-orange-600">৳ 8,000</span>
                        </div>
                         <button class="bg-orange-50 text-orange-600 p-2 rounded-lg hover:bg-orange-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
