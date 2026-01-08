@extends('site-layout')

@section('meta-title', 'Why Choose Us | TriptonicBD')

@section('meta-description', 'Discover why millions of travelers trust TriptonicBD for their bookings. Best prices, reliable support, and seamless experiences.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Why Us Hero -->
    <div class="relative bg-slate-900 h-[400px] flex items-center justify-center pt-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Happy Traveler" class="w-full h-full object-cover opacity-40">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900 via-slate-900/40 to-transparent"></div>

        <div class="container max-w-4xl mx-auto px-4 text-center text-white relative z-10">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 tracking-tight">
                {{ $page->title }}
            </h1>
        </div>
    </div>

    <!-- Core Features Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 -mt-16 relative z-20">
        {!! $page->content !!}
    </section>
@endsection
