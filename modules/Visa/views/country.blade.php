@extends('site-layout')

@section('meta-title', $country->name . ' Visa Services | ' . config('app.name'))

@section('meta-description', $country->meta_description ?? 'Get your ' . $country->name . ' visa hassle-free. Professional visa services with high success rate.')

@section('content')
    <!-- Country Hero -->
    <div class="relative bg-slate-900 min-h-[400px] flex items-center pt-28 pb-16 overflow-hidden isolate">
        <!-- Background Image -->
        <div class="absolute inset-0 -z-20">
            @if($country->hasMedia('image'))
                <img src="{{ $country->getFirstMediaUrl('image') }}" alt="{{ $country->name }}" class="w-full h-full object-cover">
            @else
                <img src="https://images.unsplash.com/photo-1544733422-251e532ca221?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="{{ $country->name }}" class="w-full h-full object-cover">
            @endif
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/95 via-slate-900/70 to-slate-900/40"></div>

        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm text-white/70">
                    <li><a href="{{ route('site.visa.index') }}" class="hover:text-white transition-colors">Visa</a></li>
                    <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
                    <li class="text-white font-medium">{{ $country->name }}</li>
                </ol>
            </nav>

            <div class="flex items-center gap-4 mb-4">
                @if($country->flag_emoji)
                    <span class="text-6xl">{{ $country->flag_emoji }}</span>
                @endif
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-xl">
                        {{ $country->name }} Visa
                    </h1>
                    @if($country->code)
                        <span class="text-purple-300 font-medium">{{ $country->code }}</span>
                    @endif
                </div>
            </div>

            @if($country->description)
                <p class="text-lg text-white/80 max-w-3xl leading-relaxed mt-4">
                    {{ Str::limit(strip_tags($country->description), 200) }}
                </p>
            @endif

            <div class="flex items-center gap-6 mt-6">
                <div class="flex items-center gap-2 text-white/80">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>{{ $visas->count() }} Visa{{ $visas->count() != 1 ? 's' : '' }} Available</span>
                </div>
                @if($country->visa_required !== null)
                    <div class="flex items-center gap-2">
                        @if($country->visa_required)
                            <span class="bg-amber-500/20 text-amber-300 px-3 py-1 rounded-full text-sm font-medium">Visa Required</span>
                        @else
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm font-medium">Visa Free / On Arrival</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Filter & Visas -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Category Filter -->
        @if($categories->isNotEmpty())
            <div class="flex items-center gap-3 mb-8 overflow-x-auto pb-2">
                <a href="{{ route('site.visa.country', $country->slug) }}" 
                   class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-purple-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('site.visa.country', ['slug' => $country->slug, 'category' => $category->id]) }}" 
                       class="shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') == $category->id ? 'bg-purple-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Visa Grid -->
        @if($visas->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($visas as $visa)
                    <a href="{{ route('site.visa.show', $visa->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all overflow-hidden border border-slate-100 block">
                        <div class="relative overflow-hidden aspect-[4/3]">
                            @if($visa->hasMedia('featured_image'))
                                <img src="{{ $visa->getFirstMediaUrl('featured_image', 'thumb') }}" alt="{{ $visa->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @elseif($country->hasMedia('image'))
                                <img src="{{ $country->getFirstMediaUrl('image') }}" alt="{{ $visa->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-purple-500 to-fuchsia-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                </div>
                            @endif
                            @if($visa->is_featured)
                                <div class="absolute top-3 left-3">
                                    <span class="bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">Featured</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                @if($visa->category)
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ $visa->category->name }}</span>
                                @endif
                                @if($visa->visa_type)
                                    <span class="text-xs font-semibold text-fuchsia-500 bg-fuchsia-50 px-2.5 py-1 rounded-full">{{ $visa->visa_type }}</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 mb-2 group-hover:text-purple-600 transition-colors">{{ $visa->name }}</h3>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4">{{ $visa->short_description ?? Str::limit(strip_tags($visa->description), 100) }}</p>
                            
                            <!-- Key Info -->
                            <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                                @if($visa->validity_period)
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $visa->validity_period }}
                                    </div>
                                @endif
                                @if($visa->entry_type)
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        {{ ucfirst($visa->entry_type) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                <div>
                                    <span class="text-xs text-slate-400">Starting from</span>
                                    <p class="text-xl font-bold text-purple-600">৳{{ number_format($visa->base_price) }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-slate-400">Processing</span>
                                    <p class="text-sm font-semibold text-slate-700">{{ $visa->processing_time ?? 'Varies' }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($visas->hasPages())
                <div class="mt-10">
                    {{ $visas->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16 bg-slate-50 rounded-2xl">
                <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                <h3 class="text-xl font-bold text-slate-700 mb-2">No visa services available</h3>
                <p class="text-slate-500 mb-6">Check back later or browse other countries</p>
                <a href="{{ route('site.visa.search') }}" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    Browse All Visas
                </a>
            </div>
        @endif
    </section>

    <!-- Country Info -->
    @if($country->description || $country->embassy_address || $country->embassy_phone)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="bg-slate-50 rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-6">{{ $country->name }} Visa Information</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @if($country->description)
                        <div class="prose prose-slate max-w-none">
                            {!! $country->description !!}
                        </div>
                    @endif

                    @if($country->embassy_address || $country->embassy_phone || $country->embassy_email)
                        <div class="bg-white rounded-xl p-6 border border-slate-200">
                            <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Embassy Information
                            </h3>
                            <div class="space-y-3 text-slate-600">
                                @if($country->embassy_address)
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>{{ $country->embassy_address }}</span>
                                    </div>
                                @endif
                                @if($country->embassy_phone)
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <a href="tel:{{ $country->embassy_phone }}" class="hover:text-purple-600">{{ $country->embassy_phone }}</a>
                                    </div>
                                @endif
                                @if($country->embassy_email)
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <a href="mailto:{{ $country->embassy_email }}" class="hover:text-purple-600">{{ $country->embassy_email }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection
