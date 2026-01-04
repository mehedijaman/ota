@extends('site-layout')

@section('meta-title', 'About Us | Triptonic')

@section('meta-description', 'Learn about Triptonic, our mission, vision, and the team dedicated to making your travel seamless.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- About Hero -->
    <div class="relative bg-slate-900 h-[400px] flex items-center justify-center pt-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Office Team" class="w-full h-full object-cover opacity-30">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-b from-transparent to-slate-900"></div>

        <div class="container max-w-4xl mx-auto px-4 text-center text-white relative z-10">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 tracking-tight">
                Reimagining <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-400 to-teal-300">Travel</span>
            </h1>
            <p class="text-xl text-slate-300 max-w-2xl mx-auto font-light">
                We are building the future of travel booking, making it easier, faster, and more accessible for everyone.
            </p>
        </div>
    </div>

    <!-- Our Story & Mission -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-6">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-sm">Our Story</span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 leading-tight">
                    From a Simple Idea <br> to a Global Platform.
                </h2>
                <p class="text-slate-600 text-lg leading-relaxed">
                    Started in 2024, Triptonic began with a simple mission: to simplify the complex world of travel booking. Frustrated by hidden fees and confusing interfaces, we set out to build something better.
                </p>
                <p class="text-slate-600 text-lg leading-relaxed">
                    Today, we help thousands of travelers explore the world with confidence, offering flights, hotels, tours, and more—all in one seamless experience.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-6">
                 <div class="bg-blue-50 p-8 rounded-2xl">
                     <svg class="w-10 h-10 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                     <h3 class="font-bold text-xl text-slate-800 mb-2">Our Mission</h3>
                     <p class="text-slate-600">To empower travelers with innovative tools and transparent pricing.</p>
                 </div>
                 <div class="bg-teal-50 p-8 rounded-2xl translate-y-8">
                     <svg class="w-10 h-10 text-teal-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                     <h3 class="font-bold text-xl text-slate-800 mb-2">Our Vision</h3>
                     <p class="text-slate-600">To be the world's most trusted and beloved travel companion.</p>
                 </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-slate-900 py-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                     <div class="text-4xl md:text-5xl font-bold text-blue-400 mb-2">50k+</div>
                     <div class="text-slate-400">Happy Travelers</div>
                </div>
                <div>
                     <div class="text-4xl md:text-5xl font-bold text-teal-400 mb-2">120+</div>
                     <div class="text-slate-400">Destinations</div>
                </div>
                 <div>
                     <div class="text-4xl md:text-5xl font-bold text-purple-400 mb-2">500+</div>
                     <div class="text-slate-400">Hotel Partners</div>
                </div>
                 <div>
                     <div class="text-4xl md:text-5xl font-bold text-pink-400 mb-2">24/7</div>
                     <div class="text-slate-400">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet the Team (Placeholder) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Meet the Team</h2>
            <p class="text-slate-500 mt-2">The people behind the platform</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Member 1 -->
            <div class="group text-center">
                <div class="relative w-48 h-48 mx-auto rounded-full overflow-hidden mb-4 ring-4 ring-slate-100 group-hover:ring-blue-100 transition-all">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="CEO" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">David Scott</h3>
                <p class="text-blue-600 text-sm font-medium">Founder & CEO</p>
            </div>
             <!-- Member 2 -->
             <div class="group text-center">
                <div class="relative w-48 h-48 mx-auto rounded-full overflow-hidden mb-4 ring-4 ring-slate-100 group-hover:ring-blue-100 transition-all">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="CTO" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Sarah Jen</h3>
                <p class="text-blue-600 text-sm font-medium">Head of Product</p>
            </div>
             <!-- Member 3 -->
             <div class="group text-center">
                <div class="relative w-48 h-48 mx-auto rounded-full overflow-hidden mb-4 ring-4 ring-slate-100 group-hover:ring-blue-100 transition-all">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Head of Ops" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Michael Chen</h3>
                <p class="text-blue-600 text-sm font-medium">Head of Operations</p>
            </div>
             <!-- Member 4 -->
             <div class="group text-center">
                <div class="relative w-48 h-48 mx-auto rounded-full overflow-hidden mb-4 ring-4 ring-slate-100 group-hover:ring-blue-100 transition-all">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Head of Marketing" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Emily Davis</h3>
                <p class="text-blue-600 text-sm font-medium">Marketing Lead</p>
            </div>
        </div>
    </section>
@endsection
