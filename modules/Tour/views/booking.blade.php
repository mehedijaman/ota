@extends('site-layout')

@section('meta-title', 'Book ' . $tour->name . ' | ' . config('app.name'))

@section('content')
    <!-- Booking Header -->
    <div class="bg-slate-900 pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-white/70 text-sm mb-4">
                <a href="{{ route('site.tour.index') }}" class="hover:text-white transition-colors">Tours</a>
                <i class="ri-arrow-right-s-line"></i>
                <a href="{{ route('site.tour.show', $tour->slug) }}"
                    class="hover:text-white transition-colors">{{ $tour->name }}</a>
                <i class="ri-arrow-right-s-line"></i>
                <span class="text-white">Book Now</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Book Your Trip</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
        x-data="bookingForm({{ json_encode($tour) }}, {{ json_encode($user) }}, {{ json_encode($customer) }})">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <!-- Tour Summary Card -->
                <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 mb-6">
                    <div class="flex gap-4">
                        <img src="{{ $tour->thumbnail_url ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=200' }}"
                            alt="{{ $tour->name }}" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg sm:text-xl text-slate-800">{{ $tour->name }}</h3>
                            <p class="text-slate-500 text-sm mt-1">
                                @if($tour->duration_days)
                                    {{ $tour->duration_days }} {{ $tour->duration_days > 1 ? 'Days' : 'Day' }}
                                    @if($tour->duration_nights)
                                        {{ $tour->duration_nights }} {{ $tour->duration_nights > 1 ? 'Nights' : 'Night' }}
                                    @endif
                                @endif
                                @if(($tour->duration_days || $tour->duration_nights) && $tour->destination)
                                    •
                                @endif
                                @if($tour->destination)
                                    {{ $tour->destination->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit="submitBooking" class="space-y-6">
                    @csrf

                    <!-- Pricing Plan Selection -->
                    @if($tour->pricings && count($tour->pricings) > 0)
                        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="ri-price-tag-3-line text-blue-500"></i>
                                Select Pricing Plan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($tour->pricings as $pricing)
                                    <div @click="formData.pricing_id = {{ $pricing->id }}"
                                        :class="formData.pricing_id === {{ $pricing->id }} ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-blue-300'"
                                        class="border-2 rounded-xl p-4 cursor-pointer transition-all">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-bold text-slate-800">{{ $pricing->name }}</h4>
                                            <input type="radio" name="pricing_id" value="{{ $pricing->id }}"
                                                x-model="formData.pricing_id" class="text-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div class="space-y-1 text-sm">
                                            <p class="text-blue-600 font-bold text-lg">
                                                ৳{{ number_format($pricing->adult_price, 2) }} <span
                                                    class="text-xs text-slate-500">per adult</span></p>
                                            @if($pricing->child_price)
                                                <p class="text-slate-600">Child: ৳{{ number_format($pricing->child_price, 2) }}</p>
                                            @endif
                                            @if($pricing->infant_price)
                                                <p class="text-slate-600">Infant: ৳{{ number_format($pricing->infant_price, 2) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Travel Date -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ri-calendar-line text-blue-500"></i>
                            Select Travel Date
                        </h3>
                        <input type="date" name="travel_date" x-model="formData.travel_date" :min="minDate" :max="maxDate"
                            required
                            class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <!-- Number of Travelers -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ri-group-line text-blue-500"></i>
                            Number of Travelers
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Adults (12+) *</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" @click="updateCount('adults', -1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" name="adults_count" x-model.number="formData.adults_count" min="1"
                                        readonly class="w-full text-center border-0 focus:ring-0 bg-transparent">
                                    <button type="button" @click="updateCount('adults', 1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Children (2-11)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" @click="updateCount('children', -1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" name="children_count" x-model.number="formData.children_count"
                                        min="0" readonly class="w-full text-center border-0 focus:ring-0 bg-transparent">
                                    <button type="button" @click="updateCount('children', 1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Infants (0-2)</label>
                                <div class="flex items-center border border-slate-200 rounded-lg">
                                    <button type="button" @click="updateCount('infants', -1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" name="infants_count" x-model.number="formData.infants_count"
                                        min="0" readonly class="w-full text-center border-0 focus:ring-0 bg-transparent">
                                    <button type="button" @click="updateCount('infants', 1)"
                                        class="p-3 hover:bg-slate-50 transition-colors">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ri-contacts-line text-blue-500"></i>
                            Contact Information
                        </h3>

                        <div class="flex gap-4 mb-4">
                            <button type="button" @click="setContactType('myself')"
                                :class="formData.contact_type === 'myself' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="flex-1 py-2 px-4 rounded-lg font-medium transition-colors">
                                Myself
                            </button>
                            <button type="button" @click="setContactType('other')"
                                :class="formData.contact_type === 'other' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="flex-1 py-2 px-4 rounded-lg font-medium transition-colors">
                                Someone Else
                            </button>
                        </div>

                        <input type="hidden" name="contact_type" x-model="formData.contact_type">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Full Name *</label>
                                <input type="text" name="contact_name" x-model="formData.contact_name" required
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Email *</label>
                                <input type="email" name="contact_email" x-model="formData.contact_email" required
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">Phone *</label>
                                <input type="tel" name="contact_phone" x-model="formData.contact_phone" required
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 mb-2 block">WhatsApp</label>
                                <input type="tel" name="whatsapp" x-model="formData.whatsapp"
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm text-slate-600 mb-2 block">Address</label>
                                <textarea name="contact_address" x-model="formData.contact_address" rows="2"
                                    class="w-full border border-slate-200 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Traveler Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ri-user-line text-blue-500"></i>
                            Traveler Details
                        </h3>

                        <template x-for="(traveler, index) in formData.travelers" :key="index">
                            <div class="border border-slate-200 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-slate-700 mb-3"
                                    x-text="`Traveler ${index + 1} (${traveler.type.charAt(0).toUpperCase() + traveler.type.slice(1)})`">
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">First Name *</label>
                                        <input type="text" :name="`travelers[${index}][first_name]`"
                                            x-model="traveler.first_name" required
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                        <input type="hidden" :name="`travelers[${index}][type]`" x-model="traveler.type">
                                        <input type="hidden" :name="`travelers[${index}][is_lead]`"
                                            :value="traveler.is_lead ? 1 : 0">
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Last Name *</label>
                                        <input type="text" :name="`travelers[${index}][last_name]`"
                                            x-model="traveler.last_name" required
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Email</label>
                                        <input type="email" :name="`travelers[${index}][email]`" x-model="traveler.email"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-sm text-slate-600 mb-1 block">Phone</label>
                                        <input type="tel" :name="`travelers[${index}][phone]`" x-model="traveler.phone"
                                            class="w-full border border-slate-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Optional Extras -->
                    @if($tour->extras && count($tour->extras) > 0)
                        <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i class="ri-add-circle-line text-blue-500"></i>
                                Optional Extras
                            </h3>
                            <div class="space-y-3">
                                @foreach($tour->extras as $extra)
                                    <div class="border border-slate-200 rounded-lg p-4">
                                        <div class="flex items-start gap-3">
                                            <input type="checkbox" :checked="isExtraSelected({{ $extra->id }})"
                                                @change="toggleExtra({{ json_encode($extra) }})"
                                                class="mt-1 text-blue-500 focus:ring-blue-500 rounded">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-slate-800">{{ $extra->name }}</h4>
                                                @if($extra->description)
                                                    <p class="text-sm text-slate-600 mt-1">{{ $extra->description }}</p>
                                                @endif
                                                <p class="text-blue-600 font-bold mt-2">৳{{ number_format($extra->price, 2) }}</p>
                                            </div>
                                            <div x-show="isExtraSelected({{ $extra->id }})"
                                                class="flex items-center border border-slate-200 rounded-lg">
                                                <button type="button" @click="updateExtraQuantity({{ $extra->id }}, -1)"
                                                    class="p-2 hover:bg-slate-50">
                                                    <i class="ri-subtract-line text-sm"></i>
                                                </button>
                                                <span class="px-3 text-sm font-medium"
                                                    x-text="getExtraQuantity({{ $extra->id }})"></span>
                                                <button type="button" @click="updateExtraQuantity({{ $extra->id }}, 1)"
                                                    class="p-2 hover:bg-slate-50">
                                                    <i class="ri-add-line text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Hidden inputs for extras -->
                            <template x-for="(extra, index) in formData.extras" :key="extra.extra_id">
                                <div>
                                    <input type="hidden" :name="`extras[${index}][extra_id]`" :value="extra.extra_id">
                                    <input type="hidden" :name="`extras[${index}][quantity]`" :value="extra.quantity">
                                </div>
                            </template>
                        </div>
                    @endif

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="ri-bank-card-line text-blue-500"></i>
                            Payment Method
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label
                                class="border-2 border-slate-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 transition-colors"
                                :class="formData.payment_method === 'instant' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="payment_method" value="instant" x-model="formData.payment_method"
                                    class="sr-only">
                                <div class="flex items-center gap-3">
                                    <i class="ri-flashlight-line text-2xl text-blue-500"></i>
                                    <div>
                                        <p class="font-semibold text-slate-800">Pay Now</p>
                                        <p class="text-xs text-slate-500">Instant confirmation</p>
                                    </div>
                                </div>
                            </label>
                            <label
                                class="border-2 border-slate-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 transition-colors"
                                :class="formData.payment_method === 'later' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="payment_method" value="later" x-model="formData.payment_method"
                                    class="sr-only">
                                <div class="flex items-center gap-3">
                                    <i class="ri-time-line text-2xl text-green-500"></i>
                                    <div>
                                        <p class="font-semibold text-slate-800">Pay Later</p>
                                        <p class="text-xs text-slate-500">Pay after confirmation</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="submitting"
                        class="w-full bg-linear-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-4 px-6 rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="ri-check-line text-xl"></i>
                        <span
                            x-text="submitting ? 'Processing...' : (formData.payment_method === 'instant' ? 'Proceed to Payment' : 'Confirm Booking')"></span>
                    </button>
                </form>
            </div>

            <!-- Price Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="font-bold text-slate-800 mb-4">Price Summary</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Subtotal</span>
                            <span class="font-medium">৳ <span x-text="formatNumber(totals.subtotal)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Extras</span>
                            <span class="font-medium">৳ <span x-text="formatNumber(totals.extrasTotal)"></span></span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-blue-600">৳ <span x-text="formatNumber(totals.total)"></span></span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                        <p class="flex items-center gap-2">
                            <i class="ri-shield-check-line text-green-500"></i>
                            Your booking is protected
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="ri-lock-line text-blue-500"></i>
                            Secure payment
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="ri-customer-service-2-line text-orange-500"></i>
                            24/7 Customer support
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function bookingForm(tour, user, customer) {
                return {
                    tour: tour,
                    user: user,
                    customer: customer,
                    submitting: false,
                    formData: {
                        pricing_id: tour.pricings && tour.pricings.length > 0 ? tour.pricings[0].id : null,
                        travel_date: '',
                        adults_count: 1,
                        children_count: 0,
                        infants_count: 0,
                        contact_type: 'myself',
                        contact_name: '',
                        contact_email: '',
                        contact_phone: '',
                        contact_address: '',
                        whatsapp: '',
                        travelers: [],
                        extras: [],
                        payment_method: 'later'
                    },

                    init() {
                        // Auto-fill contact info if customer data available
                        if (this.customer && this.formData.contact_type === 'myself') {
                            this.setContactType('myself');
                        }
                        // Generate initial travelers
                        this.generateTravelers();
                    },

                    get minDate() {
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        return tomorrow.toISOString().split('T')[0];
                    },

                    get maxDate() {
                        return this.tour.valid_to || null;
                    },

                    get selectedPricing() {
                        if (!this.formData.pricing_id || !this.tour.pricings) return null;
                        return this.tour.pricings.find(p => p.id === this.formData.pricing_id);
                    },

                    get totals() {
                        if (!this.selectedPricing) {
                            return { subtotal: 0, extrasTotal: 0, total: 0 };
                        }

                        const subtotal = (this.formData.adults_count * (this.selectedPricing.adult_price || 0))
                            + (this.formData.children_count * (this.selectedPricing.child_price || 0))
                            + (this.formData.infants_count * (this.selectedPricing.infant_price || 0));

                        let extrasTotal = 0;
                        this.formData.extras.forEach(extra => {
                            const tourExtra = this.tour.extras?.find(e => e.id === extra.extra_id);
                            if (tourExtra) {
                                extrasTotal += (tourExtra.price || 0) * extra.quantity;
                            }
                        });

                        return {
                            subtotal,
                            extrasTotal,
                            total: subtotal + extrasTotal
                        };
                    },

                    formatNumber(num) {
                        if (num === null || num === undefined) return '0';
                        return new Intl.NumberFormat('en-US').format(num);
                    },

                    updateCount(type, delta) {
                        const key = `${type}_count`;
                        let newValue = this.formData[key] + delta;

                        if (type === 'adults' && newValue < 1) newValue = 1;
                        if (newValue < 0) newValue = 0;

                        this.formData[key] = newValue;
                        this.generateTravelers();
                    },

                    setContactType(type) {
                        this.formData.contact_type = type;

                        if (type === 'myself' && this.customer) {
                            this.formData.contact_name = this.customer.full_name || (this.customer.first_name + ' ' + this.customer.last_name) || '';
                            this.formData.contact_email = this.customer.email || '';
                            this.formData.contact_phone = this.customer.phone || '';
                            this.formData.contact_address = this.customer.address || '';
                            this.formData.whatsapp = this.customer.whatsapp || '';
                        } else if (type === 'myself' && this.user) {
                            this.formData.contact_name = this.user.name || '';
                            this.formData.contact_email = this.user.email || '';
                            this.formData.contact_phone = this.user.phone || '';
                        } else {
                            this.formData.contact_name = '';
                            this.formData.contact_email = '';
                            this.formData.contact_phone = '';
                            this.formData.contact_address = '';
                            this.formData.whatsapp = '';
                        }
                    },

                    generateTravelers() {
                        const travelers = [];

                        // Add adults
                        for (let i = 0; i < this.formData.adults_count; i++) {
                            travelers.push({
                                type: 'adult',
                                is_lead: i === 0,
                                first_name: '',
                                last_name: '',
                                email: '',
                                phone: ''
                            });
                        }

                        // Add children
                        for (let i = 0; i < this.formData.children_count; i++) {
                            travelers.push({
                                type: 'child',
                                is_lead: false,
                                first_name: '',
                                last_name: '',
                                email: '',
                                phone: ''
                            });
                        }

                        // Add infants
                        for (let i = 0; i < this.formData.infants_count; i++) {
                            travelers.push({
                                type: 'infant',
                                is_lead: false,
                                first_name: '',
                                last_name: '',
                                email: '',
                                phone: ''
                            });
                        }

                        this.formData.travelers = travelers;
                    },

                    toggleExtra(extra) {
                        const index = this.formData.extras.findIndex(e => e.extra_id === extra.id);
                        if (index > -1) {
                            this.formData.extras.splice(index, 1);
                        } else {
                            this.formData.extras.push({
                                extra_id: extra.id,
                                quantity: 1
                            });
                        }
                    },

                    isExtraSelected(extraId) {
                        return this.formData.extras.some(e => e.extra_id === extraId);
                    },

                    getExtraQuantity(extraId) {
                        const extra = this.formData.extras.find(e => e.extra_id === extraId);
                        return extra ? extra.quantity : 0;
                    },

                    updateExtraQuantity(extraId, delta) {
                        const extra = this.formData.extras.find(e => e.extra_id === extraId);
                        if (extra) {
                            extra.quantity = Math.max(1, extra.quantity + delta);
                        }
                    },

                    submitBooking(event) {
                        // Basic validation
                        if (!this.formData.pricing_id) {
                            event.preventDefault();
                            alert('Please select a pricing plan');
                            return false;
                        }

                        if (!this.formData.travel_date) {
                            event.preventDefault();
                            alert('Please select a travel date');
                            return false;
                        }

                        if (this.formData.travelers.length === 0) {
                            event.preventDefault();
                            alert('Please add traveler information');
                            return false;
                        }

                        this.submitting = true;
                        // Allow form to submit naturally
                        return true;
                    }
                }
            }
        </script>
    @endpush
@endsection