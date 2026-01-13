@extends('site-layout')

@section('meta-title', 'Apply for ' . $visa->name . ' | ' . config('app.name'))

@section('content')
    <!-- Booking Header -->
    <div class="bg-gradient-to-r from-purple-700 to-fuchsia-700 pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.visa.index') }}" class="hover:text-white transition-colors">Visa</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <a href="{{ route('site.visa.show', $visa->slug) }}" class="hover:text-white transition-colors">{{ $visa->name }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-white">Apply Now</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Visa Application</h1>
        </div>
    </div>
@endsection

@section('alpine-content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="visaBookingForm()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <!-- Visa Summary Card -->
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                    <div class="flex gap-4">
                        @if($visa->hasMedia('featured_image'))
                            <img src="{{ $visa->getFirstMediaUrl('featured_image', 'thumb') }}" alt="{{ $visa->name }}" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl">
                        @elseif($visa->country && $visa->country->hasMedia('image'))
                            <img src="{{ $visa->country->getFirstMediaUrl('image') }}" alt="{{ $visa->name }}" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl">
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-xl flex items-center justify-center">
                                <svg class="w-10 h-10 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"></path></svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-lg sm:text-xl text-slate-800">{{ $visa->name }}</h3>
                            <p class="text-slate-500 text-sm mt-1">
                                @if($visa->country)
                                    {{ $visa->country->name }}
                                @endif
                                @if($visa->processing_time)
                                    • Processing: {{ $visa->processing_time }}
                                @endif
                            </p>
                            @if($visa->validity_period)
                                <p class="text-purple-600 text-sm mt-1">Valid for {{ $visa->validity_period }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('site.visa.submit', $visa->slug) }}" method="POST" @submit="submitForm">
                    @csrf

                    <!-- Processing Time Selection -->
                    @if($visa->processingTimes->where('is_active', true)->count())
                        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Select Processing Speed
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div @click="formData.processing_time_id = null"
                                    :class="formData.processing_time_id === null ? 'border-purple-500 bg-purple-50' : 'border-slate-200 hover:border-purple-300'"
                                    class="border-2 rounded-xl p-4 cursor-pointer transition-all">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-slate-800">Standard</h4>
                                        <input type="radio" name="processing_time_id" value=""
                                            :checked="formData.processing_time_id === null" class="text-purple-500">
                                    </div>
                                    <p class="text-purple-600 font-semibold">{{ $visa->processing_time ?? 'Standard Time' }}</p>
                                    <p class="text-green-600 text-sm mt-1">No extra charge</p>
                                </div>
                                @foreach($visa->processingTimes->where('is_active', true)->sortBy('sort_order') as $processingTime)
                                    <div @click="formData.processing_time_id = {{ $processingTime->id }}; expressFeePer = {{ $processingTime->additional_fee }}"
                                        :class="formData.processing_time_id === {{ $processingTime->id }} ? 'border-purple-500 bg-purple-50' : 'border-slate-200 hover:border-purple-300'"
                                        class="border-2 rounded-xl p-4 cursor-pointer transition-all {{ $processingTime->is_recommended ? 'ring-2 ring-purple-300' : '' }}">
                                        @if($processingTime->is_recommended)
                                            <span class="inline-block text-xs bg-purple-500 text-white px-2 py-0.5 rounded mb-2">Recommended</span>
                                        @endif
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-bold text-slate-800">{{ $processingTime->name }}</h4>
                                            <input type="radio" name="processing_time_id" value="{{ $processingTime->id }}"
                                                :checked="formData.processing_time_id === {{ $processingTime->id }}" class="text-purple-500">
                                        </div>
                                        <p class="text-purple-600 font-semibold">{{ $processingTime->duration }}</p>
                                        @if($processingTime->additional_fee > 0)
                                            <p class="text-amber-600 text-sm mt-1">+৳{{ number_format($processingTime->additional_fee) }}/person</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Travel Dates -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Travel Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Intended Travel Date *</label>
                                <input type="date" name="intended_travel_date" x-model="formData.intended_travel_date" 
                                    :min="minDate" required
                                    class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Intended Return Date</label>
                                <input type="date" name="intended_return_date" x-model="formData.intended_return_date"
                                    :min="formData.intended_travel_date"
                                    class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm text-slate-600 mb-2 block">Purpose of Travel</label>
                                <select name="travel_purpose" x-model="formData.travel_purpose"
                                    class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    <option value="Tourism">Tourism</option>
                                    <option value="Business">Business</option>
                                    <option value="Medical">Medical</option>
                                    <option value="Education">Education</option>
                                    <option value="Employment">Employment</option>
                                    <option value="Family Visit">Family Visit</option>
                                    <option value="Transit">Transit</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Travelers -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Number of Applicants
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Adults (18+) *</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" @click="updateCount('adult', -1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <input type="number" name="adult_count" x-model.number="formData.adult_count" min="1"
                                        readonly class="w-full text-center border-0 focus:ring-0 bg-transparent font-medium">
                                    <button type="button" @click="updateCount('adult', 1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Children (2-17)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" @click="updateCount('child', -1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <input type="number" name="child_count" x-model.number="formData.child_count" min="0"
                                        readonly class="w-full text-center border-0 focus:ring-0 bg-transparent font-medium">
                                    <button type="button" @click="updateCount('child', 1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Infants (0-2)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" @click="updateCount('infant', -1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <input type="number" name="infant_count" x-model.number="formData.infant_count" min="0"
                                        readonly class="w-full text-center border-0 focus:ring-0 bg-transparent font-medium">
                                    <button type="button" @click="updateCount('infant', 1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Contact Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Full Name *</label>
                                <input type="text" name="contact_name" x-model="formData.contact_name" required
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Email *</label>
                                <input type="email" name="contact_email" x-model="formData.contact_email" required
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Phone *</label>
                                <input type="tel" name="contact_phone" x-model="formData.contact_phone" required
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">WhatsApp</label>
                                <input type="tel" name="whatsapp" x-model="formData.whatsapp"
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm text-slate-600 mb-2 block">Address</label>
                                <textarea name="contact_address" x-model="formData.contact_address" rows="2"
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"></textarea>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">City</label>
                                <input type="text" name="city" x-model="formData.city"
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Postal Code</label>
                                <input type="text" name="postal_code" x-model="formData.postal_code"
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <h4 class="font-semibold text-slate-700 mb-4">Emergency Contact (Optional)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm text-slate-600 mb-2 block">Name</label>
                                    <input type="text" name="emergency_contact_name" x-model="formData.emergency_contact_name"
                                        class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                </div>
                                <div>
                                    <label class="text-sm text-slate-600 mb-2 block">Phone</label>
                                    <input type="tel" name="emergency_contact_phone" x-model="formData.emergency_contact_phone"
                                        class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                </div>
                                <div>
                                    <label class="text-sm text-slate-600 mb-2 block">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" x-model="formData.emergency_contact_relationship"
                                        class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Traveler Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            Applicant Details
                        </h3>
                        <p class="text-slate-500 text-sm mb-4">Please provide passport details for each applicant as they appear on their passport.</p>

                        <template x-for="(traveler, index) in formData.travelers" :key="index">
                            <div class="border border-slate-200 rounded-xl p-4 mb-4 bg-slate-50">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-semibold text-slate-700" x-text="`Applicant ${index + 1} (${traveler.type.charAt(0).toUpperCase() + traveler.type.slice(1)})`"></h4>
                                    <span x-show="index === 0" class="text-xs bg-purple-500 text-white px-2 py-1 rounded">Lead Applicant</span>
                                </div>

                                <input type="hidden" :name="`travelers[${index}][type]`" x-model="traveler.type">
                                <input type="hidden" :name="`travelers[${index}][is_lead]`" :value="index === 0 ? 1 : 0">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Title</label>
                                        <select :name="`travelers[${index}][title]`" x-model="traveler.title"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                            <option value="">Select</option>
                                            <option value="Mr">Mr</option>
                                            <option value="Mrs">Mrs</option>
                                            <option value="Ms">Ms</option>
                                            <option value="Miss">Miss</option>
                                            <option value="Dr">Dr</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">First Name *</label>
                                        <input type="text" :name="`travelers[${index}][first_name]`"
                                            x-model="traveler.first_name" required
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Last Name *</label>
                                        <input type="text" :name="`travelers[${index}][last_name]`"
                                            x-model="traveler.last_name" required
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Date of Birth *</label>
                                        <input type="date" :name="`travelers[${index}][date_of_birth]`"
                                            x-model="traveler.date_of_birth" required :max="today"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Gender *</label>
                                        <select :name="`travelers[${index}][gender]`" x-model="traveler.gender" required
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                            <option value="">Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Nationality *</label>
                                        <input type="text" :name="`travelers[${index}][nationality]`"
                                            x-model="traveler.nationality" required value="Bangladeshi"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Email</label>
                                        <input type="email" :name="`travelers[${index}][email]`" x-model="traveler.email"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Phone</label>
                                        <input type="tel" :name="`travelers[${index}][phone]`" x-model="traveler.phone"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                    </div>
                                </div>

                                <!-- Passport Details -->
                                <div class="pt-4 border-t border-slate-200">
                                    <h5 class="font-medium text-slate-700 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"></path></svg>
                                        Passport Information
                                    </h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm text-slate-600 mb-1 block">Passport Number *</label>
                                            <input type="text" :name="`travelers[${index}][passport_number]`"
                                                x-model="traveler.passport_number" required
                                                class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white font-mono uppercase">
                                        </div>
                                        <div>
                                            <label class="text-sm text-slate-600 mb-1 block">Place of Issue</label>
                                            <input type="text" :name="`travelers[${index}][passport_issue_place]`"
                                                x-model="traveler.passport_issue_place"
                                                class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="text-sm text-slate-600 mb-1 block">Issue Date *</label>
                                            <input type="date" :name="`travelers[${index}][passport_issue_date]`"
                                                x-model="traveler.passport_issue_date" required :max="today"
                                                class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                        </div>
                                        <div>
                                            <label class="text-sm text-slate-600 mb-1 block">Expiry Date *</label>
                                            <input type="date" :name="`travelers[${index}][passport_expiry_date]`"
                                                x-model="traveler.passport_expiry_date" required :min="minPassportExpiry"
                                                class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Special Requests -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                            Special Requests
                        </h3>
                        <textarea name="special_requests" x-model="formData.special_requests" rows="3"
                            placeholder="Any special requirements or notes for your visa application..."
                            class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"></textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Payment Option
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="border-2 border-slate-200 rounded-xl p-4 cursor-pointer hover:border-purple-300 transition-colors"
                                :class="formData.payment_method === 'instant' ? 'border-purple-500 bg-purple-50' : ''">
                                <input type="radio" name="payment_method" value="instant" x-model="formData.payment_method" class="sr-only">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">Pay Now</p>
                                        <p class="text-xs text-slate-500">Instant processing starts</p>
                                    </div>
                                </div>
                            </label>
                            <label class="border-2 border-slate-200 rounded-xl p-4 cursor-pointer hover:border-purple-300 transition-colors"
                                :class="formData.payment_method === 'later' ? 'border-purple-500 bg-purple-50' : ''">
                                <input type="radio" name="payment_method" value="later" x-model="formData.payment_method" class="sr-only">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">Pay Later</p>
                                        <p class="text-xs text-slate-500">Pay after document review</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Terms & Submit -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <label class="flex items-start gap-3 mb-6 cursor-pointer">
                            <input type="checkbox" x-model="formData.terms_accepted" required
                                class="mt-1 text-purple-500 focus:ring-purple-500 rounded">
                            <span class="text-sm text-slate-600">
                                I agree to the <a href="#" class="text-purple-600 underline">Terms & Conditions</a> and 
                                <a href="#" class="text-purple-600 underline">Privacy Policy</a>. I confirm that all information 
                                provided is accurate and matches the passport details.
                            </span>
                        </label>

                        <button type="submit" :disabled="submitting || !formData.terms_accepted"
                            class="w-full bg-linear-to-r from-purple-600 to-fuchsia-600 hover:from-purple-700 hover:to-fuchsia-700 text-white py-4 px-6 rounded-xl font-bold shadow-lg shadow-purple-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span x-text="submitting ? 'Processing...' : (formData.payment_method === 'instant' ? 'Proceed to Payment' : 'Submit Application')"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Price Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="font-bold text-slate-800 mb-4">Price Summary</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Base Price × <span x-text="totalTravelers"></span></span>
                            <span>৳<span x-text="formatNumber(baseTotal)"></span></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Embassy Fee × <span x-text="totalTravelers"></span></span>
                            <span>৳<span x-text="formatNumber(embassyTotal)"></span></span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Service Fee</span>
                            <span>৳<span x-text="formatNumber(serviceFee)"></span></span>
                        </div>
                        @if($visa->vfs_fee)
                        <div class="flex justify-between text-slate-600">
                            <span>VFS Fee</span>
                            <span>৳<span x-text="formatNumber(vfsFee)"></span></span>
                        </div>
                        @endif
                        <div x-show="expressTotal > 0" class="flex justify-between text-amber-600">
                            <span>Express Processing</span>
                            <span>৳<span x-text="formatNumber(expressTotal)"></span></span>
                        </div>

                        <div class="border-t border-slate-200 pt-3 mt-3">
                            <div class="flex justify-between font-bold text-lg text-slate-800">
                                <span>Total</span>
                                <span class="text-purple-600">৳<span x-text="formatNumber(grandTotal)"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown by traveler type -->
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                        <div x-show="formData.adult_count > 0" class="flex justify-between">
                            <span><span x-text="formData.adult_count"></span> Adult(s)</span>
                            <span>৳{{ number_format($visa->base_price + ($visa->embassy_fee ?? 0)) }}/person</span>
                        </div>
                        <div x-show="formData.child_count > 0" class="flex justify-between">
                            <span><span x-text="formData.child_count"></span> Child(ren)</span>
                            <span>৳{{ number_format($visa->base_price + ($visa->embassy_fee ?? 0)) }}/person</span>
                        </div>
                        <div x-show="formData.infant_count > 0" class="flex justify-between">
                            <span><span x-text="formData.infant_count"></span> Infant(s)</span>
                            <span>৳{{ number_format($visa->base_price + ($visa->embassy_fee ?? 0)) }}/person</span>
                        </div>
                    </div>

                    <!-- Processing Time Info -->
                    <div class="mt-4 p-3 bg-slate-50 rounded-lg text-sm">
                        <p class="text-slate-600">
                            <strong>Estimated Processing:</strong>
                            <span x-text="formData.processing_time_id ? 'Express' : 'Standard'"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function visaBookingForm() {
            return {
                submitting: false,
                basePrice: {{ $visa->base_price }},
                embassyFee: {{ $visa->embassy_fee ?? 0 }},
                serviceFee: {{ $visa->service_fee ?? 0 }},
                vfsFee: {{ $visa->vfs_fee ?? 0 }},
                expressFeePer: 0,

                formData: {
                    processing_time_id: null,
                    intended_travel_date: '',
                    intended_return_date: '',
                    travel_purpose: 'Tourism',
                    adult_count: 1,
                    child_count: 0,
                    infant_count: 0,
                    contact_name: '{{ $user->name ?? '' }}',
                    contact_email: '{{ $user->email ?? '' }}',
                    contact_phone: '{{ $customer->phone ?? '' }}',
                    whatsapp: '',
                    contact_address: '{{ $customer->address ?? '' }}',
                    city: '{{ $customer->city ?? '' }}',
                    postal_code: '',
                    emergency_contact_name: '',
                    emergency_contact_phone: '',
                    emergency_contact_relationship: '',
                    travelers: [],
                    special_requests: '',
                    payment_method: 'instant',
                    terms_accepted: false
                },

                get today() {
                    return new Date().toISOString().split('T')[0];
                },

                get minDate() {
                    const date = new Date();
                    date.setDate(date.getDate() + 7);
                    return date.toISOString().split('T')[0];
                },

                get minPassportExpiry() {
                    const date = new Date();
                    date.setMonth(date.getMonth() + 6);
                    return date.toISOString().split('T')[0];
                },

                get totalTravelers() {
                    return this.formData.adult_count + this.formData.child_count + this.formData.infant_count;
                },

                get baseTotal() {
                    return this.basePrice * this.totalTravelers;
                },

                get embassyTotal() {
                    return this.embassyFee * this.totalTravelers;
                },

                get expressTotal() {
                    return this.expressFeePer * this.totalTravelers;
                },

                get grandTotal() {
                    return this.baseTotal + this.embassyTotal + this.serviceFee + this.vfsFee + this.expressTotal;
                },

                formatNumber(num) {
                    return num.toLocaleString();
                },

                createTraveler(type, isLead = false) {
                    return {
                        type: type,
                        is_lead: isLead,
                        title: '',
                        first_name: isLead ? '{{ $user->name ?? '' }}'.split(' ')[0] : '',
                        last_name: isLead ? ('{{ $user->name ?? '' }}'.split(' ').slice(1).join(' ') || '') : '',
                        email: isLead ? '{{ $user->email ?? '' }}' : '',
                        phone: isLead ? '{{ $customer->phone ?? '' }}' : '',
                        date_of_birth: '',
                        gender: '',
                        nationality: 'Bangladeshi',
                        passport_number: '',
                        passport_issue_date: '',
                        passport_expiry_date: '',
                        passport_issue_place: ''
                    };
                },

                updateCount(type, delta) {
                    const key = type + '_count';
                    const newValue = this.formData[key] + delta;

                    if (type === 'adult' && newValue < 1) return;
                    if (newValue < 0) return;
                    if (newValue > 20) return;

                    this.formData[key] = newValue;
                    this.updateTravelers();
                },

                updateTravelers() {
                    const travelers = [];
                    let isFirst = true;

                    for (let i = 0; i < this.formData.adult_count; i++) {
                        const existing = this.formData.travelers.find((t, idx) => t.type === 'adult' && idx === travelers.length);
                        travelers.push(existing || this.createTraveler('adult', isFirst));
                        isFirst = false;
                    }

                    for (let i = 0; i < this.formData.child_count; i++) {
                        const existing = this.formData.travelers.find((t, idx) => t.type === 'child' && idx === travelers.length);
                        travelers.push(existing || this.createTraveler('child'));
                    }

                    for (let i = 0; i < this.formData.infant_count; i++) {
                        const existing = this.formData.travelers.find((t, idx) => t.type === 'infant' && idx === travelers.length);
                        travelers.push(existing || this.createTraveler('infant'));
                    }

                    this.formData.travelers = travelers;
                },

                submitForm(e) {
                    if (!this.formData.terms_accepted) {
                        e.preventDefault();
                        alert('Please accept the terms and conditions');
                        return false;
                    }
                    this.submitting = true;
                    return true;
                },

                init() {
                    this.updateTravelers();
                }
            };
        }
    </script>
@endsection
