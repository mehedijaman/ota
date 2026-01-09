@extends('site-layout')

@section('meta-title', $tour->meta_title ?: $tour->name . ' | ' . config('app.name'))

@section('meta-description', $tour->meta_description ?: $tour->excerpt)

@section('content')
    <!-- Tour Header -->
    <div class="relative bg-slate-900 pt-24 pb-40 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ $tour->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920' }}" alt="{{ $tour->name }}" class="w-full h-full object-cover opacity-40">
        </div>
        <div class="absolute inset-0 bg-linear-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.tour.index') }}" class="hover:text-white transition-colors">Tours</a>
                <i class="ri-arrow-right-s-line"></i>
                @if($tour->category)
                <a href="{{ route('site.tour.search', ['category' => $tour->category->id]) }}" class="hover:text-white transition-colors">{{ $tour->category->name }}</a>
                <i class="ri-arrow-right-s-line"></i>
                @endif
                <span class="text-white">{{ $tour->name }}</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $tour->name }}</h1>
            <div class="flex flex-wrap items-center gap-4 text-white/90">
                <span class="flex items-center gap-1">
                    <i class="ri-time-line"></i>
                    {{ $tour->duration }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="ri-map-pin-line"></i>
                    {{ $tour->destination?->name ?? $tour->start_location }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="ri-star-fill text-amber-400"></i>
                    {{ number_format($tour->average_rating, 1) }} ({{ $tour->review_count }} reviews)
                </span>
                @if($tour->difficulty_level)
                <span class="bg-white/20 backdrop-blur px-3 py-1 rounded-lg text-sm capitalize">
                    {{ $tour->difficulty_level }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Overview Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Overview</h2>
                    <div class="prose prose-slate max-w-none">
                        {!! nl2br(e($tour->description)) !!}
                    </div>

                    @if($tour->highlights->count())
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h3 class="font-bold text-slate-800 mb-4">Highlights</h3>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($tour->highlights as $highlight)
                            <li class="flex items-start gap-2">
                                <i class="ri-checkbox-circle-fill text-green-500 mt-0.5"></i>
                                <span class="text-slate-600">{{ $highlight->title }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Itinerary -->
                @if($tour->itineraries->count())
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6">Day-by-Day Itinerary</h2>
                    <div class="space-y-6">
                        @foreach($tour->itineraries as $day)
                        <div class="relative pl-8 pb-6 border-l-2 border-orange-200 last:border-0 last:pb-0">
                            <div class="absolute -left-3 top-0 w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                {{ $day->day_number }}
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4">
                                <h4 class="font-bold text-slate-800 mb-2">Day {{ $day->day_number }}: {{ $day->title }}</h4>
                                <p class="text-slate-600 text-sm">{{ $day->description }}</p>
                                @if($day->accommodation)
                                <div class="flex items-center gap-2 mt-3 text-sm text-slate-500">
                                    <i class="ri-hotel-line"></i>
                                    {{ $day->accommodation }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Inclusions -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6">What's Included</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-green-600 mb-3 flex items-center gap-2">
                                <i class="ri-checkbox-circle-fill"></i>
                                Included
                            </h3>
                            <ul class="space-y-2">
                                @foreach($tour->inclusions->where('type', 'inclusion') as $item)
                                <li class="flex items-start gap-2 text-slate-600">
                                    <i class="ri-check-line text-green-500 mt-0.5"></i>
                                    {{ $item->title }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-medium text-red-600 mb-3 flex items-center gap-2">
                                <i class="ri-close-circle-fill"></i>
                                Not Included
                            </h3>
                            <ul class="space-y-2">
                                @foreach($tour->inclusions->where('type', 'exclusion') as $item)
                                <li class="flex items-start gap-2 text-slate-600">
                                    <i class="ri-close-line text-red-500 mt-0.5"></i>
                                    {{ $item->title }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQs -->
                @if($tour->faqs->count())
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        @foreach($tour->faqs as $faq)
                        <details class="group border border-slate-200 rounded-xl">
                            <summary class="p-4 cursor-pointer flex items-center justify-between font-medium text-slate-800 hover:bg-slate-50 rounded-xl">
                                {{ $faq->question }}
                                <i class="ri-arrow-down-s-line text-slate-400 group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <div class="px-4 pb-4 text-slate-600">
                                {{ $faq->answer }}
                            </div>
                        </details>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reviews -->
                @if($tour->reviews->count())
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6">Reviews</h2>
                    <div class="space-y-6">
                        @foreach($tour->reviews as $review)
                        <div class="border-b border-slate-100 last:border-0 pb-6 last:pb-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold">
                                        {{ strtoupper(substr($review->reviewer_name ?: 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $review->reviewer_name ?: 'Anonymous' }}</p>
                                        <p class="text-xs text-slate-500">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'ri-star-fill text-amber-400' : 'ri-star-line text-slate-300' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($review->title)
                            <h4 class="font-medium text-slate-800 mb-1">{{ $review->title }}</h4>
                            @endif
                            <p class="text-slate-600">{{ $review->comment }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Write a Review Form -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6">Write a Review</h2>

                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                        <i class="ri-check-line mr-2"></i>{{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                        <ul class="list-disc list-inside text-sm">
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
                            <div class="flex items-center gap-2" x-data="{ rating: 0, hover: 0 }">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    onclick="document.getElementById('rating').value = {{ $i }}; this.parentElement.querySelectorAll('i').forEach((el, idx) => { el.className = idx < {{ $i }} ? 'ri-star-fill text-2xl text-amber-400 cursor-pointer' : 'ri-star-line text-2xl text-slate-300 cursor-pointer'; })"
                                    class="focus:outline-none">
                                    <i class="ri-star-line text-2xl text-slate-300 cursor-pointer hover:text-amber-400 transition-colors"></i>
                                </button>
                                @endfor
                                <input type="hidden" name="rating" id="rating" value="{{ old('rating', 5) }}" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Your Name *</label>
                                <input type="text" name="reviewer_name" value="{{ old('reviewer_name', auth()->user()?->name) }}" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email *</label>
                                <input type="email" name="reviewer_email" value="{{ old('reviewer_email', auth()->user()?->email) }}" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Review Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="Summarize your experience" class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Travel Date</label>
                                <input type="date" name="travel_date" value="{{ old('travel_date') }}" max="{{ date('Y-m-d') }}" class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Your Review *</label>
                            <textarea name="comment" rows="4" required minlength="10" placeholder="Share your experience with this tour..." class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">{{ old('comment') }}</textarea>
                            <p class="text-xs text-slate-500 mt-1">Minimum 10 characters</p>
                        </div>

                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-6 rounded-xl font-bold transition-colors flex items-center justify-center gap-2">
                            <i class="ri-send-plane-line"></i>
                            Submit Review
                        </button>

                        <p class="text-xs text-slate-500 text-center">Your review will be published after approval by our team.</p>
                    </form>
                </div>
            </div>

            <!-- Sidebar: Booking Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <div class="mb-6">
                        <span class="text-xs text-slate-400 font-medium uppercase">From</span>
                        <div class="flex items-baseline gap-2">
                            @if($tour->discount_price && $tour->discount_price < $tour->base_price)
                            <span class="text-3xl font-bold text-orange-600">৳ {{ number_format($tour->discount_price) }}</span>
                            <span class="text-lg text-slate-400 line-through">৳ {{ number_format($tour->base_price) }}</span>
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-sm font-medium">
                                {{ round((($tour->base_price - $tour->discount_price) / $tour->base_price) * 100) }}% OFF
                            </span>
                            @else
                            <span class="text-3xl font-bold text-orange-600">৳ {{ number_format($tour->base_price) }}</span>
                            @endif
                        </div>
                        <span class="text-sm text-slate-500">per person</span>
                    </div>

                    @if($tour->departureDates->count())
                    <div class="mb-6">
                        <label class="text-sm font-medium text-slate-700 mb-2 block">Select Departure Date</label>
                        <select class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            @foreach($tour->departureDates as $date)
                            <option value="{{ $date->id }}">
                                {{ $date->departure_date->format('M d, Y') }} 
                                ({{ $date->available_slots - $date->booked_slots }} slots left)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <a href="{{ route('site.tour.booking', $tour->slug) }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white py-4 px-6 rounded-xl font-bold text-center shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02]">
                        Book Now
                    </a>

                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-shield-check-line text-green-500"></i>
                            Free cancellation up to {{ $tour->cancellation_days ?? 7 }} days
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-group-line text-blue-500"></i>
                            {{ $tour->tour_type === 'private' ? 'Private tour' : 'Group size: ' . ($tour->min_group_size ?? 2) . '-' . ($tour->max_group_size ?? 20) }}
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="ri-customer-service-2-line text-orange-500"></i>
                            24/7 Customer support
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100 flex gap-3">
                        <button class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="ri-heart-line"></i>
                            Save
                        </button>
                        <button class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="ri-share-line"></i>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Tours -->
    @if($relatedTours->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold text-slate-800 mb-8">You May Also Like</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedTours as $related)
            <a href="{{ route('site.tour.show', $related->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                <div class="h-48 overflow-hidden relative">
                    <img src="{{ $related->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800' }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">
                        {{ $related->duration }}
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-slate-800 mb-2 group-hover:text-orange-600 transition-colors">{{ $related->name }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-orange-600">৳ {{ number_format($related->discount_price ?? $related->base_price) }}</span>
                        <i class="ri-arrow-right-line text-orange-600"></i>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
@endsection
