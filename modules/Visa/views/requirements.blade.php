@extends('site-layout')

@section('meta-title', $country->name . ' Visa Requirements | ' . config('app.name'))
@section('meta-description', 'Complete visa requirements and application process for ' . $country->name . '. Get your ' . $country->name . ' visa hassle-free.')

@section('content')
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-700 to-fuchsia-700 pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-purple-200 text-sm mb-4">
                <a href="{{ route('site.visa.index') }}" class="hover:text-white transition-colors">Visa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">{{ $country->name }}</span>
            </div>
            <div class="flex items-center gap-4">
                @if($country->flag_emoji)
                    <span class="text-6xl">{{ $country->flag_emoji }}</span>
                @elseif($country->hasMedia('flag'))
                    <img src="{{ $country->getFirstMediaUrl('flag') }}" alt="{{ $country->name }} flag" class="w-20 h-14 object-cover rounded-lg shadow-lg">
                @else
                    <div class="w-20 h-14 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">{{ $country->name }} Visa</h1>
                    <p class="text-purple-100 mt-1">For Bangladeshi Citizens</p>
                </div>
            </div>
        </div>
    </div>

    @php
        $featuredVisa = $visas->first();
    @endphp

    <!-- Quick Info Bar -->
    @if($featuredVisa)
    <div class="bg-white border-b border-slate-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-6">
                    <div>
                        <p class="text-xs text-slate-500">Visa Type</p>
                        <p class="font-semibold">{{ $featuredVisa->visaType?->name ?? $featuredVisa->category?->name ?? 'Tourist Visa' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Processing</p>
                        <p class="font-semibold">{{ $featuredVisa->processing_days ?? '5-7' }} Working Days</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Validity</p>
                        <p class="font-semibold">{{ $featuredVisa->validity_days ?? 'N/A' }} Days</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Max Stay</p>
                        <p class="font-semibold">{{ $featuredVisa->stay_days ?? 'N/A' }} Days</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Starting From</p>
                        <p class="text-2xl font-bold text-purple-600">৳{{ number_format($featuredVisa->price ?? 0) }}</p>
                    </div>
                    <a href="{{ route('site.visa.show', $featuredVisa->slug) }}"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-bold transition-colors">
                        Apply Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Content -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Country Overview -->
                    @if($country->description)
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            About {{ $country->name }} Visa
                        </h2>
                        <div class="prose prose-slate max-w-none">
                            {!! $country->description !!}
                        </div>
                    </div>
                    @endif

                    <!-- Required Documents -->
                    @if($featuredVisa && $featuredVisa->requirements && $featuredVisa->requirements->count() > 0)
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Required Documents
                        </h2>
                        <div class="space-y-3">
                            @foreach($featuredVisa->requirements as $requirement)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $requirement->name }}</p>
                                    @if($requirement->description)
                                        <p class="text-sm text-slate-500">{{ $requirement->description }}</p>
                                    @endif
                                    @if($requirement->is_mandatory)
                                        <span class="inline-block mt-1 text-xs font-medium text-red-600">* Required</span>
                                    @else
                                        <span class="inline-block mt-1 text-xs font-medium text-slate-400">Optional</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <!-- Default Requirements -->
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            General Required Documents
                        </h2>
                        <div class="space-y-3">
                            @foreach([
                                ['doc' => 'Valid Passport', 'note' => 'Minimum 6 months validity with 2 blank pages'],
                                ['doc' => 'Passport Size Photos', 'note' => '2 recent photos (35mm x 45mm, white background)'],
                                ['doc' => 'Bank Statement', 'note' => 'Last 6 months with sufficient balance'],
                                ['doc' => 'Flight Reservation', 'note' => 'Round-trip ticket booking confirmation'],
                                ['doc' => 'Hotel Booking', 'note' => 'Confirmed accommodation for entire stay'],
                                ['doc' => 'Visa Application Form', 'note' => 'Completed and signed application form'],
                                ['doc' => 'NOC Letter', 'note' => 'From employer or business (if applicable)'],
                            ] as $item)
                            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $item['doc'] }}</p>
                                    <p class="text-sm text-slate-500">{{ $item['note'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Application Process -->
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Application Process
                        </h2>
                        <div class="relative">
                            @foreach([
                                ['step' => '1', 'title' => 'Submit Application', 'desc' => 'Fill out the online form and upload required documents'],
                                ['step' => '2', 'title' => 'Document Review', 'desc' => 'Our experts review your documents for accuracy'],
                                ['step' => '3', 'title' => 'Embassy Submission', 'desc' => 'We submit your application to the embassy'],
                                ['step' => '4', 'title' => 'Get Your Visa', 'desc' => 'Receive your approved visa via courier'],
                            ] as $idx => $item)
                            <div class="flex gap-4 mb-4 last:mb-0">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">{{ $item['step'] }}</div>
                                    @if($idx < 3)<div class="w-0.5 h-8 bg-purple-200 mt-2"></div>@endif
                                </div>
                                <div class="flex-1 pb-4">
                                    <h3 class="font-semibold text-slate-800">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-slate-500">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Available Visa Services -->
                    @if($visas->count() > 0)
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Available {{ $country->name }} Visa Services
                        </h2>
                        <div class="space-y-4">
                            @foreach($visas as $visa)
                            <a href="{{ route('site.visa.show', $visa->slug) }}"
                                class="block border border-slate-200 rounded-xl p-4 hover:border-purple-300 hover:bg-purple-50/50 transition-colors group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-slate-800 group-hover:text-purple-600 transition-colors">
                                            {{ $visa->name }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-slate-500">
                                            @if($visa->visaType)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                    {{ $visa->visaType->name }}
                                                </span>
                                            @endif
                                            @if($visa->validity_days)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    {{ $visa->validity_days }} Days Validity
                                                </span>
                                            @endif
                                            @if($visa->processing_days)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $visa->processing_days }} Days Processing
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <p class="text-2xl font-bold text-purple-600">৳{{ number_format($visa->price ?? 0) }}</p>
                                        <p class="text-xs text-slate-500">per person</p>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Important Notes -->
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                        <h2 class="text-lg font-bold text-amber-800 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Important Notes
                        </h2>
                        <ul class="space-y-2 text-sm text-amber-800">
                            <li class="flex items-start gap-2">
                                <span class="shrink-0">•</span>
                                <span>Processing times may vary during peak seasons and holidays</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="shrink-0">•</span>
                                <span>Embassy may request additional documents during processing</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="shrink-0">•</span>
                                <span>Visa approval is at the sole discretion of the {{ $country->name }} embassy</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="shrink-0">•</span>
                                <span>Service fee is non-refundable in case of visa rejection</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="shrink-0">•</span>
                                <span>Ensure passport has minimum 6 months validity from travel date</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Embassy Information -->
                    @if($country->embassy_address || $country->embassy_phone || $country->embassy_email)
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $country->name }} Embassy Information
                        </h2>
                        <div class="space-y-4">
                            @if($country->embassy_address)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Address</p>
                                    <p class="text-slate-800">{{ $country->embassy_address }}</p>
                                </div>
                            </div>
                            @endif
                            @if($country->embassy_phone)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Phone</p>
                                    <p class="text-slate-800">{{ $country->embassy_phone }}</p>
                                </div>
                            </div>
                            @endif
                            @if($country->embassy_email)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Email</p>
                                    <p class="text-slate-800">{{ $country->embassy_email }}</p>
                                </div>
                            </div>
                            @endif
                            @if($country->embassy_website)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Website</p>
                                    <a href="{{ $country->embassy_website }}" target="_blank" class="text-purple-600 hover:text-purple-700">
                                        {{ $country->embassy_website }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside>
                    @if($featuredVisa)
                    <div class="bg-white rounded-2xl p-6 sticky top-40 shadow-lg">
                        <h3 class="font-bold text-lg mb-4">Apply for {{ $country->name }} Visa</h3>
                        <div class="space-y-4">
                            <!-- Available Visas Dropdown -->
                            @if($visas->count() > 1)
                            <div>
                                <label class="text-sm text-slate-500 block mb-1">Select Visa Service</label>
                                <select id="visaSelect" onchange="updateSidebarPrice()"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    @foreach($visas as $visa)
                                        <option value="{{ $visa->slug }}" data-price="{{ $visa->price ?? 0 }}" data-embassy="{{ $visa->embassy_fee ?? 0 }}">
                                            {{ $visa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Processing Options -->
                            @if($featuredVisa->processingTimes && $featuredVisa->processingTimes->count() > 0)
                            <div>
                                <label class="text-sm text-slate-500 block mb-1">Processing Speed</label>
                                <select id="processingSelect" onchange="updateSidebarPrice()"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    @foreach($featuredVisa->processingTimes as $pt)
                                        <option value="{{ $pt->id }}" data-fee="{{ $pt->additional_fee ?? 0 }}">
                                            {{ $pt->name }} ({{ $pt->min_days }}-{{ $pt->max_days }} Days)
                                            @if($pt->additional_fee > 0) +৳{{ number_format($pt->additional_fee) }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Fee Breakdown -->
                            <div class="border-t border-slate-200 pt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-slate-600">Service Fee</span>
                                    <span id="serviceFee">৳{{ number_format($featuredVisa->price ?? 0) }}</span>
                                </div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-slate-600">Embassy Fee</span>
                                    <span id="embassyFee">৳{{ number_format($featuredVisa->embassy_fee ?? 0) }}</span>
                                </div>
                                <div id="processingFeeRow" class="flex justify-between text-sm mb-2 hidden">
                                    <span class="text-slate-600">Express Fee</span>
                                    <span id="processingFee">৳0</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg pt-2 border-t">
                                    <span>Total</span>
                                    <span id="totalFee" class="text-purple-600">
                                        ৳{{ number_format(($featuredVisa->price ?? 0) + ($featuredVisa->embassy_fee ?? 0)) }}
                                    </span>
                                </div>
                            </div>

                            <a id="applyButton" href="{{ route('site.visa.show', $featuredVisa->slug) }}"
                                class="block w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-bold text-center transition-colors">
                                View Details & Apply
                            </a>
                            <p class="text-center text-xs text-slate-500">99% Success Rate • Secure Payment</p>
                        </div>
                    </div>
                    @else
                    <div class="bg-white rounded-2xl p-6 sticky top-40 shadow-lg text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 mb-2">No Visa Services Available</h3>
                        <p class="text-slate-600 text-sm mb-4">Currently, we don't have visa services for {{ $country->name }}. Please check back later.</p>
                        <a href="{{ route('site.visa.index') }}"
                            class="block w-full border-2 border-purple-600 text-purple-600 py-3 rounded-xl font-bold text-center hover:bg-purple-50 transition-colors">
                            Browse All Visas
                        </a>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-slate-800 text-center mb-8">Frequently Asked Questions</h2>
            <div class="space-y-4">
                @foreach([
                    ['q' => 'How long does the ' . $country->name . ' visa processing take?', 'a' => 'Standard processing typically takes 5-7 working days. Express options are available for faster processing at additional cost.'],
                    ['q' => 'Can I track my visa application?', 'a' => 'Yes, you will receive email and SMS updates at each stage of your application. You can also log in to check your application status.'],
                    ['q' => 'What if my visa is rejected?', 'a' => 'We have a 99% success rate. In rare cases of rejection, we provide guidance for reapplication. Note that service fees are non-refundable.'],
                    ['q' => 'Do I need to visit the embassy in person?', 'a' => 'For most visa types, we handle the embassy submission on your behalf. However, some visa categories may require a personal appearance for biometrics.'],
                    ['q' => 'How do I receive my passport after visa approval?', 'a' => 'You can collect your passport from our office or opt for courier delivery to your address at an additional cost.'],
                ] as $faq)
                <div class="border border-slate-200 rounded-xl p-4 hover:border-purple-200 transition-colors">
                    <h3 class="font-semibold text-slate-800">{{ $faq['q'] }}</h3>
                    <p class="text-sm text-slate-600 mt-2">{{ $faq['a'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-purple-700 to-fuchsia-700 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Ready to Apply for Your {{ $country->name }} Visa?</h2>
            <p class="text-white/80 mb-6">Start your application today and travel with confidence</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if($featuredVisa)
                <a href="{{ route('site.visa.show', $featuredVisa->slug) }}"
                    class="bg-white text-purple-600 px-8 py-3 rounded-xl font-bold hover:bg-slate-100 transition-colors">
                    Apply Now
                </a>
                @endif
                <a href="{{ route('site.visa.search', ['country' => $country->slug]) }}"
                    class="border-2 border-white text-white px-8 py-3 rounded-xl font-bold hover:bg-white/10 transition-colors">
                    View All {{ $country->name }} Visas
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function updateSidebarPrice() {
    const visaSelect = document.getElementById('visaSelect');
    const processingSelect = document.getElementById('processingSelect');
    const applyButton = document.getElementById('applyButton');
    
    let servicePrice = 0;
    let embassyPrice = 0;
    let processingFee = 0;
    let visaSlug = '{{ $featuredVisa?->slug ?? "" }}';
    
    if (visaSelect) {
        const selectedOption = visaSelect.options[visaSelect.selectedIndex];
        servicePrice = parseFloat(selectedOption.dataset.price) || 0;
        embassyPrice = parseFloat(selectedOption.dataset.embassy) || 0;
        visaSlug = selectedOption.value;
    } else {
        servicePrice = {{ $featuredVisa?->price ?? 0 }};
        embassyPrice = {{ $featuredVisa?->embassy_fee ?? 0 }};
    }
    
    if (processingSelect) {
        const selectedProcessing = processingSelect.options[processingSelect.selectedIndex];
        processingFee = parseFloat(selectedProcessing.dataset.fee) || 0;
    }
    
    const total = servicePrice + embassyPrice + processingFee;
    
    document.getElementById('serviceFee').textContent = '৳' + servicePrice.toLocaleString();
    document.getElementById('embassyFee').textContent = '৳' + embassyPrice.toLocaleString();
    document.getElementById('totalFee').textContent = '৳' + total.toLocaleString();
    
    if (processingFee > 0) {
        document.getElementById('processingFeeRow').classList.remove('hidden');
        document.getElementById('processingFee').textContent = '৳' + processingFee.toLocaleString();
    } else {
        document.getElementById('processingFeeRow').classList.add('hidden');
    }
    
    if (applyButton && visaSlug) {
        applyButton.href = '{{ url("visa") }}/' + visaSlug;
    }
}
</script>
@endpush
