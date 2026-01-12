@extends('site-layout')

@section('meta-title', 'Explore Tours | ' . config('app.name'))

@section('meta-description', 'Discover amazing tour packages to Bangladesh\'s most beautiful destinations. Adventure awaits!')

@section('content')

    <x-tour-search-form :categories="$categories" :destinations="$destinations" />

    <!-- Featured Destinations -->
    @if($destinations->count())
        <featured-tour-destinations-carousel :destinations="{{ json_encode($destinations->map(function ($destination) {
                return [
                    'id' => $destination->id,
                    'url' => route('site.tour.search', ['destination' => $destination->id]),
                    'image_url' => $destination->image_url,
                    'name' => $destination->name,
                    'tours_count' => $destination->tours_count ?? 0
                ];
            })) }}"></featured-tour-destinations-carousel>
    @endif

    <!-- Featured Tours -->
    <featured-tours-carousel :tours="{{ json_encode($featuredTours->map(function ($tour) {
        return [
            'id' => $tour->id,
            'url' => route('site.tour.show', $tour->slug),
            'thumbnail_url' => $tour->thumbnail_url,
            'name' => $tour->name,
            'average_rating' => $tour->average_rating,
            'review_count' => $tour->review_count,
            'is_bestseller' => $tour->is_bestseller,
            'category_name' => $tour->category->name ?? null,
            'duration' => $tour->duration,
            'destination_name' => $tour->destination->name ?? null,
            'discount_price' => $tour->discount_price,
            'base_price' => $tour->base_price,
            'min_price' => $tour->pricings_min_adult_price,
            'excerpt' => $tour->excerpt ?: Str::limit(strip_tags($tour->description), 80)
        ];
    })) }}"></featured-tours-carousel>

    <!-- Categories -->
    @if($categories->count())
        <featured-tour-categories-carousel :categories="{{ json_encode($categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'url' => route('site.tour.search', ['category' => $category->id]),
                    'name' => $category->name,
                    'tours_count' => $category->tours_count,
                    'icon' => $category->icon
                ];
            })) }}"></featured-tour-categories-carousel>
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