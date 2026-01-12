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
                    <tour-card :tour="{{ json_encode([
                        'id' => $tour->id,
                        'url' => route('site.tour.show', $tour->slug),
                        'thumbnail_url' => $tour->thumbnail_url,
                        'name' => $tour->name,
                        'average_rating' => $tour->average_rating,
                        'is_bestseller' => $tour->is_bestseller,
                        'is_featured' => $tour->is_featured,
                        'category_name' => $tour->category->name ?? null,
                        'duration' => $tour->duration,
                        'destination_name' => $tour->destination->name ?? null,
                        'discount_price' => $tour->discount_price,
                        'base_price' => $tour->base_price
                    ]) }}"></tour-card>
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