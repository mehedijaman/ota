@extends('site-layout')

@section('meta-title', 'Payment - ' . $booking->booking_number . ' | ' . config('app.name'))

@section('content')
    <!-- Payment Header -->
    <div class="bg-slate-900 pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.tour.index') }}" class="hover:text-white transition-colors">Tours</a>
                <i class="ri-arrow-right-s-line"></i>
                <a href="{{ route('site.tour.show', $booking->tour->slug) }}"
                    class="hover:text-white transition-colors">{{ $booking->tour->name }}</a>
                <i class="ri-arrow-right-s-line"></i>
                <span class="text-white">Payment</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Complete Your Payment</h1>
            <p class="text-white/70 mt-2">Booking #{{ $booking->booking_number }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Booking Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="font-bold text-xl text-slate-800 mb-4">Booking Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-600">Tour</p>
                    <p class="font-semibold text-slate-800">{{ $booking->tour->name }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Travel Date</p>
                    <p class="font-semibold text-slate-800">{{ $booking->travel_date->format('d M, Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Travelers</p>
                    <p class="font-semibold text-slate-800">{{ $booking->total_travelers }} persons</p>
                </div>
                <div>
                    <p class="text-slate-600">Total Amount</p>
                    <p class="font-bold text-2xl text-blue-600">৳{{ number_format($booking->total_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="font-bold text-xl text-slate-800 mb-6">Select Payment Method</h2>

            <form action="{{ route('site.tour.processPayment', $booking->booking_number) }}" method="POST"
                x-data="{ gateway: '' }">
                @csrf

                <div class="space-y-4">
                    <!-- SSLCommerz -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_gateway" value="sslcommerz" required class="sr-only peer"
                            x-model="gateway">
                        <div
                            class="border-2 border-slate-200 rounded-xl p-6 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-16 h-16 bg-linear-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <i class="ri-bank-card-line text-3xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">SSLCommerz</h3>
                                        <p class="text-sm text-slate-600">Credit/Debit Card, Mobile Banking</p>
                                    </div>
                                </div>
                                <div
                                    class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-blue-500 peer-checked:bg-blue-500 flex items-center justify-center">
                                    <i class="ri-check-line text-white text-sm hidden peer-checked:block"></i>
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- bKash -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_gateway" value="bkash" required class="sr-only peer"
                            x-model="gateway">
                        <div
                            class="border-2 border-slate-200 rounded-xl p-6 transition-all peer-checked:border-pink-500 peer-checked:bg-pink-50 hover:border-pink-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-16 h-16 bg-linear-to-br from-pink-500 to-pink-600 rounded-lg flex items-center justify-center">
                                        <i class="ri-smartphone-line text-3xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">bKash</h3>
                                        <p class="text-sm text-slate-600">Pay with bKash Mobile Banking</p>
                                    </div>
                                </div>
                                <div
                                    class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-pink-500 peer-checked:bg-pink-500 flex items-center justify-center">
                                    <i class="ri-check-line text-white text-sm hidden peer-checked:block"></i>
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Manual Payment -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_gateway" value="manual" required class="sr-only peer"
                            x-model="gateway">
                        <div
                            class="border-2 border-slate-200 rounded-xl p-6 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-16 h-16 bg-linear-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                        <i class="ri-hand-coin-line text-3xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">Manual Payment</h3>
                                        <p class="text-sm text-slate-600">Bank Transfer / Cash Payment</p>
                                    </div>
                                </div>
                                <div
                                    class="w-6 h-6 rounded-full border-2 border-slate-300 peer-checked:border-green-500 peer-checked:bg-green-500 flex items-center justify-center">
                                    <i class="ri-check-line text-white text-sm hidden peer-checked:block"></i>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
        </div>

        <!-- Manual Payment Details -->
        <div x-show="gateway === 'manual'"
            class="mt-8 border-l-4 border-green-500 bg-green-50 p-6 rounded-r-xl transition-all duration-300"
            style="display: none;">
            <div class="flex gap-3 mb-4">
                <div class="p-2 bg-green-100 rounded-lg h-fit">
                    <i class="ri-information-line text-green-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-lg">Manual Payment Instructions</h4>
                    <p class="text-slate-600 text-sm mt-1">
                        Please provide the transaction details of your payment. We will verify the transaction and confirm
                        your booking.
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Transaction ID / Number <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="transaction_number" x-bind:required="gateway === 'manual'"
                        class="w-full border-slate-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-4"
                        placeholder="e.g. TRPM12345678">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full border-slate-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5 px-4"
                        placeholder="Bank name, branch, or any additional information..."></textarea>
                </div>
            </div>
        </div>

        @error('payment_gateway')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
        @enderror

        <div class="mt-8 flex gap-4">
            <a href="{{ route('site.tour.bookingConfirmation', ['slug' => $booking->tour->slug, 'booking' => $booking->booking_number]) }}"
                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-4 px-6 rounded-xl font-semibold text-center transition-colors">
                Pay Later
            </a>
            <button type="submit"
                class="flex-1 bg-linear-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-4 px-6 rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                <i class="ri-secure-payment-line"></i>
                Proceed to Payment
            </button>
        </div>
        </form>
    </div>

    <!-- Security Notice -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <i class="ri-shield-check-line text-blue-600 text-xl"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Secure Payment</p>
                <p>Your payment information is encrypted and secure. We never store your card details.</p>
            </div>
        </div>
    </div>
    </div>
@endsection