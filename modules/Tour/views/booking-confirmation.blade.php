@extends('site-layout')

@section('meta-title', 'Booking Confirmation - ' . $booking->booking_number . ' | ' . config('app.name'))

@section('content')
    <div class="bg-gradient-to-b from-slate-100 to-white min-h-screen pt-28 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Success Header --}}
            <div class="text-center mb-8">
                @if($booking->payment_status === 'paid')
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-green-100">
                        <i class="ri-checkbox-circle-fill text-5xl text-green-500"></i>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-2">Payment Successful!</h1>
                    <p class="text-lg text-slate-600">Your booking has been confirmed and payment received.</p>
                @else
                    <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-amber-100">
                        <i class="ri-time-line text-5xl text-amber-500"></i>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-2">Booking Received!</h1>
                    <p class="text-lg text-slate-600">Your booking has been submitted and is awaiting confirmation.</p>
                @endif
            </div>

            {{-- Main Booking Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
                {{-- Booking Number Header --}}
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Booking Reference</p>
                            <p class="text-white text-2xl font-bold tracking-wide">{{ $booking->booking_number }}</p>
                        </div>
                        <div class="flex gap-2">
                            {{-- Booking Status Badge --}}
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'confirmed' => 'bg-green-100 text-green-700',
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    'refunded' => 'bg-purple-100 text-purple-700',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $statusColor }}">
                                <i class="ri-bookmark-line mr-1"></i>
                                {{ ucfirst($booking->status) }}
                            </span>
                            {{-- Payment Status Badge --}}
                            @php
                                $paymentColors = [
                                    'unpaid' => 'bg-red-100 text-red-700',
                                    'partial' => 'bg-amber-100 text-amber-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'refunded' => 'bg-purple-100 text-purple-700',
                                ];
                                $paymentColor = $paymentColors[$booking->payment_status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $paymentColor }}">
                                <i class="ri-wallet-3-line mr-1"></i>
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tour Information --}}
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-start gap-4">
                        @if($tour->featured_image)
                            <img src="{{ $tour->featured_image }}" alt="{{ $tour->name }}" class="w-24 h-24 rounded-xl object-cover hidden sm:block">
                        @else
                            <div class="w-24 h-24 rounded-xl bg-slate-100 flex items-center justify-center hidden sm:flex">
                                <i class="ri-landscape-line text-3xl text-slate-400"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $tour->name }}</h2>
                            <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                                @if($tour->destination)
                                    <span class="flex items-center gap-1">
                                        <i class="ri-map-pin-line text-orange-500"></i>
                                        {{ $tour->destination->name }}
                                    </span>
                                @endif
                                @if($tour->duration_days)
                                    <span class="flex items-center gap-1">
                                        <i class="ri-time-line text-orange-500"></i>
                                        {{ $tour->duration_days }} {{ Str::plural('Day', $tour->duration_days) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Booking Details Grid --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Travel Details --}}
                    <div class="space-y-4">
                        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                            <i class="ri-calendar-event-line text-orange-500"></i>
                            Travel Details
                        </h3>
                        <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 text-sm">Travel Date</span>
                                <span class="font-medium text-slate-800">
                                    {{ $booking->travel_date ? $booking->travel_date->format('D, d M Y') : 'Flexible' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 text-sm">Adults</span>
                                <span class="font-medium text-slate-800">{{ $booking->adult_count ?? 0 }}</span>
                            </div>
                            @if(($booking->child_count ?? 0) > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 text-sm">Children</span>
                                <span class="font-medium text-slate-800">{{ $booking->child_count }}</span>
                            </div>
                            @endif
                            @if(($booking->infant_count ?? 0) > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 text-sm">Infants</span>
                                <span class="font-medium text-slate-800">{{ $booking->infant_count }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                                <span class="text-slate-600 font-medium">Total Travelers</span>
                                <span class="font-bold text-slate-800">{{ ($booking->adult_count ?? 0) + ($booking->child_count ?? 0) + ($booking->infant_count ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div class="space-y-4">
                        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                            <i class="ri-user-line text-orange-500"></i>
                            Contact Information
                        </h3>
                        <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 text-sm">Name</span>
                                <span class="font-medium text-slate-800 text-right">{{ $booking->contact_name }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 text-sm">Email</span>
                                <span class="font-medium text-slate-800 text-right break-all">{{ $booking->contact_email }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 text-sm">Phone</span>
                                <span class="font-medium text-slate-800 text-right">{{ $booking->contact_phone }}</span>
                            </div>
                            @if($booking->whatsapp)
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 text-sm">WhatsApp</span>
                                <span class="font-medium text-slate-800 text-right">{{ $booking->whatsapp }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="px-6 pb-6">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-5 text-white">
                        <h3 class="font-semibold mb-4 flex items-center gap-2">
                            <i class="ri-money-dollar-circle-line text-orange-400"></i>
                            Payment Summary
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-300">Subtotal</span>
                                <span>৳ {{ number_format($booking->subtotal ?? $booking->total_amount, 2) }}</span>
                            </div>
                            @if(($booking->extras_total ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-300">Extras</span>
                                <span>৳ {{ number_format($booking->extras_total, 2) }}</span>
                            </div>
                            @endif
                            @if(($booking->discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-green-400">
                                <span>Discount</span>
                                <span>- ৳ {{ number_format($booking->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between pt-3 mt-3 border-t border-slate-600 text-lg font-bold">
                                <span>Total Amount</span>
                                <span class="text-orange-400">৳ {{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            @if($booking->payment_status === 'paid')
                            <div class="flex justify-between text-green-400">
                                <span>Amount Paid</span>
                                <span>৳ {{ number_format($booking->amount_paid ?? $booking->total_amount, 2) }}</span>
                            </div>
                            @elseif(($booking->amount_due ?? 0) > 0)
                            <div class="flex justify-between text-amber-400">
                                <span>Amount Due</span>
                                <span>৳ {{ number_format($booking->amount_due, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- What's Next Section --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ri-road-map-line text-orange-500"></i>
                    What Happens Next?
                </h3>
                
                @if($booking->payment_status === 'paid')
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="ri-check-line text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">Payment Confirmed</p>
                                <p class="text-sm text-slate-500">Your payment has been successfully processed.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="ri-mail-send-line text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">Confirmation Email Sent</p>
                                <p class="text-sm text-slate-500">A detailed confirmation has been sent to <strong>{{ $booking->contact_email }}</strong></p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="ri-customer-service-2-line text-orange-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">Our Team Will Contact You</p>
                                <p class="text-sm text-slate-500">You'll receive tour details, pickup information, and any additional instructions within 24 hours.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="ri-check-line text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">Booking Received</p>
                                <p class="text-sm text-slate-500">We've received your booking request.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                <i class="ri-search-eye-line text-amber-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">Under Review</p>
                                <p class="text-sm text-slate-500">Our team will review your booking and confirm availability.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center">
                                <i class="ri-phone-line text-slate-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">We'll Contact You</p>
                                <p class="text-sm text-slate-500">Expect a call or email at <strong>{{ $booking->contact_email }}</strong> with payment instructions and confirmation.</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Pay Now Button for Unpaid Bookings --}}
                    @if($booking->payment_status === 'unpaid' && $booking->status !== 'cancelled')
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('site.tour.payment', $booking->booking_number) }}" 
                           class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                            <i class="ri-bank-card-line"></i>
                            Pay Now - ৳ {{ number_format($booking->total_amount, 2) }}
                        </a>
                    </div>
                    @endif
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="window.print()" 
                        class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-6 py-3 rounded-xl font-medium transition-colors border border-slate-200 shadow-sm">
                    <i class="ri-printer-line"></i>
                    Print Confirmation
                </button>
                <a href="{{ route('site.tour.index') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-sm">
                    <i class="ri-compass-3-line"></i>
                    Browse More Tours
                </a>
                <a href="/" 
                   class="inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-sm">
                    <i class="ri-home-line"></i>
                    Back to Home
                </a>
            </div>

            {{-- Support Section --}}
            <div class="mt-10 text-center">
                <div class="inline-flex items-center gap-6 text-sm text-slate-500">
                    <span>Need help?</span>
                    <a href="/contact" class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-700 font-medium">
                        <i class="ri-customer-service-line"></i>
                        Contact Support
                    </a>
                    <a href="tel:+8801700000000" class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-700 font-medium">
                        <i class="ri-phone-line"></i>
                        Call Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            header, footer, nav, .no-print { display: none !important; }
            body { background: white !important; }
            .shadow-xl, .shadow-lg, .shadow-sm { box-shadow: none !important; }
        }
    </style>
@endsection
