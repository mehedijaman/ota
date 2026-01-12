@extends('site-layout')

@section('meta-title', 'TriptonicBD:Online Travel Agency')

@section('meta-description', 'TriptonicBD:Online Travel Agency')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-hero></x-hero>

    {{-- <x-exclusive-offers></x-exclusive-offers> --}}

    {{-- <x-explore-bangladesh></x-explore-bangladesh> --}}

    {{-- <x-top-airlines></x-top-airlines> --}}

    {{-- <x-popular-destinations></x-popular-destinations> --}}

    {{-- <x-best-hotels></x-best-hotels> --}}

    @php
        $toursData = $featuredTours->map(fn($tour) => [
            'id' => $tour->id,
            'name' => $tour->name,
            'slug' => $tour->slug,
            'url' => route('site.tour.show', $tour->slug),
            'thumbnail_url' => $tour->thumbnail_url,
            'average_rating' => $tour->average_rating ?? 4.5,
            'is_bestseller' => $tour->is_bestseller,
            'category_name' => $tour->category?->name,
            'duration' => $tour->duration,
            'destination_name' => $tour->destination?->name,
            'base_price' => $tour->base_price,
            'min_price' => $tour->pricings_min_adult_price,
            'discount_price' => $tour->discount_price,
        ])->values()->toArray();
    @endphp

    <featured-tours-carousel 
        :tours='@json($toursData)'    
    ></featured-tours-carousel>

    @php
        $testimonialsData = $topTestimonials->map(fn($testimonial) => [
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'title' => $testimonial->title,
            'description' => $testimonial->description,
            'designation' => $testimonial->designation,
            'company' => $testimonial->company,
            'image_url' => $testimonial->image_url,
        ])->values()->toArray();
    @endphp

    <testimonials-carousel
        :testimonials='@json($testimonialsData)'
        see-all-url="{{ route('site.testimonial.index') }}"
    ></testimonials-carousel>

    @php
        $postsData = $latestPosts->map(fn($post) => [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'url' => route('site.blog.show', $post->slug),
            'image_url' => $post->image_url,
            'summary' => $post->summary,
            'published_at' => $post->published_at?->format('M d, Y') ?? 'Draft',
            'author_name' => $post->author?->name,
            'tags' => $post->tags?->pluck('name')->toArray() ?? [],
        ])->values()->toArray();
    @endphp

    <latest-posts-carousel
        :posts='@json($postsData)'
        see-all-url="{{ route('site.blog.index') }}"
    ></latest-posts-carousel>

    {{-- <x-business-cta></x-business-cta> --}}

    {{-- <x-app-download></x-app-download> --}}
@endsection
