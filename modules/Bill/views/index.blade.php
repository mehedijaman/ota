@extends('site-layout')

@section('meta-title', 'Pay Bills | TriptonicBD')

@section('meta-description', 'Pay your electricity, gas, internet, and other utility bills instantly with TriptonicBD. Secure and fast.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Bill Hero & Search -->
    <div class="relative bg-slate-900 min-h-[500px] flex items-center pt-32 pb-20 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f7a07d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Bill Payment Background" class="w-full h-full object-cover">
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
                Pay Your Bills <span class="text-transparent bg-clip-text bg-linear-to-r from-green-300 to-emerald-200">Instantly</span>
            </h1>
            <p class="text-lg md:text-xl text-green-100 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
                Electricity, Gas, Internet, and more. One click payment.
            </p>
            
            <!-- Bill Search/Action Form -->
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-4xl mx-auto text-left ring-1 ring-white/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Select Service</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all font-medium text-slate-700">
                                <option>Electricity (Prepaid)</option>
                                <option>Electricity (Postpaid)</option>
                                <option>Gas Bill</option>
                                <option>Water Bill</option>
                                <option>Internet</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5 relative">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Biller / Vendor</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <select class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all font-medium text-slate-700">
                                <option>DESCO</option>
                                <option>DPDC</option>
                                <option>Titis Gas</option>
                                <option>WASA</option>
                                <option>Carnival Internet</option>
                            </select>
                        </div>
                    </div>
                     <div class="space-y-1.5 relative md:col-span-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Account / Meter Number</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            </div>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all font-medium text-slate-700" placeholder="Enter your customer ID, account no, or meter no">
                        </div>
                    </div>
                </div>
                 <div class="mt-6 flex justify-end">
                    <button class="w-full md:w-auto bg-linear-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-4 px-10 rounded-xl font-bold shadow-lg shadow-green-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Check Bill
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Supported Services -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Supported Services</h2>
            <p class="text-slate-500 mt-2">Pay for everything in one place</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <!-- Service 1 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-slate-100 flex flex-col items-center text-center group cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">Electricity</h3>
            </div>
            <!-- Service 2 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-slate-100 flex flex-col items-center text-center group cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">Water</h3>
            </div>
             <!-- Service 3 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-slate-100 flex flex-col items-center text-center group cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">Gas</h3>
            </div>
             <!-- Service 4 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-slate-100 flex flex-col items-center text-center group cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">Internet</h3>
            </div>
             <!-- Service 5 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-slate-100 flex flex-col items-center text-center group cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">Govt. Fees</h3>
            </div>
             <!-- Service 6 -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-slate-100 flex flex-col items-center text-center group cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-700">Tuition</h3>
            </div>
        </div>
    </section>
@endsection
