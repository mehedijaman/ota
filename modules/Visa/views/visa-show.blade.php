@extends('site-layout')

@section('meta-title', $visa->meta_title ?: $visa->name . ' | ' . config('app.name'))

@section('meta-description', $visa->meta_description ?: $visa->short_description)

@section('content')
    <!-- Visa Header -->
    <div class="relative bg-slate-900 pt-20 sm:pt-24 pb-32 sm:pb-40 overflow-hidden">
        <div class="absolute inset-0">
            @if($visa->hasMedia('featured_image'))
                <img src="{{ $visa->getFirstMediaUrl('featured_image') }}" alt="{{ $visa->name }}" class="w-full h-full object-cover opacity-40">
            @elseif($visa->country && $visa->country->hasMedia('image'))
                <img src="{{ $visa->country->getFirstMediaUrl('image') }}" alt="{{ $visa->name }}" class="w-full h-full object-cover opacity-40">
            @else
                <img src="https://images.unsplash.com/photo-1544733422-251e532ca221?w=1920" alt="{{ $visa->name }}" class="w-full h-full object-cover opacity-40">
            @endif
        </div>
        <div class="absolute inset-0 bg-linear-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-white/70 text-xs sm:text-sm mb-4 flex-wrap">
                <a href="{{ route('site.visa.index') }}" class="hover:text-white transition-colors">Visa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @if($visa->country)
                    <a href="{{ route('site.visa.country', $visa->country->slug) }}" class="hover:text-white transition-colors">{{ $visa->country->name }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @endif
                <span class="text-white truncate max-w-[200px] sm:max-w-none">{{ $visa->name }}</span>
            </nav>

            <!-- Badges -->
            <div class="flex flex-wrap items-start gap-3 mb-4">
                @if($visa->is_featured)
                    <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        Featured
                    </span>
                @endif
                @if($visa->is_popular)
                    <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">Popular</span>
                @endif
                @if($visa->category)
                    <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur">{{ $visa->category->name }}</span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-white mb-4 leading-tight">{{ $visa->name }}</h1>

            <!-- Quick Info -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-white/90 text-sm sm:text-base">
                @if($visa->country)
                    <span class="flex items-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $visa->country->name }}
                    </span>
                @endif
                @if($visa->processing_time)
                    <span class="flex items-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $visa->processing_time }}
                    </span>
                @endif
                @if($visa->validity_period)
                    <span class="flex items-center gap-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Valid: {{ $visa->validity_period }}
                    </span>
                @endif
                @if($visa->entry_type)
                    <span class="flex items-center gap-1 bg-white/20 backdrop-blur px-3 py-1 rounded-lg text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        {{ ucfirst($visa->entry_type) }} Entry
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 sm:-mt-24 relative z-20 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Overview Card -->
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Overview
                    </h2>

                    @if($visa->short_description)
                        <p class="text-slate-600 text-base sm:text-lg mb-4 leading-relaxed italic border-l-4 border-purple-300 pl-4 bg-purple-50 py-3 rounded-r-lg">
                            {{ $visa->short_description }}
                        </p>
                    @endif

                    @if($visa->description)
                        <div class="prose prose-slate prose-sm sm:prose-base max-w-none">
                            {!! $visa->description !!}
                        </div>
                    @endif

                    <!-- Quick Facts -->
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @if($visa->visa_type)
                                <div class="text-center p-3 bg-slate-50 rounded-xl">
                                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Visa Type</p>
                                    <p class="font-semibold text-slate-700">{{ $visa->visa_type }}</p>
                                </div>
                            @endif
                            @if($visa->max_stay)
                                <div class="text-center p-3 bg-slate-50 rounded-xl">
                                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Max Stay</p>
                                    <p class="font-semibold text-slate-700">{{ $visa->max_stay }}</p>
                                </div>
                            @endif
                            @if($visa->processing_time)
                                <div class="text-center p-3 bg-slate-50 rounded-xl">
                                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Processing</p>
                                    <p class="font-semibold text-slate-700">{{ $visa->processing_time }}</p>
                                </div>
                            @endif
                            @if($visa->success_rate)
                                <div class="text-center p-3 bg-green-50 rounded-xl">
                                    <p class="text-xs text-green-600 uppercase tracking-wide mb-1">Success Rate</p>
                                    <p class="font-bold text-green-700">{{ $visa->success_rate }}%</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Requirements Section -->
                @if($visa->requirements->count())
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Required Documents
                        </h2>
                        <div class="space-y-4">
                            @foreach($visa->requirements->where('is_active', true)->sortBy('sort_order') as $requirement)
                                <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors {{ $requirement->is_mandatory ? 'border-l-4 border-amber-400' : '' }}">
                                    <div class="w-10 h-10 bg-{{ $requirement->is_mandatory ? 'amber' : 'slate' }}-100 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-{{ $requirement->is_mandatory ? 'amber-600' : 'slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-slate-800">{{ $requirement->name }}</h4>
                                            @if($requirement->is_mandatory)
                                                <span class="text-xs bg-amber-500 text-white px-2 py-0.5 rounded">Required</span>
                                            @else
                                                <span class="text-xs bg-slate-300 text-slate-700 px-2 py-0.5 rounded">Optional</span>
                                            @endif
                                        </div>
                                        @if($requirement->description)
                                            <p class="text-slate-600 text-sm">{{ $requirement->description }}</p>
                                        @endif
                                        @if($requirement->notes)
                                            <p class="text-slate-500 text-xs mt-2 italic">{{ $requirement->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Processing Times -->
                @if($visa->processingTimes->count())
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Processing Options
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($visa->processingTimes->where('is_active', true)->sortBy('sort_order') as $processingTime)
                                <div class="border-2 border-slate-200 rounded-xl p-4 hover:border-purple-300 transition-colors {{ $processingTime->is_recommended ? 'border-purple-400 bg-purple-50' : '' }}">
                                    @if($processingTime->is_recommended)
                                        <span class="inline-block text-xs bg-purple-500 text-white px-2 py-0.5 rounded mb-2">Recommended</span>
                                    @endif
                                    <h4 class="font-bold text-slate-800 mb-1">{{ $processingTime->name }}</h4>
                                    <p class="text-purple-600 font-semibold text-lg">{{ $processingTime->duration }}</p>
                                    @if($processingTime->additional_fee > 0)
                                        <p class="text-slate-500 text-sm mt-2">
                                            +৳{{ number_format($processingTime->additional_fee) }} extra
                                        </p>
                                    @else
                                        <p class="text-green-600 text-sm mt-2">Standard processing</p>
                                    @endif
                                    @if($processingTime->description)
                                        <p class="text-slate-500 text-xs mt-2">{{ $processingTime->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Fee Breakdown -->
                @if($visa->fees->count())
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Fee Breakdown
                        </h2>
                        <div class="space-y-3">
                            @foreach($visa->fees->where('is_active', true)->sortBy('sort_order') as $fee)
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-slate-700">{{ $fee->name }}</p>
                                        @if($fee->description)
                                            <p class="text-slate-500 text-sm">{{ $fee->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-800">৳{{ number_format($fee->amount) }}</p>
                                        @if($fee->fee_type !== 'fixed')
                                            <p class="text-slate-400 text-xs">{{ ucfirst($fee->fee_type) }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- FAQs -->
                @if($visa->faqs->count())
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Frequently Asked Questions
                        </h2>
                        <div class="space-y-4">
                            @foreach($visa->faqs->where('is_active', true)->sortBy('sort_order') as $faq)
                                <details class="group bg-slate-50 rounded-xl">
                                    <summary class="flex items-center justify-between cursor-pointer p-4 font-medium text-slate-800">
                                        {{ $faq->question }}
                                        <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </summary>
                                    <div class="px-4 pb-4 text-slate-600">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Booking Card -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <!-- Price & Book Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-purple-100">
                        <div class="mb-4">
                            <p class="text-sm text-slate-500 mb-1">Starting from</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-bold text-purple-600">৳{{ number_format($visa->base_price) }}</span>
                                <span class="text-slate-500 text-sm">/person</span>
                            </div>
                        </div>

                        <!-- Fee Summary -->
                        <div class="space-y-2 py-4 border-t border-b border-slate-100 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Base Price</span>
                                <span>৳{{ number_format($visa->base_price) }}</span>
                            </div>
                            @if($visa->embassy_fee)
                                <div class="flex justify-between text-slate-600">
                                    <span>Embassy Fee</span>
                                    <span>৳{{ number_format($visa->embassy_fee) }}</span>
                                </div>
                            @endif
                            @if($visa->service_fee)
                                <div class="flex justify-between text-slate-600">
                                    <span>Service Fee</span>
                                    <span>৳{{ number_format($visa->service_fee) }}</span>
                                </div>
                            @endif
                            @if($visa->vfs_fee)
                                <div class="flex justify-between text-slate-600">
                                    <span>VFS Fee</span>
                                    <span>৳{{ number_format($visa->vfs_fee) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-slate-800 pt-2 border-t">
                                <span>Total (per person)</span>
                                <span>৳{{ number_format($visa->base_price + ($visa->embassy_fee ?? 0) + ($visa->service_fee ?? 0) + ($visa->vfs_fee ?? 0)) }}</span>
                            </div>
                        </div>

                        <!-- Processing Time -->
                        @if($visa->processing_time)
                            <div class="flex items-center gap-2 py-3 text-slate-600">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Processing: <strong>{{ $visa->processing_time }}</strong></span>
                            </div>
                        @endif

                        <!-- CTA Button -->
                        @auth
                            <a href="{{ route('site.visa.apply', $visa->slug) }}" class="block w-full bg-linear-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white py-4 px-6 rounded-xl font-bold text-center shadow-lg shadow-purple-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                Apply Now
                            </a>
                        @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(route('site.visa.apply', $visa->slug)) }}" class="block w-full bg-linear-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white py-4 px-6 rounded-xl font-bold text-center shadow-lg shadow-purple-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                Login to Apply
                            </a>
                        @endauth

                        <p class="text-center text-slate-500 text-xs mt-3">
                            No hidden fees • 99% Success Rate
                        </p>
                    </div>

                    <!-- Contact Card -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <h3 class="font-bold text-slate-800 mb-4">Need Help?</h3>
                        <div class="space-y-3">
                            <a href="tel:{{ config('services.support.phone', '+8801700000000') }}" class="flex items-center gap-3 text-slate-600 hover:text-purple-600 transition-colors">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Call Us</p>
                                    <p class="font-medium">{{ config('services.support.phone', '+880 1700-000000') }}</p>
                                </div>
                            </a>
                            <a href="https://wa.me/{{ config('services.support.whatsapp', '8801700000000') }}" target="_blank" class="flex items-center gap-3 text-slate-600 hover:text-green-600 transition-colors">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow">
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">WhatsApp</p>
                                    <p class="font-medium">Chat with us</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Visas -->
    @if($relatedVisas->count())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">You May Also Like</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedVisas as $relatedVisa)
                    <a href="{{ route('site.visa.show', $relatedVisa->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all overflow-hidden border border-slate-100 block">
                        <div class="relative overflow-hidden aspect-[16/9]">
                            @if($relatedVisa->hasMedia('featured_image'))
                                <img src="{{ $relatedVisa->getFirstMediaUrl('featured_image', 'thumb') }}" alt="{{ $relatedVisa->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @elseif($relatedVisa->country && $relatedVisa->country->hasMedia('image'))
                                <img src="{{ $relatedVisa->country->getFirstMediaUrl('image') }}" alt="{{ $relatedVisa->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-purple-500 to-fuchsia-600"></div>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($relatedVisa->country)
                                <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">{{ $relatedVisa->country->name }}</span>
                            @endif
                            <h3 class="font-bold text-slate-800 mt-2 group-hover:text-purple-600 transition-colors">{{ $relatedVisa->name }}</h3>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-purple-600 font-bold">৳{{ number_format($relatedVisa->base_price) }}</span>
                                <span class="text-slate-500 text-sm">{{ $relatedVisa->processing_time }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
