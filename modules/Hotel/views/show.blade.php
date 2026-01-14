@extends('site-layout')

@section('title', $hotel->name . ' - ' . $hotel->city?->name)

@section('meta')
    <meta name="description" content="{{ $hotel->meta_description ?? $hotel->excerpt }}">
    <meta name="keywords" content="{{ $hotel->meta_keywords }}">
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-gray-100 border-b">
        <div class="container mx-auto px-4 py-3">
            <nav class="text-sm">
                <a href="{{ route('site.hotel.index') }}" class="text-blue-600 hover:text-blue-800">Hotels</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('site.hotel.search', ['city_id' => $hotel->city_id]) }}" class="text-blue-600 hover:text-blue-800">{{ $hotel->city?->name }}</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-600">{{ $hotel->name }}</span>
            </nav>
        </div>
    </div>

    <div class="bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <!-- Hotel Header -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                <!-- Gallery -->
                <div class="grid grid-cols-4 gap-1 h-80">
                    <div class="col-span-2 row-span-2">
                        <img src="{{ $hotel->thumbnail_url ?? '/images/hotel-placeholder.jpg' }}" alt="{{ $hotel->name }}"
                             class="w-full h-full object-cover">
                    </div>
                    @foreach($hotel->gallery_urls ?? [] as $index => $image)
                        @if($index < 4)
                            <div>
                                <img src="{{ $image }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @for($i = 0; $i < $hotel->star_rating; $i++)
                                    <i class="fas fa-star text-yellow-500"></i>
                                @endfor
                                <span class="text-sm text-gray-500 ml-2">{{ ucfirst($hotel->property_type) }}</span>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $hotel->name }}</h1>
                            <p class="text-gray-600 mt-2">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                {{ $hotel->full_address }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            @if($hotel->avg_rating)
                                <div class="bg-blue-600 text-white rounded-lg px-4 py-3 text-center">
                                    <div class="text-2xl font-bold">{{ number_format($hotel->avg_rating, 1) }}</div>
                                    <div class="text-sm">{{ $hotel->reviews_count }} reviews</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Description -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">About This Hotel</h2>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($hotel->description)) !!}
                        </div>
                    </div>

                    <!-- Amenities -->
                    @if($hotel->amenities->count())
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Amenities</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($hotel->amenities as $amenity)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-{{ $amenity->icon ?? 'check' }} text-blue-600"></i>
                                        </div>
                                        <span class="text-gray-700">{{ $amenity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Rooms -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Available Rooms</h2>
                        <div class="space-y-4">
                            @foreach($hotel->rooms as $room)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <div class="md:w-48 flex-shrink-0">
                                            <div class="aspect-[4/3] bg-gray-200 rounded-lg overflow-hidden">
                                                <img src="{{ $room->thumbnail_url ?? '/images/room-placeholder.jpg' }}" alt="{{ $room->name }}"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900">{{ $room->name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $room->roomType?->name }}</p>

                                            <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                                <span><i class="fas fa-bed mr-1"></i> {{ $room->bed_count }} {{ Str::title($room->bed_type) }}</span>
                                                <span><i class="fas fa-users mr-1"></i> Max {{ $room->max_occupancy }} guests</span>
                                                @if($room->room_size_sqm)
                                                    <span><i class="fas fa-expand-arrows-alt mr-1"></i> {{ $room->room_size_sqm }} m²</span>
                                                @endif
                                            </div>

                                            @if($room->amenities->count())
                                                <div class="flex flex-wrap gap-2 mt-3">
                                                    @foreach($room->amenities->take(4) as $amenity)
                                                        <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $amenity->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="md:w-40 flex-shrink-0 text-right">
                                            @if($room->base_rate)
                                                <div class="text-2xl font-bold text-blue-600">৳{{ number_format($room->base_rate) }}</div>
                                                <div class="text-sm text-gray-500">per night</div>
                                            @endif
                                            <a href="{{ route('site.hotel.booking', ['slug' => $hotel->slug, 'room_id' => $room->id, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}"
                                               class="inline-block mt-3 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                                Select Room
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reviews -->
                    @if($hotel->reviews->count())
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Guest Reviews</h2>
                            <div class="space-y-4">
                                @foreach($hotel->reviews as $review)
                                    <div class="border-b border-gray-100 pb-4 last:border-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $review->reviewer_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded">
                                                {{ number_format($review->overall_rating, 1) }}
                                            </div>
                                        </div>
                                        <h4 class="font-medium text-gray-900">{{ $review->title }}</h4>
                                        <p class="text-gray-600 text-sm mt-1">{{ $review->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- FAQs -->
                    @if($hotel->faqs->count())
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                            <div class="space-y-4">
                                @foreach($hotel->faqs as $faq)
                                    <details class="group">
                                        <summary class="flex items-center justify-between cursor-pointer font-medium text-gray-900 py-3 border-b border-gray-100">
                                            {{ $faq->question }}
                                            <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
                                        </summary>
                                        <p class="text-gray-600 py-3">{{ $faq->answer }}</p>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside class="space-y-6">
                    <!-- Booking Card -->
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">Check Availability</h3>
                        <form action="{{ route('site.hotel.show', $hotel->slug) }}" method="GET" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                                <input type="date" name="check_in" value="{{ request('check_in', date('Y-m-d')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                                <input type="date" name="check_out" value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rooms</label>
                                    <input type="number" name="rooms" value="{{ request('rooms', 1) }}" min="1"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Adults</label>
                                    <input type="number" name="adults" value="{{ request('adults', 2) }}" min="1"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Children</label>
                                    <input type="number" name="children" value="{{ request('children', 0) }}" min="0"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                Check Availability
                            </button>
                        </form>
                    </div>

                    <!-- Hotel Info -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">Hotel Info</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-clock text-blue-600 w-5"></i>
                                <span><strong>Check-in:</strong> {{ $hotel->check_in_time }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-clock text-blue-600 w-5"></i>
                                <span><strong>Check-out:</strong> {{ $hotel->check_out_time }}</span>
                            </div>
                            @if($hotel->phone)
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-phone text-blue-600 w-5"></i>
                                    <a href="tel:{{ $hotel->phone }}" class="text-blue-600 hover:underline">{{ $hotel->phone }}</a>
                                </div>
                            @endif
                            @if($hotel->email)
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-envelope text-blue-600 w-5"></i>
                                    <a href="mailto:{{ $hotel->email }}" class="text-blue-600 hover:underline">{{ $hotel->email }}</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Map -->
                    @if($hotel->latitude && $hotel->longitude)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-4">Location</h3>
                            <div class="aspect-video bg-gray-200 rounded-lg">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ $hotel->latitude }},{{ $hotel->longitude }}&z=15&output=embed"
                                    class="w-full h-full rounded-lg"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                            </div>
                        </div>
                    @endif
                </aside>
            </div>

            <!-- Related Hotels -->
            @if($relatedHotels->count())
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Hotels Nearby</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedHotels as $related)
                            <a href="{{ route('site.hotel.show', $related->slug) }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="aspect-[4/3]">
                                    <img src="{{ $related->thumbnail_url ?? '/images/hotel-placeholder.jpg' }}" alt="{{ $related->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center gap-1 mb-1">
                                        @for($i = 0; $i < $related->star_rating; $i++)
                                            <i class="fas fa-star text-yellow-500 text-xs"></i>
                                        @endfor
                                    </div>
                                    <h3 class="font-bold text-gray-900">{{ $related->name }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
