@extends('site-layout')

@section('meta-title', $hotel->name . ' | ' . $hotel->city?->name . ' | ' . config('app.name'))

@section('meta-description', $hotel->meta_description ?? $hotel->excerpt ?? Str::limit(strip_tags($hotel->description), 160))

@section('content')
    <!-- Hero Gallery Section -->
    <section class="relative bg-slate-900">
        <div class="max-w-7xl mx-auto">
            <!-- Gallery Grid -->
            <div class="grid grid-cols-4 grid-rows-2 gap-1 h-[450px]">
                <div class="col-span-2 row-span-2 relative group overflow-hidden">
                    <img src="{{ $hotel->thumbnail_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80' }}" 
                         alt="{{ $hotel->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-linear-to-t from-slate-900/60 to-transparent"></div>
                </div>
                @foreach($hotel->gallery_urls ?? [] as $index => $image)
                    @if($index < 4)
                        <div class="relative group overflow-hidden">
                            <img src="{{ $image }}" alt="{{ $hotel->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @endif
                @endforeach
                @for($i = count($hotel->gallery_urls ?? []); $i < 4; $i++)
                    <div class="bg-slate-800 flex items-center justify-center">
                        <i class="ri-image-line text-3xl text-slate-600"></i>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('site.hotel.index') }}" class="text-blue-600 hover:text-blue-700 transition-colors">Hotels</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <a href="{{ route('site.hotel.search', ['city_id' => $hotel->city_id]) }}" class="text-blue-600 hover:text-blue-700 transition-colors">{{ $hotel->city?->name }}</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <span class="text-slate-500 truncate max-w-[200px]">{{ $hotel->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hotel Header Card -->
            <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div class="flex-1">
                        <!-- Star Rating & Type -->
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex items-center gap-0.5">
                                @for($i = 0; $i < ($hotel->star_rating ?? 3); $i++)
                                    <i class="ri-star-fill text-amber-400 text-lg"></i>
                                @endfor
                            </div>
                            <span class="text-sm text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ ucfirst($hotel->property_type ?? 'Hotel') }}</span>
                        </div>

                        <!-- Hotel Name -->
                        <h1 class="text-3xl lg:text-4xl font-bold text-slate-800 mb-3">{{ $hotel->name }}</h1>

                        <!-- Location -->
                        <div class="flex items-center gap-2 text-slate-600">
                            <i class="ri-map-pin-2-fill text-blue-500"></i>
                            <span>{{ $hotel->full_address ?? ($hotel->city?->name . ', ' . ($hotel->country?->name ?? 'Bangladesh')) }}</span>
                        </div>
                    </div>

                    <!-- Rating Badge -->
                    @if($hotel->avg_rating)
                        <div class="flex items-center gap-4">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-slate-700">
                                    @if($hotel->avg_rating >= 4.5) Exceptional
                                    @elseif($hotel->avg_rating >= 4) Excellent
                                    @elseif($hotel->avg_rating >= 3.5) Very Good
                                    @else Good
                                    @endif
                                </p>
                                <p class="text-sm text-slate-500">{{ $hotel->reviews_count }} reviews</p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl px-4 py-3 text-center min-w-[70px]">
                                <div class="text-2xl font-bold">{{ number_format($hotel->avg_rating, 1) }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- About Section -->
                    <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ri-information-line text-blue-500"></i>
                            About This Property
                        </h2>
                        <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                            {!! nl2br(e($hotel->description ?? 'Experience comfort and luxury at this exceptional property.')) !!}
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    @if($hotel->amenities && $hotel->amenities->count())
                        <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                                <i class="ri-service-line text-blue-500"></i>
                                Amenities & Services
                            </h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($hotel->amenities as $amenity)
                                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                        <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                            <i class="ri-{{ $amenity->icon ?? 'check-line' }} text-blue-500 group-hover:text-white transition-colors"></i>
                                        </div>
                                        <span class="text-slate-700 font-medium">{{ $amenity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Available Rooms Section -->
                    <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <i class="ri-hotel-bed-line text-blue-500"></i>
                            Available Rooms
                        </h2>
                        <div class="space-y-4">
                            @forelse($hotel->rooms ?? [] as $room)
                                <div class="border border-slate-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-md transition-all">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <!-- Room Image -->
                                        <div class="md:w-52 flex-shrink-0">
                                            <div class="aspect-4/3 bg-slate-100 rounded-lg overflow-hidden">
                                                <img src="{{ $room->thumbnail_url ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400&q=80' }}" 
                                                     alt="{{ $room->name }}"
                                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                            </div>
                                        </div>

                                        <!-- Room Details -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-800">{{ $room->name }}</h3>
                                                    <p class="text-sm text-slate-500">{{ $room->roomType?->name }}</p>
                                                </div>
                                            </div>

                                            <!-- Room Features -->
                                            <div class="flex flex-wrap gap-4 mt-3 text-sm text-slate-600">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="ri-hotel-bed-line text-blue-500"></i>
                                                    {{ $room->bed_count ?? 1 }} {{ Str::title($room->bed_type ?? 'Bed') }}
                                                </span>
                                                <span class="flex items-center gap-1.5">
                                                    <i class="ri-user-line text-blue-500"></i>
                                                    Max {{ $room->max_occupancy ?? 2 }} guests
                                                </span>
                                                @if($room->room_size_sqm)
                                                    <span class="flex items-center gap-1.5">
                                                        <i class="ri-ruler-line text-blue-500"></i>
                                                        {{ $room->room_size_sqm }} m²
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Room Amenities Tags -->
                                            @if($room->amenities && $room->amenities->count())
                                                <div class="flex flex-wrap gap-2 mt-3">
                                                    @foreach($room->amenities->take(4) as $amenity)
                                                        <span class="text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">{{ $amenity->name }}</span>
                                                    @endforeach
                                                    @if($room->amenities->count() > 4)
                                                        <span class="text-xs text-blue-600 font-medium">+{{ $room->amenities->count() - 4 }} more</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Price & CTA -->
                                        <div class="md:w-44 flex-shrink-0 flex flex-col items-end justify-between">
                                            <div class="text-right">
                                                @if($room->base_rate)
                                                    <div class="text-2xl font-bold text-blue-600">৳{{ number_format($room->base_rate) }}</div>
                                                    <div class="text-sm text-slate-500">per night</div>
                                                @endif
                                            </div>
                                            <a href="{{ route('site.hotel.booking', ['slug' => $hotel->slug, 'room_id' => $room->id, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}"
                                               class="mt-3 inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-colors shadow-lg shadow-blue-500/25">
                                                <i class="ri-check-line"></i>
                                                Select Room
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 bg-slate-50 rounded-xl">
                                    <i class="ri-hotel-bed-line text-4xl text-slate-300 mb-3"></i>
                                    <p class="text-slate-500">No rooms available. Please check back later.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    @if($hotel->reviews && $hotel->reviews->count())
                        <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                                <i class="ri-chat-quote-line text-blue-500"></i>
                                Guest Reviews
                            </h2>
                            <div class="space-y-5">
                                @foreach($hotel->reviews as $review)
                                    <div class="pb-5 border-b border-slate-100 last:border-0 last:pb-0">
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-11 h-11 bg-linear-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($review->reviewer_name ?? 'G', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-slate-800">{{ $review->reviewer_name ?? 'Guest' }}</p>
                                                    <p class="text-xs text-slate-500">{{ $review->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="bg-blue-600 text-white font-bold px-3 py-1.5 rounded-lg text-sm">
                                                {{ number_format($review->overall_rating, 1) }}
                                            </div>
                                        </div>
                                        @if($review->title)
                                            <h4 class="font-semibold text-slate-800 mb-1">{{ $review->title }}</h4>
                                        @endif
                                        <p class="text-slate-600 leading-relaxed">{{ $review->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- FAQs Section -->
                    @if($hotel->faqs && $hotel->faqs->count())
                        <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                                <i class="ri-question-answer-line text-blue-500"></i>
                                Frequently Asked Questions
                            </h2>
                            <div class="space-y-3">
                                @foreach($hotel->faqs as $faq)
                                    <details class="group bg-slate-50 rounded-xl">
                                        <summary class="flex items-center justify-between cursor-pointer font-medium text-slate-800 p-4 hover:bg-slate-100 rounded-xl transition-colors">
                                            <span>{{ $faq->question }}</span>
                                            <i class="ri-add-line text-blue-500 group-open:hidden"></i>
                                            <i class="ri-subtract-line text-blue-500 hidden group-open:block"></i>
                                        </summary>
                                        <div class="px-4 pb-4 text-slate-600">
                                            {{ $faq->answer }}
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside class="space-y-6">
                    <!-- Booking Card -->
                    <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6 sticky top-4">
                        <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                            <i class="ri-calendar-check-line text-blue-500"></i>
                            Check Availability
                        </h3>
                        <form action="{{ route('site.hotel.show', $hotel->slug) }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Check-in</label>
                                    <div class="relative">
                                        <i class="ri-calendar-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        <input type="date" name="check_in" value="{{ request('check_in', date('Y-m-d')) }}"
                                               class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Check-out</label>
                                    <div class="relative">
                                        <i class="ri-calendar-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                        <input type="date" name="check_out" value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                                               class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Rooms</label>
                                    <select name="rooms" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ request('rooms', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Adults</label>
                                    <select name="adults" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ request('adults', 2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Children</label>
                                    <select name="children" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
                                        @for($i = 0; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ request('children', 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                                <i class="ri-search-line"></i>
                                Check Availability
                            </button>
                        </form>
                    </div>

                    <!-- Hotel Info Card -->
                    <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                            <i class="ri-hotel-line text-blue-500"></i>
                            Property Info
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="ri-login-box-line text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Check-in</p>
                                    <p class="font-semibold text-slate-800">{{ $hotel->check_in_time ?? '2:00 PM' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="ri-logout-box-line text-red-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Check-out</p>
                                    <p class="font-semibold text-slate-800">{{ $hotel->check_out_time ?? '12:00 PM' }}</p>
                                </div>
                            </div>
                            @if($hotel->phone)
                                <a href="tel:{{ $hotel->phone }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                        <i class="ri-phone-line text-blue-600 group-hover:text-white transition-colors"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase tracking-wider">Phone</p>
                                        <p class="font-semibold text-blue-600">{{ $hotel->phone }}</p>
                                    </div>
                                </a>
                            @endif
                            @if($hotel->email)
                                <a href="mailto:{{ $hotel->email }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                        <i class="ri-mail-line text-blue-600 group-hover:text-white transition-colors"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase tracking-wider">Email</p>
                                        <p class="font-semibold text-blue-600 text-sm truncate">{{ $hotel->email }}</p>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Map Card -->
                    @if($hotel->latitude && $hotel->longitude)
                        <div class="bg-white rounded-2xl shadow-lg ring-1 ring-slate-100 p-6">
                            <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                                <i class="ri-map-pin-line text-blue-500"></i>
                                Location
                            </h3>
                            <div class="aspect-video bg-slate-100 rounded-xl overflow-hidden">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ $hotel->latitude }},{{ $hotel->longitude }}&z=15&output=embed"
                                    class="w-full h-full"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                            </div>
                            <a href="https://maps.google.com/maps?q={{ $hotel->latitude }},{{ $hotel->longitude }}" 
                               target="_blank"
                               class="mt-4 flex items-center justify-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm">
                                <i class="ri-external-link-line"></i>
                                Open in Google Maps
                            </a>
                        </div>
                    @endif
                </aside>
            </div>

            <!-- Related Hotels -->
            @if(isset($relatedHotels) && $relatedHotels->count())
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ri-hotel-line text-blue-500"></i>
                        Similar Hotels Nearby
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedHotels as $related)
                                                <hotel-card :hotel="{{ json_encode([
                                'id' => $related->id,
                                'url' => route('site.hotel.show', $related->slug),
                                'thumbnail_url' => $related->thumbnail_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
                                'name' => $related->name,
                                'star_rating' => $related->star_rating,
                                'avg_rating' => $related->avg_rating,
                                'reviews_count' => $related->reviews_count,
                                'is_featured' => $related->is_featured,
                                'city_name' => $related->city?->name,
                                'country_name' => $related->country?->name,
                                'min_price' => $related->min_price
                            ]) }}"></hotel-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
