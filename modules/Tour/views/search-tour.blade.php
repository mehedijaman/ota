@extends('site-layout')

@section('meta-title', 'Search Tours | ' . config('app.name'))

@section('meta-description', 'Find your perfect tour package. Filter by destination, category, and duration.')

@section('content')
    <!-- Search Header -->
    <x-tour-search-form :categories="$categories" :destinations="$destinations" />

    <!-- Results -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-6">
            <p class="text-slate-600">
                <span class="font-bold text-slate-800">{{ $tours->total() }}</span> tours found
            </p>
        </div>

        @if($tours->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tours as $tour)
                    <a href="{{ route('site.tour.show', $tour->slug) }}"
                        class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden">
                        <div class="h-56 overflow-hidden relative">
                            <img src="{{ $tour->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800' }}"
                                alt="{{ $tour->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div
                                class="absolute top-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-sm font-bold text-slate-800 shadow-sm">
                                {{ $tour->duration }}
                            </div>
                            @if($tour->is_featured)
                                <div
                                    class="absolute top-3 left-3 bg-orange-500 text-white px-3 py-1 rounded-lg text-xs font-bold shadow-sm">
                                    Featured
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                @if($tour->category)
                                    <span
                                        class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-lg">{{ $tour->category->name }}</span>
                                @endif
                                @if($tour->destination)
                                    <span
                                        class="text-xs bg-orange-50 text-orange-600 px-2 py-1 rounded-lg">{{ $tour->destination->name }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 mb-1 group-hover:text-orange-600 transition-colors">
                                {{ $tour->name }}</h3>
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2">
                                {{ $tour->excerpt ?: Str::limit(strip_tags($tour->description), 100) }}</p>

                            <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
                                <span class="flex items-center gap-1">
                                    <i class="ri-time-line"></i>
                                    {{ $tour->duration }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="ri-group-line"></i>
                                    Max {{ $tour->max_group_size ?? 20 }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-400 font-medium uppercase">From</span>
                                    @if($tour->discount_price && $tour->discount_price < $tour->base_price)
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-orange-600">৳
                                                {{ number_format($tour->discount_price) }}</span>
                                            <span class="text-sm text-slate-400 line-through">৳
                                                {{ number_format($tour->base_price) }}</span>
                                        </div>
                                    @else
                                        <span class="text-lg font-bold text-orange-600">৳ {{ number_format($tour->base_price) }}</span>
                                    @endif
                                </div>
                                <span
                                    class="bg-orange-50 text-orange-600 py-2 px-4 rounded-lg font-medium group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                    View Details
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $tours->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-slate-50 rounded-2xl">
                <i class="ri-compass-3-line text-6xl text-slate-300 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-700 mb-2">No tours found</h3>
                <p class="text-slate-500 mb-6">Try adjusting your search filters</p>
                <a href="{{ route('site.tour.search') }}"
                    class="inline-flex items-center gap-2 bg-orange-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-orange-700 transition-colors">
                    <i class="ri-refresh-line"></i>
                    Clear Filters
                </a>
            </div>
        @endif
    </section>
@endsection