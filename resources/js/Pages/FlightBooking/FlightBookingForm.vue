<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('flightBooking.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back to List
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submit">
        <!-- Trip Information -->
        <AppCard class="mb-6">
            <template #title>Trip Information</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <AppLabel for="trip_type" required>Trip Type</AppLabel>
                        <AppCombobox
                            v-model="form.trip_type"
                            :options="tripTypes"
                            emit-value
                            :error="form.errors.trip_type"
                        />
                    </div>
                    <div>
                        <AppLabel for="cabin_class" required>Cabin Class</AppLabel>
                        <AppCombobox
                            v-model="form.cabin_class"
                            :options="cabinClasses"
                            emit-value
                            :error="form.errors.cabin_class"
                        />
                    </div>
                    <div>
                        <AppLabel for="departure_date" required>Departure Date</AppLabel>
                        <AppInput
                            type="date"
                            v-model="form.departure_date"
                            :error="form.errors.departure_date"
                        />
                    </div>
                    <div>
                        <AppLabel for="return_date">Return Date</AppLabel>
                        <AppInput
                            type="date"
                            v-model="form.return_date"
                            :error="form.errors.return_date"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Passenger Counts -->
        <AppCard class="mb-6">
            <template #title>Passengers</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <AppLabel for="adult_count" required>Adults</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.adult_count"
                            min="1"
                            max="9"
                            :error="form.errors.adult_count"
                        />
                    </div>
                    <div>
                        <AppLabel for="child_count">Children</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.child_count"
                            min="0"
                            max="9"
                            :error="form.errors.child_count"
                        />
                    </div>
                    <div>
                        <AppLabel for="infant_count">Infants</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.infant_count"
                            min="0"
                            max="4"
                            :error="form.errors.infant_count"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Contact Information -->
        <AppCard class="mb-6">
            <template #title>Contact Information</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <AppLabel for="contact_title">Title</AppLabel>
                        <AppCombobox
                            v-model="form.contact_title"
                            :options="titles"
                            emit-value
                        />
                    </div>
                    <div>
                        <AppLabel for="contact_first_name" required>First Name</AppLabel>
                        <AppInput
                            v-model="form.contact_first_name"
                            :error="form.errors.contact_first_name"
                        />
                    </div>
                    <div>
                        <AppLabel for="contact_last_name" required>Last Name</AppLabel>
                        <AppInput
                            v-model="form.contact_last_name"
                            :error="form.errors.contact_last_name"
                        />
                    </div>
                    <div>
                        <AppLabel for="contact_email" required>Email</AppLabel>
                        <AppInput
                            type="email"
                            v-model="form.contact_email"
                            :error="form.errors.contact_email"
                        />
                    </div>
                    <div>
                        <AppLabel for="contact_phone" required>Phone</AppLabel>
                        <AppInput
                            v-model="form.contact_phone"
                            :error="form.errors.contact_phone"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Pricing -->
        <AppCard class="mb-6">
            <template #title>Pricing</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <AppLabel for="base_fare_total">Base Fare</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.base_fare_total"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <AppLabel for="tax_total">Taxes</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.tax_total"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <AppLabel for="fee_total">Fees</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.fee_total"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <AppLabel for="total_amount">Total Amount</AppLabel>
                        <AppInput
                            type="number"
                            v-model="form.total_amount"
                            step="0.01"
                            readonly
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Notes -->
        <AppCard class="mb-6">
            <template #title>Notes</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <AppLabel for="special_requests">Special Requests</AppLabel>
                        <AppTextArea v-model="form.special_requests" rows="3" />
                    </div>
                    <div>
                        <AppLabel for="internal_notes">Internal Notes</AppLabel>
                        <AppTextArea v-model="form.internal_notes" rows="3" />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <AppButton
                type="button"
                class="btn btn-secondary"
                @click="$inertia.visit(route('flightBooking.index'))"
            >
                Cancel
            </AppButton>
            <AppButton
                type="submit"
                class="btn btn-primary"
                :loading="form.processing"
            >
                <i class="ri-save-line mr-1"></i>
                {{ isEdit ? 'Update Booking' : 'Create Booking' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const props = defineProps({
    booking: {
        type: Object,
        default: () => null
    }
})

const isEdit = computed(() => !!props.booking)
const { title } = useTitle(isEdit.value ? 'Edit Booking' : 'Create Booking')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Bookings', href: route('flightBooking.index') },
    { label: isEdit.value ? 'Edit' : 'Create', last: true }
]

const tripTypes = [
    { value: 'one_way', label: 'One Way' },
    { value: 'round_trip', label: 'Round Trip' },
    { value: 'multi_city', label: 'Multi City' }
]

const cabinClasses = [
    { value: 'economy', label: 'Economy' },
    { value: 'premium_economy', label: 'Premium Economy' },
    { value: 'business', label: 'Business' },
    { value: 'first', label: 'First Class' }
]

const titles = [
    { value: 'Mr', label: 'Mr' },
    { value: 'Mrs', label: 'Mrs' },
    { value: 'Ms', label: 'Ms' },
    { value: 'Miss', label: 'Miss' },
    { value: 'Dr', label: 'Dr' }
]

const form = useForm({
    trip_type: props.booking?.trip_type ?? 'one_way',
    cabin_class: props.booking?.cabin_class ?? 'economy',
    departure_date: props.booking?.departure_date ?? '',
    return_date: props.booking?.return_date ?? '',
    adult_count: props.booking?.adult_count ?? 1,
    child_count: props.booking?.child_count ?? 0,
    infant_count: props.booking?.infant_count ?? 0,
    contact_title: props.booking?.contact_title ?? 'Mr',
    contact_first_name: props.booking?.contact_first_name ?? '',
    contact_last_name: props.booking?.contact_last_name ?? '',
    contact_email: props.booking?.contact_email ?? '',
    contact_phone: props.booking?.contact_phone ?? '',
    base_fare_total: props.booking?.base_fare_total ?? 0,
    tax_total: props.booking?.tax_total ?? 0,
    fee_total: props.booking?.fee_total ?? 0,
    total_amount: props.booking?.total_amount ?? 0,
    special_requests: props.booking?.special_requests ?? '',
    internal_notes: props.booking?.internal_notes ?? ''
})

// Auto-calculate total
watch([() => form.base_fare_total, () => form.tax_total, () => form.fee_total], () => {
    form.total_amount = (parseFloat(form.base_fare_total) || 0) + 
                        (parseFloat(form.tax_total) || 0) + 
                        (parseFloat(form.fee_total) || 0)
})

const submit = () => {
    if (isEdit.value) {
        form.put(route('flightBooking.update', props.booking.id))
    } else {
        form.post(route('flightBooking.store'))
    }
}
</script>
