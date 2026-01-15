@extends('site-layout')

@section('title', 'Booking Confirmed - ' . $booking->booking_number)

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <!-- Success Message -->
                <div class="bg-white rounded-xl shadow-md p-8 text-center mb-8">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check text-green-600 text-4xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>
                    <p class="text-gray-600 mb-4">Your reservation has been successfully confirmed.</p>
                    <p class="text-lg font-medium text-blue-600">Booking # {{ $booking->booking_number }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        A confirmation email has been sent to {{ $booking->contact_email }}
                    </p>
                </div>

                <!-- Booking Details -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Hotel Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
                        <div class="flex items-center gap-4">
                            <img src="{{ $booking->hotel->thumbnail_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80' }}"
                                alt="{{ $booking->hotel->name }}" class="w-20 h-20 rounded-lg object-cover">
                            <div>
                                <div class="flex items-center gap-1 mb-1">
                                    @for($i = 0; $i < $booking->hotel->star_rating; $i++)
                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                    @endfor
                                </div>
                                <h2 class="text-xl font-bold">{{ $booking->hotel->name }}</h2>
                                <p class="text-blue-100">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $booking->hotel->city?->name }}, {{ $booking->hotel->country?->name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Check-in/out -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Check-in</p>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ $booking->check_in_date->format('D, M d, Y') }}</p>
                                <p class="text-sm text-gray-600">From {{ $booking->hotel->check_in_time }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Check-out</p>
                                <p class="text-xl font-bold text-gray-900">
                                    {{ $booking->check_out_date->format('D, M d, Y') }}</p>
                                <p class="text-sm text-gray-600">Until {{ $booking->hotel->check_out_time }}</p>
                            </div>
                        </div>

                        <!-- Stay Summary -->
                        <div class="border-t border-gray-100 pt-6 mb-6">
                            <h3 class="font-bold text-gray-900 mb-4">Stay Summary</h3>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-blue-600">{{ $booking->nights }}</p>
                                    <p class="text-sm text-gray-600">{{ Str::plural('Night', $booking->nights) }}</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-blue-600">{{ $booking->total_rooms }}</p>
                                    <p class="text-sm text-gray-600">{{ Str::plural('Room', $booking->total_rooms) }}</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="text-2xl font-bold text-blue-600">
                                        {{ $booking->total_adults + $booking->total_children }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ Str::plural('Guest', $booking->total_adults + $booking->total_children) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Rooms Booked -->
                        <div class="border-t border-gray-100 pt-6 mb-6">
                            <h3 class="font-bold text-gray-900 mb-4">Rooms Booked</h3>
                            @foreach($booking->rooms as $bookingRoom)
                                <div class="bg-gray-50 rounded-lg p-4 mb-3 last:mb-0">
                                    <p class="font-medium text-gray-900">{{ $bookingRoom->room?->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $bookingRoom->ratePlan?->name }} •
                                        {{ $bookingRoom->adults }} {{ Str::plural('adult', $bookingRoom->adults) }}
                                        @if($bookingRoom->children)
                                            , {{ $bookingRoom->children }} {{ Str::plural('child', $bookingRoom->children) }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Guest Details -->
                        <div class="border-t border-gray-100 pt-6 mb-6">
                            <h3 class="font-bold text-gray-900 mb-4">Contact Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="font-medium text-gray-900">{{ $booking->contact_name }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-envelope mr-2 w-4"></i>{{ $booking->contact_email }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-phone mr-2 w-4"></i>{{ $booking->contact_phone }}
                                </p>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="font-bold text-gray-900 mb-4">Payment Summary</h3>
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
                            @if($booking->amount_paid > 0)
                                <div class="flex justify-between text-sm mt-2 text-green-600">
                                    <span>Amount Paid</span>
                                    <span>৳{{ number_format($booking->amount_paid) }}</span>
                                </div>
                            @endif
                            @if($booking->amount_due > 0)
                                <div class="flex justify-between text-sm text-orange-600">
                                    <span>Amount Due</span>
                                    <span class="font-bold">৳{{ number_format($booking->amount_due) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-gray-50 p-6 flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('site.hotel.show', $booking->hotel->slug) }}"
                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                            <i class="fas fa-hotel mr-2"></i> View Hotel
                        </a>
                        <button onclick="window.print()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            <i class="fas fa-print mr-2"></i> Print Confirmation
                        </button>
                    </div>
                </div>

                <!-- Important Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-8">
                    <h3 class="font-bold text-yellow-800 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Important Information
                    </h3>
                    <ul class="text-sm text-yellow-700 space-y-2">
                        <li>• Please present this confirmation at check-in along with a valid ID</li>
                        <li>• Check-in time is from {{ $booking->hotel->check_in_time }}</li>
                        <li>• Check-out time is until {{ $booking->hotel->check_out_time }}</li>
                        <li>• For any changes or cancellations, please contact us at least 24 hours before arrival</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection