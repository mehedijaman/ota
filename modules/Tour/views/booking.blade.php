@extends('site-layout')

@section('meta-title', 'Book ' . $tour->name . ' | ' . config('app.name'))

@section('content')
    <!-- Booking Header -->
    <div class="bg-slate-900 pt-28 pb-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.tour.index') }}" class="hover:text-white transition-colors">Tours</a>
                <i class="ri-arrow-right-s-line"></i>
                <a href="{{ route('site.tour.show', $tour->slug) }}" class="hover:text-white transition-colors">{{ $tour->name }}</a>
                <i class="ri-arrow-right-s-line"></i>
                <span class="text-white">Book Now</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Book Your Trip</h1>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('site.tour.submitBooking', $tour->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Tour Summary -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex gap-4">
                            <img src="{{ $tour->thumbnail_url ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=200' }}" alt="{{ $tour->name }}" class="w-24 h-24 object-cover rounded-xl">
                            <div>
                                <h3 class="font-bold text-lg text-slate-800">{{ $tour->name }}</h3>
                                <p class="text-slate-500 text-sm">{{ $tour->duration }} • {{ $tour->destination?->name }}</p>
                                <p class="text-orange-600 font-bold mt-2">৳ {{ number_format($tour->discount_price ?? $tour->base_price) }} / person</p>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Date -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="font-bold text-slate-800 mb-4">Select Travel Date</h3>
                        <div>
                            <label class="text-sm text-slate-600 mb-2 block">Preferred Travel Date</label>
                            <input type="date" name="travel_date" class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" min="{{ date('Y-m-d') }}">
                            @if($tour->valid_from || $tour->valid_to)
                            <p class="text-xs text-slate-500 mt-2">
                                Tour available: 
                                {{ $tour->valid_from?->format('M d, Y') ?? 'Anytime' }} 
                                - 
                                {{ $tour->valid_to?->format('M d, Y') ?? 'Ongoing' }}
                            </p>
                            @endif
                        </div>
                    </div>


                    <!-- Travelers -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="font-bold text-slate-800 mb-4">Number of Travelers</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Adults (12+)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" onclick="updateCount('adults', -1)" class="p-3 hover:bg-slate-50">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" name="adults_count" id="adults_count" value="2" min="1" class="w-full text-center border-0 focus:ring-0">
                                    <button type="button" onclick="updateCount('adults', 1)" class="p-3 hover:bg-slate-50">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Children (2-11)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" onclick="updateCount('children', -1)" class="p-3 hover:bg-slate-50">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" name="children_count" id="children_count" value="0" min="0" class="w-full text-center border-0 focus:ring-0">
                                    <button type="button" onclick="updateCount('children', 1)" class="p-3 hover:bg-slate-50">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Infants (0-2)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" onclick="updateCount('infants', -1)" class="p-3 hover:bg-slate-50">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" name="infants_count" id="infants_count" value="0" min="0" class="w-full text-center border-0 focus:ring-0">
                                    <button type="button" onclick="updateCount('infants', 1)" class="p-3 hover:bg-slate-50">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="font-bold text-slate-800 mb-4">Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Full Name *</label>
                                <input type="text" name="contact_name" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" value="{{ auth()->user()?->name }}">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Email *</label>
                                <input type="email" name="contact_email" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" value="{{ auth()->user()?->email }}">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Phone *</label>
                                <input type="tel" name="contact_phone" required class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Address</label>
                                <input type="text" name="contact_address" class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="font-bold text-slate-800 mb-4">Special Requests</h3>
                        <textarea name="special_requests" rows="3" placeholder="Any dietary requirements, medical conditions, or special requests..." class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500"></textarea>
                    </div>

                    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                    
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-4 px-6 rounded-xl font-bold text-center shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02]">
                        Proceed to Payment
                    </button>
                </form>
            </div>

            <!-- Price Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="font-bold text-slate-800 mb-4">Price Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Adults (2 x ৳{{ number_format($tour->discount_price ?? $tour->base_price) }})</span>
                            <span class="font-medium">৳ {{ number_format(($tour->discount_price ?? $tour->base_price) * 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Children</span>
                            <span>৳ 0</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Infants</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-orange-600">৳ {{ number_format(($tour->discount_price ?? $tour->base_price) * 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                        <p class="flex items-center gap-2">
                            <i class="ri-shield-check-line text-green-500"></i>
                            Your booking is protected
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="ri-lock-line text-blue-500"></i>
                            Secure payment
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateCount(type, delta) {
        const input = document.getElementById(type + '_count');
        let value = parseInt(input.value) + delta;
        if (type === 'adults' && value < 1) value = 1;
        if (value < 0) value = 0;
        input.value = value;
    }
    </script>
@endsection
