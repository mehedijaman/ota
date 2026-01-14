@extends('site-layout')

@section('title', 'Book ' . $hotel->name)

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Progress Steps -->
                <div class="flex items-center justify-center mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                        <span class="ml-2 font-medium text-gray-900">Your Details</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 mx-4"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold">2</div>
                        <span class="ml-2 text-gray-500">Payment</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 mx-4"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold">3</div>
                        <span class="ml-2 text-gray-500">Confirmation</span>
                    </div>
                </div>

                <form action="{{ route('site.hotel.submitBooking', $hotel->slug) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Form -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Hotel Summary -->
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <div class="flex gap-4">
                                    <img src="{{ $hotel->thumbnail_url ?? '/images/hotel-placeholder.jpg' }}" alt="{{ $hotel->name }}"
                                         class="w-24 h-24 rounded-lg object-cover">
                                    <div>
                                        <div class="flex items-center gap-1 mb-1">
                                            @for($i = 0; $i < $hotel->star_rating; $i++)
                                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                            @endfor
                                        </div>
                                        <h2 class="font-bold text-xl text-gray-900">{{ $hotel->name }}</h2>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $hotel->city?->name }}, {{ $hotel->country?->name }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Stay Details -->
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Stay Details</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                                        <input type="date" name="check_in_date" value="{{ $checkIn ?? date('Y-m-d') }}" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                                        <input type="date" name="check_out_date" value="{{ $checkOut ?? date('Y-m-d', strtotime('+1 day')) }}" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rooms</label>
                                        <input type="number" name="rooms_count" value="{{ $rooms ?? 1 }}" min="1" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Guests</label>
                                        <input type="number" name="adults" value="{{ $adults ?? 2 }}" min="1" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <!-- Room Selection -->
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Room Type</label>
                                    <div class="space-y-3">
                                        @foreach($hotel->rooms as $room)
                                            <label class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ $selectedRoom?->id === $room->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                                <input type="radio" name="room_id" value="{{ $room->id }}"
                                                       {{ $selectedRoom?->id === $room->id ? 'checked' : '' }} required
                                                       class="text-blue-600 focus:ring-blue-500">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900">{{ $room->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $room->roomType?->name }} • Max {{ $room->max_occupancy }} guests</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-bold text-blue-600">৳{{ number_format($room->base_rate ?? 0) }}</p>
                                                    <p class="text-xs text-gray-500">per night</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Rate Plan Selection -->
                                @if($hotel->ratePlans->count())
                                    <div class="mt-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Rate Plan</label>
                                        <select name="rate_plan_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                            @foreach($hotel->ratePlans as $ratePlan)
                                                <option value="{{ $ratePlan->id }}" {{ $selectedRatePlan?->id === $ratePlan->id ? 'selected' : '' }}>
                                                    {{ $ratePlan->name }} - {{ ucwords(str_replace('_', ' ', $ratePlan->meal_plan)) }}
                                                    @if(!$ratePlan->is_refundable) (Non-refundable) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>

                            <!-- Contact Details -->
                            <div class="bg-white rounded-xl shadow-md p-6">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Contact Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                        <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()?->name) }}" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                        @error('contact_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                        <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()?->email) }}" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                        @error('contact_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                        <input type="tel" name="contact_phone" value="{{ old('contact_phone', auth()->user()?->phone) }}" required
                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                        @error('contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                                        <textarea name="special_requests" rows="3"
                                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Any special requests or preferences...">{{ old('special_requests') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for children -->
                            <input type="hidden" name="children" value="{{ $children ?? 0 }}">
                        </div>

                        <!-- Sidebar Summary -->
                        <aside>
                            <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Booking Summary</h3>

                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Check-in</span>
                                        <span class="font-medium" id="summary-checkin">{{ $checkIn ?? date('Y-m-d') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Check-out</span>
                                        <span class="font-medium" id="summary-checkout">{{ $checkOut ?? date('Y-m-d', strtotime('+1 day')) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nights</span>
                                        <span class="font-medium">1</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Rooms</span>
                                        <span class="font-medium">{{ $rooms ?? 1 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Guests</span>
                                        <span class="font-medium">{{ ($adults ?? 2) + ($children ?? 0) }}</span>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-blue-600">৳{{ number_format($selectedRoom?->base_rate ?? 0) }}</span>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">Taxes and fees included</p>

                                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors mt-4">
                                    Continue to Payment
                                </button>

                                <p class="text-xs text-gray-500 text-center mt-4">
                                    By continuing, you agree to our terms and conditions
                                </p>
                            </div>
                        </aside>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
