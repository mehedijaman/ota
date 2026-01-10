<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="isEditing ? 'Edit Booking' : 'Create Booking'" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('tourBooking.index'))">
                <i class="ri-arrow-left-line mr-1"></i> Back to List
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submit" class="space-y-6">
        <!-- Tour Selection & Basic Info -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-map-pin-line text-primary-600"></i>
                    Tour & Travel Details
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tour Selection -->
                    <div class="md:col-span-2">
                        <AppLabel for="tour_id" required>Select Tour</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="form.tour_id"
                            :options="tourOptions"
                            :error="form.errors.tour_id"
                            @change="onTourChange"
                            placeholder="Choose a tour..."
                        />
                    </div>

                    <!-- Pricing Plan -->
                    <div v-if="selectedTour && pricingOptions.length" class="md:col-span-2">
                        <AppLabel for="pricing_id" required>Select Pricing Plan</AppLabel>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Choose a pricing plan for this tour</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="pricing in pricingOptions"
                                :key="pricing.id"
                                @click="form.pricing_id = pricing.id"
                                :class="[
                                    'relative border-2 rounded-lg p-4 cursor-pointer transition-all',
                                    form.pricing_id === pricing.id
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-md'
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-primary-300 dark:hover:border-primary-600'
                                ]"
                            >
                                <!-- Selected Indicator -->
                                <div 
                                    v-if="form.pricing_id === pricing.id" 
                                    class="absolute top-2 right-2 bg-primary-500 text-white rounded-full p-1"
                                >
                                    <i class="ri-check-line text-sm"></i>
                                </div>
                                
                                <h4 class="font-semibold text-slate-800 dark:text-slate-200 mb-3 pr-8">{{ pricing.name }}</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600 dark:text-slate-400">Adult:</span>
                                        <span class="text-primary-600 dark:text-primary-400 font-bold">৳{{ formatNumber(pricing.adult_price) }}</span>
                                    </div>
                                    <div v-if="pricing.child_price > 0" class="flex justify-between items-center">
                                        <span class="text-slate-600 dark:text-slate-400">Child:</span>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium">৳{{ formatNumber(pricing.child_price) }}</span>
                                    </div>
                                    <div v-if="pricing.infant_price > 0" class="flex justify-between items-center">
                                        <span class="text-slate-600 dark:text-slate-400">Infant:</span>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium">৳{{ formatNumber(pricing.infant_price) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.pricing_id" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.pricing_id }}</p>
                    </div>

                    <!-- Travel Date -->
                    <div>
                        <AppLabel for="travel_date" required>Travel Date</AppLabel>
                        <AppInputText
                            v-model="form.travel_date"
                            type="date"
                            :error="form.errors.travel_date"
                        />
                    </div>

                    <!-- Booking Source -->
                    <div>
                        <AppLabel for="booking_source">Booking Source</AppLabel>
                        <AppSelect
                            v-model="form.booking_source"
                            :options="bookingSourceOptions"
                            :error="form.errors.booking_source"
                        />
                    </div>

                    <!-- Traveler Counts -->
                    <div>
                        <AppLabel for="adult_count" required>Adults (12+)</AppLabel>
                        <AppInputText
                            v-model.number="form.adult_count"
                            type="number"
                            min="1"
                            :error="form.errors.adult_count"
                            @input="updateTravelerForms"
                        />
                    </div>

                    <div>
                        <AppLabel for="child_count">Children (2-11)</AppLabel>
                        <AppInputText
                            v-model.number="form.child_count"
                            type="number"
                            min="0"
                            :error="form.errors.child_count"
                            @input="updateTravelerForms"
                        />
                    </div>

                    <div>
                        <AppLabel for="infant_count">Infants (0-2)</AppLabel>
                        <AppInputText
                            v-model.number="form.infant_count"
                            type="number"
                            min="0"
                            :error="form.errors.infant_count"
                            @input="updateTravelerForms"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Customer & Agent Selection -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-user-line text-primary-600"></i>
                    Customer & Agent
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <AppLabel for="customer_id">Customer</AppLabel>
                        <AppSelect
                            v-model="form.customer_id"
                            :options="customerOptions"
                            :error="form.errors.customer_id"
                            placeholder="Select customer (optional)"
                            clearable
                        />
                    </div>

                    <div>
                        <AppLabel for="agent_id">Agent</AppLabel>
                        <AppSelect
                            v-model="form.agent_id"
                            :options="agentOptions"
                            :error="form.errors.agent_id"
                            placeholder="Select agent (optional)"
                            clearable
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Contact Information -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-contacts-line text-primary-600"></i>
                    Contact Information
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <AppLabel for="contact_name" required>Contact Name</AppLabel>
                        <AppInputText
                            v-model="form.contact_name"
                            :error="form.errors.contact_name"
                        />
                    </div>

                    <div>
                        <AppLabel for="contact_email" required>Contact Email</AppLabel>
                        <AppInputText
                            v-model="form.contact_email"
                            type="email"
                            :error="form.errors.contact_email"
                        />
                    </div>

                    <div>
                        <AppLabel for="contact_phone">Contact Phone</AppLabel>
                        <AppInputText
                            v-model="form.contact_phone"
                            :error="form.errors.contact_phone"
                        />
                    </div>

                    <div>
                        <AppLabel for="whatsapp">WhatsApp Number</AppLabel>
                        <AppInputText
                            v-model="form.whatsapp"
                            :error="form.errors.whatsapp"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <AppLabel for="contact_address">Address</AppLabel>
                        <AppTextArea
                            v-model="form.contact_address"
                            :error="form.errors.contact_address"
                            rows="2"
                        />
                    </div>

                    <div>
                        <AppLabel for="emergency_contact">Emergency Contact Name</AppLabel>
                        <AppInputText
                            v-model="form.emergency_contact"
                            :error="form.errors.emergency_contact"
                        />
                    </div>

                    <div>
                        <AppLabel for="emergency_phone">Emergency Contact Phone</AppLabel>
                        <AppInputText
                            v-model="form.emergency_phone"
                            :error="form.errors.emergency_phone"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Travelers -->
        <AppCard>
            <template #title>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="ri-group-line text-primary-600"></i>
                        Traveler Details ({{ totalTravelers }})
                    </div>
                </div>
            </template>
            <template #content>
                <div class="space-y-6">
                    <div
                        v-for="(traveler, index) in form.travelers"
                        :key="index"
                        class="p-6 bg-slate-50 dark:bg-slate-800 rounded-lg border-2 border-slate-200 dark:border-slate-700"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-slate-800 dark:text-slate-200">
                                Traveler {{ index + 1 }}
                                <span :class="getTravelerTypeBadge(traveler.type)" class="ml-2 text-xs px-2 py-0.5 rounded">
                                    {{ traveler.type }}
                                </span>
                            </h4>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="traveler.is_lead"
                                    @change="updateLeadTraveler(index)"
                                    class="rounded text-primary-600 focus:ring-primary-500"
                                />
                                <span class="text-sm text-slate-600 dark:text-slate-400">Lead Traveler</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <AppLabel :for="`traveler_${index}_first_name`" required>First Name</AppLabel>
                                <AppInputText
                                    v-model="traveler.first_name"
                                    :error="form.errors[`travelers.${index}.first_name`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_last_name`" required>Last Name</AppLabel>
                                <AppInputText
                                    v-model="traveler.last_name"
                                    :error="form.errors[`travelers.${index}.last_name`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_email`">Email</AppLabel>
                                <AppInputText
                                    v-model="traveler.email"
                                    type="email"
                                    :error="form.errors[`travelers.${index}.email`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_phone`">Phone</AppLabel>
                                <AppInputText
                                    v-model="traveler.phone"
                                    :error="form.errors[`travelers.${index}.phone`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_date_of_birth`">Date of Birth</AppLabel>
                                <AppInputText
                                    v-model="traveler.date_of_birth"
                                    type="date"
                                    :error="form.errors[`travelers.${index}.date_of_birth`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_gender`">Gender</AppLabel>
                                <AppSelect
                                    v-model="traveler.gender"
                                    :options="genderOptions"
                                    :error="form.errors[`travelers.${index}.gender`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_nationality`">Nationality</AppLabel>
                                <AppInputText
                                    v-model="traveler.nationality"
                                    :error="form.errors[`travelers.${index}.nationality`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_passport_number`">Passport Number</AppLabel>
                                <AppInputText
                                    v-model="traveler.passport_number"
                                    :error="form.errors[`travelers.${index}.passport_number`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_passport_expiry`">Passport Expiry</AppLabel>
                                <AppInputText
                                    v-model="traveler.passport_expiry"
                                    type="date"
                                    :error="form.errors[`travelers.${index}.passport_expiry`]"
                                />
                            </div>

                            <div>
                                <AppLabel :for="`traveler_${index}_nid_number`">NID Number</AppLabel>
                                <AppInputText
                                    v-model="traveler.nid_number"
                                    :error="form.errors[`travelers.${index}.nid_number`]"
                                />
                            </div>

                            <div class="md:col-span-3">
                                <AppLabel :for="`traveler_${index}_special_requests`">Special Requests</AppLabel>
                                <AppTextArea
                                    v-model="traveler.special_requests"
                                    :error="form.errors[`travelers.${index}.special_requests`]"
                                    rows="2"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Special Requirements -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-file-list-line text-primary-600"></i>
                    Special Requirements
                </div>
            </template>
            <template #content>
                <div class="space-y-4">
                    <div>
                        <AppLabel for="special_requests">Special Requests</AppLabel>
                        <AppTextArea
                            v-model="form.special_requests"
                            :error="form.errors.special_requests"
                            rows="3"
                            placeholder="Any special requests or requirements..."
                        />
                    </div>

                    <div>
                        <AppLabel for="dietary_requirements">Dietary Requirements</AppLabel>
                        <AppTextArea
                            v-model="form.dietary_requirements"
                            :error="form.errors.dietary_requirements"
                            rows="2"
                            placeholder="Vegetarian, vegan, allergies, etc..."
                        />
                    </div>

                    <div>
                        <AppLabel for="medical_conditions">Medical Conditions</AppLabel>
                        <AppTextArea
                            v-model="form.medical_conditions"
                            :error="form.errors.medical_conditions"
                            rows="2"
                            placeholder="Any medical conditions we should be aware of..."
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Pricing & Payment -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-money-dollar-circle-line text-primary-600"></i>
                    Pricing & Payment
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 bg-primary-50 dark:bg-primary-900/20 p-4 rounded-lg">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span class="font-medium">৳{{ formatNumber(calculatedSubtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Extras:</span>
                                <span class="font-medium">৳{{ formatNumber(form.extras_total) }}</span>
                            </div>
                            <div v-if="form.discount_amount > 0" class="flex justify-between text-sm text-green-600">
                                <span>Discount:</span>
                                <span class="font-medium">-৳{{ formatNumber(form.discount_amount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Tax:</span>
                                <span class="font-medium">৳{{ formatNumber(form.tax_amount) }}</span>
                            </div>
                            <hr class="border-primary-200 dark:border-primary-700" />
                            <div class="flex justify-between text-lg font-bold text-primary-600">
                                <span>Total:</span>
                                <span>৳{{ formatNumber(calculatedTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <AppLabel for="extras_total">Extras Total</AppLabel>
                        <AppInputText
                            v-model.number="form.extras_total"
                            type="number"
                            step="0.01"
                            :error="form.errors.extras_total"
                        />
                    </div>

                    <div>
                        <AppLabel for="discount_amount">Discount Amount</AppLabel>
                        <AppInputText
                            v-model.number="form.discount_amount"
                            type="number"
                            step="0.01"
                            :error="form.errors.discount_amount"
                        />
                    </div>

                    <div>
                        <AppLabel for="discount_code">Discount Code</AppLabel>
                        <AppInputText
                            v-model="form.discount_code"
                            :error="form.errors.discount_code"
                        />
                    </div>

                    <div>
                        <AppLabel for="tax_amount">Tax Amount</AppLabel>
                        <AppInputText
                            v-model.number="form.tax_amount"
                            type="number"
                            step="0.01"
                            :error="form.errors.tax_amount"
                        />
                    </div>

                    <div>
                        <AppLabel for="deposit_amount">Deposit Amount</AppLabel>
                        <AppInputText
                            v-model.number="form.deposit_amount"
                            type="number"
                            step="0.01"
                            :error="form.errors.deposit_amount"
                        />
                    </div>

                    <div>
                        <AppLabel for="deposit_paid">Deposit Paid</AppLabel>
                        <AppInputText
                            v-model.number="form.deposit_paid"
                            type="number"
                            step="0.01"
                            :error="form.errors.deposit_paid"
                        />
                    </div>

                    <div>
                        <AppLabel for="amount_paid">Amount Paid</AppLabel>
                        <AppInputText
                            v-model.number="form.amount_paid"
                            type="number"
                            step="0.01"
                            :error="form.errors.amount_paid"
                        />
                    </div>

                    <div>
                        <AppLabel for="currency">Currency</AppLabel>
                        <AppSelect
                            v-model="form.currency"
                            :options="currencyOptions"
                            :error="form.errors.currency"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Status & Administrative -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="ri-settings-line text-primary-600"></i>
                    Status & Administrative
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <AppLabel for="status">Booking Status</AppLabel>
                        <AppSelect
                            v-model="form.status"
                            :options="statusOptions"
                            :error="form.errors.status"
                        />
                    </div>

                    <div>
                        <AppLabel for="payment_status">Payment Status</AppLabel>
                        <AppSelect
                            v-model="form.payment_status"
                            :options="paymentStatusOptions"
                            :error="form.errors.payment_status"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <AppLabel for="internal_notes">Internal Notes</AppLabel>
                        <AppTextArea
                            v-model="form.internal_notes"
                            :error="form.errors.internal_notes"
                            rows="4"
                            placeholder="Internal notes (not visible to customer)..."
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3">
            <AppButton
                type="button"
                class="btn btn-secondary"
                @click="$inertia.visit(route('tourBooking.index'))"
            >
                Cancel
            </AppButton>
            <AppButton
                type="submit"
                class="btn btn-primary"
                :disabled="form.processing"
            >
                <i class="ri-save-line mr-1"></i>
                {{ isEditing ? 'Update Booking' : 'Create Booking' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Booking Form')

const props = defineProps({
    booking: Object,
    tours: Array,
    customers: Array,
    agents: Array,
})

const isEditing = computed(() => !!props.booking)

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', href: route('tourBooking.index') },
    { label: isEditing.value ? 'Edit Booking' : 'Create Booking', last: true }
]

// Form initialization
const form = useForm({
    tour_id: props.booking?.tour_id || null,
    pricing_id: props.booking?.pricing_id || null,
    customer_id: props.booking?.customer_id || null,
    agent_id: props.booking?.agent_id || null,
    travel_date: props.booking?.travel_date || '',
    status: props.booking?.status || 'pending',
    payment_status: props.booking?.payment_status || 'unpaid',
    adult_count: props.booking?.adult_count || 1,
    child_count: props.booking?.child_count || 0,
    infant_count: props.booking?.infant_count || 0,
    contact_name: props.booking?.contact_name || '',
    contact_email: props.booking?.contact_email || '',
    contact_phone: props.booking?.contact_phone || '',
    whatsapp: props.booking?.whatsapp || '',
    contact_address: props.booking?.contact_address || '',
    emergency_contact: props.booking?.emergency_contact || '',
    emergency_phone: props.booking?.emergency_phone || '',
    special_requests: props.booking?.special_requests || '',
    dietary_requirements: props.booking?.dietary_requirements || '',
    medical_conditions: props.booking?.medical_conditions || '',
    subtotal: props.booking?.subtotal || 0,
    extras_total: props.booking?.extras_total || 0,
    discount_amount: props.booking?.discount_amount || 0,
    discount_code: props.booking?.discount_code || '',
    tax_amount: props.booking?.tax_amount || 0,
    total_amount: props.booking?.total_amount || 0,
    deposit_amount: props.booking?.deposit_amount || 0,
    deposit_paid: props.booking?.deposit_paid || 0,
    amount_paid: props.booking?.amount_paid || 0,
    currency: props.booking?.currency || 'BDT',
    booking_source: props.booking?.booking_source || 'admin',
    internal_notes: props.booking?.internal_notes || '',
    travelers: props.booking?.travelers || [],
})

// Options
const tourOptions = computed(() => 
    props.tours?.map(tour => ({ 
        value: tour.id || tour.value, 
        label: tour.name || tour.label 
    })) || []
)

const customerOptions = computed(() => 
    props.customers?.map(customer => ({ 
        value: customer.id, 
        label: `${customer.name} (${customer.email})` 
    })) || []
)

const agentOptions = computed(() => 
    props.agents?.map(agent => ({ 
        value: agent.id, 
        label: `${agent.name} (${agent.email})` 
    })) || []
)

const selectedTour = computed(() => {
    if (!form.tour_id) return null
    return props.tours?.find(tour => (tour.id || tour.value) === form.tour_id)
})

const pricingOptions = computed(() => {
    if (!selectedTour.value) return []
    const pricings = selectedTour.value.pricings || []
    return pricings.map(p => ({
        id: p.id || p.value,
        name: p.name || p.label,
        adult_price: p.adult_price || 0,
        child_price: p.child_price || 0,
        infant_price: p.infant_price || 0,
    }))
})

const bookingSourceOptions = [
    { value: 'website', label: 'Website' },
    { value: 'admin', label: 'Admin Panel' },
    { value: 'agent', label: 'Agent' },
    { value: 'api', label: 'API' },
]

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'refunded', label: 'Refunded' },
]

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'partial', label: 'Partial' },
    { value: 'paid', label: 'Paid' },
    { value: 'refunded', label: 'Refunded' },
]

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
]

const currencyOptions = [
    { value: 'BDT', label: 'BDT (৳)' },
    { value: 'USD', label: 'USD ($)' },
    { value: 'EUR', label: 'EUR (€)' },
]

// Computed
const totalTravelers = computed(() => 
    form.adult_count + form.child_count + form.infant_count
)

const selectedPricing = computed(() => 
    pricingOptions.value.find(p => p.id === form.pricing_id)
)

const calculatedSubtotal = computed(() => {
    if (!selectedPricing.value) return 0
    
    const adultTotal = form.adult_count * (selectedPricing.value.adult_price || 0)
    const childTotal = form.child_count * (selectedPricing.value.child_price || 0)
    const infantTotal = form.infant_count * (selectedPricing.value.infant_price || 0)
    
    return adultTotal + childTotal + infantTotal
})

const calculatedTotal = computed(() => {
    return calculatedSubtotal.value + 
           form.extras_total - 
           form.discount_amount + 
           form.tax_amount
})

// Methods
const formatNumber = (num) => new Intl.NumberFormat('en-BD').format(num || 0)

const getTravelerTypeBadge = (type) => {
    const badges = {
        adult: 'bg-blue-100 text-blue-700',
        child: 'bg-amber-100 text-amber-700',
        infant: 'bg-green-100 text-green-700',
    }
    return badges[type] || 'bg-slate-100 text-slate-700'
}

const onTourChange = () => {
    // Reset pricing when tour changes
    form.pricing_id = null
    
    // Auto-select first pricing if available
    if (pricingOptions.value.length > 0) {
        form.pricing_id = pricingOptions.value[0].id
    }
    
    // Reset calculated values
    form.subtotal = 0
    form.total_amount = 0
}

const updateTravelerForms = () => {
    const newTravelers = []
    
    // Add adults
    for (let i = 0; i < form.adult_count; i++) {
        newTravelers.push(form.travelers[i] || createTraveler('adult', i === 0))
    }
    
    // Add children
    for (let i = 0; i < form.child_count; i++) {
        const index = form.adult_count + i
        newTravelers.push(form.travelers[index] || createTraveler('child', false))
    }
    
    // Add infants
    for (let i = 0; i < form.infant_count; i++) {
        const index = form.adult_count + form.child_count + i
        newTravelers.push(form.travelers[index] || createTraveler('infant', false))
    }
    
    form.travelers = newTravelers
}

const createTraveler = (type, isLead = false) => ({
    type,
    is_lead: isLead,
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    date_of_birth: '',
    gender: '',
    nationality: '',
    passport_number: '',
    passport_expiry: '',
    passport_country: '',
    nid_number: '',
    dietary_requirements: '',
    medical_conditions: '',
    room_preference: '',
    special_requests: '',
})

const updateLeadTraveler = (index) => {
    if (form.travelers[index].is_lead) {
        form.travelers.forEach((traveler, i) => {
            if (i !== index) {
                traveler.is_lead = false
            }
        })
    }
}

const submit = () => {
    // Update calculated values
    form.subtotal = calculatedSubtotal.value
    form.total_amount = calculatedTotal.value
    form.amount_due = calculatedTotal.value - form.amount_paid
    
    if (isEditing.value) {
        form.put(route('tourBooking.update', props.booking.id))
    } else {
        form.post(route('tourBooking.store'))
    }
}

// Initialize travelers on mount
if (!isEditing.value || form.travelers.length === 0) {
    updateTravelerForms()
}

// Watch for pricing changes to update subtotal
watch(() => form.pricing_id, () => {
    form.subtotal = calculatedSubtotal.value
})
</script>
