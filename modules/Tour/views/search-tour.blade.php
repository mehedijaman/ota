@extends('site-layout')

@section('meta-title', 'Tour Search Results | TriptonicBD')
@section('meta-description', 'Browse and compare tour packages to amazing destinations.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-linear-to-r from-orange-600 to-amber-600 pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-white">
                    <h1 class="text-2xl md:text-3xl font-bold">Tour Packages</h1>
                    <p class="text-orange-100 mt-1">Explore 45+ amazing destinations</p>
                </div>
                <button class="self-start bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl font-semibold transition-all">
                    Modify Search
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Filters Sidebar -->
                <aside class="w-full lg:w-72 shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-slate-800">Filters</h2>
                            <button class="text-sm text-orange-600 font-medium">Reset</button>
                        </div>

                        <!-- Duration -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Duration</h3>
                            <div class="space-y-2">
                                @foreach(['1-3 Days', '4-7 Days', '7-14 Days', '14+ Days'] as $d)
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 rounded text-orange-600">
                                    <span class="text-sm text-slate-600">{{ $d }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Price Range</h3>
                            <input type="range" min="2000" max="50000" class="w-full accent-orange-600">
                            <div class="flex justify-between text-xs text-slate-500 mt-2">
                                <span>৳2,000</span><span>৳50,000</span>
                            </div>
                        </div>

                        <!-- Tour Type -->
                        <div class="border-b border-slate-100 pb-5 mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Tour Type</h3>
                            <div class="space-y-2">
                                @foreach(['Adventure', 'Beach', 'Cultural', 'Nature', 'Wildlife'] as $t)
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 rounded text-orange-600">
                                    <span class="text-sm text-slate-600">{{ $t }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Rating -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Rating</h3>
                            <div class="space-y-2">
                                @foreach(['4.5+ Excellent', '4.0+ Very Good', '3.5+ Good'] as $r)
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="rating" class="w-4 h-4 text-orange-600">
                                    <span class="text-sm text-slate-600">{{ $r }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Results -->
                <main class="flex-1">
                    <div class="bg-white rounded-xl p-4 mb-6 flex items-center justify-between">
                        <p class="text-slate-600"><span class="font-semibold">45 tours</span> found</p>
                        <select class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                            <option>Recommended</option>
                            <option>Price (Low to High)</option>
                            <option>Rating</option>
                            <option>Duration</option>
                        </select>
                    </div>

                    <div class="grid gap-6">
                        @foreach([
                            ['name' => "Cox's Bazar Beach Tour", 'days' => '3 Days', 'rating' => '4.8', 'reviews' => '120', 'price' => '5,500', 'oldPrice' => '7,000', 'img' => 'photo-1548013146-72479768bada', 'badge' => 'BEST SELLER'],
                            ['name' => 'Sylhet Tea Garden Tour', 'days' => '2 Days', 'rating' => '4.9', 'reviews' => '85', 'price' => '4,200', 'oldPrice' => null, 'img' => 'photo-1596436889106-be35e843f974', 'badge' => null],
                            ['name' => 'Sajek Valley Adventure', 'days' => '3 Days', 'rating' => '4.7', 'reviews' => '200', 'price' => '6,500', 'oldPrice' => null, 'img' => 'photo-1566863473556-2e8c14771d2b', 'badge' => 'POPULAR'],
                            ['name' => 'Sundarbans Wildlife Safari', 'days' => '4 Days', 'rating' => '4.6', 'reviews' => '95', 'price' => '8,000', 'oldPrice' => '10,000', 'img' => 'photo-1578559115206-a83d69c6f2a7', 'badge' => null],
                            ['name' => 'Bandarban Hill Track', 'days' => '3 Days', 'rating' => '4.8', 'reviews' => '150', 'price' => '5,800', 'oldPrice' => null, 'img' => 'photo-1469854523086-cc02fe5d8800', 'badge' => null],
                            ['name' => 'Saint Martin Island Escape', 'days' => '4 Days', 'rating' => '4.9', 'reviews' => '180', 'price' => '9,500', 'oldPrice' => '12,000', 'img' => 'photo-1507525428034-b723cf961d3e', 'badge' => 'HOT DEAL'],
                        ] as $idx => $tour)
                        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-72 h-56 md:h-auto relative shrink-0">
                                    <img src="https://images.unsplash.com/{{ $tour['img'] }}?w=600&q=80" alt="{{ $tour['name'] }}" class="w-full h-full object-cover">
                                    @if($tour['badge'])
                                    <div class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">{{ $tour['badge'] }}</div>
                                    @endif
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </button>
                                </div>
                                <div class="flex-1 p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-orange-500">★</span>
                                        <span class="text-sm font-medium">{{ $tour['rating'] }}</span>
                                        <span class="text-sm text-slate-400">({{ $tour['reviews'] }} reviews)</span>
                                        <span class="text-xs bg-slate-100 px-2 py-1 rounded ml-auto">{{ $tour['days'] }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-2">
                                        <a href="{{ route('site.tour.show', $idx + 1) }}" class="hover:text-orange-600">{{ $tour['name'] }}</a>
                                    </h3>
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span class="text-xs bg-slate-100 px-2 py-1 rounded">Transport</span>
                                        <span class="text-xs bg-slate-100 px-2 py-1 rounded">Meals</span>
                                        <span class="text-xs bg-slate-100 px-2 py-1 rounded">Guide</span>
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Free Cancellation</span>
                                    </div>
                                    <div class="flex items-end justify-between pt-4 border-t border-slate-100">
                                        <div>
                                            <p class="text-xs text-slate-500">Starting from</p>
                                            @if($tour['oldPrice'])<span class="text-sm text-slate-400 line-through mr-2">৳{{ $tour['oldPrice'] }}</span>@endif
                                            <span class="text-2xl font-bold text-orange-600">৳{{ $tour['price'] }}</span>
                                            <span class="text-xs text-slate-500">/person</span>
                                        </div>
                                        <a href="{{ route('site.tour.show', $idx + 1) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-8">
                        <button class="bg-white border border-slate-200 px-8 py-3 rounded-xl font-semibold text-slate-700 hover:bg-slate-50">Load More Tours</button>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
