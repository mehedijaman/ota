@extends('site-layout')

@php
    $countryData = [
        'thailand' => ['name' => 'Thailand', 'flag' => '🇹🇭', 'type' => 'Sticker Visa', 'processing' => '5-7 Days', 'validity' => '60 Days', 'stay' => '30 Days', 'fee' => '4,500'],
        'malaysia' => ['name' => 'Malaysia', 'flag' => '🇲🇾', 'type' => 'E-Visa', 'processing' => '2-3 Days', 'validity' => '90 Days', 'stay' => '30 Days', 'fee' => '3,500'],
        'singapore' => ['name' => 'Singapore', 'flag' => '🇸🇬', 'type' => 'E-Visa', 'processing' => '3-5 Days', 'validity' => '35 Days', 'stay' => '30 Days', 'fee' => '5,000'],
        'uae' => ['name' => 'UAE', 'flag' => '🇦🇪', 'type' => 'E-Visa', 'processing' => '1-2 Days', 'validity' => '60 Days', 'stay' => '30 Days', 'fee' => '8,500'],
    ];
    $data = $countryData[$country] ?? $countryData['thailand'];
@endphp

@section('meta-title', $data['name'] . ' Visa Requirements | TriptonicBD')
@section('meta-description', 'Complete visa requirements and application process for ' . $data['name'])

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Header -->
    <div class="bg-linear-to-r from-purple-600 to-fuchsia-600 pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-purple-200 text-sm mb-4">
                <a href="{{ route('site.visa.index') }}" class="hover:text-white">Visa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">{{ $data['name'] }}</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-6xl">{{ $data['flag'] }}</span>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">{{ $data['name'] }} Visa</h1>
                    <p class="text-purple-100 mt-1">For Bangladeshi Citizens</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Bar -->
    <div class="bg-white border-b border-slate-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-6">
                    <div><p class="text-xs text-slate-500">Visa Type</p><p class="font-semibold">{{ $data['type'] }}</p></div>
                    <div><p class="text-xs text-slate-500">Processing</p><p class="font-semibold">{{ $data['processing'] }}</p></div>
                    <div><p class="text-xs text-slate-500">Validity</p><p class="font-semibold">{{ $data['validity'] }}</p></div>
                    <div><p class="text-xs text-slate-500">Max Stay</p><p class="font-semibold">{{ $data['stay'] }}</p></div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Service Fee</p>
                        <p class="text-2xl font-bold text-purple-600">৳{{ $data['fee'] }}</p>
                    </div>
                    <button class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-bold">Apply Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Required Documents -->
                    <div class="bg-white rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Required Documents
                        </h2>
                        <div class="space-y-3">
                            @foreach([
                                ['doc' => 'Valid Passport', 'note' => 'Minimum 6 months validity with 2 blank pages'],
                                ['doc' => 'Passport Size Photos', 'note' => '2 recent photos (35mm x 45mm, white background)'],
                                ['doc' => 'Bank Statement', 'note' => 'Last 6 months with minimum balance of ৳50,000'],
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

                    <!-- Important Notes -->
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                        <h2 class="text-lg font-bold text-amber-800 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Important Notes
                        </h2>
                        <ul class="space-y-2 text-sm text-amber-800">
                            <li>• Processing times may vary during peak seasons</li>
                            <li>• Embassy may request additional documents</li>
                            <li>• Visa approval is at the sole discretion of the embassy</li>
                            <li>• Service fee is non-refundable if visa is rejected</li>
                        </ul>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside>
                    <div class="bg-white rounded-2xl p-6 sticky top-40">
                        <h3 class="font-bold text-lg mb-4">Apply for {{ $data['name'] }} Visa</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-slate-500">Visa Type</label>
                                <select class="w-full border border-slate-200 rounded-lg px-3 py-2 mt-1">
                                    <option>Tourist Visa (30 Days)</option>
                                    <option>Business Visa (60 Days)</option>
                                    <option>Multiple Entry (90 Days)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Processing Speed</label>
                                <select class="w-full border border-slate-200 rounded-lg px-3 py-2 mt-1">
                                    <option>Standard (5-7 Days)</option>
                                    <option>Express (2-3 Days) +৳2,000</option>
                                    <option>Urgent (24 Hours) +৳5,000</option>
                                </select>
                            </div>
                            <div class="border-t border-slate-200 pt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span>Service Fee</span><span>৳{{ $data['fee'] }}</span>
                                </div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span>Embassy Fee</span><span>৳2,500</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg pt-2 border-t">
                                    <span>Total</span><span class="text-purple-600">৳{{ number_format((int)str_replace(',', '', $data['fee']) + 2500) }}</span>
                                </div>
                            </div>
                            <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-bold">Start Application</button>
                            <p class="text-center text-xs text-slate-500">99% Success Rate • Secure Payment</p>
                        </div>
                    </div>
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
                    ['q' => 'How long does the visa processing take?', 'a' => 'Standard processing takes 5-7 working days. Express options are available for faster processing.'],
                    ['q' => 'Can I track my visa application?', 'a' => 'Yes, you will receive email and SMS updates at each stage of your application.'],
                    ['q' => 'What if my visa is rejected?', 'a' => 'We have a 99% success rate. In rare cases of rejection, we provide free reapplication assistance.'],
                ] as $faq)
                <div class="border border-slate-200 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-800">{{ $faq['q'] }}</h3>
                    <p class="text-sm text-slate-600 mt-2">{{ $faq['a'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
