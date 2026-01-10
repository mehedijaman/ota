@extends('site-layout')

@section('meta-title', $tour->meta_title ?: $tour->name . ' | ' . config('app.name'))

@section('meta-description', $tour->meta_description ?: $tour->excerpt)

@section('content')
    <!-- Tour Header -->
    <div class="relative bg-slate-900 pt-20 sm:pt-24 pb-32 sm:pb-40 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ $tour->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920' }}" alt="{{ $tour->name }}" class="w-full h-full object-cover opacity-40">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-white/70 text-xs sm:text-sm mb-4 flex-wrap">
                <a href="{{ route('site.tour.index') }}" class="hover:text-white transition-colors">Tours</a>
                <i class="ri-arrow-right-s-line"></i>
                @if($tour->category)
                <a href="{{ route('site.tour.search', ['category' => $tour->category->id]) }}" class="hover:text-white transition-colors">{{ $tour->category->name }}</a>
                <i class="ri-arrow-right-s-line"></i>
                @endif
                <span class="text-white truncate max-w-[200px] sm:max-w-none">{{ $tour->name }}</span>
            </nav>
            
            <!-- Title & Badges -->
            <div class="flex flex-wrap items-start gap-3 mb-4">
                @if($tour->is_featured)
                <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    <i class="ri-star-fill mr-1"></i>Featured
                </span>
                @endif
                @if($tour->is_bestseller)
                <span class="bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    <i class="ri-fire-fill mr-1"></i>Bestseller
                </span>
                @endif
            </div>
            
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">{{ $tour->name }}</h1>
            
            <!-- Quick Info -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-white/90 text-sm sm:text-base">
                <span class="flex items-center gap-1">
                    <i class="ri-time-line"></i>
                    {{ $tour->duration }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="ri-map-pin-line"></i>
                    {{ $tour->destination?->name ?? $tour->start_location }}
                </span>
                @if($tour->review_count > 0)
                <span class="flex items-center gap-1">
                    <i class="ri-star-fill text-amber-400"></i>
                    {{ number_format($tour->average_rating, 1) }} ({{ $tour->review_count }} reviews)
                </span>
                @endif
                @if($tour->languages && count($tour->languages))
                <span class="hidden sm:flex items-center gap-1 bg-white/20 backdrop-blur px-3 py-1 rounded-lg text-sm">
                    <i class="ri-translate-2"></i>
                    {{ implode(', ', $tour->languages) }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 sm:-mt-24 relative z-20 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Quick Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-white rounded-xl shadow-lg p-4 text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ri-calendar-line text-orange-600 text-lg sm:text-xl"></i>
                        </div>
                        <p class="text-xs text-slate-500">Duration</p>
                        <p class="font-bold text-slate-800 text-sm sm:text-base">{{ $tour->duration }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ri-map-pin-line text-blue-600 text-lg sm:text-xl"></i>
                        </div>
                        <p class="text-xs text-slate-500">Start</p>
                        <p class="font-bold text-slate-800 text-sm sm:text-base truncate">{{ $tour->start_location }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ri-flag-line text-green-600 text-lg sm:text-xl"></i>
                        </div>
                        <p class="text-xs text-slate-500">End</p>
                        <p class="font-bold text-slate-800 text-sm sm:text-base truncate">{{ $tour->end_location }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ri-star-fill text-amber-500 text-lg sm:text-xl"></i>
                        </div>
                        <p class="text-xs text-slate-500">Rating</p>
                        <p class="font-bold text-slate-800 text-sm sm:text-base">{{ $tour->review_count > 0 ? number_format($tour->average_rating, 1) . '/5' : 'New' }}</p>
                    </div>
                </div>

                <!-- Photo Gallery with Vue Carousel -->
                @php
                    $gallery = $tour->getMedia('gallery');
                    $galleryImages = $gallery->map(function($image) use ($tour) {
                        return [
                            'url' => $image->getUrl(),
                            'alt' => $tour->name . ' - Gallery Image'
                        ];
                    })->values()->toArray();
                @endphp
                
                @if(count($galleryImages) > 0)
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ri-image-line text-orange-500"></i>
                        Photo Gallery
                    </h2>
                    <tour-carousel :images='@json($galleryImages)'></tour-carousel>
                </div>
                @else
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ri-image-line text-orange-500"></i>
                        Photo Gallery
                    </h2>
                    <div class="aspect-video rounded-xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 relative">
                        @if($tour->thumbnail_url)
                        <img src="{{ $tour->thumbnail_url }}" alt="{{ $tour->name }}" class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 flex items-center justify-center {{ $tour->thumbnail_url ? 'bg-slate-900/20' : '' }}">
                            <div class="text-center {{ $tour->thumbnail_url ? 'text-white' : 'text-slate-400' }}">
                                <i class="ri-image-line text-4xl sm:text-5xl opacity-80"></i>
                                <p class="text-sm sm:text-base mt-2 opacity-80">{{ $tour->thumbnail_url ? 'Tour Preview' : 'No photos available' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Overview Card -->
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ri-information-line text-blue-500"></i>
                        Overview
                    </h2>
                    
                    @if($tour->excerpt)
                    <p class="text-slate-600 text-base sm:text-lg mb-4 leading-relaxed italic border-l-4 border-orange-300 pl-4 bg-orange-50 py-3 rounded-r-lg">
                        {{ $tour->excerpt }}
                    </p>
                    @endif

                    <div class="prose prose-slate prose-sm sm:prose-base max-w-none">
                        {!! $tour->description !!}
                    </div>

                    <!-- Tour Code & Category -->
                    <div class="mt-6 pt-6 border-t border-slate-100 flex flex-wrap gap-4 text-sm">
                        @if($tour->code)
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="ri-price-tag-3-line"></i>
                            Tour Code: <span class="font-mono font-bold text-slate-700">{{ $tour->code }}</span>
                        </div>
                        @endif
                        @if($tour->category)
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="ri-folder-line"></i>
                            Category: <a href="{{ route('site.tour.search', ['category' => $tour->category->id]) }}" class="font-medium text-orange-600 hover:underline">{{ $tour->category->name }}</a>
                        </div>
                        @endif
                        @if($tour->destination)
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="ri-compass-line"></i>
                            Destination: <span class="font-medium text-slate-700">{{ $tour->destination->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Highlights -->
                @if($tour->highlights->count())
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ri-star-line text-amber-500"></i>
                        Tour Highlights
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($tour->highlights as $highlight)
                        <div class="flex items-start gap-3 p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-100">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-500 rounded-lg flex items-center justify-center shrink-0">
                                <i class="{{ $highlight->icon ?: 'ri-checkbox-circle-fill' }} text-white text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 text-sm sm:text-base">{{ $highlight->title }}</p>
                                @if($highlight->description)
                                <p class="text-slate-500 text-xs sm:text-sm mt-1">{{ $highlight->description }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Day-by-Day Itinerary -->
                @if($tour->itineraries->count())
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ri-route-line text-orange-500"></i>
                        Day-by-Day Itinerary
                    </h2>
                    <div class="space-y-4 sm:space-y-6">
                        @foreach($tour->itineraries as $index => $day)
                        <div class="relative pl-6 sm:pl-10 pb-6 border-l-2 border-orange-200 last:border-0 last:pb-0">
                            <div class="absolute -left-3 sm:-left-4 top-0 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-red-500 text-white rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 sm:p-5 ml-2 sm:ml-0">
                                <h4 class="font-bold text-slate-800 text-base sm:text-lg mb-2">{{ $day->title }}</h4>
                                @if($day->description)
                                <p class="text-slate-600 text-sm mb-4 leading-relaxed">{{ $day->description }}</p>
                                @endif
                                
                                <!-- Day Meta Info -->
                                <div class="flex flex-wrap gap-2 sm:gap-3 text-xs sm:text-sm">
                                    @if($day->accommodation)
                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 sm:px-3 py-1 rounded-full">
                                        <i class="ri-hotel-line"></i>
                                        {{ $day->accommodation }}
                                    </span>
                                    @endif
                                    @if($day->distance_km)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full">
                                        <i class="ri-road-map-line"></i>
                                        {{ $day->distance_km }} km
                                    </span>
                                    @endif
                                    @if($day->meals_included && count($day->meals_included))
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 px-2 sm:px-3 py-1 rounded-full">
                                        <i class="ri-restaurant-line"></i>
                                        {{ ucwords(implode(', ', $day->meals_included)) }}
                                    </span>
                                    @endif
                                </div>

                                @if($day->activities && count($day->activities))
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <p class="text-xs text-slate-400 mb-2 font-medium uppercase tracking-wide">Activities</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($day->activities as $activity)
                                        <span class="bg-orange-100 text-orange-700 px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium">{{ $activity }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($day->highlights && count($day->highlights))
                                <div class="mt-3 pt-3 border-t border-slate-200">
                                    <p class="text-xs text-slate-400 mb-2 font-medium uppercase tracking-wide">Day Highlights</p>
                                    <ul class="space-y-1">
                                        @foreach($day->highlights as $dayHighlight)
                                        <li class="flex items-start gap-2 text-xs sm:text-sm text-slate-600">
                                            <i class="ri-arrow-right-s-line text-orange-500 mt-0.5"></i>
                                            {{ $dayHighlight }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Inclusions & Exclusions -->
                @php
                    $inclusions = $tour->inclusions->where('type', 'inclusion');
                    $exclusions = $tour->inclusions->where('type', 'exclusion');
                @endphp
                @if($inclusions->count() > 0 || $exclusions->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ri-list-check text-blue-500"></i>
                        What's Included & Excluded
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Inclusions -->
                        @if($inclusions->count() > 0)
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 sm:p-5 border-2 border-green-100">
                            <h3 class="font-bold text-green-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i class="ri-checkbox-circle-fill text-white text-lg"></i>
                                </div>
                                Included in Package
                            </h3>
                            <ul class="space-y-2 sm:space-y-3">
                                @foreach($inclusions as $item)
                                <li class="flex items-start gap-2 sm:gap-3 text-slate-700 text-sm sm:text-base group">
                                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-green-200 transition-colors">
                                        <i class="{{ $item->icon ?: 'ri-check-line' }} text-green-600 text-sm"></i>
                                    </div>
                                    <span class="leading-relaxed">{{ $item->title }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <!-- Exclusions -->
                        @if($exclusions->count() > 0)
                        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl p-4 sm:p-5 border-2 border-red-100">
                            <h3 class="font-bold text-red-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                    <i class="ri-close-circle-fill text-white text-lg"></i>
                                </div>
                                Not Included
                            </h3>
                            <ul class="space-y-2 sm:space-y-3">
                                @foreach($exclusions as $item)
                                <li class="flex items-start gap-2 sm:gap-3 text-slate-700 text-sm sm:text-base group">
                                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-red-200 transition-colors">
                                        <i class="{{ $item->icon ?: 'ri-close-line' }} text-red-600 text-sm"></i>
                                    </div>
                                    <span class="leading-relaxed">{{ $item->title }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Optional Extras -->
                @if($tour->extras->where('is_active', true)->count())
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ri-add-box-line text-purple-500"></i>
                        Optional Extras
                    </h2>
                    <p class="text-slate-500 text-sm mb-4">Enhance your experience with these add-ons</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($tour->extras->where('is_active', true) as $extra)
                        <div class="border border-slate-200 rounded-xl p-4 hover:border-purple-300 hover:shadow-md transition-all {{ $extra->is_required ? 'bg-purple-50 border-purple-200' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-800 text-sm sm:text-base flex items-center gap-2">
                                        {{ $extra->name }}
                                        @if($extra->is_required)
                                        <span class="text-xs bg-purple-500 text-white px-2 py-0.5 rounded">Required</span>
                                        @endif
                                    </h4>
                                    @if($extra->description)
                                    <p class="text-slate-500 text-xs sm:text-sm mt-1">{{ $extra->description }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-orange-600 font-bold text-sm sm:text-base">৳{{ number_format($extra->price) }}</p>
                                    <p class="text-slate-400 text-xs">{{ str_replace('_', ' ', $extra->price_type) }}</p>
                                </div>
                            </div>
                            @if($extra->max_quantity)
                            <p class="text-xs text-slate-400 mt-2">Max: {{ $extra->max_quantity }} per booking</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Cancellation Policy -->
                @if($tour->cancellationPolicies->where('is_active', true)->count())
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ri-shield-check-line text-teal-500"></i>
                        Cancellation Policy
                    </h2>
                    <div class="space-y-3">
                        @foreach($tour->cancellationPolicies->where('is_active', true)->sortByDesc('days_before_departure') as $policy)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl {{ $policy->is_default ? 'bg-teal-50 border-2 border-teal-200' : 'bg-slate-50' }} gap-3">
                            <div>
                                <h4 class="font-semibold text-slate-800 text-sm sm:text-base flex items-center gap-2 flex-wrap">
                                    {{ $policy->name }}
                                    @if($policy->is_default)
                                    <span class="text-xs bg-teal-500 text-white px-2 py-0.5 rounded">Default</span>
                                    @endif
                                </h4>
                                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                                    {{ $policy->days_before_departure }}+ days before departure
                                    @if($policy->description)
                                    — {{ $policy->description }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <p class="text-lg sm:text-xl font-bold {{ $policy->refund_percentage >= 75 ? 'text-green-600' : ($policy->refund_percentage >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $policy->refund_percentage }}% refund
                                </p>
                                @if($policy->cancellation_fee > 0)
                                <p class="text-xs text-slate-400">৳{{ number_format($policy->cancellation_fee) }} processing fee</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- FAQs -->
                @if($tour->faqs->where('is_active', true)->count())
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ri-question-line text-indigo-500"></i>
                        Frequently Asked Questions
                    </h2>
                    <div class="space-y-3">
                        @foreach($tour->faqs->where('is_active', true) as $faq)
                        <details class="group border border-slate-200 rounded-xl overflow-hidden">
                            <summary class="p-4 cursor-pointer flex items-center justify-between font-medium text-slate-800 hover:bg-slate-50 transition-colors text-sm sm:text-base">
                                <span class="pr-4">{{ $faq->question }}</span>
                                <i class="ri-arrow-down-s-line text-slate-400 group-open:rotate-180 transition-transform shrink-0"></i>
                            </summary>
                            <div class="px-4 pb-4 text-slate-600 text-sm sm:text-base leading-relaxed border-t border-slate-100 pt-3 bg-slate-50">
                                {{ $faq->answer }}
                            </div>
                        </details>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reviews -->
                @if($tour->reviews->count())
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ri-chat-quote-line text-pink-500"></i>
                        Reviews ({{ $tour->reviews->count() }})
                    </h2>
                    <div class="space-y-4 sm:space-y-6">
                        @foreach($tour->reviews->take(5) as $review)
                        <div class="border-b border-slate-100 last:border-0 pb-4 sm:pb-6 last:pb-0">
                            <div class="flex items-start sm:items-center justify-between mb-3 flex-col sm:flex-row gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-400 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm sm:text-lg">
                                        {{ strtoupper(substr($review->reviewer_name ?: 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-sm sm:text-base">{{ $review->reviewer_name ?: 'Anonymous' }}</p>
                                        <p class="text-xs text-slate-500">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'ri-star-fill text-amber-400' : 'ri-star-line text-slate-300' }} text-sm sm:text-base"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($review->title)
                            <h4 class="font-semibold text-slate-800 mb-2 text-sm sm:text-base">{{ $review->title }}</h4>
                            @endif
                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">{{ $review->comment }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Write a Review Form -->
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="ri-edit-line text-cyan-500"></i>
                        Write a Review
                    </h2>

                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm">
                        <i class="ri-check-line text-lg"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('site.tour.submitReview', $tour->slug) }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Star Rating -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Your Rating *</label>
                            <div class="flex items-center gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    onclick="document.getElementById('rating').value = {{ $i }}; this.parentElement.querySelectorAll('i').forEach((el, idx) => { el.className = idx < {{ $i }} ? 'ri-star-fill text-2xl sm:text-3xl text-amber-400 cursor-pointer' : 'ri-star-line text-2xl sm:text-3xl text-slate-300 cursor-pointer'; })"
                                    class="focus:outline-none">
                                    <i class="ri-star-line text-2xl sm:text-3xl text-slate-300 cursor-pointer hover:text-amber-400 transition-colors"></i>
                                </button>
                                @endfor
                                <input type="hidden" name="rating" id="rating" value="{{ old('rating', 5) }}" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Your Name *</label>
                                <input type="text" name="reviewer_name" value="{{ old('reviewer_name', auth()->user()?->name) }}" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm sm:text-base">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email *</label>
                                <input type="email" name="reviewer_email" value="{{ old('reviewer_email', auth()->user()?->email) }}" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm sm:text-base">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Review Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="Summarize your experience" class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm sm:text-base">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Travel Date</label>
                                <input type="date" name="travel_date" value="{{ old('travel_date') }}" max="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm sm:text-base">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Your Review *</label>
                            <textarea name="comment" rows="4" required minlength="10" placeholder="Share your experience with this tour..." class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm sm:text-base">{{ old('comment') }}</textarea>
                            <p class="text-xs text-slate-500 mt-1">Minimum 10 characters</p>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white py-3 px-6 rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm sm:text-base shadow-lg shadow-orange-500/25">
                            <i class="ri-send-plane-line"></i>
                            Submit Review
                        </button>

                        <p class="text-xs text-slate-500 text-center">Your review will be published after approval by our team.</p>
                    </form>
                </div>
            </div>

            <!-- Sidebar: Booking Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 sticky top-20 sm:top-24 space-y-4 sm:space-y-6">
                    <!-- Pricing -->
                    @php
                        $activePricings = $tour->pricings->where('is_active', true);
                        $defaultPricing = $activePricings->first();
                    @endphp
                    
                    @if($activePricings->count())
                    <div>
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Starting From</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl sm:text-4xl font-bold text-orange-600">৳{{ number_format($defaultPricing->adult_price) }}</span>
                        </div>
                        <span class="text-sm text-slate-500">per adult</span>
                        
                        @if($activePricings->count() > 1)
                        <div class="mt-4 space-y-2">
                            <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Pricing Options</p>
                            @foreach($activePricings as $pricing)
                            <div class="bg-slate-50 rounded-xl p-3 {{ $loop->first ? 'ring-2 ring-orange-200' : '' }}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold text-slate-700 text-sm">{{ $pricing->name }}</span>
                                        @if($loop->first)
                                        <span class="ml-2 text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded">Popular</span>
                                        @endif
                                    </div>
                                    <span class="text-orange-600 font-bold">৳{{ number_format($pricing->adult_price) }}</span>
                                </div>
                                <div class="flex gap-3 text-xs text-slate-400 mt-1">
                                    @if($pricing->child_price)
                                    <span>Child: ৳{{ number_format($pricing->child_price) }}</span>
                                    @endif
                                    @if($pricing->infant_price)
                                    <span>Infant: ৳{{ number_format($pricing->infant_price) }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @else
                    <div>
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Price</span>
                        <p class="text-xl font-bold text-slate-600 mt-1">Contact for pricing</p>
                    </div>
                    @endif

                    <!-- Validity -->
                    @if($tour->valid_from || $tour->valid_to)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-2">Tour Validity</p>
                        <div class="flex items-center gap-2 text-slate-700 text-sm">
                            <i class="ri-calendar-check-line text-orange-500"></i>
                            {{ $tour->valid_from?->format('M d, Y') ?? 'Anytime' }} - {{ $tour->valid_to?->format('M d, Y') ?? 'Ongoing' }}
                        </div>
                    </div>
                    @endif

                    <!-- Age Restrictions -->
                    @if($tour->age_restrictions && (isset($tour->age_restrictions['min_age']) || isset($tour->age_restrictions['max_age'])))
                    <div class="bg-amber-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-2">Age Requirements</p>
                        <div class="flex items-center gap-2 text-slate-700 text-sm">
                            <i class="ri-user-line text-amber-500"></i>
                            @if(isset($tour->age_restrictions['min_age']) && isset($tour->age_restrictions['max_age']) && $tour->age_restrictions['max_age'])
                            Ages {{ $tour->age_restrictions['min_age'] }} - {{ $tour->age_restrictions['max_age'] }}
                            @elseif(isset($tour->age_restrictions['min_age']))
                            Minimum age: {{ $tour->age_restrictions['min_age'] }}+
                            @elseif(isset($tour->age_restrictions['max_age']))
                            Maximum age: {{ $tour->age_restrictions['max_age'] }}
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Languages -->
                    @if($tour->languages && count($tour->languages))
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-2">Available Languages</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tour->languages as $language)
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                <i class="ri-translate-2"></i>
                                {{ $language }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Book Now Button -->
                    <a href="{{ route('site.tour.booking', $tour->slug) }}" class="block w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white py-4 px-6 rounded-xl font-bold text-center shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02] text-base sm:text-lg">
                        <i class="ri-calendar-check-line mr-2"></i>
                        Book Now
                    </a>

                    <!-- Quick Info -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-time-line text-blue-500"></i>
                            <span>Duration: <strong>{{ $tour->duration }}</strong></span>
                        </div>
                        @if($tour->start_location)
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-map-pin-line text-green-500"></i>
                            <span>Starts: <strong>{{ $tour->start_location }}</strong></span>
                        </div>
                        @endif
                        @if($tour->end_location)
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-flag-line text-red-500"></i>
                            <span>Ends: <strong>{{ $tour->end_location }}</strong></span>
                        </div>
                        @endif
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-customer-service-2-line text-orange-500"></i>
                            <span>24/7 Customer support</span>
                        </div>
                    </div>

                    <!-- Social Share Buttons -->
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-3">Share This Tour</p>
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('site.tour.show', $tour->slug)) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-3 rounded-lg font-medium transition-all text-xs sm:text-sm group">
                                <i class="ri-facebook-fill text-base group-hover:scale-110 transition-transform"></i>
                                <span class="hidden sm:inline">Facebook</span>
                            </a>
                            <!-- Twitter -->
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('site.tour.show', $tour->slug)) }}&text={{ urlencode($tour->name) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white py-2.5 px-3 rounded-lg font-medium transition-all text-xs sm:text-sm group">
                                <i class="ri-twitter-x-fill text-base group-hover:scale-110 transition-transform"></i>
                                <span class="hidden sm:inline">Twitter</span>
                            </a>
                            <!-- WhatsApp -->
                            <a href="https://wa.me/?text={{ urlencode($tour->name . ' - ' . route('site.tour.show', $tour->slug)) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white py-2.5 px-3 rounded-lg font-medium transition-all text-xs sm:text-sm group">
                                <i class="ri-whatsapp-fill text-base group-hover:scale-110 transition-transform"></i>
                                <span class="hidden sm:inline">WhatsApp</span>
                            </a>
                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('site.tour.show', $tour->slug)) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white py-2.5 px-3 rounded-lg font-medium transition-all text-xs sm:text-sm group">
                                <i class="ri-linkedin-fill text-base group-hover:scale-110 transition-transform"></i>
                                <span class="hidden sm:inline">LinkedIn</span>
                            </a>
                            <!-- Email -->
                            <a href="mailto:?subject={{ urlencode($tour->name) }}&body={{ urlencode('Check out this tour: ' . route('site.tour.show', $tour->slug)) }}" class="flex items-center justify-center gap-2 bg-slate-600 hover:bg-slate-700 text-white py-2.5 px-3 rounded-lg font-medium transition-all text-xs sm:text-sm group">
                                <i class="ri-mail-fill text-base group-hover:scale-110 transition-transform"></i>
                                <span class="hidden sm:inline">Email</span>
                            </a>
                            <!-- Copy Link -->
                            <button onclick="navigator.clipboard.writeText('{{ route('site.tour.show', $tour->slug) }}'); this.innerHTML = '<i class=\"ri-check-line text-base\"></i><span class=\"hidden sm:inline\">Copied!</span>'; setTimeout(() => { this.innerHTML = '<i class=\"ri-link text-base\"></i><span class=\"hidden sm:inline\">Copy Link</span>'; }, 2000);" class="flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white py-2.5 px-3 rounded-lg font-medium transition-all text-xs sm:text-sm group">
                                <i class="ri-link text-base group-hover:scale-110 transition-transform"></i>
                                <span class="hidden sm:inline">Copy Link</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Tours -->
    @if($relatedTours->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 sm:mb-8 flex items-center gap-2">
            <i class="ri-compass-3-line text-orange-500"></i>
            You May Also Like
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($relatedTours as $related)
            @php
                $relatedPrice = $related->pricings->where('is_active', true)->first()?->adult_price ?? 0;
            @endphp
            <a href="{{ route('site.tour.show', $related->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-40 sm:h-48 overflow-hidden relative">
                    <img src="{{ $related->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800' }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-lg text-xs sm:text-sm font-bold text-slate-800 shadow-sm">
                        {{ $related->duration }}
                    </div>
                    @if($related->is_featured)
                    <div class="absolute top-3 left-3 bg-amber-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                        Featured
                    </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-slate-800 mb-2 group-hover:text-orange-600 transition-colors text-sm sm:text-base line-clamp-2">{{ $related->name }}</h3>
                    <div class="flex items-center justify-between">
                        @if($relatedPrice > 0)
                        <span class="text-base sm:text-lg font-bold text-orange-600">৳{{ number_format($relatedPrice) }}</span>
                        @else
                        <span class="text-sm text-slate-500">Contact for price</span>
                        @endif
                        <i class="ri-arrow-right-line text-orange-600 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
@endsection

