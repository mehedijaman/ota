@extends('site-layout')

@section('meta-title', 'Booking Confirmed | ' . config('app.name'))

@section('content')
    <div class="bg-slate-50 min-h-screen pt-28 pb-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-check-line text-4xl text-green-600"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-slate-800 mb-2">Booking Confirmed!</h1>
                <p class="text-slate-500 mb-6">Thank you for choosing us. Your booking has been received.</p>

                <div class="bg-slate-50 rounded-xl p-6 text-left mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Booking Number</p>
                            <p class="font-bold text-lg text-orange-600">{{ $booking['booking_number'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">
                                {{ ucfirst($booking['status']) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Tour</p>
                            <p class="font-medium text-slate-800">{{ $booking['tour'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Departure Date</p>
                            <p class="font-medium text-slate-800">{{ $booking['departure_date'] ?? 'Flexible' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Travelers</p>
                            <p class="font-medium text-slate-800">{{ $booking['total_travelers'] }} person(s)</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Amount</p>
                            <p class="font-bold text-lg text-slate-800">৳ {{ number_format($booking['total_amount']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl p-4 text-left mb-6">
                    <div class="flex items-start gap-3">
                        <i class="ri-information-line text-blue-600 text-xl mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">What happens next?</p>
                            <ul class="space-y-1 text-blue-700">
                                <li>• Our team will review your booking and contact you shortly</li>
                                <li>• You will receive an email confirmation at <strong>{{ $booking['contact_email'] }}</strong></li>
                                <li>• Payment instructions will be provided after confirmation</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('site.tour.index') }}" class="inline-flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                        <i class="ri-compass-3-line"></i>
                        Browse More Tours
                    </a>
                    <a href="/" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-medium transition-colors">
                        <i class="ri-home-line"></i>
                        Back to Home
                    </a>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm">
                    Have questions? <a href="/contact" class="text-orange-600 hover:underline font-medium">Contact our support team</a>
                </p>
            </div>
        </div>
    </div>
@endsection
