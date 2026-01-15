@extends('site-layout')

@section('meta-title', 'Find Your Perfect Hotel | ' . config('app.name'))

@section('meta-description', 'Discover amazing hotels at the best prices. Book your perfect stay in Bangladesh\'s top destinations.')

@section('content')

    <x-hotel-search-form :countries="$countries" />

    <!-- Featured Hotels -->
    @if($featuredHotels->count())
        <featured-hotels-carousel :hotels="{{ json_encode($featuredHotels->map(function ($hotel) {
                return [
                    'id' => $hotel->id,
                    'url' => route('site.hotel.show', $hotel->slug),
                    'thumbnail_url' => $hotel->thumbnail_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
                    'name' => $hotel->name,
                    'star_rating' => $hotel->star_rating,
                    'avg_rating' => $hotel->avg_rating,
                    'reviews_count' => $hotel->reviews_count,
                    'is_featured' => $hotel->is_featured,
                    'city_name' => $hotel->city?->name,
                    'country_name' => $hotel->country?->name,
                    'min_price' => $hotel->min_price,
                    'excerpt' => $hotel->excerpt ?: Str::limit(strip_tags($hotel->description), 80)
                ];
            })) }}"></featured-hotels-carousel>
    @endif

    <!-- Popular Destinations -->
    @if(count($popularDestinations) > 0)
        <featured-hotel-destinations-carousel :destinations="{{ json_encode(collect($popularDestinations)->map(function ($destination) {
                return [
                    'id' => $destination['city']->id,
                    'url' => route('site.hotel.search', ['city_id' => $destination['city']->id]),
                    'image_url' => $destination['city']->image_url ?? null,
                    'name' => $destination['city']->name,
                    'hotels_count' => $destination['hotels_count'] ?? 0
                ];
            })) }}"></featured-hotel-destinations-carousel>
    @endif

    <!-- CTA Section -->
    <section class="bg-linear-to-r from-blue-600 to-indigo-600 py-16">
        <div class="max-w-4xl mx-auto px-4 text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Book Your Stay?</h2>
            <p class="text-blue-100 text-lg mb-8">Contact us for special rates and exclusive deals</p>
            <a href="/contact"
                class="inline-flex items-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-blue-50 transition-all shadow-lg">
                <i class="ri-phone-line"></i>
                Contact Us
            </a>
        </div>
    </section>
@endsection