<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton v-if="can('hotel.booking.edit') && booking.status !== 'cancelled'" class="btn btn-primary" @click="$inertia.visit(route('hotelBooking.edit', booking.id))">
                    <i class="ri-edit-line mr-1"></i> Edit
                </AppButton>
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotelBooking.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Header -->
            <AppCard>
                <template #content>
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-2xl font-bold text-neutral-12">{{ booking.booking_number }}</span>
                                <span :class="statusBadgeClass(booking.status)" class="rounded-full px-3 py-1 text-sm font-medium capitalize">
                                    {{ booking.status }}
                                </span>
                                <span :class="paymentBadgeClass(booking.payment_status)" class="rounded-full px-3 py-1 text-sm font-medium capitalize">
                                    {{ booking.payment_status }}
                                </span>
                            </div>
                            <p class="text-neutral-9 text-sm">Created {{ formatDate(booking.created_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-neutral-9 text-sm">Total Amount</p>
                            <p class="text-3xl font-bold text-emerald-600">৳{{ formatNumber(booking.total_amount) }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Hotel Info -->
            <AppCard>
                <template #title>
                    <i class="ri-hotel-line mr-2 text-blue-500"></i> Hotel Details
                </template>
                <template #content>
                    <div class="flex items-start gap-4">
                        <div class="shrink-0">
                            <div class="flex h-20 w-28 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 text-white">
                                <i class="ri-hotel-line text-3xl"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-neutral-12">{{ booking.hotel?.name }}</h3>
                            <div class="flex items-center gap-1 mb-2">
                                <i v-for="n in (booking.hotel?.star_rating || 0)" :key="n" class="ri-star-fill text-amber-500 text-sm"></i>
                            </div>
                            <p class="text-neutral-9 text-sm">{{ booking.hotel?.full_address }}</p>
                            <p class="text-neutral-9 text-sm mt-1">
                                <i class="ri-phone-line mr-1"></i> {{ booking.hotel?.phone }}
                            </p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Stay Details -->
            <AppCard>
                <template #title>
                    <i class="ri-calendar-check-line mr-2 text-teal-500"></i> Stay Details
                </template>
                <template #content>
                    <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                        <div class="text-center bg-emerald-50 rounded-xl p-4">
                            <i class="ri-login-box-line text-2xl text-emerald-600"></i>
                            <p class="text-neutral-9 text-xs mt-1">Check-in</p>
                            <p class="font-bold text-neutral-12">{{ formatDate(booking.check_in_date) }}</p>
                            <p class="text-xs text-neutral-9">{{ booking.hotel?.check_in_time }}</p>
                        </div>
                        <div class="text-center bg-red-50 rounded-xl p-4">
                            <i class="ri-logout-box-line text-2xl text-red-600"></i>
                            <p class="text-neutral-9 text-xs mt-1">Check-out</p>
                            <p class="font-bold text-neutral-12">{{ formatDate(booking.check_out_date) }}</p>
                            <p class="text-xs text-neutral-9">{{ booking.hotel?.check_out_time }}</p>
                        </div>
                        <div class="text-center bg-blue-50 rounded-xl p-4">
                            <i class="ri-moon-line text-2xl text-blue-600"></i>
                            <p class="text-neutral-9 text-xs mt-1">Nights</p>
                            <p class="font-bold text-neutral-12">{{ booking.nights }}</p>
                        </div>
                        <div class="text-center bg-purple-50 rounded-xl p-4">
                            <i class="ri-group-line text-2xl text-purple-600"></i>
                            <p class="text-neutral-9 text-xs mt-1">Guests</p>
                            <p class="font-bold text-neutral-12">{{ booking.total_adults }} Adults, {{ booking.total_children }} Children</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Booked Rooms -->
            <AppCard>
                <template #title>
                    <i class="ri-door-line mr-2 text-purple-500"></i> Booked Rooms ({{ booking.rooms?.length || 0 }})
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div
                            v-for="room in booking.rooms"
                            :key="room.id"
                            class="flex items-center justify-between border-neutral-5 rounded-lg border p-4"
                        >
                            <div>
                                <p class="font-medium text-neutral-12">{{ room.room?.name }}</p>
                                <p class="text-sm text-neutral-9">
                                    {{ room.rate_plan?.name }} • {{ room.adults }} Adult(s), {{ room.children }} Child(ren)
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">৳{{ formatNumber(room.total_rate) }}</p>
                                <p class="text-xs text-neutral-9">৳{{ formatNumber(room.rate_per_night) }}/night</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Guests -->
            <AppCard v-if="booking.guests?.length">
                <template #title>
                    <i class="ri-user-line mr-2 text-amber-500"></i> Guests
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div
                            v-for="guest in booking.guests"
                            :key="guest.id"
                            class="flex items-center gap-3 border-neutral-5 rounded-lg border p-3"
                            :class="{ 'border-amber-400 bg-amber-50': guest.is_primary }"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 text-neutral-600">
                                <i class="ri-user-line"></i>
                            </div>
                            <div>
                                <p class="font-medium text-neutral-12">
                                    {{ guest.full_name }}
                                    <span v-if="guest.is_primary" class="text-xs text-amber-600">(Primary)</span>
                                </p>
                                <p class="text-xs text-neutral-9 capitalize">{{ guest.guest_type }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Special Requests -->
            <AppCard v-if="booking.special_requests">
                <template #title>
                    <i class="ri-chat-quote-line mr-2 text-pink-500"></i> Special Requests
                </template>
                <template #content>
                    <p class="text-neutral-11">{{ booking.special_requests }}</p>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <!-- Actions -->
            <AppCard v-if="booking.status !== 'cancelled'">
                <template #title>Quick Actions</template>
                <template #content>
                    <div class="space-y-2">
                        <AppButton v-if="booking.status === 'pending'" class="btn btn-success w-full" @click="updateStatus('confirmed')">
                            <i class="ri-checkbox-circle-line mr-1"></i> Confirm Booking
                        </AppButton>
                        <AppButton v-if="booking.status === 'confirmed'" class="btn btn-primary w-full" @click="updateStatus('checked_in')">
                            <i class="ri-login-box-line mr-1"></i> Check In
                        </AppButton>
                        <AppButton v-if="booking.status === 'checked_in'" class="btn btn-secondary w-full" @click="updateStatus('checked_out')">
                            <i class="ri-logout-box-line mr-1"></i> Check Out
                        </AppButton>
                        <AppButton v-if="['pending', 'confirmed'].includes(booking.status)" class="btn btn-destructive w-full" @click="updateStatus('cancelled')">
                            <i class="ri-close-circle-line mr-1"></i> Cancel Booking
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <!-- Contact Info -->
            <AppCard>
                <template #title>Contact Information</template>
                <template #content>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="ri-user-line text-neutral-9"></i>
                            <span class="text-neutral-12">{{ booking.contact_name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ri-mail-line text-neutral-9"></i>
                            <a :href="'mailto:' + booking.contact_email" class="text-blue-600 hover:underline">{{ booking.contact_email }}</a>
                        </div>
                        <div v-if="booking.contact_phone" class="flex items-center gap-2">
                            <i class="ri-phone-line text-neutral-9"></i>
                            <a :href="'tel:' + booking.contact_phone" class="text-blue-600 hover:underline">{{ booking.contact_phone }}</a>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Payment Summary -->
            <AppCard>
                <template #title>Payment Summary</template>
                <template #content>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-9">Room Charges</span>
                            <span class="text-neutral-12 font-medium">৳{{ formatNumber(booking.room_total) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-9">Taxes & Fees</span>
                            <span class="text-neutral-12 font-medium">৳{{ formatNumber(booking.tax_amount) }}</span>
                        </div>
                        <div v-if="booking.discount_amount > 0" class="flex justify-between text-emerald-600">
                            <span>Discount</span>
                            <span>-৳{{ formatNumber(booking.discount_amount) }}</span>
                        </div>
                        <div class="border-neutral-4 border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-neutral-12">Total</span>
                                <span class="text-emerald-600">৳{{ formatNumber(booking.total_amount) }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between text-blue-600">
                            <span>Paid</span>
                            <span>৳{{ formatNumber(booking.amount_paid) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600 font-semibold">
                            <span>Due</span>
                            <span>৳{{ formatNumber(booking.amount_due) }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Payment History -->
            <AppCard v-if="booking.payments?.length">
                <template #title>Payment History</template>
                <template #content>
                    <div class="space-y-2">
                        <div
                            v-for="payment in booking.payments"
                            :key="payment.id"
                            class="flex items-center justify-between text-sm border-neutral-5 rounded-lg border p-3"
                        >
                            <div>
                                <p class="font-medium text-neutral-12 capitalize">{{ payment.payment_method }}</p>
                                <p class="text-xs text-neutral-9">{{ formatDate(payment.payment_date) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">৳{{ formatNumber(payment.amount) }}</p>
                                <span :class="payment.is_verified ? 'text-emerald-600' : 'text-amber-600'" class="text-xs">
                                    {{ payment.is_verified ? 'Verified' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Booking Source -->
            <AppCard>
                <template #title>Booking Info</template>
                <template #content>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-9">Source</span>
                            <span class="text-neutral-12 font-medium capitalize">{{ booking.booking_source }}</span>
                        </div>
                        <div v-if="booking.customer" class="flex justify-between">
                            <span class="text-neutral-9">Customer</span>
                            <span class="text-neutral-12 font-medium">{{ booking.customer?.name }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>
        </aside>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    booking: { type: Object, required: true }
})

const { title } = useTitle(`Booking ${props.booking.booking_number}`)

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotel Bookings', href: route('hotelBooking.index') },
    { label: props.booking.booking_number, last: true }
]

const formatNumber = (num) => num ? new Intl.NumberFormat('en-BD').format(num) : '0'
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : ''

const statusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        confirmed: 'bg-blue-100 text-blue-700',
        checked_in: 'bg-emerald-100 text-emerald-700',
        checked_out: 'bg-purple-100 text-purple-700',
        cancelled: 'bg-red-100 text-red-700',
        no_show: 'bg-gray-100 text-gray-700'
    }
    return classes[status] || 'bg-gray-100 text-gray-700'
}

const paymentBadgeClass = (status) => {
    const classes = {
        unpaid: 'bg-red-100 text-red-700',
        partial: 'bg-amber-100 text-amber-700',
        paid: 'bg-emerald-100 text-emerald-700',
        refunded: 'bg-purple-100 text-purple-700'
    }
    return classes[status] || 'bg-gray-100 text-gray-700'
}

const updateStatus = (status) => {
    router.patch(route('hotelBooking.updateStatus', { id: props.booking.id, status }), {}, { preserveScroll: true })
}
</script>
