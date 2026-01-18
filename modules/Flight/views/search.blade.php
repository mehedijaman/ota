@extends('site-layout')

@section('meta-title', 'Search Flights | ' . config('app.name'))

@section('meta-description', 'Search for the best flights. Compare prices and book your next journey.')

@section('content')
    <!-- Hero Search Section -->
    <section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 py-16 md:py-24">
        <div
            class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1920&q=80')] bg-cover bg-center opacity-20">
        </div>
        <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">Find Your Perfect Flight</h1>
                <p class="text-lg text-blue-100">Search from thousands of flights to your dream destination</p>
            </div>

            <!-- Search Box -->
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <form action="{{ route('flight.results') }}" method="GET">
                    <!-- Trip Type -->
                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="trip_type" value="one_way" class="text-blue-600">
                            <span class="text-slate-700">One Way</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="trip_type" value="round_trip" checked class="text-blue-600">
                            <span class="text-slate-700">Round Trip</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- From -->
                        <div class="relative">
                            <label class="block text-xs text-slate-500 mb-1">From</label>
                            <input type="text" name="origin" placeholder="City or Airport"
                                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <!-- To -->
                        <div class="relative">
                            <label class="block text-xs text-slate-500 mb-1">To</label>
                            <input type="text" name="destination" placeholder="City or Airport"
                                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <!-- Departure -->
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Departure</label>
                            <input type="date" name="departure_date"
                                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <!-- Return -->
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Return</label>
                            <input type="date" name="return_date"
                                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Adults</label>
                            <select name="adults" class="w-full px-4 py-3 border border-slate-200 rounded-lg">
                                @for($i = 1; $i <= 9; $i++)
                                    <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Children</label>
                            <select name="children" class="w-full px-4 py-3 border border-slate-200 rounded-lg">
                                @for($i = 0; $i <= 9; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Infants</label>
                            <select name="infants" class="w-full px-4 py-3 border border-slate-200 rounded-lg">
                                @for($i = 0; $i <= 4; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Class</label>
                            <select name="cabin_class" class="w-full px-4 py-3 border border-slate-200 rounded-lg">
                                <option value="economy">Economy</option>
                                <option value="premium_economy">Premium Economy</option>
                                <option value="business">Business</option>
                                <option value="first">First Class</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full md:w-auto px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg transition-colors shadow-lg">
                        <i class="ri-search-line mr-2"></i> Search Flights
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Routes -->
    @if(isset($popularRoutes) && $popularRoutes->count())
        <section class="py-16 bg-slate-50">
            <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-8">Popular Routes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($popularRoutes as $route)
                        <a href="{{ route('flight.results', ['origin' => $route->origin_code, 'destination' => $route->destination_code]) }}"
                            class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow group">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-2xl font-bold text-slate-800">{{ $route->origin_code }}</span>
                                <i
                                    class="ri-flight-takeoff-line text-2xl text-blue-500 group-hover:translate-x-1 transition-transform"></i>
                                <span class="text-2xl font-bold text-slate-800">{{ $route->destination_code }}</span>
                            </div>
                            <p class="text-sm text-slate-500">{{ $route->origin_city }} → {{ $route->destination_city }}</p>
                            @if($route->min_price)
                                <p class="text-lg font-bold text-orange-500 mt-2">From ৳{{ number_format($route->min_price) }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Popular Airports -->
    @if(isset($popularAirports) && $popularAirports->count())
        <section class="py-16">
            <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-8">Explore Destinations</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($popularAirports as $airport)
                        <a href="{{ route('flight.results', ['destination' => $airport->iata_code]) }}"
                            class="text-center p-4 bg-white rounded-xl shadow hover:shadow-lg transition-shadow">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="ri-plane-line text-blue-600 text-xl"></i>
                            </div>
                            <p class="font-bold text-slate-800">{{ $airport->iata_code }}</p>
                            <p class="text-xs text-slate-500">{{ $airport->city }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection