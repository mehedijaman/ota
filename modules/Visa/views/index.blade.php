@extends('site-layout')

@section('meta-title', 'Apply for Visas Online | TriptonicBD')

@section('meta-description', 'Hassle-free visa application assistance for top destinations. Get your visa approved quickly with TriptonicBD.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Visa Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1544733422-251e532ca221?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                alt="Visa Passport Background" class="w-full h-full object-cover">
        </div>

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Get Your Visa <span
                    class="text-transparent bg-clip-text bg-linear-to-r from-purple-300 to-fuchsia-200">Hassle-Free</span>
            </h1>
            <p class="text-lg md:text-xl text-purple-100 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Fast, reliable, and secure visa processing for over {{ $countries->count() }}+ countries.
            </p>

            <!-- Visa Search Form -->
            <form action="{{ route('site.visa.search') }}" method="GET"
                class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-3xl mx-auto text-left ring-1 ring-white/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Visa Category</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-purple-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                            </div>
                            <select name="category"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all font-medium text-slate-700">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Travelling to</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-purple-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <select name="country"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all font-medium text-slate-700">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto bg-linear-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-purple-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        Search Visas
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

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