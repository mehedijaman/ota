@extends('site-layout')

@section('title', 'Payment - Booking #' . $booking->booking_number)

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Progress Steps -->
                <div class="flex items-center justify-center mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="ml-2 text-gray-500">Your Details</span>
                    </div>
                    <div class="w-16 h-1 bg-blue-600 mx-4"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">2</div>
                        <span class="ml-2 font-medium text-gray-900">Payment</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 mx-4"></div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold">3</div>
                        <span class="ml-2 text-gray-500">Confirmation</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Payment Options -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-4">Select Payment Method</h3>

                            <div class="space-y-4">
                                <!-- bKash -->
                                <label class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors border-gray-200">
                                    <input type="radio" name="payment_method" value="bkash" class="text-pink-600 focus:ring-pink-500">
                                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                                        <img src="/images/bkash-logo.png" alt="bKash" class="h-8">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">bKash</p>
                                        <p class="text-sm text-gray-500">Pay with bKash mobile wallet</p>
                                    </div>
                                </label>

                                <!-- Nagad -->
                                <label class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors border-gray-200">
                                    <input type="radio" name="payment_method" value="nagad" class="text-orange-600 focus:ring-orange-500">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <img src="/images/nagad-logo.png" alt="Nagad" class="h-8">
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Nagad</p>
                                        <p class="text-sm text-gray-500">Pay with Nagad mobile wallet</p>
                                    </div>
                                </label>

                                <!-- Card -->
                                <label class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors border-gray-200">
                                    <input type="radio" name="payment_method" value="card" class="text-blue-600 focus:ring-blue-500">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Credit/Debit Card</p>
                                        <p class="text-sm text-gray-500">Visa, Mastercard, AMEX</p>
                                    </div>
                                </label>

                                <!-- Bank Transfer -->
                                <label class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors border-gray-200">
                                    <input type="radio" name="payment_method" value="bank" class="text-green-600 focus:ring-green-500">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-university text-green-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Bank Transfer</p>
                                        <p class="text-sm text-gray-500">Direct bank deposit</p>
                                    </div>
                                </label>

                                <!-- Pay at Hotel -->
                                <label class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors border-gray-200">
                                    <input type="radio" name="payment_method" value="pay_at_hotel" class="text-purple-600 focus:ring-purple-500">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-hotel text-purple-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Pay at Hotel</p>
                                        <p class="text-sm text-gray-500">Pay when you check in</p>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors mt-6">
                                Complete Payment
                            </button>
                        </div>
                    </div>

                    <!-- Booking Summary -->
                    <aside>
                        <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                            <h3 class="font-bold text-lg text-gray-900 mb-4">Booking Summary</h3>

                            <!-- Hotel Info -->
                            <div class="flex gap-3 mb-4">
                                <img src="{{ $booking->hotel->thumbnail_url ?? '/images/hotel-placeholder.jpg' }}" alt="{{ $booking->hotel->name }}"
                                     class="w-16 h-16 rounded-lg object-cover">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $booking->hotel->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $booking->hotel->city?->name }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Booking #</span>
                                    <span class="font-medium">{{ $booking->booking_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-in</span>
                                    <span class="font-medium">{{ $booking->check_in_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-out</span>
                                    <span class="font-medium">{{ $booking->check_out_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nights</span>
                                    <span class="font-medium">{{ $booking->nights }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Rooms</span>
                                    <span class="font-medium">{{ $booking->total_rooms }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Guests</span>
                                    <span class="font-medium">{{ $booking->total_adults + $booking->total_children }}</span>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Room Charges</span>
                                    <span>৳{{ number_format($booking->room_total) }}</span>
                                </div>
                                @if($booking->tax_amount > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Taxes & Fees</span>
                                        <span>৳{{ number_format($booking->tax_amount) }}</span>
                                    </div>
                                @endif
                                @if($booking->discount_amount > 0)
                                    <div class="flex justify-between text-green-600">
                                        <span>Discount</span>
                                        <span>-৳{{ number_format($booking->discount_amount) }}</span>
                                    </div>
                                @endif
                            </div>

                            <hr class="my-4">

                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-blue-600">৳{{ number_format($booking->total_amount) }}</span>
                            </div>

                            @if($booking->amount_due > 0)
                                <div class="flex justify-between text-sm mt-2 text-orange-600">
                                    <span>Amount Due</span>
                                    <span class="font-bold">৳{{ number_format($booking->amount_due) }}</span>
                                </div>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
@endsection
