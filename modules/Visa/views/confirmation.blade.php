@extends('site-layout')

@section('meta-title', 'Booking Confirmed - ' . $application->application_number . ' | ' . config('app.name'))

@section('content')
    <!-- Confirmation Header -->
    <div class="bg-gradient-to-r from-purple-700 to-fuchsia-700 pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.visa.index') }}" class="hover:text-white transition-colors">Visa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('site.visa.show', $visa->slug) }}" class="hover:text-white transition-colors">{{ $visa->name }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-white">Confirmation</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Application Submitted</h1>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-green-700">{{ session('success') }}</p>
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

        <!-- Success Icon Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center mb-8">
            <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Application Successfully Submitted!</h2>
            <p class="text-slate-600">Thank you for applying. We have received your visa application and will process it shortly.</p>
            <div class="mt-6 inline-flex items-center gap-2 bg-slate-100 px-6 py-3 rounded-lg">
                <span class="text-slate-600">Application Number:</span>
                <span class="font-mono font-bold text-purple-600 text-lg">{{ $application->application_number }}</span>
            </div>
        </div>

        <!-- Application Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-xl text-slate-800">Application Status</h3>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold 
                    @if($application->status === 'pending') bg-amber-100 text-amber-700
                    @elseif($application->status === 'processing') bg-blue-100 text-blue-700
                    @elseif($application->status === 'approved') bg-green-100 text-green-700
                    @elseif($application->status === 'rejected') bg-red-100 text-red-700
                    @else bg-slate-100 text-slate-700 @endif">
                    <span class="w-2 h-2 rounded-full 
                        @if($application->status === 'pending') bg-amber-500
                        @elseif($application->status === 'processing') bg-blue-500
                        @elseif($application->status === 'approved') bg-green-500
                        @elseif($application->status === 'rejected') bg-red-500
                        @else bg-slate-500 @endif"></span>
                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                </span>
            </div>

            <!-- Status Timeline -->
            <div class="relative">
                <div class="absolute left-5 top-3 bottom-3 w-0.5 bg-slate-200"></div>
                <div class="space-y-6">
                    <!-- Submitted -->
                    <div class="relative flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0 z-10">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="pt-1.5">
                            <p class="font-semibold text-slate-800">Application Submitted</p>
                            <p class="text-sm text-slate-500">{{ $application->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="relative flex items-start gap-4">
                        @if($application->payment_status === 'paid')
                            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @elseif($application->payment_status === 'pending')
                            <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        @endif
                        <div class="pt-1.5">
                            <p class="font-semibold text-slate-800">Payment</p>
                            <p class="text-sm text-slate-500">
                                @if($application->payment_status === 'paid')
                                    Payment received on {{ $application->paid_at?->format('d M Y, h:i A') ?? 'N/A' }}
                                @elseif($application->payment_status === 'pending')
                                    Payment pending - ৳{{ number_format($application->total_amount, 2) }}
                                @else
                                    Awaiting payment
                                @endif
                            </p>
                            @if($application->payment_status !== 'paid')
                                <a href="{{ route('site.visa.payment', $application->application_number) }}"
                                    class="inline-flex items-center gap-1 mt-2 text-sm font-semibold text-purple-600 hover:text-purple-700">
                                    Complete Payment
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="relative flex items-start gap-4">
                        @if(in_array($application->status, ['processing', 'approved', 'completed']))
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                        @endif
                        <div class="pt-1.5">
                            <p class="font-semibold text-slate-800">Document Review</p>
                            <p class="text-sm text-slate-500">
                                @if(in_array($application->status, ['processing', 'approved', 'completed']))
                                    Documents being reviewed
                                @else
                                    Pending document review
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Approval -->
                    <div class="relative flex items-start gap-4">
                        @if($application->status === 'approved' || $application->status === 'completed')
                            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @elseif($application->status === 'rejected')
                            <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0 z-10">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        @endif
                        <div class="pt-1.5">
                            <p class="font-semibold text-slate-800">
                                @if($application->status === 'rejected')
                                    Application Rejected
                                @else
                                    Visa Decision
                                @endif
                            </p>
                            <p class="text-sm text-slate-500">
                                @if($application->status === 'approved' || $application->status === 'completed')
                                    Visa approved!
                                @elseif($application->status === 'rejected')
                                    {{ $application->rejection_reason ?? 'Please contact support for details.' }}
                                @else
                                    Awaiting decision
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Visa Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-4">Visa Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Visa Service</span>
                        <span class="font-medium text-slate-800">{{ $visa->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Destination</span>
                        <span class="font-medium text-slate-800">{{ $visa->country->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Visa Type</span>
                        <span class="font-medium text-slate-800">{{ $visa->visaType?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Processing Time</span>
                        <span class="font-medium text-slate-800">
                            @if($application->processingTime)
                                {{ $application->processingTime->name }}
                            @else
                                Standard
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Entry Type</span>
                        <span class="font-medium text-slate-800">{{ ucfirst($visa->entry_type ?? 'single') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Validity</span>
                        <span class="font-medium text-slate-800">{{ $visa->validity_days ?? 'N/A' }} days</span>
                    </div>
                </div>
            </div>

            <!-- Travel Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-4">Travel Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Travel Date</span>
                        <span class="font-medium text-slate-800">{{ $application->intended_travel_date?->format('d M, Y') ?? 'Not specified' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Return Date</span>
                        <span class="font-medium text-slate-800">{{ $application->intended_return_date?->format('d M, Y') ?? 'Not specified' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Purpose</span>
                        <span class="font-medium text-slate-800">{{ ucfirst($application->travel_purpose ?? 'Tourism') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Total Travelers</span>
                        <span class="font-medium text-slate-800">{{ $application->total_travelers }} person(s)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Travelers -->
        @if($application->travelers && $application->travelers->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="font-bold text-lg text-slate-800 mb-4">Travelers ({{ $application->travelers->count() }})</h3>
            <div class="space-y-4">
                @foreach($application->travelers as $index => $traveler)
                <div class="border border-slate-100 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $traveler->full_name }}</p>
                            <p class="text-sm text-slate-500">{{ ucfirst($traveler->traveler_type ?? 'Adult') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500">Passport</p>
                            <p class="font-medium text-slate-700">{{ $traveler->passport_number }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Nationality</p>
                            <p class="font-medium text-slate-700">{{ $traveler->nationality ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Date of Birth</p>
                            <p class="font-medium text-slate-700">{{ $traveler->date_of_birth?->format('d M, Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Passport Expiry</p>
                            <p class="font-medium text-slate-700">{{ $traveler->passport_expiry_date?->format('d M, Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-800">Payment Summary</h3>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold 
                    @if($application->payment_status === 'paid') bg-green-100 text-green-700
                    @elseif($application->payment_status === 'pending') bg-amber-100 text-amber-700
                    @else bg-slate-100 text-slate-700 @endif">
                    {{ ucfirst($application->payment_status) }}
                </span>
            </div>
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
                @if($application->discount_amount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Discount</span>
                    <span>-৳{{ number_format($application->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between font-bold text-slate-800 pt-2 border-t">
                    <span>Total Amount</span>
                    <span>৳{{ number_format($application->total_amount, 2) }}</span>
                </div>
                @if($application->paid_amount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Paid Amount</span>
                    <span>৳{{ number_format($application->paid_amount, 2) }}</span>
                </div>
                @endif
                @if($application->due_amount > 0)
                <div class="flex justify-between text-red-600 font-semibold">
                    <span>Due Amount</span>
                    <span>৳{{ number_format($application->due_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Next Steps / Important Information -->
        <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 border border-purple-200 rounded-2xl p-6 mb-8">
            <h3 class="font-bold text-lg text-purple-800 mb-4">What Happens Next?</h3>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-purple-600 font-bold">1</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Document Verification</p>
                        <p class="text-sm text-slate-600">We will review your submitted documents within 24-48 hours.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-purple-600 font-bold">2</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Embassy Submission</p>
                        <p class="text-sm text-slate-600">Once verified, your application will be submitted to the embassy.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-purple-600 font-bold">3</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Visa Decision</p>
                        <p class="text-sm text-slate-600">You will be notified via email and SMS once a decision is made.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-purple-600 font-bold">4</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Passport Collection</p>
                        <p class="text-sm text-slate-600">Upon approval, collect your passport with the visa from our office or receive it via courier.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="font-bold text-lg text-slate-800 mb-4">Need Help?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="tel:+8801700000000" class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Call Us</p>
                        <p class="font-medium text-slate-800">+880 1700-000000</p>
                    </div>
                </a>
                <a href="mailto:visa@example.com" class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Email</p>
                        <p class="font-medium text-slate-800">visa@example.com</p>
                    </div>
                </a>
                <a href="https://wa.me/8801700000000" target="_blank" class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">WhatsApp</p>
                        <p class="font-medium text-slate-800">+880 1700-000000</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            @if($application->payment_status !== 'paid')
            <a href="{{ route('site.visa.payment', $application->application_number) }}"
                class="flex-1 bg-linear-to-r from-purple-600 to-fuchsia-600 text-white py-4 px-6 rounded-xl font-bold text-center hover:from-purple-700 hover:to-fuchsia-700 transition-all shadow-lg shadow-purple-500/30">
                Complete Payment
            </a>
            @endif
            <a href="{{ route('site.visa.index') }}"
                class="flex-1 border-2 border-purple-600 text-purple-600 py-4 px-6 rounded-xl font-bold text-center hover:bg-purple-50 transition-colors">
                Back to Visa Services
            </a>
        </div>
    </div>
@endsection
