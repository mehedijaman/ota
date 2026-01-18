<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('flightBooking.index'))">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </AppButton>
                <AppButton v-if="can('flight.booking.edit')" class="btn btn-primary" @click="$inertia.visit(route('flightBooking.edit', booking.id))">
                    <i class="ri-edit-line mr-1"></i> Edit
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Summary -->
            <AppCard>
                <template #title>
                    <div class="flex items-center justify-between">
                        <span>Booking Details</span>
                        <div class="flex gap-2">
                            <span :class="getStatusClass(booking.status)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium ring-1">
                                {{ booking.status }}
                            </span>
                            <span v-if="booking.ticketing_status === 'ticketed'" class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700 ring-1 ring-purple-200">
                                <i class="ri-ticket-2-line"></i> Ticketed
                            </span>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Booking Number</p>
                            <p class="text-neutral-12 font-mono font-bold">{{ booking.booking_number }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">PNR</p>
                            <p class="text-neutral-12 font-mono">{{ booking.pnr || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Trip Type</p>
                            <p class="text-neutral-12 capitalize">{{ booking.trip_type?.replace('_', ' ') }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Cabin Class</p>
                            <p class="text-neutral-12 capitalize">{{ booking.cabin_class }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Departure</p>
                            <p class="text-neutral-12">{{ booking.departure_date }}</p>
                        </div>
                        <div v-if="booking.return_date">
                            <p class="text-neutral-9 text-xs font-medium">Return</p>
                            <p class="text-neutral-12">{{ booking.return_date }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Source</p>
                            <p class="text-neutral-12 capitalize">{{ booking.booking_source }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Created</p>
                            <p class="text-neutral-12">{{ new Date(booking.created_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Flight Segments -->
            <AppCard v-if="booking.segments?.length">
                <template #title>Flight Segments</template>
                <template #content>
                    <div class="space-y-4">
                        <div v-for="segment in booking.segments" :key="segment.id" class="border-neutral-4 rounded-lg border p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 font-bold text-white">
                                        {{ segment.airline?.iata_code }}
                                    </div>
                                    <div>
                                        <p class="text-neutral-12 font-bold">{{ segment.airline?.name }}</p>
                                        <p class="text-neutral-9 text-sm">{{ segment.airline?.iata_code }}{{ segment.flight_number }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-neutral-12 font-medium">{{ segment.cabin_class }}</p>
                                    <p class="text-neutral-9 text-xs">{{ segment.booking_class }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-center">
                                    <p class="text-neutral-12 text-xl font-bold">{{ segment.departure_airport?.iata_code }}</p>
                                    <p class="text-neutral-9 text-xs">{{ segment.departure_datetime }}</p>
                                </div>
                                <div class="flex-1 px-4">
                                    <div class="border-neutral-4 relative border-t-2 border-dashed">
                                        <i class="ri-plane-fill text-primary-9 absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2"></i>
                                    </div>
                                    <p class="text-neutral-9 mt-1 text-center text-xs">{{ segment.duration_minutes }} min</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-neutral-12 text-xl font-bold">{{ segment.arrival_airport?.iata_code }}</p>
                                    <p class="text-neutral-9 text-xs">{{ segment.arrival_datetime }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Passengers -->
            <AppCard v-if="booking.passengers?.length">
                <template #title>Passengers ({{ booking.total_passengers }})</template>
                <template #content>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-neutral-4 border-b">
                                    <th class="text-neutral-9 py-2 text-left font-medium">Name</th>
                                    <th class="text-neutral-9 py-2 text-left font-medium">Type</th>
                                    <th class="text-neutral-9 py-2 text-left font-medium">Passport</th>
                                    <th class="text-neutral-9 py-2 text-left font-medium">Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pax in booking.passengers" :key="pax.id" class="border-neutral-4 border-b">
                                    <td class="text-neutral-12 py-3">
                                        {{ pax.title }} {{ pax.first_name }} {{ pax.last_name }}
                                        <span v-if="pax.is_lead_passenger" class="ml-1 text-xs text-blue-600">(Lead)</span>
                                    </td>
                                    <td class="text-neutral-11 py-3 capitalize">{{ pax.passenger_type }}</td>
                                    <td class="text-neutral-11 py-3 font-mono text-xs">{{ pax.passport_number || '-' }}</td>
                                    <td class="py-3">
                                        <span v-if="pax.ticket_number" class="bg-emerald-50 px-2 py-1 rounded text-xs font-mono text-emerald-700">
                                            {{ pax.ticket_number }}
                                        </span>
                                        <span v-else class="text-neutral-9">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <AppCard>
                <template #title>Contact Information</template>
                <template #content>
                    <div class="space-y-3">
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Name</p>
                            <p class="text-neutral-12">{{ booking.contact_title }} {{ booking.contact_first_name }} {{ booking.contact_last_name }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Email</p>
                            <p class="text-neutral-12">{{ booking.contact_email }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs font-medium">Phone</p>
                            <p class="text-neutral-12">{{ booking.contact_phone }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Payment Summary -->
            <AppCard>
                <template #title>Payment Summary</template>
                <template #content>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-9">Base Fare</span>
                            <span class="text-neutral-12">৳{{ Number(booking.base_fare_total).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-9">Taxes</span>
                            <span class="text-neutral-12">৳{{ Number(booking.tax_total).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-9">Fees</span>
                            <span class="text-neutral-12">৳{{ Number(booking.fee_total).toLocaleString() }}</span>
                        </div>
                        <div class="border-neutral-4 my-2 border-t"></div>
                        <div class="flex justify-between font-bold">
                            <span class="text-neutral-12">Total</span>
                            <span class="text-primary-9">৳{{ Number(booking.total_amount).toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600">
                            <span>Paid</span>
                            <span>৳{{ Number(booking.amount_paid).toLocaleString() }}</span>
                        </div>
                        <div v-if="booking.amount_due > 0" class="flex justify-between text-red-600">
                            <span>Due</span>
                            <span>৳{{ Number(booking.amount_due).toLocaleString() }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Quick Actions -->
            <AppCard>
                <template #title>Actions</template>
                <template #content>
                    <div class="space-y-2">
                        <AppButton v-if="booking.status === 'confirmed' && booking.ticketing_status !== 'ticketed'" class="btn btn-primary w-full">
                            <i class="ri-ticket-2-line mr-1"></i> Issue Ticket
                        </AppButton>
                        <AppButton v-if="booking.status !== 'cancelled'" class="btn btn-secondary w-full">
                            <i class="ri-mail-line mr-1"></i> Send Confirmation
                        </AppButton>
                        <AppButton v-if="booking.status !== 'cancelled'" class="btn btn-danger-outline w-full">
                            <i class="ri-close-circle-line mr-1"></i> Cancel Booking
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    booking: { type: Object, required: true }
})

const { title } = useTitle(`Booking ${props.booking.booking_number}`)

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Bookings', href: route('flightBooking.index') },
    { label: props.booking.booking_number, last: true }
]

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200',
        confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        ticketing: 'bg-blue-50 text-blue-700 ring-blue-200',
        cancelled: 'bg-red-50 text-red-700 ring-red-200',
        refunded: 'bg-purple-50 text-purple-700 ring-purple-200'
    }
    return classes[status] || 'bg-neutral-50 text-neutral-700 ring-neutral-200'
}
</script>
