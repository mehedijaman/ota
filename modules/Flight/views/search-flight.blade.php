@extends('site-layout')

@section('meta-title', 'Flight Search Results | TriptonicBD')

@section('meta-description', 'Compare and book cheap flights from the best airlines. Find the best deals on your next trip.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Search Summary Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-white">
                    <div class="flex items-center gap-3 text-sm text-blue-100 mb-2">
                        <a href="{{ route('site.flight.index') }}" class="hover:text-white transition-colors">Flights</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span>Search Results</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                        <span>Dhaka (DAC)</span>
                        <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        <span>New York (JFK)</span>
                    </h1>
                    <p class="text-blue-100 mt-1">Thu, 15 Jan 2026 • 1 Adult • Economy</p>
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
                            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">Reset All</button>
                        </div>

                        <!-- Stops Filter -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Stops</h3>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Non-stop</span>
                                    <span class="ml-auto text-xs text-slate-400">$850+</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">1 Stop</span>
                                    <span class="ml-auto text-xs text-slate-400">$650+</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">2+ Stops</span>
                                    <span class="ml-auto text-xs text-slate-400">$520+</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Price</h3>
                            <div class="px-1">
                                <input type="range" min="500" max="2000" value="1200" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                <div class="flex justify-between text-xs text-slate-500 mt-2">
                                    <span>$500</span>
                                    <span class="font-semibold text-blue-600">$1,200</span>
                                    <span>$2,000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Airlines Filter -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Airlines</h3>
                            <div class="space-y-2.5 max-h-48 overflow-y-auto">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Emirates</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Qatar Airways</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Singapore Airlines</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Turkish Airlines</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Etihad Airways</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Biman Bangladesh</span>
                                </label>
                            </div>
                        </div>

                        <!-- Departure Time -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Departure Time</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex flex-col items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all">
                                    <input type="checkbox" class="hidden">
                                    <svg class="w-5 h-5 text-amber-500 mb-1" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4 12H2m20 0h-2m-2.93-7.07l-1.41 1.41m-9.9 9.9l-1.41 1.41m12.72 0l-1.41-1.41M6.34 6.34L4.93 4.93"/></svg>
                                    <span class="text-xs font-medium text-slate-600">Morning</span>
                                    <span class="text-[10px] text-slate-400">6am - 12pm</span>
                                </label>
                                <label class="flex flex-col items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all">
                                    <input type="checkbox" class="hidden">
                                    <svg class="w-5 h-5 text-orange-500 mb-1" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>
                                    <span class="text-xs font-medium text-slate-600">Afternoon</span>
                                    <span class="text-[10px] text-slate-400">12pm - 6pm</span>
                                </label>
                                <label class="flex flex-col items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all">
                                    <input type="checkbox" class="hidden">
                                    <svg class="w-5 h-5 text-indigo-500 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3a9 9 0 109 9c0-5-4-9-9-9z"/></svg>
                                    <span class="text-xs font-medium text-slate-600">Evening</span>
                                    <span class="text-[10px] text-slate-400">6pm - 12am</span>
                                </label>
                                <label class="flex flex-col items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all">
                                    <input type="checkbox" class="hidden">
                                    <svg class="w-5 h-5 text-slate-600 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3a9 9 0 019 9 9 9 0 01-9 9 9 9 0 01-9-9 9 9 0 019-9m0 4a5 5 0 00-5 5 5 5 0 005 5 5 5 0 005-5 5 5 0 00-5-5z"/></svg>
                                    <span class="text-xs font-medium text-slate-600">Night</span>
                                    <span class="text-[10px] text-slate-400">12am - 6am</span>
                                </label>
                            </div>
                        </div>

                        <!-- Baggage -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Baggage</h3>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Carry-on included</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800">Checked bag included</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Results -->
                <main class="flex-1">
                    <!-- Sort Bar -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-slate-600 text-sm"><span class="font-semibold text-slate-800">24 flights</span> found for your search</p>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-slate-500">Sort by:</span>
                            <select class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <option>Best Match</option>
                                <option>Price (Lowest)</option>
                                <option>Duration (Shortest)</option>
                                <option>Departure (Earliest)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Flight Cards -->
                    <div class="space-y-4">
                        
                        <!-- Flight Card 1 - Best Deal -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-1.5">
                                <span class="text-white text-xs font-bold">🏆 BEST DEAL</span>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <!-- Airline Info -->
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                            <span class="text-red-600 font-bold text-lg">EK</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Emirates</p>
                                            <p class="text-xs text-slate-500">EK 584</p>
                                        </div>
                                    </div>

                                    <!-- Flight Details -->
                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">08:45</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">18h 30m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-blue-600 font-medium mt-1">1 Stop (DXB)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">14:15</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+1 day</p>
                                        </div>
                                    </div>

                                    <!-- Price & CTA -->
                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$687</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 2 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-lg">QR</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Qatar Airways</p>
                                            <p class="text-xs text-slate-500">QR 635</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">10:30</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">20h 15m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-blue-600 font-medium mt-1">1 Stop (DOH)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">18:45</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+1 day</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$712</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 3 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                            <span class="text-orange-600 font-bold text-lg">SQ</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Singapore Airlines</p>
                                            <p class="text-xs text-slate-500">SQ 437</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">23:55</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">22h 40m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-blue-600 font-medium mt-1">1 Stop (SIN)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">10:35</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+2 days</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$845</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 4 - Non-stop -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-4 py-1.5">
                                <span class="text-white text-xs font-bold">✈️ NON-STOP</span>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                            <span class="text-red-700 font-bold text-lg">TK</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Turkish Airlines</p>
                                            <p class="text-xs text-slate-500">TK 720</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">02:30</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">16h 45m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-emerald-600 font-medium mt-1">Non-stop</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">07:15</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$1,120</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 5 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                            <span class="text-amber-700 font-bold text-lg">EY</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Etihad Airways</p>
                                            <p class="text-xs text-slate-500">EY 247</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">15:20</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">19h 55m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-blue-600 font-medium mt-1">1 Stop (AUH)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">23:15</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+1 day</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$698</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 6 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                            <span class="text-green-700 font-bold text-lg">BG</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Biman Bangladesh</p>
                                            <p class="text-xs text-slate-500">BG 201</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">21:00</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">25h 30m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-orange-600 font-medium mt-1">2 Stops (LHR, BOS)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">10:30</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+2 days</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$542</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 7 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <span class="text-blue-700 font-bold text-lg">KL</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">KLM Royal Dutch</p>
                                            <p class="text-xs text-slate-500">KL 872</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">06:15</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">21h 10m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-blue-600 font-medium mt-1">1 Stop (AMS)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">15:25</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+1 day</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$789</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flight Card 8 -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                    <div class="flex items-center gap-4 lg:w-40 shrink-0">
                                        <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center">
                                            <span class="text-sky-700 font-bold text-lg">MH</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">Malaysia Airlines</p>
                                            <p class="text-xs text-slate-500">MH 193</p>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-8">
                                        <div class="text-center sm:text-left">
                                            <p class="text-2xl font-bold text-slate-800">11:45</p>
                                            <p class="text-sm text-slate-500">DAC</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center px-4">
                                            <p class="text-xs text-slate-400 mb-1">24h 20m</p>
                                            <div class="w-full flex items-center gap-2">
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <div class="h-px flex-1 bg-slate-200"></div>
                                            </div>
                                            <p class="text-xs text-blue-600 font-medium mt-1">1 Stop (KUL)</p>
                                        </div>
                                        <div class="text-center sm:text-right">
                                            <p class="text-2xl font-bold text-slate-800">00:05</p>
                                            <p class="text-sm text-slate-500">JFK</p>
                                            <p class="text-[10px] text-slate-400">+2 days</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:w-40 shrink-0 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
                                        <div class="text-left lg:text-right">
                                            <p class="text-2xl font-bold text-blue-600">$624</p>
                                            <p class="text-xs text-slate-400">per person</p>
                                        </div>
                                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                                            View Deal
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
                            Load More Flights
                        </button>
                    </div>
                </main>

            </div>
        </div>
    </div>

    <!-- Promotional Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left text-white">
                    <h3 class="text-2xl font-bold mb-2">Can't find what you're looking for?</h3>
                    <p class="text-indigo-100">Contact our travel experts for personalized flight recommendations</p>
                </div>
                <div class="flex gap-4">
                    <a href="#" class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-semibold hover:bg-indigo-50 transition-colors">
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
