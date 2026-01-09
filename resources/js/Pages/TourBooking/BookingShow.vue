<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="'Booking #' + booking.booking_number" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="booking.status === 'pending'"
                    class="btn btn-success"
                    @click="updateStatus('confirmed')"
                >
                    <i class="ri-check-line mr-1"></i> Confirm
                </AppButton>
                <AppButton
                    v-if="['pending', 'confirmed'].includes(booking.status)"
                    class="btn btn-destructive"
                    @click="updateStatus('cancelled')"
                >
                    <i class="ri-close-line mr-1"></i> Cancel
                </AppButton>
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tourBooking.edit', booking.id))"
                >
                    <i class="ri-edit-line mr-1"></i> Edit
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tour Info -->
            <AppCard>
                <template #title>Tour Details</template>
                <template #content>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-slate-500">Tour</label>
                            <p class="font-medium">{{ booking.tour?.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Departure Date</label>
                            <p class="font-medium">{{ formatDate(booking.departure_date?.departure_date) }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Duration</label>
                            <p class="font-medium">{{ booking.tour?.duration }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Travelers</label>
                            <p class="font-medium">
                                {{ booking.adults_count }} Adults
                                <span v-if="booking.children_count">, {{ booking.children_count }} Children</span>
                                <span v-if="booking.infants_count">, {{ booking.infants_count }} Infants</span>
                            </p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Contact Info -->
            <AppCard>
                <template #title>Contact Information</template>
                <template #content>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-slate-500">Name</label>
                            <p class="font-medium">{{ booking.contact_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Email</label>
                            <p class="font-medium">{{ booking.contact_email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Phone</label>
                            <p class="font-medium">{{ booking.contact_phone || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Address</label>
                            <p class="font-medium">{{ booking.contact_address || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Emergency Contact</label>
                            <p class="font-medium">{{ booking.emergency_contact || '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Emergency Phone</label>
                            <p class="font-medium">{{ booking.emergency_phone || '-' }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Travelers -->
            <AppCard v-if="booking.travelers?.length">
                <template #title>Travelers ({{ booking.travelers.length }})</template>
                <template #content>
                    <div class="space-y-4">
                        <div
                            v-for="(traveler, index) in booking.travelers"
                            :key="traveler.id"
                            class="p-4 bg-slate-50 rounded-lg"
                        >
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-medium">
                                    {{ traveler.title }} {{ traveler.first_name }} {{ traveler.last_name }}
                                    <span v-if="traveler.is_lead" class="text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded ml-2">Lead</span>
                                </h4>
                                <span :class="traveler.type === 'adult' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700'" class="text-xs px-2 py-0.5 rounded capitalize">
                                    {{ traveler.type }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                                <div v-if="traveler.date_of_birth">
                                    <span class="text-slate-500">DOB:</span> {{ formatDate(traveler.date_of_birth) }}
                                </div>
                                <div v-if="traveler.gender">
                                    <span class="text-slate-500">Gender:</span> {{ traveler.gender }}
                                </div>
                                <div v-if="traveler.passport_number">
                                    <span class="text-slate-500">Passport:</span> {{ traveler.passport_number }}
                                </div>
                                <div v-if="traveler.nid_number">
                                    <span class="text-slate-500">NID:</span> {{ traveler.nid_number }}
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Special Requests -->
            <AppCard v-if="booking.special_requests || booking.dietary_requirements || booking.medical_conditions">
                <template #title>Special Requests</template>
                <template #content>
                    <div class="space-y-3">
                        <div v-if="booking.special_requests">
                            <label class="text-sm text-slate-500">Special Requests</label>
                            <p>{{ booking.special_requests }}</p>
                        </div>
                        <div v-if="booking.dietary_requirements">
                            <label class="text-sm text-slate-500">Dietary Requirements</label>
                            <p>{{ booking.dietary_requirements }}</p>
                        </div>
                        <div v-if="booking.medical_conditions">
                            <label class="text-sm text-slate-500">Medical Conditions</label>
                            <p>{{ booking.medical_conditions }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <AppCard>
                <template #title>Booking Status</template>
                <template #content>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Status</span>
                            <span :class="getStatusClass(booking.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatStatus(booking.status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Payment</span>
                            <span :class="getPaymentStatusClass(booking.payment_status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatStatus(booking.payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Booked On</span>
                            <span>{{ formatDateTime(booking.created_at) }}</span>
                        </div>
                        <div v-if="booking.confirmed_at" class="flex justify-between items-center">
                            <span class="text-slate-500">Confirmed</span>
                            <span>{{ formatDateTime(booking.confirmed_at) }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Price Breakdown -->
            <AppCard>
                <template #title>Price Breakdown</template>
                <template #content>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>৳{{ formatNumber(booking.subtotal) }}</span>
                        </div>
                        <div v-if="booking.extras_total > 0" class="flex justify-between">
                            <span>Extras</span>
                            <span>৳{{ formatNumber(booking.extras_total) }}</span>
                        </div>
                        <div v-if="booking.discount_amount > 0" class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>-৳{{ formatNumber(booking.discount_amount) }}</span>
                        </div>
                        <div v-if="booking.tax_amount > 0" class="flex justify-between">
                            <span>Tax</span>
                            <span>৳{{ formatNumber(booking.tax_amount) }}</span>
                        </div>
                        <hr />
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-primary-600">৳{{ formatNumber(booking.total_amount) }}</span>
                        </div>
                        <hr />
                        <div class="flex justify-between text-green-600">
                            <span>Paid</span>
                            <span>৳{{ formatNumber(booking.amount_paid) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600 font-medium">
                            <span>Due</span>
                            <span>৳{{ formatNumber(booking.amount_due) }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Internal Notes -->
            <AppCard v-if="booking.internal_notes">
                <template #title>Internal Notes</template>
                <template #content>
                    <p class="text-sm text-slate-600">{{ booking.internal_notes }}</p>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Booking Details')

const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', href: route('tourBooking.index') },
    { label: props.booking.booking_number, last: true }
]

const formatNumber = (num) => new Intl.NumberFormat('en-BD').format(num || 0)
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'
const formatDateTime = (date) => date ? new Date(date).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'
const formatStatus = (status) => status?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) || '-'

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 text-amber-700',
        confirmed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
        completed: 'bg-blue-100 text-blue-700',
    }
    return classes[status] || 'bg-slate-100 text-slate-700'
}

const getPaymentStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 text-amber-700',
        partial: 'bg-blue-100 text-blue-700',
        paid: 'bg-green-100 text-green-700',
        refunded: 'bg-red-100 text-red-700',
    }
    return classes[status] || 'bg-slate-100 text-slate-700'
}

const updateStatus = (status) => {
    if (confirm(`Are you sure you want to ${status} this booking?`)) {
        router.patch(route('tourBooking.updateStatus', [props.booking.id, status]))
    }
}
</script>
