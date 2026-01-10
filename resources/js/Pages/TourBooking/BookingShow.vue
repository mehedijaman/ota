<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="'Booking #' + booking.booking_number" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap gap-2">
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
                <AppButton
                    class="btn btn-secondary"
                    @click="window.print()"
                >
                    <i class="ri-printer-line mr-1"></i> Print
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tour Info -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-map-pin-line text-primary-600"></i>
                        Tour Details
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-slate-500 dark:text-slate-400">Tour</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.tour?.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500 dark:text-slate-400">Travel Date</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ formatDate(booking.travel_date) }}</p>
                        </div>
                        <div v-if="booking.pricing">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Pricing Plan</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.pricing.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500 dark:text-slate-400">Travelers</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">
                                {{ booking.adult_count }} Adults
                                <span v-if="booking.child_count">, {{ booking.child_count }} Children</span>
                                <span v-if="booking.infant_count">, {{ booking.infant_count }} Infants</span>
                            </p>
                        </div>
                        <div v-if="booking.customer">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Customer</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.customer.name }}</p>
                        </div>
                        <div v-if="booking.agent">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Agent</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.agent.name }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Contact Info -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-contacts-line text-primary-600"></i>
                        Contact Information
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-slate-500 dark:text-slate-400">Name</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500 dark:text-slate-400">Email</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500 dark:text-slate-400">Phone</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_phone || '-' }}</p>
                        </div>
                        <div v-if="booking.whatsapp">
                            <label class="text-sm text-slate-500 dark:text-slate-400">WhatsApp</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.whatsapp }}</p>
                        </div>
                        <div v-if="booking.contact_address" class="md:col-span-2">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Address</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_address }}</p>
                        </div>
                        <div v-if="booking.emergency_contact">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Emergency Contact</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.emergency_contact }}</p>
                        </div>
                        <div v-if="booking.emergency_phone">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Emergency Phone</label>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.emergency_phone }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Travelers -->
            <AppCard v-if="booking.travelers?.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-group-line text-primary-600"></i>
                        Travelers ({{ booking.travelers.length }})
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div
                            v-for="(traveler, index) in booking.travelers"
                            :key="traveler.id"
                            class="p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700"
                        >
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold text-slate-800 dark:text-slate-200">
                                    {{ traveler.first_name }} {{ traveler.last_name }}
                                    <span v-if="traveler.is_lead" class="text-xs bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 px-2 py-0.5 rounded ml-2">Lead</span>
                                </h4>
                                <span :class="getTravelerTypeBadge(traveler.type)" class="text-xs px-2 py-0.5 rounded capitalize">
                                    {{ traveler.type }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div v-if="traveler.email">
                                    <span class="text-slate-500 dark:text-slate-400">Email:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.email }}</p>
                                </div>
                                <div v-if="traveler.phone">
                                    <span class="text-slate-500 dark:text-slate-400">Phone:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.phone }}</p>
                                </div>
                                <div v-if="traveler.date_of_birth">
                                    <span class="text-slate-500 dark:text-slate-400">DOB:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ formatDate(traveler.date_of_birth) }}</p>
                                </div>
                                <div v-if="traveler.gender">
                                    <span class="text-slate-500 dark:text-slate-400">Gender:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200 capitalize">{{ traveler.gender }}</p>
                                </div>
                                <div v-if="traveler.nationality">
                                    <span class="text-slate-500 dark:text-slate-400">Nationality:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.nationality }}</p>
                                </div>
                                <div v-if="traveler.passport_number">
                                    <span class="text-slate-500 dark:text-slate-400">Passport:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.passport_number }}</p>
                                </div>
                                <div v-if="traveler.nid_number">
                                    <span class="text-slate-500 dark:text-slate-400">NID:</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.nid_number }}</p>
                                </div>
                            </div>
                            <div v-if="traveler.special_requests" class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Special Requests:</span>
                                <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ traveler.special_requests }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Extras -->
            <AppCard v-if="booking.extras?.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-add-circle-line text-primary-600"></i>
                        Tour Extras ({{ booking.extras.length }})
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div
                            v-for="extra in booking.extras"
                            :key="extra.id"
                            class="flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-800 rounded-lg"
                        >
                            <div>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ extra.extra?.name }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Quantity: {{ extra.quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-primary-600">৳{{ formatNumber(extra.total_price) }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">৳{{ formatNumber(extra.unit_price) }} each</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Payment History -->
            <AppCard v-if="booking.payments?.length">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-money-dollar-circle-line text-primary-600"></i>
                        Payment History ({{ booking.payments.length }})
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div
                            v-for="payment in booking.payments"
                            :key="payment.id"
                            class="p-4 border-l-4 rounded-lg bg-slate-50 dark:bg-slate-800"
                            :class="getPaymentBorderClass(payment.status)"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">৳{{ formatNumber(payment.amount) }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ formatDateTime(payment.paid_at || payment.created_at) }}</p>
                                </div>
                                <span :class="getPaymentStatusClass(payment.status)" class="px-2 py-1 rounded text-xs font-medium">
                                    {{ formatStatus(payment.status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400">Method:</span>
                                    <span class="ml-1 font-medium capitalize text-slate-800 dark:text-slate-200">{{ payment.payment_method }}</span>
                                </div>
                                <div v-if="payment.transaction_id">
                                    <span class="text-slate-500 dark:text-slate-400">Transaction:</span>
                                    <span class="ml-1 font-mono text-xs text-slate-800 dark:text-slate-200">{{ payment.transaction_id }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Special Requests -->
            <AppCard v-if="booking.special_requests || booking.dietary_requirements || booking.medical_conditions">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-file-list-line text-primary-600"></i>
                        Special Requests
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div v-if="booking.special_requests">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Special Requests</label>
                            <p class="text-slate-800 dark:text-slate-200">{{ booking.special_requests }}</p>
                        </div>
                        <div v-if="booking.dietary_requirements">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Dietary Requirements</label>
                            <p class="text-slate-800 dark:text-slate-200">{{ booking.dietary_requirements }}</p>
                        </div>
                        <div v-if="booking.medical_conditions">
                            <label class="text-sm text-slate-500 dark:text-slate-400">Medical Conditions</label>
                            <p class="text-slate-800 dark:text-slate-200">{{ booking.medical_conditions }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-information-line text-primary-600"></i>
                        Booking Status
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Status</span>
                            <span :class="getStatusClass(booking.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatStatus(booking.status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Payment</span>
                            <span :class="getPaymentStatusClass(booking.payment_status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatStatus(booking.payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Source</span>
                            <span class="text-slate-800 dark:text-slate-200 capitalize">{{ booking.booking_source }}</span>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700" />
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Booked On</span>
                            <span class="text-slate-800 dark:text-slate-200">{{ formatDateTime(booking.created_at) }}</span>
                        </div>
                        <div v-if="booking.confirmed_at" class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Confirmed</span>
                            <span class="text-slate-800 dark:text-slate-200">{{ formatDateTime(booking.confirmed_at) }}</span>
                        </div>
                        <div v-if="booking.cancelled_at" class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Cancelled</span>
                            <span class="text-slate-800 dark:text-slate-200">{{ formatDateTime(booking.cancelled_at) }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Price Breakdown -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-calculator-line text-primary-600"></i>
                        Price Breakdown
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div class="flex justify-between text-slate-700 dark:text-slate-300">
                            <span>Subtotal</span>
                            <span>৳{{ formatNumber(booking.subtotal) }}</span>
                        </div>
                        <div v-if="booking.extras_total > 0" class="flex justify-between text-slate-700 dark:text-slate-300">
                            <span>Extras</span>
                            <span>৳{{ formatNumber(booking.extras_total) }}</span>
                        </div>
                        <div v-if="booking.discount_amount > 0" class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>-৳{{ formatNumber(booking.discount_amount) }}</span>
                        </div>
                        <div v-if="booking.tax_amount > 0" class="flex justify-between text-slate-700 dark:text-slate-300">
                            <span>Tax</span>
                            <span>৳{{ formatNumber(booking.tax_amount) }}</span>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700" />
                        <div class="flex justify-between font-bold text-lg text-slate-800 dark:text-slate-200">
                            <span>Total</span>
                            <span class="text-primary-600">৳{{ formatNumber(booking.total_amount) }}</span>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700" />
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
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-sticky-note-line text-primary-600"></i>
                        Internal Notes
                    </div>
                </template>
                <template #content>
                    <p class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap">{{ booking.internal_notes }}</p>
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
        pending: 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300',
        processing: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        confirmed: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
        completed: 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300',
        cancelled: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
        refunded: 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300',
    }
    return classes[status] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getPaymentStatusClass = (status) => {
    const classes = {
        unpaid: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
        pending: 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300',
        partial: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        paid: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
        refunded: 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300',
    }
    return classes[status] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getTravelerTypeBadge = (type) => {
    const badges = {
        adult: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        child: 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300',
        infant: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
    }
    return badges[type] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getPaymentBorderClass = (status) => {
    const classes = {
        pending: 'border-amber-400',
        completed: 'border-green-400',
        failed: 'border-red-400',
        refunded: 'border-purple-400',
    }
    return classes[status] || 'border-slate-300'
}

const updateStatus = (status) => {
    if (confirm(`Are you sure you want to ${status} this booking?`)) {
        router.patch(route('tourBooking.updateStatus', [props.booking.id, status]))
    }
}
</script>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
