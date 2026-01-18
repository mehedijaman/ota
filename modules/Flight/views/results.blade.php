@extends('site-layout')

@section('meta-title', 'Flight Results | ' . config('app.name'))

@section('content')
    <!-- Search Summary -->
    <section class="bg-gradient-to-r from-blue-900 to-blue-700 py-8">
        <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-white">
                    <h1 class="text-2xl font-bold">
                        {{ $search->originCode ?? 'DAC' }} → {{ $search->destinationCode ?? 'DXB' }}
                    </h1>
                    <p class="text-blue-200">
                        {{ $search->departureDate ?? 'Select Date' }} • {{ $search->getTotalPassengers() ?? 1 }}
                        Passenger(s) • {{ ucfirst($search->cabinClass ?? 'economy') }}
                    </p>
                </div>
                <a href="{{ route('flight.home') }}"
                    class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors">
                    Modify Search
                </a>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="py-8 bg-slate-50 min-h-screen">
        <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <aside class="lg:w-72 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h3 class="font-bold text-lg text-slate-800 mb-6">Filters</h3>

                        <!-- Stops -->
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <h4 class="font-semibold text-slate-700 mb-4">Stops</h4>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600">
                                    <span class="text-slate-600">Non-stop</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600">
                                    <span class="text-slate-600">1 Stop</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600">
                                    <span class="text-slate-600">2+ Stops</span>
                                </label>
                            </div>
                        </div>

                        <!-- Airlines -->
                        @if(isset($filters['airlines']) && count($filters['airlines']))
                            <div class="mb-6 pb-6 border-b border-slate-100">
                                <h4 class="font-semibold text-slate-700 mb-4">Airlines</h4>
                                <div class="space-y-3">
                                    @foreach($filters['airlines'] as $code => $name)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600">
                                            <span class="text-slate-600">{{ $name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Price Range -->
                        <div>
                            <h4 class="font-semibold text-slate-700 mb-4">Price Range</h4>
                            <div class="flex items-center justify-between text-sm text-slate-500 mb-2">
                                <span>৳{{ number_format($filters['price_range']['min'] ?? 0) }}</span>
                                <span>৳{{ number_format($filters['price_range']['max'] ?? 100000) }}</span>
                            </div>
                            <input type="range" class="w-full" min="{{ $filters['price_range']['min'] ?? 0 }}"
                                max="{{ $filters['price_range']['max'] ?? 100000 }}">
                        </div>
                    </div>
                </aside>

                <!-- Results Grid -->
                <main class="flex-1">
                    <!-- Sort -->
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-slate-600"><span class="font-bold text-slate-800">{{ count($results) }}</span>
                            flights found</p>
                        <select class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm">
                            <option>Sort by: Price (Low to High)</option>
                            <option>Sort by: Price (High to Low)</option>
                            <option>Sort by: Duration</option>
                            <option>Sort by: Departure Time</option>
                        </select>
                    </div>

                    @if(count($results))
                        <div class="space-y-4">
                            @foreach($results as $flight)
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                    <div class="p-6">
                                        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                                            <!-- Airline -->
                                            <div class="flex items-center gap-4 w-full md:w-auto">
                                                <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                                    <span class="font-bold text-slate-600">{{ $flight->airlineCode }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $flight->airlineName }}</p>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $flight->airlineCode }}{{ $flight->flightNumber }}</p>
                                                </div>
                                            </div>

                                            <!-- Flight Times -->
                                            <div class="flex-1 flex items-center justify-center gap-8">
                                                <div class="text-center">
                                                    <p class="text-2xl font-bold text-slate-800">
                                                        {{ \Carbon\Carbon::parse($flight->departureDateTime)->format('H:i') }}</p>
                                                    <p class="text-sm text-slate-500">{{ $flight->originCode }}</p>
                                                </div>
                                                <div class="flex-1 max-w-[200px] relative">
                                                    <div class="border-t-2 border-dashed border-slate-300"></div>
                                                    <div
                                                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2">
                                                        <p class="text-xs text-slate-500">
                                                            {{ floor($flight->durationMinutes / 60) }}h
                                                            {{ $flight->durationMinutes % 60 }}m
                                                        </p>
                                                        <p
                                                            class="text-xs text-center {{ $flight->stops === 0 ? 'text-green-600' : 'text-orange-500' }}">
                                                            {{ $flight->stops === 0 ? 'Non-stop' : $flight->stops . ' Stop(s)' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-2xl font-bold text-slate-800">
                                                        {{ \Carbon\Carbon::parse($flight->arrivalDateTime)->format('H:i') }}</p>
                                                    <p class="text-sm text-slate-500">{{ $flight->destinationCode }}</p>
                                                </div>
                                            </div>

                                            <!-- Price & Book -->
                                            <div class="text-right w-full md:w-auto">
                                                <p class="text-3xl font-bold text-orange-500">
                                                    ৳{{ number_format($flight->totalPrice) }}</p>
                                                <p class="text-xs text-slate-500 mb-3">per person</p>
                                                <a href="{{ route('flight.booking', ['offer_id' => $flight->offerId]) }}"
                                                    class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                                                    Select
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Details Toggle -->
                                    <div class="border-t border-slate-100 px-6 py-3 bg-slate-50">
                                        <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            <i class="ri-arrow-down-s-line"></i> Flight Details
                                        </button>
                                        @if($flight->isRefundable)
                                            <span class="ml-4 text-xs text-green-600"><i class="ri-refund-2-line"></i> Refundable</span>
                                        @endif
                                        <span class="ml-4 text-xs text-slate-500"><i class="ri-luggage-cart-line"></i>
                                            {{ $flight->baggageAllowance }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 bg-white rounded-2xl">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="ri-flight-takeoff-line text-4xl text-slate-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">No flights found</h3>
                            <p class="text-slate-500 mb-8">Try adjusting your search criteria</p>
                            <a href="{{ route('flight.home') }}"
                                class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                                <i class="ri-search-line"></i> New Search
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>
@endsection