@extends('site-layout')

@section('meta-title', 'Hotel Search Results | TriptonicBD')

@section('meta-description', 'Compare and book hotels from the best providers. Find the perfect stay for your trip.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Search Summary Header -->
    <div class="bg-gradient-to-r from-teal-600 to-emerald-700 pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-white">
                    <div class="flex items-center gap-3 text-sm text-teal-100 mb-2">
                        <a href="{{ route('site.hotel.index') }}" class="hover:text-white transition-colors">Hotels</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span>Search Results</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold">Cox's Bazar, Bangladesh</h1>
                    <p class="text-teal-100 mt-1">Jan 20 - Jan 25, 2026 • 2 Adults, 1 Room • 5 Nights</p>
                </div>
                <button class="self-start md:self-center bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-5 py-2.5 rounded-xl font-semibold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Modify Search
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Left Sidebar Filters -->
                <aside class="w-full lg:w-72 shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-slate-800">Filters</h2>
                            <button class="text-sm text-teal-600 hover:text-teal-700 font-medium">Reset All</button>
                        </div>

                        <!-- Price Range -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Price per night</h3>
                            <div class="px-1">
                                <input type="range" min="1000" max="50000" value="15000" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-teal-600">
                                <div class="flex justify-between text-xs text-slate-500 mt-2">
                                    <span>৳1,000</span>
                                    <span class="font-semibold text-teal-600">৳15,000</span>
                                    <span>৳50,000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Star Rating -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Star Rating</h3>
                            <div class="flex flex-wrap gap-2">
                                <label class="flex items-center justify-center px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-teal-300 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 transition-all">
                                    <input type="checkbox" class="hidden">
                                    <span class="text-sm font-medium text-slate-600">5 ⭐</span>
                                </label>
                                <label class="flex items-center justify-center px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-teal-300 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 transition-all">
                                    <input type="checkbox" checked class="hidden">
                                    <span class="text-sm font-medium text-slate-600">4 ⭐</span>
                                </label>
                                <label class="flex items-center justify-center px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-teal-300 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 transition-all">
                                    <input type="checkbox" checked class="hidden">
                                    <span class="text-sm font-medium text-slate-600">3 ⭐</span>
                                </label>
                                <label class="flex items-center justify-center px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-teal-300 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 transition-all">
                                    <input type="checkbox" class="hidden">
                                    <span class="text-sm font-medium text-slate-600">2 ⭐</span>
                                </label>
                            </div>
                        </div>

                        <!-- Property Type -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Property Type</h3>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Hotels</span>
                                    <span class="ml-auto text-xs text-slate-400">156</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Resorts</span>
                                    <span class="ml-auto text-xs text-slate-400">42</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Guest Houses</span>
                                    <span class="ml-auto text-xs text-slate-400">28</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Apartments</span>
                                    <span class="ml-auto text-xs text-slate-400">19</span>
                                </label>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Amenities</h3>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Free WiFi</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Swimming Pool</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Free Breakfast</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Beach Access</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Spa & Wellness</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Parking</span>
                                </label>
                            </div>
                        </div>

                        <!-- Guest Rating -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Guest Rating</h3>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="rating" class="w-4 h-4 border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Excellent (9+)</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="rating" checked class="w-4 h-4 border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Very Good (8+)</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="rating" class="w-4 h-4 border-slate-300 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Good (7+)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Results -->
                <main class="flex-1">
                    <!-- Sort Bar -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-slate-600 text-sm"><span class="font-semibold text-slate-800">245 properties</span> found in Cox's Bazar</p>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-slate-500">Sort by:</span>
                            <select class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                <option>Recommended</option>
                                <option>Price (Lowest)</option>
                                <option>Price (Highest)</option>
                                <option>Guest Rating</option>
                                <option>Star Rating</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hotel Cards -->
                    <div class="space-y-4">
                        
                        <!-- Hotel Card 1 - Featured -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col md:flex-row">
                                <!-- Image -->
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ocean Paradise Hotel" class="w-full h-full object-cover">
                                    <div class="absolute top-3 left-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                        🏆 TOP RATED
                                    </div>
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <svg class="w-5 h-5 text-slate-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>
                                <!-- Content -->
                                <div class="flex-1 p-5 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-amber-500 text-sm">★★★★★</span>
                                                    <span class="text-xs text-slate-400">5-star hotel</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                                    <a href="{{ route('site.hotel.show', 1) }}">Ocean Paradise Hotel & Resort</a>
                                                </h3>
                                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Kolatoli Beach Road, Cox's Bazar
                                                </p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="bg-teal-600 text-white px-2.5 py-1 rounded-lg text-sm font-bold">9.2</div>
                                                <p class="text-xs text-slate-500 mt-1">Excellent</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.778 8.222c-4.296-4.296-11.26-4.296-15.556 0A1 1 0 01.808 6.808c5.076-5.077 13.308-5.077 18.384 0a1 1 0 01-1.414 1.414zM14.95 11.05a7 7 0 00-9.9 0 1 1 0 01-1.414-1.414 9 9 0 0112.728 0 1 1 0 01-1.414 1.414zM12.12 13.88a3 3 0 00-4.242 0 1 1 0 01-1.415-1.415 5 5 0 017.072 0 1 1 0 01-1.415 1.415zM9 16a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                                                Free WiFi
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                Pool
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                                Beach Access
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                Spa
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Breakfast</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-between mt-4 pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">5 nights, 2 adults</p>
                                            <p class="text-sm text-slate-400 line-through">৳85,000</p>
                                            <p class="text-2xl font-bold text-teal-600">৳72,500</p>
                                            <p class="text-xs text-slate-500">+৳5,800 taxes & fees</p>
                                        </div>
                                        <a href="{{ route('site.hotel.show', 1) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                            Show Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Card 2 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Royal Tulip Sea Pearl" class="w-full h-full object-cover">
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <svg class="w-5 h-5 text-slate-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>
                                <div class="flex-1 p-5 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-amber-500 text-sm">★★★★★</span>
                                                    <span class="text-xs text-slate-400">5-star hotel</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                                    <a href="{{ route('site.hotel.show', 2) }}">Royal Tulip Sea Pearl Beach Resort</a>
                                                </h3>
                                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Inani Beach, Cox's Bazar
                                                </p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="bg-teal-600 text-white px-2.5 py-1 rounded-lg text-sm font-bold">8.9</div>
                                                <p class="text-xs text-slate-500 mt-1">Excellent</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Free WiFi</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Private Beach</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Pool</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Restaurant</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-between mt-4 pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">5 nights, 2 adults</p>
                                            <p class="text-2xl font-bold text-teal-600">৳68,000</p>
                                            <p class="text-xs text-slate-500">+৳5,440 taxes & fees</p>
                                        </div>
                                        <a href="{{ route('site.hotel.show', 2) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                            Show Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Card 3 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Sayeman Beach Resort" class="w-full h-full object-cover">
                                    <div class="absolute top-3 left-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                        💰 GREAT DEAL
                                    </div>
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <svg class="w-5 h-5 text-slate-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>
                                <div class="flex-1 p-5 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-amber-500 text-sm">★★★★</span>
                                                    <span class="text-xs text-slate-400">4-star hotel</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                                    <a href="{{ route('site.hotel.show', 3) }}">Sayeman Beach Resort</a>
                                                </h3>
                                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Laboni Beach, Cox's Bazar
                                                </p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="bg-teal-600 text-white px-2.5 py-1 rounded-lg text-sm font-bold">8.5</div>
                                                <p class="text-xs text-slate-500 mt-1">Very Good</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Free WiFi</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Sea View</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Restaurant</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Parking</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-between mt-4 pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">5 nights, 2 adults</p>
                                            <p class="text-sm text-slate-400 line-through">৳55,000</p>
                                            <p class="text-2xl font-bold text-teal-600">৳42,500</p>
                                            <p class="text-xs text-slate-500">+৳3,400 taxes & fees</p>
                                        </div>
                                        <a href="{{ route('site.hotel.show', 3) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                            Show Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Card 4 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Long Beach Hotel" class="w-full h-full object-cover">
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <svg class="w-5 h-5 text-slate-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>
                                <div class="flex-1 p-5 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-amber-500 text-sm">★★★★</span>
                                                    <span class="text-xs text-slate-400">4-star hotel</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                                    <a href="{{ route('site.hotel.show', 4) }}">Long Beach Hotel Cox's Bazar</a>
                                                </h3>
                                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Kolatoli Road, Cox's Bazar
                                                </p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="bg-teal-600 text-white px-2.5 py-1 rounded-lg text-sm font-bold">8.2</div>
                                                <p class="text-xs text-slate-500 mt-1">Very Good</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Free WiFi</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Air Conditioning</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">24h Front Desk</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-between mt-4 pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">5 nights, 2 adults</p>
                                            <p class="text-2xl font-bold text-teal-600">৳35,000</p>
                                            <p class="text-xs text-slate-500">+৳2,800 taxes & fees</p>
                                        </div>
                                        <a href="{{ route('site.hotel.show', 4) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                            Show Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Card 5 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/photo-1596436889106-be35e843f974?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Sea Gull Hotel" class="w-full h-full object-cover">
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <svg class="w-5 h-5 text-slate-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>
                                <div class="flex-1 p-5 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-amber-500 text-sm">★★★</span>
                                                    <span class="text-xs text-slate-400">3-star hotel</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                                    <a href="{{ route('site.hotel.show', 5) }}">Sea Gull Hotel</a>
                                                </h3>
                                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Hotel Motel Zone, Cox's Bazar
                                                </p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="bg-teal-600 text-white px-2.5 py-1 rounded-lg text-sm font-bold">7.8</div>
                                                <p class="text-xs text-slate-500 mt-1">Good</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Free WiFi</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Room Service</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">TV</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-between mt-4 pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">5 nights, 2 adults</p>
                                            <p class="text-2xl font-bold text-teal-600">৳18,500</p>
                                            <p class="text-xs text-slate-500">+৳1,480 taxes & fees</p>
                                        </div>
                                        <a href="{{ route('site.hotel.show', 5) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                            Show Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotel Card 6 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Mermaid Beach Resort" class="w-full h-full object-cover">
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <svg class="w-5 h-5 text-slate-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </div>
                                <div class="flex-1 p-5 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-amber-500 text-sm">★★★★★</span>
                                                    <span class="text-xs text-slate-400">5-star resort</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-800 hover:text-teal-600 transition-colors">
                                                    <a href="{{ route('site.hotel.show', 6) }}">Mermaid Beach Resort</a>
                                                </h3>
                                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Himchori, Cox's Bazar
                                                </p>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="bg-teal-600 text-white px-2.5 py-1 rounded-lg text-sm font-bold">9.0</div>
                                                <p class="text-xs text-slate-500 mt-1">Excellent</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Free WiFi</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Private Beach</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Pool</span>
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md">Spa</span>
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-between mt-4 pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">5 nights, 2 adults</p>
                                            <p class="text-2xl font-bold text-teal-600">৳95,000</p>
                                            <p class="text-xs text-slate-500">+৳7,600 taxes & fees</p>
                                        </div>
                                        <a href="{{ route('site.hotel.show', 6) }}" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                            Show Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Load More -->
                    <div class="text-center mt-8">
                        <button class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-8 py-3 rounded-xl font-semibold transition-colors inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            Load More Hotels
                        </button>
                    </div>
                </main>

            </div>
        </div>
    </div>

    <!-- Promotional Banner -->
    <div class="bg-gradient-to-r from-teal-600 to-emerald-600 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left text-white">
                    <h3 class="text-2xl font-bold mb-2">Need help finding the perfect stay?</h3>
                    <p class="text-teal-100">Our travel experts are here to help you find the best hotels</p>
                </div>
                <div class="flex gap-4">
                    <a href="#" class="bg-white text-teal-600 px-6 py-3 rounded-xl font-semibold hover:bg-teal-50 transition-colors">
                        Chat with Us
                    </a>
                    <a href="tel:+8801234567890" class="bg-white/20 text-white px-6 py-3 rounded-xl font-semibold hover:bg-white/30 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Call Now
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
