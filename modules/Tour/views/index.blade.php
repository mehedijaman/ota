@extends('site-layout')

@section('meta-title', 'Explore Tours | ' . config('app.name'))

@section('meta-description', 'Discover amazing tour packages to Bangladesh\'s most beautiful destinations. Adventure awaits!')

@section('content')
    <!-- Tour Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                alt="Tour Background" class="w-full h-full object-cover">
        </div>

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Explore <span
                    class="text-transparent bg-clip-text bg-linear-to-r from-orange-300 to-amber-200">Bangladesh</span>
            </h1>
            <p class="text-lg md:text-xl text-orange-50 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Unforgettable journeys, curated just for you.
            </p>

            <!-- Tour Search Form -->
            <form action="{{ route('site.tour.search') }}" method="GET"
                class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-4xl mx-auto text-left ring-1 ring-white/20">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="space-y-1.5 relative md:col-span-3 lg:col-span-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Search</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i
                                    class="ri-search-line text-slate-400 group-focus-within:text-orange-500 transition-colors"></i>
                            </div>
                            <input name="q"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700"
                                placeholder="Where do you want to go?">
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Category</label>
                        <select name="category"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Duration</label>
                        <select name="duration"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                            <option value="">Any Duration</option>
                            <option value="1-3">1-3 Days</option>
                            <option value="4-7">4-7 Days</option>
                            <option value="7+">7+ Days</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto bg-linear-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class="ri-search-line"></i>
                        Search Tours
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Featured Destinations -->
    @if($destinations->count())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-800">Popular Destinations</h2>
                <p class="text-slate-500 mt-2">Explore Bangladesh's most beautiful places</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($destinations as $destination)
                    <a href="{{ route('site.tour.search', ['destination' => $destination->id]) }}"
                        class="group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg hover:shadow-xl transition-all">
                        <img src="{{ $destination->image_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400' }}"
                            alt="{{ $destination->name }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="font-bold text-lg">{{ $destination->name }}</h3>
                            <p class="text-sm text-white/80">{{ $destination->tours_count ?? 0 }} Tours</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured Tours -->
    @if($featuredTours->count())
        <section class="bg-slate-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-slate-800">Featured Tour Packages</h2>
                    <p class="text-slate-500 mt-2">Curated experiences for every traveler</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredTours as $tour)
                        <a href="{{ route('site.tour.show', $tour->slug) }}"
                            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                            <div class="relative aspect-4/5 overflow-hidden rounded-2xl group">
                                <img src="{{ $tour->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800' }}"
                                    alt="{{ $tour->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div
                                    class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">
                                    {{ $tour->duration }}
                                </div>
                                @if($tour->is_bestseller)
                                    <div
                                        class="absolute top-3 left-3 bg-orange-500 text-white px-3 py-1 rounded-lg text-xs font-bold shadow-sm">
                                        Bestseller
                                    </div>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-1 mb-2">
                                    <i class="ri-star-fill text-orange-500"></i>
                                    <span class="text-sm font-medium text-slate-600">{{ number_format($tour->average_rating, 1) }}
                                        ({{ $tour->review_count }} Reviews)</span>
                                </div>
                                <h3 class="font-bold text-lg text-slate-800 mb-1 group-hover:text-orange-600 transition-colors">
                                    {{ $tour->name }}</h3>
                                <p class="text-slate-500 text-sm mb-4 line-clamp-2">
                                    {{ $tour->excerpt ?: Str::limit(strip_tags($tour->description), 80) }}</p>
                                <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-slate-400 font-medium uppercase">Starts from</span>
                                        @if($tour->discount_price && $tour->discount_price < $tour->base_price)
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg font-bold text-orange-600">৳
                                                    {{ number_format($tour->discount_price) }}</span>
                                                <span class="text-sm text-slate-400 line-through">৳
                                                    {{ number_format($tour->base_price) }}</span>
                                            </div>
                                        @else
                                            <span class="text-lg font-bold text-orange-600">৳
                                                {{ number_format($tour->base_price) }}</span>
                                        @endif
                                    </div>
                                    <span
                                        class="bg-orange-50 text-orange-600 p-2 rounded-lg group-hover:bg-orange-100 transition-colors">
                                        <i class="ri-arrow-right-line"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-10">
                    <a href="{{ route('site.tour.search') }}"
                        class="inline-flex items-center gap-2 bg-white border-2 border-orange-500 text-orange-600 px-8 py-3 rounded-xl font-bold hover:bg-orange-500 hover:text-white transition-all">
                        View All Tours
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Categories -->
    @if($categories->count())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-800">Browse by Category</h2>
                <p class="text-slate-500 mt-2">Find your perfect adventure</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('site.tour.search', ['category' => $category->id]) }}"
                        class="group bg-white rounded-2xl p-6 text-center border border-slate-100 hover:border-orange-200 hover:shadow-lg transition-all">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                            <i class="{{ $category->icon ?: 'ri-compass-3-line' }} text-3xl text-orange-600"></i>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-1">{{ $category->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $category->tours_count }} Tours</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- CTA Section -->
    <section class="bg-linear-to-r from-orange-600 to-amber-600 py-16">
        <div class="max-w-4xl mx-auto px-4 text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready for Your Next Adventure?</h2>
            <p class="text-orange-100 text-lg mb-8">Contact us to plan your custom tour package</p>
            <a href="/contact"
                class="inline-flex items-center gap-2 bg-white text-orange-600 px-8 py-4 rounded-xl font-bold hover:bg-orange-50 transition-all shadow-lg">
                <i class="ri-phone-line"></i>
                Contact Us
            </a>
        </div>
    </section>
@endsection