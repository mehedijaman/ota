@php
    use Modules\Tour\Models\Tour;

    $featuredTours = Tour::query()
        ->active()
        ->featured()
        ->with(['category', 'destination'])
        ->take(8)
        ->get();
@endphp

<!-- TOURS -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Featured Tour Packages</h2>
            <p class="text-slate-500 mt-2">Curated experiences for unforgettable memories</p>
        </div>
        <a href="{{ route('site.tour.search') }}" class="text-orange-600 font-medium hover:text-orange-700 flex items-center gap-1 group">
            See All Packages
            <i class="ri-arrow-right-line transform group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>

    @if($featuredTours->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredTours as $tour)
        <a href="{{ route('site.tour.show', $tour->slug) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden border border-gray-100 flex flex-col">
            <div class="relative h-48 overflow-hidden">
                <img src="{{ $tour->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800' }}" alt="{{ $tour->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-slate-800 flex items-center gap-1">
                    <i class="ri-star-fill text-amber-400"></i>
                    <span>{{ number_format($tour->average_rating ?? 4.5, 1) }}</span>
                </div>
                @if($tour->is_bestseller)
                <div class="absolute top-3 left-3 bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                    Bestseller
                </div>
                @endif
            </div>
            <div class="p-5 flex flex-col flex-1">
                @if($tour->category)
                <div class="text-xs font-bold text-orange-600 mb-2 uppercase tracking-wide">{{ $tour->category->name }}</div>
                @endif
                <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-orange-600 transition-colors">{{ $tour->name }}</h3>
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                    <i class="ri-time-line"></i>
                    <span>{{ $tour->duration }}</span>
                    @if($tour->destination)
                    <span class="mx-1">•</span>
                    <i class="ri-map-pin-line"></i>
                    <span>{{ $tour->destination->name }}</span>
                    @endif
                </div>
                <div class="mt-auto flex items-end justify-between border-t border-gray-100 pt-4">
                    <div>
                        <span class="text-xs text-slate-400 block">From</span>
                        @if($tour->discount_price && $tour->discount_price < $tour->base_price)
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-orange-600">৳{{ number_format($tour->discount_price) }}</span>
                            <span class="text-sm text-slate-400 line-through">৳{{ number_format($tour->base_price) }}</span>
                        </div>
                        @else
                        <span class="text-lg font-bold text-slate-800">৳{{ number_format($tour->base_price) }}</span>
                        @endif
                    </div>
                    <span class="text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        View Details
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 bg-slate-50 rounded-2xl">
        <i class="ri-compass-3-line text-5xl text-slate-300 mb-4"></i>
        <h3 class="text-xl font-bold text-slate-700 mb-2">No Featured Tours</h3>
        <p class="text-slate-500">Check back soon for exciting tour packages!</p>
    </div>
    @endif
</section>