@extends('site-layout')

@section('title', 'Hotels - Find Your Perfect Stay')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-900 via-indigo-800 to-purple-900 py-20">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center text-white mb-10">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Find Your Perfect Hotel</h1>
                <p class="text-xl text-blue-200">Discover amazing hotels at the best prices</p>
            </div>

            <!-- Search Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-5xl mx-auto">
                <form action="{{ route('site.hotel.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                        <input type="text" name="destination" placeholder="City, hotel, or area"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                        <input type="date" name="check_in" value="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                        <input type="date" name="check_out" value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
                            <i class="fas fa-search mr-2"></i> Search Hotels
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Hotels -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Featured Hotels</h2>
                <p class="text-gray-600">Hand-picked hotels with exceptional ratings</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredHotels as $hotel)
                    <a href="{{ route('site.hotel.show', $hotel->slug) }}" class="group bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $hotel->thumbnail_url ?? '/images/hotel-placeholder.jpg' }}" alt="{{ $hotel->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @if($hotel->is_featured)
                                <span class="absolute top-3 left-3 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded">Featured</span>
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 flex items-center gap-1">
                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                <span class="font-semibold text-sm">{{ number_format($hotel->avg_rating, 1) }}</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 0; $i < $hotel->star_rating; $i++)
                                    <i class="fas fa-star text-yellow-500 text-xs"></i>
                                @endfor
                            </div>
                            <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $hotel->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $hotel->city?->name }}, {{ $hotel->country?->name }}
                            </p>
                            @if($hotel->min_price)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <span class="text-sm text-gray-500">From</span>
                                    <span class="text-xl font-bold text-blue-600">৳{{ number_format($hotel->min_price) }}</span>
                                    <span class="text-sm text-gray-500">/night</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popular Destinations -->
    @if(count($popularDestinations) > 0)
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Popular Destinations</h2>
                <p class="text-gray-600">Explore hotels in trending locations</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($popularDestinations as $destination)
                    <a href="{{ route('site.hotel.search', ['city_id' => $destination['city']->id]) }}"
                       class="group relative bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all">
                        <div class="aspect-square bg-gradient-to-br from-blue-500 to-purple-600 flex flex-col items-center justify-center text-white p-4">
                            <i class="fas fa-city text-4xl mb-3 group-hover:scale-110 transition-transform"></i>
                            <h3 class="font-bold text-center">{{ $destination['city']->name }}</h3>
                            <p class="text-sm text-white/80 mt-1">{{ $destination['hotels_count'] }} hotels</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Why Book With Us -->
    <section class="py-16 bg-gradient-to-br from-blue-50 to-indigo-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Why Book With Us?</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tag text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Best Price Guarantee</h3>
                    <p class="text-gray-600 text-sm">Find a lower price? We'll match it</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Free Cancellation</h3>
                    <p class="text-gray-600 text-sm">On most rooms, cancel for free</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-gray-600 text-sm">We're here to help anytime</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lock text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Secure Booking</h3>
                    <p class="text-gray-600 text-sm">Your payment is always safe</p>
                </div>
            </div>
        </div>
    </section>
@endsection
