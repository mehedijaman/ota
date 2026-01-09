@extends('site-layout')

@section('meta-title', 'TriptonicBD:Online Travel Agency')

@section('meta-description', 'TriptonicBD:Online Travel Agency')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    {{-- <x-hero></x-hero> --}}

    {{-- <x-exclusive-offers></x-exclusive-offers> --}}

    {{-- <x-explore-bangladesh></x-explore-bangladesh> --}}

    {{-- <x-top-airlines></x-top-airlines> --}}

    {{-- <x-popular-destinations></x-popular-destinations> --}}

    {{-- <x-best-hotels></x-best-hotels> --}}

    {{-- <x-tour-packages></x-tour-packages> --}}

    <x-top-testimonials :testimonials="$topTestimonials"></x-top-testimonials>

    {{-- <x-latest-blogs></x-latest-blogs> --}}

    {{-- <x-business-cta></x-business-cta> --}}

    {{-- <x-app-download></x-app-download> --}}
@endsection
