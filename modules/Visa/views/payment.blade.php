@extends('site-layout')

@section('meta-title', 'Payment - ' . $application->application_number . ' | ' . config('app.name'))

@section('content')
    <!-- Payment Header -->
    <div class="bg-gradient-to-r from-purple-700 to-fuchsia-700 pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.visa.index') }}" class="hover:text-white transition-colors">Visa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('site.visa.show', $application->visa->slug) }}" class="hover:text-white transition-colors">{{ $application->visa->name }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-white">Payment</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Complete Your Payment</h1>
            <p class="text-white/70 mt-2">Application #{{ $application->application_number }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Flash Messages -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-amber-700">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Application Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="font-bold text-xl text-slate-800 mb-4">Application Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-600">Visa Service</p>
                    <p class="font-semibold text-slate-800">{{ $application->visa->name }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Destination</p>
                    <p class="font-semibold text-slate-800">{{ $application->visa->country->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Travel Date</p>
                    <p class="font-semibold text-slate-800">{{ $application->intended_travel_date?->format('d M, Y') ?? 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Applicants</p>
                    <p class="font-semibold text-slate-800">{{ $application->total_travelers }} person(s)</p>
                </div>
                <div>
                    <p class="text-slate-600">Application Status</p>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold 
                        @if($application->status === 'pending') bg-amber-100 text-amber-700
                        @elseif($application->status === 'approved') bg-green-100 text-green-700
                        @else bg-slate-100 text-slate-700 @endif">
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-600">Amount Due</p>
                    <p class="font-bold text-2xl text-purple-600">৳{{ number_format($application->total_amount, 2) }}</p>
                </div>
            </div>

            <!-- Fee Breakdown -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <h3 class="font-semibold text-slate-700 mb-3">Fee Breakdown</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>৳{{ number_format($application->subtotal, 2) }}</span>
                    </div>
                    @if($application->embassy_fees > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Embassy Fees</span>
                        <span>৳{{ number_format($application->embassy_fees, 2) }}</span>
                    </div>
                    @endif
                    @if($application->service_fees > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Service Fee</span>
                        <span>৳{{ number_format($application->service_fees, 2) }}</span>
                    </div>
                    @endif
                    @if($application->express_fees > 0)
                    <div class="flex justify-between text-amber-600">
                        <span>Express Processing</span>
                        <span>৳{{ number_format($application->express_fees, 2) }}</span>
                    </div>
                    @endif
                    @if($application->additional_fees > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Additional Fees</span>
                        <span>৳{{ number_format($application->additional_fees, 2) }}</span>
                    </div>
                    @endif
                    @if($application->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span>-৳{{ number_format($application->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-slate-800 pt-2 border-t">
                        <span>Total</span>
                        <span>৳{{ number_format($application->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="font-bold text-xl text-slate-800 mb-6">Select Payment Method</h2>

            <form action="{{ route('site.visa.processPayment', $application->application_number) }}" method="POST"
                x-data="{ gateway: '' }">
                @csrf

                <div class="space-y-4">
                    <!-- SSLCommerz -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_gateway" value="sslcommerz" required class="sr-only peer"
                            x-model="gateway">
                        <div class="border-2 border-slate-200 rounded-xl p-6 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-linear-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">SSLCommerz</h3>
                                        <p class="text-sm text-slate-600">Credit/Debit Card, Mobile Banking</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                    :class="gateway === 'sslcommerz' ? 'border-purple-500 bg-purple-500' : 'border-slate-300'">
                                    <svg x-show="gateway === 'sslcommerz'" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- bKash -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_gateway" value="bkash" required class="sr-only peer"
                            x-model="gateway">
                        <div class="border-2 border-slate-200 rounded-xl p-6 transition-all peer-checked:border-pink-500 peer-checked:bg-pink-50 hover:border-pink-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-linear-to-br from-pink-500 to-pink-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">bKash</h3>
                                        <p class="text-sm text-slate-600">Pay with bKash Mobile Banking</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                    :class="gateway === 'bkash' ? 'border-pink-500 bg-pink-500' : 'border-slate-300'">
                                    <svg x-show="gateway === 'bkash'" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Manual Payment -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_gateway" value="manual" required class="sr-only peer"
                            x-model="gateway">
                        <div class="border-2 border-slate-200 rounded-xl p-6 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-linear-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-800">Manual Payment</h3>
                                        <p class="text-sm text-slate-600">Bank Transfer / Cash Payment</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                    :class="gateway === 'manual' ? 'border-green-500 bg-green-500' : 'border-slate-300'">
                                    <svg x-show="gateway === 'manual'" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Manual Payment Details -->
                <div x-show="gateway === 'manual'"
                    class="mt-6 border-l-4 border-green-500 bg-green-50 p-6 rounded-r-xl transition-all duration-300"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="flex gap-3 mb-4">
                        <div class="p-2 bg-green-100 rounded-lg h-fit">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-lg">Manual Payment Instructions</h4>
                            <p class="text-slate-600 text-sm mt-1">
                                Please provide the transaction details of your payment. We will verify the transaction and process
                                your visa application.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-slate-600 mb-2 block">Transaction / Reference Number *</label>
                            <input type="text" name="transaction_number" placeholder="e.g., TRX123456789"
                                class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500"
                                :required="gateway === 'manual'">
                        </div>
                        <div>
                            <label class="text-sm text-slate-600 mb-2 block">Notes / Additional Information</label>
                            <textarea name="notes" rows="3" placeholder="Any additional details about your payment..."
                                class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500"></textarea>
                        </div>

                        <!-- Bank Details -->
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <h5 class="font-semibold text-slate-700 mb-3">Bank Transfer Details</h5>
                            <div class="space-y-2 text-sm text-slate-600">
                                <p><strong>Bank:</strong> {{ config('services.bank.name', 'BRAC Bank Ltd') }}</p>
                                <p><strong>Account Name:</strong> {{ config('services.bank.account_name', config('app.name')) }}</p>
                                <p><strong>Account Number:</strong> {{ config('services.bank.account_number', '1501204253849001') }}</p>
                                <p><strong>Branch:</strong> {{ config('services.bank.branch', 'Gulshan Branch') }}</p>
                                <p><strong>Routing Number:</strong> {{ config('services.bank.routing', '060261718') }}</p>
                            </div>
                        </div>

                        <!-- Mobile Banking -->
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <h5 class="font-semibold text-slate-700 mb-3">Mobile Banking</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                                <div>
                                    <p><strong>bKash (Merchant):</strong></p>
                                    <p class="font-mono">{{ config('services.mobile.bkash', '01700-000000') }}</p>
                                </div>
                                <div>
                                    <p><strong>Nagad (Merchant):</strong></p>
                                    <p class="font-mono">{{ config('services.mobile.nagad', '01700-000000') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full mt-8 bg-linear-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white py-4 px-6 rounded-xl font-bold text-lg shadow-lg shadow-purple-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span >
                        Pay ৳{{ number_format($application->total_amount, 2) }}
                    </span>
                </button>
            </form>
        </div>

        <!-- Security Notice -->
        <div class="mt-8 text-center text-sm text-slate-500">
            <div class="flex items-center justify-center gap-2 mb-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>Secure Payment</span>
            </div>
            <p>Your payment information is encrypted and secure. We do not store your card details.</p>
        </div>
    </div>
@endsection
