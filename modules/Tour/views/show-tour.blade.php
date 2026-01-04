@extends('site-layout')

@section('meta-title', "Cox's Bazar Beach Tour | TriptonicBD")
@section('meta-description', 'Experience the longest sea beach in the world with our curated 3-day tour package.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Header & Gallery -->
    <div class="bg-white pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2">Cox's Bazar Beach Tour</h1>
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 mb-4">
                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.8 (120 reviews)</span>
                <span>•</span>
                <span>3 Days, 2 Nights</span>
                <span>•</span>
                <span>Cox's Bazar, Bangladesh</span>
            </div>
            
            <div class="grid grid-cols-4 gap-2 h-80 rounded-xl overflow-hidden">
                <div class="col-span-2 row-span-2">
                    <img src="https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80" alt="Tour" class="w-full h-full object-cover">
                </div>
                <div><img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=80" alt="Beach" class="w-full h-full object-cover"></div>
                <div><img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=80" alt="Hotel" class="w-full h-full object-cover"></div>
                <div><img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&q=80" alt="View" class="w-full h-full object-cover"></div>
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?w=400&q=80" alt="Activities" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center"><span class="text-white font-semibold">+12</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Bar -->
    <div class="bg-white border-y border-slate-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-sm text-slate-500">Starting from</p>
                        <p class="text-2xl font-bold text-orange-600">৳5,500 <span class="text-sm font-normal text-slate-400">/person</span></p>
                    </div>
                    <div class="hidden md:block border-l border-slate-200 pl-6">
                        <p class="text-sm text-slate-500">Duration</p>
                        <p class="font-semibold">3 Days, 2 Nights</p>
                    </div>
                </div>
                <button class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-xl font-bold">Book Now</button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Overview -->
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4">Tour Overview</h2>
                        <p class="text-slate-600 leading-relaxed mb-4">Experience the world's longest natural sea beach with our carefully curated 3-day tour package. Enjoy stunning sunsets, fresh seafood, and the warm hospitality of Cox's Bazar.</p>
                        <p class="text-slate-600 leading-relaxed">This package includes comfortable accommodation, all meals, transportation from Dhaka, and guided tours to Himchori, Inani Beach, and the famous Marine Drive.</p>
                    </div>

                    <!-- Itinerary -->
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4">Itinerary</h2>
                        <div class="space-y-4">
                            @foreach([
                                ['day' => 'Day 1', 'title' => 'Arrival & Beach Exploration', 'desc' => 'Arrive in Cox\'s Bazar, check into hotel. Evening beach walk and sunset viewing. Dinner at seaside restaurant.'],
                                ['day' => 'Day 2', 'title' => 'Full Day Sightseeing', 'desc' => 'Visit Himchori Waterfall, Inani Beach, and Marine Drive. Lunch at local restaurant. Evening free for shopping.'],
                                ['day' => 'Day 3', 'title' => 'Departure', 'desc' => 'Breakfast at hotel, free time for last-minute beach visits. Check out and departure to Dhaka.'],
                            ] as $item)
                            <div class="flex gap-4 p-4 bg-slate-50 rounded-xl">
                                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="text-orange-600 font-bold text-sm">{{ $item['day'] }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-800">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-slate-600 mt-1">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- What's Included -->
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4">What's Included</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-semibold text-green-700 mb-2">Included</h3>
                                @foreach(['Round-trip transport', 'Hotel accommodation', 'All meals', 'Sightseeing tours', 'Professional guide'] as $i)
                                <p class="flex items-center gap-2 text-sm text-slate-600 mb-1">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $i }}
                                </p>
                                @endforeach
                            </div>
                            <div>
                                <h3 class="font-semibold text-red-700 mb-2">Not Included</h3>
                                @foreach(['Personal expenses', 'Travel insurance', 'Tips & gratuities'] as $n)
                                <p class="flex items-center gap-2 text-sm text-slate-600 mb-1">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    {{ $n }}
                                </p>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="bg-white rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-slate-800">Reviews</h2>
                            <span class="bg-orange-600 text-white px-3 py-1 rounded-lg font-bold">4.8</span>
                        </div>
                        <div class="space-y-4">
                            @foreach([['name' => 'Rafiq H.', 'text' => 'Amazing experience! The beach was beautiful and the guide was very helpful.'], ['name' => 'Fatima K.', 'text' => 'Well organized tour. Food and accommodation were excellent.']] as $r)
                            <div class="p-4 bg-slate-50 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center font-bold text-orange-600">{{ substr($r['name'], 0, 1) }}</div>
                                    <p class="font-semibold">{{ $r['name'] }}</p>
                                </div>
                                <p class="text-sm text-slate-600">{{ $r['text'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside>
                    <div class="bg-white rounded-2xl p-6 sticky top-40">
                        <h3 class="font-bold text-lg mb-4">Book This Tour</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-slate-500">Select Date</label>
                                <input type="date" class="w-full border border-slate-200 rounded-lg px-3 py-2 mt-1">
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Travelers</label>
                                <select class="w-full border border-slate-200 rounded-lg px-3 py-2 mt-1">
                                    <option>1 Person</option>
                                    <option>2 Persons</option>
                                    <option>3 Persons</option>
                                    <option>4+ Persons</option>
                                </select>
                            </div>
                            <div class="border-t border-slate-200 pt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span>৳5,500 x 2 persons</span>
                                    <span>৳11,000</span>
                                </div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span>Service fee</span>
                                    <span>৳500</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg pt-2 border-t">
                                    <span>Total</span>
                                    <span class="text-orange-600">৳11,500</span>
                                </div>
                            </div>
                            <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-xl font-bold">Book Now</button>
                            <p class="text-center text-xs text-slate-500">Free cancellation up to 48 hours</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
