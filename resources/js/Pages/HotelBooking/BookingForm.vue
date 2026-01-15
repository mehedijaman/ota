<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="isEditing ? 'Edit Booking' : 'Create Booking'" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary group" @click="$inertia.visit(route('hotelBooking.index'))">
                <i class="ri-arrow-left-line mr-1.5 transition-transform group-hover:-translate-x-0.5"></i> Back to List
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submit">
        <!-- Tab Navigation -->
        <div class="mb-6 overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
            <div class="overflow-x-auto p-4">
                <nav class="flex min-w-max gap-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all',
                            activeTab === tab.id
                                ? 'bg-gradient-to-r from-blue-600 to-teal-600 text-white shadow-lg'
                                : 'bg-neutral-3 text-neutral-11 hover:bg-neutral-4 hover:text-neutral-12'
                        ]"
                    >
                        <i :class="[tab.icon, activeTab === tab.id ? 'text-white' : 'text-neutral-9']"></i>
                        <span class="hidden sm:inline">{{ tab.label }}</span>
                    </button>
                </nav>
            </div>
            
            <!-- Quick Summary Bar -->
            <div class="flex flex-wrap items-center gap-4 border-t border-neutral-6 bg-neutral-2 px-4 py-3 text-sm">
                <div v-if="selectedHotel" class="flex items-center gap-2">
                    <i class="ri-hotel-line text-blue-500"></i>
                    <span class="font-medium text-neutral-12">{{ selectedHotel.name }}</span>
                </div>
                <div v-if="form.check_in_date" class="flex items-center gap-2">
                    <i class="ri-calendar-line text-amber-500"></i>
                    <span class="text-neutral-11">{{ formatDisplayDate(form.check_in_date) }} - {{ formatDisplayDate(form.check_out_date) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="ri-door-line text-purple-500"></i>
                    <span class="text-neutral-11">{{ form.rooms.length }} Room(s)</span>
                </div>
                <div class="ml-auto flex items-center gap-2 rounded-lg bg-emerald-100 px-3 py-1">
                    <i class="ri-money-dollar-circle-line text-emerald-600"></i>
                    <span class="font-bold text-emerald-700">৳{{ formatNumber(calculatedTotal) }}</span>
                </div>
            </div>
        </div>

        <!-- Tab: Hotel & Stay -->
        <div v-show="activeTab === 'hotel'" class="space-y-6">
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg">
                            <i class="ri-hotel-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">Hotel & Stay Details</h3>
                            <p class="text-sm text-neutral-9">Select hotel, dates, and number of rooms</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <AppLabel required>Select Hotel</AppLabel>
                            <AppCombobox
                                class="w-full"
                                v-model="form.hotel_id"
                                :options="hotelOptions"
                                :error="form.errors.hotel_id"
                                emit-value
                                combo-label="Choose a hotel..."
                                @update:modelValue="onHotelChange"
                            />
                        </div>

                        <div>
                            <AppLabel>Booking Source</AppLabel>
                            <AppCombobox
                                v-model="form.booking_source"
                                :options="bookingSourceOptions"
                                combo-label="Select source"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <AppLabel required>Check-in Date</AppLabel>
                            <AppInputText v-model="form.check_in_date" type="date" :error="form.errors.check_in_date" />
                        </div>

                        <div>
                            <AppLabel required>Check-out Date</AppLabel>
                            <AppInputText v-model="form.check_out_date" type="date" :error="form.errors.check_out_date" />
                        </div>

                        <div>
                            <AppLabel>Total Adults</AppLabel>
                            <AppInputText v-model.number="form.total_adults" type="number" min="1" />
                        </div>

                        <div>
                            <AppLabel>Total Children</AppLabel>
                            <AppInputText v-model.number="form.total_children" type="number" min="0" />
                        </div>
                    </div>

                    <div v-if="nights > 0" class="mt-4 flex items-center gap-2 text-sm text-neutral-11">
                        <i class="ri-moon-line text-blue-500"></i>
                        <span>{{ nights }} night(s) stay</span>
                    </div>
                </template>
            </AppCard>

            <!-- Room Selection -->
            <AppCard>
                <template #title>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg">
                                <i class="ri-door-line text-lg text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-12">Room Selection</h3>
                                <p class="text-sm text-neutral-9">Add rooms to this booking</p>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-primary btn-sm" @click="addRoom">
                            <i class="ri-add-line mr-1"></i> Add Room
                        </AppButton>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.rooms.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-door-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No rooms added</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add at least one room to this booking</p>
                        <AppButton type="button" class="btn btn-primary" @click="addRoom">
                            <i class="ri-add-line mr-1"></i> Add First Room
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(room, index) in form.rooms"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-purple-600/10 to-indigo-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-sm font-bold text-purple-700">
                                        {{ index + 1 }}
                                    </span>
                                    <span class="text-neutral-12 font-medium">Room {{ index + 1 }}</span>
                                </div>
                                <button type="button" @click="removeRoom(index)" class="text-neutral-9 hover:text-red-600 transition-colors">
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <AppLabel>Room Type</AppLabel>
                                        <AppCombobox
                                            v-model="room.room_id"
                                            :options="availableRoomOptions"
                                            combo-label="Select room"
                                            class="w-full"
                                            emit-value
                                        />
                                    </div>
                                    <div>
                                        <AppLabel>Rate Plan</AppLabel>
                                        <AppCombobox
                                            v-model="room.rate_plan_id"
                                            :options="ratePlanOptions"
                                            combo-label="Select plan"
                                            class="w-full"
                                            emit-value
                                        />
                                    </div>
                                    <div>
                                        <AppLabel>Rate per Night</AppLabel>
                                        <AppInputText v-model.number="room.rate_per_night" type="number" min="0" step="0.01" />
                                    </div>
                                    <div>
                                        <AppLabel>Adults</AppLabel>
                                        <AppInputText v-model.number="room.adults" type="number" min="1" />
                                    </div>
                                    <div>
                                        <AppLabel>Children</AppLabel>
                                        <AppInputText v-model.number="room.children" type="number" min="0" />
                                    </div>
                                    <div class="flex items-end">
                                        <div class="text-lg font-bold text-emerald-600">
                                            ৳{{ formatNumber(room.rate_per_night * nights) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Tab: Contact -->
        <div v-show="activeTab === 'contact'" class="space-y-6">
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                            <i class="ri-contacts-book-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">Contact Information</h3>
                            <p class="text-sm text-neutral-9">Primary contact details for this booking</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <AppLabel>Customer</AppLabel>
                            <AppCombobox
                                v-model="form.customer_id"
                                :options="customerOptions"
                                combo-label="Select customer (optional)"
                                class="w-full"
                                emit-value
                                @update:modelValue="onCustomerChange"
                            />
                        </div>

                        <div></div>

                        <div>
                            <AppLabel required>Contact Name</AppLabel>
                            <AppInputText v-model="form.contact_name" :error="form.errors.contact_name" placeholder="Full name" />
                        </div>

                        <div>
                            <AppLabel required>Contact Email</AppLabel>
                            <AppInputText v-model="form.contact_email" type="email" :error="form.errors.contact_email" placeholder="email@example.com" />
                        </div>

                        <div>
                            <AppLabel>Contact Phone</AppLabel>
                            <AppInputText v-model="form.contact_phone" :error="form.errors.contact_phone" placeholder="+880 1XXX-XXXXXX" />
                        </div>

                        <div>
                            <AppLabel>Country</AppLabel>
                            <AppCombobox v-model="form.contact_country" :options="countryOptions" combo-label="Select country" class="w-full" emit-value />
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Special Requests -->
            <AppCard>
                <template #title>Special Requests</template>
                <template #content>
                    <AppTextArea 
                    id="special_requests"
                    v-model="form.special_requests" 
                    rows="5" 
                    placeholder="Any special requests for the hotel..." 
                    />
                </template>
            </AppCard>
        </div>

        <!-- Tab: Guests -->
        <div v-show="activeTab === 'guests'" class="space-y-6">
            <AppCard>
                <template #title>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg">
                                <i class="ri-group-line text-lg text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-12">Guest Details</h3>
                                <p class="text-sm text-neutral-9">{{ form.guests.length }} guest(s)</p>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-primary btn-sm" @click="addGuest">
                            <i class="ri-add-line mr-1"></i> Add Guest
                        </AppButton>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div
                            v-for="(guest, index) in form.guests"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 overflow-hidden rounded-xl border"
                            :class="{ 'border-amber-400 bg-amber-50': guest.is_primary }"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-amber-600/10 to-orange-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">
                                        {{ index + 1 }}
                                    </span>
                                    <span class="text-neutral-12 font-medium">Guest {{ index + 1 }}</span>
                                    <span v-if="guest.is_primary" class="rounded-full bg-amber-500 px-2 py-0.5 text-xs font-medium text-white">Primary</span>
                                </div>
                                <button type="button" @click="removeGuest(index)" class="text-neutral-9 hover:text-red-600 transition-colors">
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <AppLabel>First Name</AppLabel>
                                        <AppInputText v-model="guest.first_name" placeholder="First name" />
                                    </div>
                                    <div>
                                        <AppLabel>Last Name</AppLabel>
                                        <AppInputText v-model="guest.last_name" placeholder="Last name" />
                                    </div>
                                    <div>
                                        <AppLabel>Type</AppLabel>
                                        <AppCombobox v-model="guest.guest_type" :options="guestTypeOptions" combo-label="Select" class="w-full" emit-value />
                                    </div>
                                    <div>
                                        <AppLabel>ID Type</AppLabel>
                                        <AppCombobox v-model="guest.id_type" :options="idTypeOptions" combo-label="Select" class="w-full" emit-value />
                                    </div>
                                    <div>
                                        <AppLabel>ID Number</AppLabel>
                                        <AppInputText v-model="guest.id_number" placeholder="ID/Passport number" />
                                    </div>
                                    <div class="flex items-center gap-2 pt-6">
                                        <input type="checkbox" v-model="guest.is_primary" @change="updatePrimaryGuest(index)" class="h-4 w-4 rounded text-amber-500" />
                                        <span class="text-neutral-11 text-sm">Primary Guest</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Tab: Payment -->
        <div v-show="activeTab === 'payment'" class="space-y-6">
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                            <i class="ri-money-dollar-circle-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">Pricing & Payment</h3>
                            <p class="text-sm text-neutral-9">Booking costs and payment details</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <AppLabel>Room Total</AppLabel>
                                    <AppInputText v-model.number="form.room_total" type="number" step="0.01" class="text-lg font-semibold" />
                                </div>
                                <div>
                                    <AppLabel>Tax Amount</AppLabel>
                                    <AppInputText v-model.number="form.tax_amount" type="number" step="0.01" />
                                </div>
                                <div>
                                    <AppLabel>Discount</AppLabel>
                                    <AppInputText v-model.number="form.discount_amount" type="number" step="0.01" />
                                </div>
                                <div>
                                    <AppLabel>Amount Paid</AppLabel>
                                    <AppInputText v-model.number="form.amount_paid" type="number" step="0.01" />
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-neutral-2 rounded-xl p-6">
                            <h4 class="text-neutral-12 mb-4 font-semibold">Booking Summary</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-neutral-9">Room Charges</span>
                                    <span class="text-neutral-12 font-medium">৳{{ formatNumber(form.room_total) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-9">Taxes & Fees</span>
                                    <span class="text-neutral-12 font-medium">৳{{ formatNumber(form.tax_amount) }}</span>
                                </div>
                                <div v-if="form.discount_amount > 0" class="flex justify-between text-emerald-600">
                                    <span>Discount</span>
                                    <span>-৳{{ formatNumber(form.discount_amount) }}</span>
                                </div>
                                <div class="border-neutral-4 border-t pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-neutral-12">Total</span>
                                        <span class="text-emerald-600">৳{{ formatNumber(calculatedTotal) }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between text-blue-600">
                                    <span>Paid</span>
                                    <span>৳{{ formatNumber(form.amount_paid) }}</span>
                                </div>
                                <div class="flex justify-between text-red-600 font-semibold">
                                    <span>Due</span>
                                    <span>৳{{ formatNumber(calculatedTotal - form.amount_paid) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel>Payment Method</AppLabel>
                            <AppCombobox v-model="form.payment_method" :options="paymentMethodOptions" combo-label="Select" class="w-full" emit-value />
                        </div>
                        <div>
                            <AppLabel>Payment Status</AppLabel>
                            <AppCombobox v-model="form.payment_status" :options="paymentStatusOptions" combo-label="Select" class="w-full" emit-value />
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Internal Notes -->
            <AppCard>
                <template #title>Internal Notes</template>
                <template #content>
                    <AppTextArea v-model="form.internal_notes" rows="3" placeholder="Notes visible only to staff..." />
                </template>
            </AppCard>
        </div>

        <!-- Submit Button -->
        <div class="bg-neutral-1 border-neutral-6 sticky bottom-0 z-10 -mx-4 border-t px-4 py-4 shadow-lg sm:-mx-6 sm:px-6 mt-6">
            <div class="flex justify-end gap-3">
                <AppButton type="button" class="btn btn-secondary" @click="$inertia.visit(route('hotelBooking.index'))">Cancel</AppButton>
                <AppButton type="submit" class="btn btn-primary" :loading="form.processing">
                    <i class="ri-save-line mr-1"></i>
                    {{ isEditing ? 'Update Booking' : 'Create Booking' }}
                </AppButton>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
// import AppCombobox from '@/Components/Form/AppCombobox.vue'
// import AppLabel from '@/Components/Form/AppLabel.vue'
// import AppInputText from '@/Components/Form/AppInputText.vue'
// import AppTextArea from '@/Components/Form/AppTextArea.vue'

const props = defineProps({
    booking: { type: Object, default: null },
    hotels: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    countries: { type: Array, default: () => [] },
    rooms: { type: Array, default: () => [] },
    ratePlans: { type: Array, default: () => [] }
})

const isEditing = computed(() => !!props.booking?.id)
const { title } = useTitle(isEditing.value ? 'Edit Hotel Booking' : 'Create Hotel Booking')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotel Bookings', href: route('hotelBooking.index') },
    { label: isEditing.value ? 'Edit' : 'Create', last: true }
]

const tabs = [
    { id: 'hotel', label: 'Hotel & Stay', icon: 'ri-hotel-line' },
    { id: 'contact', label: 'Contact', icon: 'ri-contacts-book-line' },
    { id: 'guests', label: 'Guests', icon: 'ri-group-line' },
    { id: 'payment', label: 'Payment', icon: 'ri-money-dollar-circle-line' }
]

const activeTab = ref('hotel')

const form = useForm({
    hotel_id: props.booking?.hotel_id || null,
    customer_id: props.booking?.customer_id || null,
    check_in_date: props.booking?.check_in_date || '',
    check_out_date: props.booking?.check_out_date || '',
    total_adults: props.booking?.total_adults || 1,
    total_children: props.booking?.total_children || 0,
    contact_name: props.booking?.contact_name || '',
    contact_email: props.booking?.contact_email || '',
    contact_phone: props.booking?.contact_phone || '',
    contact_country: props.booking?.contact_country || null,
    special_requests: props.booking?.special_requests || '',
    internal_notes: props.booking?.internal_notes || '',
    booking_source: props.booking?.booking_source || 'direct',
    room_total: props.booking?.room_total || 0,
    tax_amount: props.booking?.tax_amount || 0,
    discount_amount: props.booking?.discount_amount || 0,
    amount_paid: props.booking?.amount_paid || 0,
    payment_method: props.booking?.payment_method || null,
    payment_status: props.booking?.payment_status || 'unpaid',
    rooms: props.booking?.rooms?.map(r => ({
        room_id: r.room_id,
        rate_plan_id: r.rate_plan_id,
        rate_per_night: r.rate_per_night,
        adults: r.adults,
        children: r.children
    })) || [],
    guests: props.booking?.guests?.map(g => ({
        first_name: g.first_name,
        last_name: g.last_name,
        guest_type: g.guest_type,
        id_type: g.id_type,
        id_number: g.id_number,
        is_primary: g.is_primary
    })) || []
})

const hotelOptions = computed(() => props.hotels.map(h => ({ value: h.id, label: h.name })))
const customerOptions = computed(() => props.customers.map(c => ({ value: c.id, label: c.name })))
const countryOptions = computed(() => props.countries.map(c => ({ value: c.id, label: c.name })))
const availableRoomOptions = computed(() => props.rooms.map(r => ({ value: r.id, label: r.name })))
const ratePlanOptions = computed(() => props.ratePlans.map(r => ({ value: r.id, label: r.name })))

const selectedHotel = computed(() => props.hotels.find(h => h.id === form.hotel_id))

const nights = computed(() => {
    if (!form.check_in_date || !form.check_out_date) return 0
    const checkIn = new Date(form.check_in_date)
    const checkOut = new Date(form.check_out_date)
    const diff = checkOut - checkIn
    return Math.ceil(diff / (1000 * 60 * 60 * 24))
})

const calculatedTotal = computed(() => {
    return (form.room_total || 0) + (form.tax_amount || 0) - (form.discount_amount || 0)
})

const bookingSourceOptions = [
    { value: 'direct', label: 'Direct' },
    { value: 'website', label: 'Website' },
    { value: 'phone', label: 'Phone' },
    { value: 'agent', label: 'Agent' },
    { value: 'ota', label: 'OTA' }
]

const paymentMethodOptions = [
    { value: 'cash', label: 'Cash' },
    { value: 'card', label: 'Card' },
    { value: 'bkash', label: 'bKash' },
    { value: 'nagad', label: 'Nagad' },
    { value: 'bank_transfer', label: 'Bank Transfer' }
]

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'partial', label: 'Partial' },
    { value: 'paid', label: 'Paid' }
]

const guestTypeOptions = [
    { value: 'adult', label: 'Adult' },
    { value: 'child', label: 'Child' }
]

const idTypeOptions = [
    { value: 'passport', label: 'Passport' },
    { value: 'nid', label: 'National ID' },
    { value: 'driving_license', label: 'Driving License' }
]

const formatNumber = (num) => num ? new Intl.NumberFormat('en-BD').format(num) : '0'
const formatDisplayDate = (date) => date ? new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''

const onHotelChange = () => {
    form.rooms = []
}

const onCustomerChange = () => {
    const customer = props.customers.find(c => c.id === form.customer_id)
    if (customer) {
        form.contact_name = customer.name
        form.contact_email = customer.email
        form.contact_phone = customer.phone
    }
}

const addRoom = () => {
    form.rooms.push({
        room_id: null,
        rate_plan_id: null,
        rate_per_night: 0,
        adults: 1,
        children: 0
    })
}

const removeRoom = (index) => {
    form.rooms.splice(index, 1)
}

const addGuest = () => {
    form.guests.push({
        first_name: '',
        last_name: '',
        guest_type: 'adult',
        id_type: null,
        id_number: '',
        is_primary: form.guests.length === 0
    })
}

const removeGuest = (index) => {
    form.guests.splice(index, 1)
}

const updatePrimaryGuest = (index) => {
    form.guests.forEach((g, i) => {
        g.is_primary = i === index
    })
}

// Auto-calculate room total
watch(() => form.rooms, () => {
    let total = 0
    form.rooms.forEach(r => {
        total += (r.rate_per_night || 0) * nights.value
    })
    form.room_total = total
}, { deep: true })

const submit = () => {
    if (isEditing.value) {
        form.put(route('hotelBooking.update', props.booking.id))
    } else {
        form.post(route('hotelBooking.store'))
    }
}
</script>
