@extends('site-layout')

@section('meta-title', 'Search Hotels | ' . config('app.name'))

@section('meta-description', 'Find your perfect hotel stay. Filter by destination, star rating, and amenities.')

@section('content')
    <!-- Search Header -->
    <x-hotel-search-form :countries="$countries ?? collect()" />

    <!-- Results Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6 sticky top-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg text-slate-800">Filters</h3>
                        @if(request()->hasAny(['star_rating', 'property_type', 'amenities']))
                            <a href="{{ route('site.hotel.search', ['destination' => $filters['destination'] ?? '', 'check_in' => $filters['check_in'] ?? '', 'check_out' => $filters['check_out'] ?? '']) }}"
                               class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Clear All
                            </a>
                        @endif
                    </div>

                    <form id="filterForm" action="{{ route('site.hotel.search') }}" method="GET">
                        <!-- Hidden fields to preserve search params -->
                        <input type="hidden" name="destination" value="{{ $filters['destination'] ?? '' }}">
                        <input type="hidden" name="check_in" value="{{ $filters['check_in'] ?? '' }}">
                        <input type="hidden" name="check_out" value="{{ $filters['check_out'] ?? '' }}">
                        <input type="hidden" name="adults" value="{{ $filters['adults'] ?? 2 }}">

                        <!-- Star Rating -->
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <h4 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
                                <i class="ri-star-line text-amber-500"></i>
                                Star Rating
                            </h4>
                            <div class="space-y-3">
                                @foreach([5, 4, 3, 2, 1] as $stars)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="star_rating[]" value="{{ $stars }}"
                                               {{ in_array($stars, (array)($filters['star_rating'] ?? [])) ? 'checked' : '' }}
                                               onchange="document.getElementById('filterForm').submit()"
                                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 0; $i < $stars; $i++)
                                                <i class="ri-star-fill text-amber-400 text-sm"></i>
                                            @endfor
                                            @for($i = $stars; $i < 5; $i++)
                                                <i class="ri-star-line text-slate-300 text-sm"></i>
                                            @endfor
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Property Type -->
                        @if(isset($propertyTypes) && count($propertyTypes))
                        <div class="mb-6 pb-6 border-b border-slate-100">
                            <h4 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
                                <i class="ri-building-line text-blue-500"></i>
                                Property Type
                            </h4>
                            <div class="space-y-3">
                                @foreach($propertyTypes as $type)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="property_type[]" value="{{ $type['value'] }}"
                                               {{ in_array($type['value'], (array)($filters['property_type'] ?? [])) ? 'checked' : '' }}
                                               onchange="document.getElementById('filterForm').submit()"
                                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-slate-600 group-hover:text-slate-800 transition-colors">{{ $type['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Amenities -->
                        @if(isset($amenities) && count($amenities))
                        <div>
                            <h4 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
                                <i class="ri-service-line text-green-500"></i>
                                Popular Amenities
                            </h4>
                            <div class="space-y-3">
                                @foreach(collect($amenities)->flatten(1)->take(8) as $amenity)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity['id'] }}"
                                               {{ in_array($amenity['id'], (array)($filters['amenities'] ?? [])) ? 'checked' : '' }}
                                               onchange="document.getElementById('filterForm').submit()"
                                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-slate-600 group-hover:text-slate-800 transition-colors">{{ $amenity['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </aside>

            <!-- Results -->
            <main class="flex-1">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">
                            @if($filters['destination'] ?? null)
                                Hotels in {{ $filters['destination'] }}
                            @else
                                All Hotels
                            @endif
                        </h2>
                        <p class="text-slate-500 mt-1">
                            <span class="font-semibold text-slate-700">{{ $hotels->total() }}</span> properties found
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-500">Sort by:</span>
                        <select name="sort_by" 
                                onchange="window.location.href = '{{ route('site.hotel.search') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort_by: this.value})"
                                class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="recommended" {{ ($filters['sort_by'] ?? '') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                            <option value="price_low" {{ ($filters['sort_by'] ?? '') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ ($filters['sort_by'] ?? '') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ ($filters['sort_by'] ?? '') == 'rating' ? 'selected' : '' }}>Guest Rating</option>
                        </select>
                    </div>
                </div>

                <!-- Hotel Grid -->
                @if($hotels->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($hotels as $hotel)
                            <hotel-card :hotel="{{ json_encode([
                                'id' => $hotel->id,
                                'url' => route('site.hotel.show', ['slug' => $hotel->slug, 'check_in' => $filters['check_in'] ?? '', 'check_out' => $filters['check_out'] ?? '']),
                                'thumbnail_url' => $hotel->thumbnail_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
                                'name' => $hotel->name,
                                'star_rating' => $hotel->star_rating,
                                'avg_rating' => $hotel->avg_rating,
                                'reviews_count' => $hotel->reviews_count,
                                'is_featured' => $hotel->is_featured,
                                'city_name' => $hotel->city?->name,
                                'country_name' => $hotel->country?->name,
                                'min_price' => $hotel->min_price,
                                'excerpt' => $hotel->excerpt ?: Str::limit(strip_tags($hotel->description ?? ''), 80)
                            ]) }}"></hotel-card>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($hotels->hasPages())
                        <div class="mt-10">
                            {{ $hotels->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 bg-slate-50 rounded-2xl">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ri-hotel-line text-4xl text-slate-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700 mb-2">No hotels found</h3>
                        <p class="text-slate-500 mb-8 max-w-md mx-auto">
                            We couldn't find any hotels matching your criteria. Try adjusting your filters or search for a different destination.
                        </p>
                        <a href="{{ route('site.hotel.search') }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/25">
                            <i class="ri-refresh-line"></i>
                            Clear All Filters
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </section>
@endsection
