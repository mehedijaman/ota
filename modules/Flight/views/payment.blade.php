@extends('site-layout')

@section('meta-title', 'Payment | ' . config('app.name'))

@section('content')
    <section class="py-8 bg-slate-50 min-h-screen">
        <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-8">Complete Payment</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <form action="{{ route('flight.processPayment', $booking->uuid) }}" method="POST">
                            @csrf
                            <h2 class="font-bold text-lg text-slate-800 mb-6">Payment Method</h2>

                            <div class="space-y-4 mb-6">
                                <label
                                    class="flex items-center gap-4 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-500">
                                    <input type="radio" name="payment_method" value="card" class="text-blue-600" required
                                        checked>
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-800">Credit / Debit Card</p>
                                        <p class="text-sm text-slate-500">Visa, Mastercard, AMEX</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <i class="ri-visa-fill text-2xl text-blue-600"></i>
                                        <i class="ri-mastercard-fill text-2xl text-red-500"></i>
                                    </div>
                                </label>

                                <label
                                    class="flex items-center gap-4 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-500">
                                    <input type="radio" name="payment_method" value="bkash" class="text-blue-600" required>
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-800">bKash</p>
                                        <p class="text-sm text-slate-500">Pay with bKash mobile wallet</p>
                                    </div>
                                    <!-- Add bKash logo if available or icon -->
                                    <i class="ri-smartphone-line text-2xl text-pink-600"></i>
                                </label>

                                <label
                                    class="flex items-center gap-4 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-blue-500">
                                    <input type="radio" name="payment_method" value="bank" class="text-blue-600" required>
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-800">Bank Transfer</p>
                                        <p class="text-sm text-slate-500">Direct bank deposit</p>
                                    </div>
                                    <i class="ri-bank-line text-2xl text-slate-600"></i>
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors shadow-lg">
                                Pay ৳{{ number_format($booking->total_amount ?? 45000) }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h3 class="font-bold text-lg text-slate-800 mb-4">Order Summary</h3>

                        <div class="space-y-3 text-sm border-b border-slate-100 pb-4 mb-4">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Base Fare</span>
                                <span class="text-slate-800">৳{{ number_format($booking->base_fare_total ?? 40000) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Taxes & Fees</span>
                                <span class="text-slate-800">৳{{ number_format($booking->tax_total ?? 5000) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between font-bold text-lg">
                            <span class="text-slate-800">Total</span>
                            <span class="text-orange-500">৳{{ number_format($booking->total_amount ?? 45000) }}</span>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <i class="ri-shield-check-line mr-1"></i>
                                Your payment is secured with SSL encryption
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection