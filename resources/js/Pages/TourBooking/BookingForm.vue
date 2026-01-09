<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full">
        <template #title>{{ isEdit ? 'Edit Booking' : 'Create Booking' }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />

            <form class="space-y-6">
                <!-- Tour Selection -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h4 class="font-medium mb-4">Tour Selection</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <AppLabel for="tour_id">Tour *</AppLabel>
                            <AppCombobox
                                v-model="form.tour_id"
                                :options="tourOptions"
                                combo-label="Select Tour"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <AppLabel for="departure_date_id">Departure Date</AppLabel>
                            <AppCombobox
                                v-model="form.departure_date_id"
                                :options="departureDateOptions"
                                combo-label="Select Date"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <AppLabel for="booking_source">Booking Source</AppLabel>
                            <AppCombobox
                                v-model="form.booking_source"
                                :options="bookingSourceOptions"
                                combo-label="Select Source"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Travelers Count -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h4 class="font-medium mb-4">Travelers</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <AppLabel for="adults_count">Adults *</AppLabel>
                            <AppInputText
                                id="adults_count"
                                v-model="form.adults_count"
                                type="number"
                                min="1"
                                :class="{ 'input-error': errorsFields.includes('adults_count') }"
                            />
                        </div>
                        <div>
                            <AppLabel for="children_count">Children (2-11 yrs)</AppLabel>
                            <AppInputText
                                id="children_count"
                                v-model="form.children_count"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="infants_count">Infants (0-2 yrs)</AppLabel>
                            <AppInputText
                                id="infants_count"
                                v-model="form.infants_count"
                                type="number"
                                min="0"
                            />
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h4 class="font-medium mb-4">Contact Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <AppLabel for="contact_name">Contact Name *</AppLabel>
                            <AppInputText
                                id="contact_name"
                                v-model="form.contact_name"
                                type="text"
                                :class="{ 'input-error': errorsFields.includes('contact_name') }"
                            />
                        </div>
                        <div>
                            <AppLabel for="contact_email">Contact Email *</AppLabel>
                            <AppInputText
                                id="contact_email"
                                v-model="form.contact_email"
                                type="email"
                                :class="{ 'input-error': errorsFields.includes('contact_email') }"
                            />
                        </div>
                        <div>
                            <AppLabel for="contact_phone">Contact Phone</AppLabel>
                            <AppInputText
                                id="contact_phone"
                                v-model="form.contact_phone"
                                type="text"
                            />
                        </div>
                        <div>
                            <AppLabel for="contact_address">Address</AppLabel>
                            <AppInputText
                                id="contact_address"
                                v-model="form.contact_address"
                                type="text"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t">
                        <div>
                            <AppLabel for="emergency_contact">Emergency Contact</AppLabel>
                            <AppInputText
                                id="emergency_contact"
                                v-model="form.emergency_contact"
                                type="text"
                            />
                        </div>
                        <div>
                            <AppLabel for="emergency_phone">Emergency Phone</AppLabel>
                            <AppInputText
                                id="emergency_phone"
                                v-model="form.emergency_phone"
                                type="text"
                            />
                        </div>
                    </div>
                </div>

                <!-- Travelers Details -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium">Traveler Details</h4>
                        <AppButton type="button" class="btn btn-sm btn-secondary" @click="addTraveler">
                            <i class="ri-add-line mr-1"></i> Add Traveler
                        </AppButton>
                    </div>

                    <div
                        v-for="(traveler, index) in form.travelers"
                        :key="index"
                        class="p-4 border rounded-lg bg-white mb-4"
                    >
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="font-medium">Traveler {{ index + 1 }} {{ index === 0 ? '(Lead)' : '' }}</h5>
                            <button
                                v-if="index > 0"
                                type="button"
                                @click="removeTraveler(index)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <AppLabel>Type</AppLabel>
                                <AppCombobox
                                    v-model="traveler.type"
                                    :options="travelerTypeOptions"
                                    combo-label="Select Type"
                                    class="w-full"
                                />
                            </div>
                            <div>
                                <AppLabel>Title</AppLabel>
                                <AppCombobox
                                    v-model="traveler.title"
                                    :options="titleOptions"
                                    combo-label="Select Title"
                                    class="w-full"
                                />
                            </div>
                            <div>
                                <AppLabel>First Name *</AppLabel>
                                <AppInputText v-model="traveler.first_name" type="text" />
                            </div>
                            <div>
                                <AppLabel>Last Name</AppLabel>
                                <AppInputText v-model="traveler.last_name" type="text" />
                            </div>
                            <div>
                                <AppLabel>Date of Birth</AppLabel>
                                <AppInputText v-model="traveler.date_of_birth" type="date" />
                            </div>
                            <div>
                                <AppLabel>Gender</AppLabel>
                                <AppCombobox
                                    v-model="traveler.gender"
                                    :options="genderOptions"
                                    combo-label="Select Gender"
                                    class="w-full"
                                />
                            </div>
                            <div>
                                <AppLabel>Passport Number</AppLabel>
                                <AppInputText v-model="traveler.passport_number" type="text" />
                            </div>
                            <div>
                                <AppLabel>NID Number</AppLabel>
                                <AppInputText v-model="traveler.nid_number" type="text" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Requests -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h4 class="font-medium mb-4">Special Requests</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <AppLabel for="special_requests">Special Requests</AppLabel>
                            <AppTextArea id="special_requests" v-model="form.special_requests" rows="2" />
                        </div>
                        <div>
                            <AppLabel for="dietary_requirements">Dietary Requirements</AppLabel>
                            <AppInputText id="dietary_requirements" v-model="form.dietary_requirements" type="text" />
                        </div>
                        <div>
                            <AppLabel for="medical_conditions">Medical Conditions</AppLabel>
                            <AppInputText id="medical_conditions" v-model="form.medical_conditions" type="text" />
                        </div>
                    </div>
                </div>

                <!-- Pricing & Discount -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <h4 class="font-medium mb-4">Pricing</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <AppLabel for="discount_code">Discount Code</AppLabel>
                            <AppInputText id="discount_code" v-model="form.discount_code" type="text" />
                        </div>
                        <div>
                            <AppLabel for="discount_amount">Discount Amount (৳)</AppLabel>
                            <AppInputText id="discount_amount" v-model="form.discount_amount" type="number" min="0" />
                        </div>
                    </div>
                </div>

                <!-- Internal Notes -->
                <div>
                    <AppLabel for="internal_notes">Internal Notes (Admin Only)</AppLabel>
                    <AppTextArea id="internal_notes" v-model="form.internal_notes" rows="2" />
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-secondary mr-2" @click="$inertia.visit(route('tourBooking.index'))">
                Cancel
            </AppButton>
            <AppButton class="btn btn-primary" @click="submitForm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Booking' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('Tour Booking')

const props = defineProps({
    booking: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    destinations: { type: Array, default: () => [] },
    tours: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', href: route('tourBooking.index') },
    { label: props.booking ? 'Edit' : 'Create', last: true }
]

const tourOptions = computed(() => props.tours?.map(t => ({ value: t.id, label: t.name })) || [])
const departureDateOptions = ref([]) // Load based on selected tour

const bookingSourceOptions = [
    { value: 'admin', label: 'Admin Panel' },
    { value: 'website', label: 'Website' },
    { value: 'agent', label: 'Agent' },
    { value: 'api', label: 'API' }
]

const travelerTypeOptions = [
    { value: 'adult', label: 'Adult' },
    { value: 'child', label: 'Child' },
    { value: 'infant', label: 'Infant' }
]

const titleOptions = [
    { value: 'Mr', label: 'Mr' },
    { value: 'Mrs', label: 'Mrs' },
    { value: 'Ms', label: 'Ms' },
    { value: 'Miss', label: 'Miss' },
    { value: 'Master', label: 'Master' }
]

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' }
]

const getDefaultTraveler = (type = 'adult') => ({
    type,
    title: 'Mr',
    first_name: '',
    last_name: '',
    date_of_birth: null,
    gender: null,
    passport_number: '',
    nid_number: ''
})

const form = useForm({
    tour_id: props.booking?.tour_id || null,
    departure_date_id: props.booking?.departure_date_id || null,
    booking_source: props.booking?.booking_source || 'admin',
    adults_count: props.booking?.adults_count || 1,
    children_count: props.booking?.children_count || 0,
    infants_count: props.booking?.infants_count || 0,
    contact_name: props.booking?.contact_name || '',
    contact_email: props.booking?.contact_email || '',
    contact_phone: props.booking?.contact_phone || '',
    contact_address: props.booking?.contact_address || '',
    emergency_contact: props.booking?.emergency_contact || '',
    emergency_phone: props.booking?.emergency_phone || '',
    special_requests: props.booking?.special_requests || '',
    dietary_requirements: props.booking?.dietary_requirements || '',
    medical_conditions: props.booking?.medical_conditions || '',
    discount_code: props.booking?.discount_code || '',
    discount_amount: props.booking?.discount_amount || 0,
    internal_notes: props.booking?.internal_notes || '',
    travelers: props.booking?.travelers || [getDefaultTraveler()]
})

const { isCreate, isEdit } = useFormContext()

const addTraveler = () => {
    form.travelers.push(getDefaultTraveler())
}

const removeTraveler = (index) => {
    form.travelers.splice(index, 1)
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('tourBooking.store'))
    }
    if (isEdit.value) {
        form.put(route('tourBooking.update', props.booking.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
