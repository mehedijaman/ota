@extends('site-layout')

@section('meta-title', 'Search Visas | ' . config('app.name'))

@section('meta-description', 'Find visa services for your destination country. Filter by country, category, and visa type.')

@section('content')

    <x-visa-search-form :categories="$categories" :countries="$countries"/>

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