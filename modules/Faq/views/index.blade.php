@extends('site-layout')

@section('meta-title', 'Help Center & FAQ | Travelly')

@section('meta-description', 'Find answers to your questions about bookings, payments, cancellations, and more.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- FAQ Hero & Search -->
    <div class="relative bg-slate-900 min-h-[400px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Support Background" class="w-full h-full object-cover opacity-50">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-3xl md:text-5xl font-extrabold mb-6 tracking-tight drop-shadow-xl">
                How can we <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-300 to-indigo-200">help you?</span>
            </h1>
            
            <!-- FAQ Search -->
            <div class="max-w-2xl mx-auto relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                     <svg class="h-6 w-6 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input class="w-full bg-white/95 backdrop-blur-md border-0 rounded-2xl py-5 pl-12 pr-4 focus:outline-none focus:ring-4 focus:ring-blue-500/30 shadow-2xl text-lg text-slate-800 placeholder-slate-400 font-medium transition-all" placeholder="Search for answers (e.g. 'refund', 'cancellation')">
            </div>
        </div>
    </div>

    <!-- FAQ Categories & Accordion -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
            <button class="bg-blue-50 text-blue-700 py-3 px-4 rounded-xl font-bold text-sm hover:bg-blue-100 transition-colors">General</button>
            <button class="bg-white text-slate-600 border border-slate-200 py-3 px-4 rounded-xl font-medium text-sm hover:bg-slate-50 transition-colors">Bookings</button>
            <button class="bg-white text-slate-600 border border-slate-200 py-3 px-4 rounded-xl font-medium text-sm hover:bg-slate-50 transition-colors">Payments</button>
            <button class="bg-white text-slate-600 border border-slate-200 py-3 px-4 rounded-xl font-medium text-sm hover:bg-slate-50 transition-colors">Cancellations</button>
        </div>

        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer group">
                <div class="p-6 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-lg">How do I verify my booking?</h3>
                    <svg class="w-6 h-6 text-slate-400 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                 <!-- Content hidden for now (simulated accordion) -->
                 <!-- <div class="px-6 pb-6 text-slate-600">You can verify your booking by logging into your account...</div> -->
            </div>

             <!-- FAQ Item 2 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer group">
                <div class="p-6 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-lg">Can I cancel my flight ticket?</h3>
                    <svg class="w-6 h-6 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

             <!-- FAQ Item 3 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer group">
                <div class="p-6 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-lg">What payment methods do you accept?</h3>
                    <svg class="w-6 h-6 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden cursor-pointer group">
                <div class="p-6 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-lg">Is my data secure?</h3>
                    <svg class="w-6 h-6 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div class="mt-12 p-8 bg-blue-50 rounded-2xl text-center">
            <h3 class="text-xl font-bold text-slate-800 mb-2">Still have questions?</h3>
            <p class="text-slate-600 mb-6">Our support team is just a click away.</p>
            <button class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-colors">Contact Support</button>
        </div>
    </section>
@endsection
