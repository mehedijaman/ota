@extends('site-layout')

@section('meta-title', 'Why Choose Us | Travelly')

@section('meta-description', 'Discover why millions of travelers trust Travelly for their bookings. Best prices, reliable support, and seamless experiences.')

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
                Why <span class="text-transparent bg-clip-text bg-linear-to-r from-yellow-300 to-orange-400">Travelly?</span>
            </h1>
            <p class="text-xl text-yellow-50 max-w-2xl mx-auto font-light">
                We don't just book trips. We craft unforgettable experiences.
            </p>
        </div>
    </div>

    <!-- Core Features Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 -mt-16 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">Best Price Guarantee</h3>
                <p class="text-slate-500">We constantly monitor fares to ensure you get the absolute best deal available.</p>
            </div>
             <!-- Feature 2 -->
             <div class="bg-white p-8 rounded-2xl shadow-xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">100% Secure</h3>
                <p class="text-slate-500">Your payments and data are protected with bank-level encryption standards.</p>
            </div>
             <!-- Feature 3 -->
             <div class="bg-white p-8 rounded-2xl shadow-xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">24/7 Support</h3>
                <p class="text-slate-500">Our team is available round the clocks to assist you with any changes or queries.</p>
            </div>
             <!-- Feature 4 -->
             <div class="bg-white p-8 rounded-2xl shadow-xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mb-6 text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">Curated with Love</h3>
                <p class="text-slate-500">Every package is handpicked by experts to ensure a quality experience.</p>
            </div>
        </div>
    </section>

    <!-- Satisfaction Focus -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 mb-16">
        <div class="bg-blue-50 rounded-3xl p-8 md:p-16 flex flex-col md:flex-row items-center gap-12">
            <div class="flex-1 space-y-6 text-center md:text-left">
                <h2 class="text-3xl md:text-5xl font-bold text-slate-900">
                    Customer Satisfaction is our <br> <span class="text-blue-600">Top Priority</span>
                </h2>
                <p class="text-slate-600 text-lg">
                    We believe in building long-term relationships with our customers. That's why we go the extra mile to make sure every trip is perfect. From booking to boarding, we are with you every step of the way.
                </p>
                <button class="bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-blue-700 transition-shadow shadow-lg shadow-blue-500/30">
                    Read Our Reviews
                </button>
            </div>
            <div class="flex-1 relative">
                 <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Customer Satisfaction" class="rounded-2xl shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500 border-4 border-white">
                 <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg flex items-center gap-4">
                     <div class="flex -space-x-2">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=1" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=2" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=3" alt="">
                     </div>
                     <div class="text-sm font-bold text-slate-700">
                         <span class="text-blue-600">2,000+</span> Reviews
                     </div>
                 </div>
            </div>
        </div>
    </section>
@endsection
