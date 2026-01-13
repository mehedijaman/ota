@extends('site-layout')

@section('meta-title', 'Search Visas | ' . config('app.name'))

@section('meta-description', 'Find visa services for your destination country. Filter by country, category, and visa type.')

@section('content')
    <!-- Search Header -->
    <div class="bg-gradient-to-r from-purple-700 to-fuchsia-700 pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-6">Find Your Visa</h1>

            <!-- Filter Form -->
            <form action="{{ route('site.visa.search') }}" method="GET"
                class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label
                            class="text-xs font-bold text-white/70 uppercase tracking-widest pl-1 mb-2 block">Country</label>
                        <select name="country"
                            class="w-full bg-white/90 border-0 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-white/50 font-medium text-slate-700">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="text-xs font-bold text-white/70 uppercase tracking-widest pl-1 mb-2 block">Category</label>
                        <select name="category"
                            class="w-full bg-white/90 border-0 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-white/50 font-medium text-slate-700">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-white/70 uppercase tracking-widest pl-1 mb-2 block">Sort
                            By</label>
                        <select name="sort"
                            class="w-full bg-white/90 border-0 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-white/50 font-medium text-slate-700">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to
                                High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to
                                Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-white text-purple-700 py-3 px-6 rounded-xl font-bold hover:bg-purple-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-6">
            <p class="text-slate-600">
                <span class="font-bold text-slate-800">{{ $visas->total() }}</span> visa services found
            </p>
            @if(request()->hasAny(['country', 'category', 'sort']))
                <a href="{{ route('site.visa.search') }}"
                    class="text-purple-600 hover:text-purple-700 font-medium text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </a>
            @endif
        </div>

        @if($visas->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($visas as $visa)
                    @php
                        $visaData = [
                            "id" => $visa->id,
                            "name" => $visa->name,
                            "slug" => $visa->slug,
                            "url" => route("site.visa.show", $visa->slug),
                            "image" => $visa->hasMedia("thumbnail")
                                ? $visa->getFirstMediaUrl("thumbnail")
                                : ($visa->country && $visa->country->hasMedia("image")
                                    ? $visa->country->getFirstMediaUrl("image")
                                    : null),
                            "country" => $visa->country?->name,
                            "category" => $visa->category?->name,
                            "type" => $visa->visa_type,
                            "description" => $visa->short_description ?? Str::limit(strip_tags($visa->description), 100),
                            "price" => number_format($visa->base_price),
                            "processing_time" => $visa->processing_days_min ? $visa->processing_days_min . ' Days' : 'Varies',
                            "is_featured" => $visa->is_featured,
                            "is_popular" => $visa->is_popular
                        ];
                    @endphp
                    <visa-card :visa='@json($visaData)'></visa-card>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $visas->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-slate-50 rounded-2xl">
                <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                    </path>
                </svg>
                <h3 class="text-xl font-bold text-slate-700 mb-2">No visa services found</h3>
                <p class="text-slate-500 mb-6">Try adjusting your search filters or browse all available visas</p>
                <a href="{{ route('site.visa.search') }}"
                    class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Clear Filters
                </a>
            </div>
        @endif
    </section>
@endsection