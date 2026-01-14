@extends('site-layout')

@section('meta-title', 'Apply for Visas Online | TriptonicBD')

@section('meta-description', 'Hassle-free visa application assistance for top destinations. Get your visa approved quickly with TriptonicBD.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-visa-search-form :categories="$categories" :countries="$countries"/>

    <!-- Popular Visa Destinations -->
    @php
        $destinations = $popularCountries->map(fn($country) => [
            "id" => $country->id,
            "name" => $country->name,
            "slug" => $country->slug,
            "url" => route('site.visa.country', $country->slug),
            "image" => $country->hasMedia("image") ? $country->getFirstMediaUrl("image") : null,
            "continent" => $country->continent,
            "visas_count" => $country->visas_count
        ]);
    @endphp

    <featured-visa-destinations-carousel :destinations='@json($destinations)'></featured-visa-destinations-carousel>

    <!-- Featured Visas -->
    @if($featuredVisas->isNotEmpty())
        @php
            $visas = $featuredVisas->map(fn($visa) => [
                "id" => $visa->id,
                "name" => $visa->name,
                "slug" => $visa->slug,
                "url" => route('site.visa.show', $visa->slug),
                "image" => $visa->hasMedia("thumbnail")
                    ? $visa->getFirstMediaUrl("thumbnail")
                    : ($visa->country && $visa->country->hasMedia("image")
                        ? $visa->country->getFirstMediaUrl("image")
                        : null),
                "country" => $visa->country?->name,
                "category" => $visa->category?->name,
                "description" => $visa->short_description ?? Str::limit(strip_tags($visa->description), 100),
                "price" => number_format($visa->base_price),
                "processing_time" => $visa->processing_days_min ? $visa->processing_days_min . ' Days' : 'Varies',
                "is_featured" => $visa->is_featured,
                "is_popular" => $visa->is_popular ?? false
            ]);
        @endphp

        <featured-visa-carousel :visas='@json($visas)'></featured-visa-carousel>
    @endif

    <!-- Why Choose Us -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-slate-50 mb-16 rounded-3xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-4">
                <div
                    class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2">99% Success Rate</h3>
                <p class="text-slate-500 text-sm">Our experts review every application to ensure maximum approval chances.
                </p>
            </div>
            <div class="p-4">
                <div
                    class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Fast Processing</h3>
                <p class="text-slate-500 text-sm">We process your visa application faster than standard agencies.</p>
            </div>
            <div class="p-4">
                <div
                    class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2">24/7 Support</h3>
                <p class="text-slate-500 text-sm">Our support team is always available to answer your queries.</p>
            </div>
        </div>
    </section>
@endsection