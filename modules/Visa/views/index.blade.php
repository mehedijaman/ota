@extends('site-layout')

@section('meta-title', 'Apply for Visas Online | Travelly')

@section('meta-description', 'Hassle-free visa application assistance for top destinations. Get your visa approved quickly with Travelly.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Visa Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1544733422-251e532ca221?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Visa Passport Background" class="w-full h-full object-cover">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Get Your Visa <span class="text-transparent bg-clip-text bg-linear-to-r from-purple-300 to-fuchsia-200">Hassle-Free</span>
            </h1>
            <p class="text-lg md:text-xl text-purple-100 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Fast, reliable, and secure visa processing for over 50 countries.
            </p>
            
            <!-- Visa Search Form -->
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-3xl mx-auto text-left ring-1 ring-white/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5 relative">
                         <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">I am a Citizen of</label>
                         <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all font-medium text-slate-700">
                                <option>Bangladesh</option>
                                <option>India</option>
                                <option>United States</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                       <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Travelling to</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </div>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all font-medium text-slate-700">
                                <option>Thailand</option>
                                <option>Malaysia</option>
                                <option>Singapore</option>
                                <option>United Arab Emirates</option>
                                <option>Canada</option>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="mt-6 flex justify-end">
                    <button class="w-full md:w-auto bg-linear-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-purple-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        Check Requirements
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Visa Destinations -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Popular Destinations</h2>
            <p class="text-slate-500 mt-2">Get your visa processed for these top countries</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all p-2 border border-slate-100 cursor-pointer">
                <div class="relative overflow-hidden rounded-xl aspect-video mb-3">
                     <img src="https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Thailand" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="px-2 pb-2 text-center">
                    <h3 class="font-bold text-lg text-slate-800">Thailand</h3>
                    <p class="text-green-600 text-sm font-semibold mt-1">Sticker Visa • 5 Days</p>
                </div>
            </div>
             <!-- Card 2 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all p-2 border border-slate-100 cursor-pointer">
                <div class="relative overflow-hidden rounded-xl aspect-video mb-3">
                     <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Malaysia" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="px-2 pb-2 text-center">
                    <h3 class="font-bold text-lg text-slate-800">Malaysia</h3>
                    <p class="text-green-600 text-sm font-semibold mt-1">E-Visa Available • 2 Days</p>
                </div>
            </div>
             <!-- Card 3 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all p-2 border border-slate-100 cursor-pointer">
                <div class="relative overflow-hidden rounded-xl aspect-video mb-3">
                     <img src="https://images.unsplash.com/photo-1565967511849-76a60a516170?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Singapore" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="px-2 pb-2 text-center">
                    <h3 class="font-bold text-lg text-slate-800">Singapore</h3>
                    <p class="text-green-600 text-sm font-semibold mt-1">E-Visa Available • 3 Days</p>
                </div>
            </div>
             <!-- Card 4 -->
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all p-2 border border-slate-100 cursor-pointer">
                <div class="relative overflow-hidden rounded-xl aspect-video mb-3">
                     <img src="https://images.unsplash.com/photo-1512453979798-5ea90b792950?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Dubai" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="px-2 pb-2 text-center">
                    <h3 class="font-bold text-lg text-slate-800">Dubai (UAE)</h3>
                    <p class="text-green-600 text-sm font-semibold mt-1">E-Visa • Instant </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-slate-50 mb-16 rounded-3xl">
         <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
             <div class="p-4">
                 <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 </div>
                 <h3 class="font-bold text-lg mb-2">99% Success Rate</h3>
                 <p class="text-slate-500 text-sm">Our experts review every application to ensure maximum approval chances.</p>
             </div>
              <div class="p-4">
                 <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 </div>
                 <h3 class="font-bold text-lg mb-2">Fast Processing</h3>
                 <p class="text-slate-500 text-sm">We process your visa application faster than standard agencies.</p>
             </div>
              <div class="p-4">
                 <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                 </div>
                 <h3 class="font-bold text-lg mb-2">24/7 Support</h3>
                 <p class="text-slate-500 text-sm">Our support team is always available to answer your queries.</p>
             </div>
         </div>
    </section>
@endsection
