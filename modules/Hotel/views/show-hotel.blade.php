@extends('site-layout')

@section('meta-title', 'Ocean Paradise Hotel & Resort | TriptonicBD')
@section('meta-description', 'Book Ocean Paradise Hotel & Resort in Cox\'s Bazar.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Header & Gallery -->
    <div class="bg-white pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2">Ocean Paradise Hotel & Resort</h1>
            <p class="text-slate-500 flex items-center gap-2 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                Kolatoli Beach Road, Cox's Bazar, Bangladesh
                <span class="text-amber-500">★★★★★</span>
            </p>
            
            <!-- Image Gallery Grid -->
            <div class="grid grid-cols-4 gap-2 h-80 rounded-xl overflow-hidden">
                <div class="col-span-2 row-span-2">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80" alt="Hotel" class="w-full h-full object-cover">
                </div>
                <div><img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=400&q=80" alt="Room" class="w-full h-full object-cover"></div>
                <div><img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&q=80" alt="Pool" class="w-full h-full object-cover"></div>
                <div><img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&q=80" alt="Beach" class="w-full h-full object-cover"></div>
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?w=400&q=80" alt="Restaurant" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center"><span class="text-white font-semibold">+24</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white border-y border-slate-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs text-slate-500">Check-in</label>
                    <input type="date" value="2026-01-20" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs text-slate-500">Check-out</label>
                    <input type="date" value="2026-01-25" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs text-slate-500">Guests & Rooms</label>
                    <select class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option>2 Adults, 1 Room</option>
                        <option>1 Adult, 1 Room</option>
                    </select>
                </div>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold mt-4 sm:mt-0">Search</button>
            </div>
        </div>
    </div>

    <!-- Available Rooms Section -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Available Rooms</h2>
            <div class="space-y-4">
                @foreach([
                    ['name' => 'Deluxe Sea View Room', 'size' => '45 sqm', 'bed' => 'King Bed', 'guests' => '2 Guests', 'price' => '14,500', 'oldPrice' => '18,000', 'img' => 'photo-1590490360182-c33d57733427'],
                    ['name' => 'Premium Suite', 'size' => '65 sqm', 'bed' => 'King Bed', 'guests' => '3 Guests', 'price' => '22,000', 'oldPrice' => null, 'img' => 'photo-1591088398332-8a7791972843'],
                    ['name' => 'Royal Beach Suite', 'size' => '100 sqm', 'bed' => 'King Bed', 'guests' => '4 Guests', 'price' => '38,000', 'oldPrice' => '45,000', 'img' => 'photo-1566665797739-1674de7a421a'],
                    ['name' => 'Family Room', 'size' => '55 sqm', 'bed' => '2 Double Beds', 'guests' => '4 Guests', 'price' => '18,500', 'oldPrice' => null, 'img' => 'photo-1618773928121-c32242e63f39'],
                ] as $room)
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-64 h-48 md:h-auto shrink-0">
                            <img src="https://images.unsplash.com/{{ $room['img'] }}?w=400&q=80" alt="{{ $room['name'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 p-5">
                            <h3 class="font-bold text-lg text-slate-800">{{ $room['name'] }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ $room['size'] }} • {{ $room['bed'] }} • Max {{ $room['guests'] }}</p>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="text-xs bg-slate-100 px-2 py-1 rounded">Free WiFi</span>
                                <span class="text-xs bg-slate-100 px-2 py-1 rounded">Breakfast</span>
                                <span class="text-xs bg-slate-100 px-2 py-1 rounded">AC</span>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Free Cancellation</span>
                            </div>
                        </div>
                        <div class="p-5 md:border-l border-t md:border-t-0 border-slate-100 md:w-48 text-center md:text-right">
                            @if($room['oldPrice'])<p class="text-sm text-slate-400 line-through">৳{{ $room['oldPrice'] }}</p>@endif
                            <p class="text-2xl font-bold text-blue-600">৳{{ $room['price'] }}</p>
                            <p class="text-xs text-slate-500 mb-3">per night</p>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold w-full">Select</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Facilities -->
    <div class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Facilities</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach(['Free WiFi', 'Swimming Pool', 'Restaurant', 'Spa', 'Gym', 'Beach Access', 'Parking', 'Room Service', 'Bar', 'Kids Club', 'Airport Shuttle', 'Laundry'] as $facility)
                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm text-slate-700">{{ $facility }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Location -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Location</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-slate-200 rounded-xl h-64 flex items-center justify-center">
                    <p class="text-slate-500">Map Placeholder</p>
                </div>
                <div>
                    <p class="text-slate-600 mb-4">Kolatoli Beach Road, Cox's Bazar 4700, Bangladesh</p>
                    <div class="space-y-2">
                        <p class="text-sm text-slate-600"><strong>Airport:</strong> 15 min drive</p>
                        <p class="text-sm text-slate-600"><strong>Beach:</strong> Direct access</p>
                        <p class="text-sm text-slate-600"><strong>City Center:</strong> 10 min drive</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policies -->
    <div class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Hotel Policies</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div><h3 class="font-semibold mb-2">Check-in/out</h3><p class="text-sm text-slate-600">Check-in: 2:00 PM<br>Check-out: 12:00 PM</p></div>
                <div><h3 class="font-semibold mb-2">Cancellation</h3><p class="text-sm text-slate-600">Free cancellation up to 48 hours before check-in</p></div>
                <div><h3 class="font-semibold mb-2">Children & Pets</h3><p class="text-sm text-slate-600">Children welcome. Pets not allowed.</p></div>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800">Guest Reviews</h2>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-lg font-bold">9.2</span>
                    <span class="text-slate-600">Excellent (1,247 reviews)</span>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach([['name' => 'John D.', 'date' => 'Dec 2025', 'text' => 'Amazing experience! Beautiful beach view and excellent service.'], ['name' => 'Sarah A.', 'date' => 'Nov 2025', 'text' => 'Perfect family vacation. Kids loved the pool and beach.']] as $review)
                <div class="bg-white p-4 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">{{ substr($review['name'], 0, 1) }}</div>
                        <div><p class="font-semibold">{{ $review['name'] }}</p><p class="text-xs text-slate-500">{{ $review['date'] }}</p></div>
                    </div>
                    <p class="text-sm text-slate-600">{{ $review['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
