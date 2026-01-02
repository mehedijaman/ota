@extends('site-layout')

@section('meta-title', 'Modular: Ready to build')

@section('meta-description', 'Your amazing site')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    {{-- <section class="py-10 md:py-12">

        <div class="container max-w-screen-xl mx-auto px-4">

            <div class="text-center">
                <div class="flex justify-center mb-16">
                    <img src="{{ Vite::asset('resources-site/images/home-img.png') }}" alt="Image">
                </div>

                <h1 class="font-medium text-gray-600 text-lg md:text-2xl uppercase mb-8">Modular: Ready to build</h1>

                <h2 class="font-normal text-gray-700 text-4xl md:text-7xl leading-none mb-8">Your amazing site</h2>

                <index-example-component></index-example-component>
            </div>

        </div>

    </section> --}}    


<x-hero></x-hero>

<x-exclusive-offers></x-exclusive-offers>

<x-explore-bangladesh></x-explore-bangladesh>

<x-top-airlines></x-top-airlines>

<x-popular-destinations></x-popular-destinations>

<x-best-hotels></x-best-hotels>

<x-tour-packages></x-tour-packages>

<x-business-cta></x-business-cta>

<x-app-download></x-app-download>

@endsection
