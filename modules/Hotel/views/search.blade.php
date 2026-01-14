@extends('site-layout')

@section('title', 'Search Hotels - ' . ($filters['destination'] ?? 'All Locations'))

@section('content')
    <div class="bg-gray-50 min-h-screen">
        <!-- Search Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="container mx-auto px-4 py-4">
                <form action="{{ route('site.hotel.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <input type="text" name="destination" placeholder="Destination" value="{{ $filters['destination'] ?? '' }}"
                           class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <input type="date" name="check_in" value="{{ $filters['check_in'] ?? date('Y-m-d') }}"
                           class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <input type="date" name="check_out" value="{{ $filters['check_out'] ?? date('Y-m-d', strtotime('+1 day')) }}"
                           class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <div class="flex gap-2">
                        <input type="number" name="rooms" value="{{ $filters['rooms'] ?? 1 }}" min="1" placeholder="Rooms"
                               class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        <input type="number" name="adults" value="{{ $filters['adults'] ?? 2 }}" min="1" placeholder="Guests"
                               class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </form>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <aside class="lg:w-72 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">Filters</h3>

                        <!-- Star Rating -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-3">Star Rating</h4>
                            <div class="space-y-2">
                                @foreach([5, 4, 3, 2, 1] as $stars)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="star_rating[]" value="{{ $stars }}"
                                               {{ in_array($stars, (array)($filters['star_rating'] ?? [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <div class="flex items-center">
                                            @for($i = 0; $i < $stars; $i++)
                                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                            @endfor
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Property Type -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-3">Property Type</h4>
                            <div class="space-y-2">
                                @foreach($propertyTypes as $type)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="property_type[]" value="{{ $type['value'] }}"
                                               {{ in_array($type['value'], (array)($filters['property_type'] ?? [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-gray-700">{{ $type['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">Popular Amenities</h4>
                            <div class="space-y-2">
                                @foreach(collect($amenities)->flatten(1)->take(8) as $amenity)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity['id'] }}"
                                               {{ in_array($amenity['id'], (array)($filters['amenities'] ?? [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-gray-700">{{ $amenity['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Results -->
                <main class="flex-1">
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-gray-600">
                            <span class="font-semibold text-gray-900">{{ $hotels->total() }}</span> hotels found
                        </p>
                        <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="recommended" {{ ($filters['sort_by'] ?? '') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                            <option value="price_low" {{ ($filters['sort_by'] ?? '') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ ($filters['sort_by'] ?? '') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ ($filters['sort_by'] ?? '') == 'rating' ? 'selected' : '' }}>Guest Rating</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        @forelse($hotels as $hotel)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="flex flex-col md:flex-row">
                                    <div class="md:w-72 flex-shrink-0">
                                        <div class="aspect-[4/3] md:h-full">
                                            <img src="{{ $hotel->thumbnail_url ?? '/images/hotel-placeholder.jpg' }}" alt="{{ $hotel->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <div class="flex-1 p-5">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center gap-1 mb-1">
                                                    @for($i = 0; $i < $hotel->star_rating; $i++)
                                                        <i class="fas fa-star text-yellow-500 text-xs"></i>
                                                    @endfor
                                                </div>
                                                <h3 class="text-xl font-bold text-gray-900 hover:text-blue-600">
                                                    <a href="{{ route('site.hotel.show', $hotel->slug) }}">{{ $hotel->name }}</a>
                                                </h3>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $hotel->city?->name }}, {{ $hotel->country?->name }}
                                                </p>
                                            </div>
                                            @if($hotel->avg_rating)
                                                <div class="bg-blue-600 text-white rounded-lg px-3 py-2 text-center">
                                                    <div class="text-lg font-bold">{{ number_format($hotel->avg_rating, 1) }}</div>
                                                    <div class="text-xs">{{ $hotel->reviews_count }} reviews</div>
                                                </div>
                                            @endif
                                        </div>

                                        @if($hotel->excerpt)
                                            <p class="text-gray-600 text-sm mt-3 line-clamp-2">{{ $hotel->excerpt }}</p>
                                        @endif

                                        <!-- Amenity Tags -->
                                        @if($hotel->amenities->count())
                                            <div class="flex flex-wrap gap-2 mt-3">
                                                @foreach($hotel->amenities->take(5) as $amenity)
                                                    <span class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                                        <i class="fas fa-{{ $amenity->icon }}"></i>
                                                        {{ $amenity->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="flex items-end justify-between mt-4 pt-4 border-t border-gray-100">
                                            @if($hotel->min_price)
                                                <div>
                                                    <span class="text-sm text-gray-500">From</span>
                                                    <span class="text-2xl font-bold text-blue-600 ml-1">৳{{ number_format($hotel->min_price) }}</span>
                                                    <span class="text-sm text-gray-500">/night</span>
                                                </div>
                                            @else
                                                <div></div>
                                            @endif
                                            <a href="{{ route('site.hotel.show', ['slug' => $hotel->slug, 'check_in' => $filters['check_in'] ?? '', 'check_out' => $filters['check_out'] ?? '']) }}"
                                               class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                                <i class="fas fa-hotel text-gray-300 text-6xl mb-4"></i>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">No Hotels Found</h3>
                                <p class="text-gray-600">Try adjusting your search criteria or filters</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($hotels->hasPages())
                        <div class="mt-8">
                            {{ $hotels->links() }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
