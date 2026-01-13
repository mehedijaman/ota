<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('visaApplication.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm">
        <!-- Tabs -->
        <div class="border-neutral-6 bg-neutral-1 mb-6 overflow-hidden rounded-xl border shadow-sm">
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
                                ? 'bg-gradient-to-r from-blue-600 to-teal-600 text-white shadow-lg shadow-blue-500/25'
                                : 'bg-neutral-3 text-neutral-11 hover:bg-neutral-4 hover:text-neutral-12'
                        ]"
                    >
                        <i :class="[tab.icon, activeTab === tab.id ? 'text-white' : 'text-neutral-9']"></i>
                        <span class="hidden sm:inline">{{ tab.label }}</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Application Info -->
        <div v-show="activeTab === 'info'">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <AppCard class="mb-4">
                        <template #title> Application Details </template>
                        <template #content>
                            <AppFormErrors class="mb-4" />
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <AppLabel for="visa_id">Visa Package <span class="text-red-500">*</span></AppLabel>
                                    <AppCombobox
                                        v-model="form.visa_id"
                                        :options="visas"
                                        combo-label="Select Visa"
                                        :class="{ 'input-error': errorsFields.includes('visa_id') }"
                                        emit-value
                                        @update:modelValue="onVisaChange"
                                    />
                                </div>
                                
                                <div>
                                    <AppLabel for="customer_id">Customer (Optional)</AppLabel>
                                    <AppCombobox
                                        v-model="form.customer_id"
                                        :options="customers"
                                        combo-label="Select Customer"
                                        emit-value
                                        @update:modelValue="onCustomerChange"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="agent_id">Agent (Optional)</AppLabel>
                                    <AppCombobox
                                        v-model="form.agent_id"
                                        :options="agents"
                                        combo-label="Select Agent"
                                        emit-value
                                    />
                                </div>

                                <div>
                                    <AppLabel for="intended_travel_date">Intended Travel Date</AppLabel>
                                    <AppInputText
                                        id="intended_travel_date"
                                        v-model="form.intended_travel_date"
                                        type="date"
                                    />
                                </div>
                                
                                <div>
                                    <AppLabel for="intended_return_date">Intended Return Date</AppLabel>
                                    <AppInputText
                                        id="intended_return_date"
                                        v-model="form.intended_return_date"
                                        type="date"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <AppLabel for="travel_purpose">Purpose of Travel</AppLabel>
                                    <AppInputText
                                        id="travel_purpose"
                                        v-model="form.travel_purpose"
                                        type="text"
                                        placeholder="e.g. Tourism, Business Meeting"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard class="mb-4">
                        <template #title> Contact Information </template>
                        <template #content>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <AppLabel for="contact_name">Contact Name <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="contact_name"
                                        v-model="form.contact_name"
                                        type="text"
                                        :class="{ 'input-error': errorsFields.includes('contact_name') }"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="contact_email">Email <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="contact_email"
                                        v-model="form.contact_email"
                                        type="email"
                                        :class="{ 'input-error': errorsFields.includes('contact_email') }"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="contact_phone">Phone <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="contact_phone"
                                        v-model="form.contact_phone"
                                        type="text"
                                        :class="{ 'input-error': errorsFields.includes('contact_phone') }"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="whatsapp">WhatsApp</AppLabel>
                                    <AppInputText
                                        id="whatsapp"
                                        v-model="form.whatsapp"
                                        type="text"
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <AppLabel for="contact_address">Address</AppLabel>
                                    <AppInputText
                                        id="contact_address"
                                        v-model="form.contact_address"
                                        type="text"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>
                </div>

                <div class="lg:col-span-1">
                    <AppCard class="mb-4">
                        <template #title> Traveler Counts </template>
                        <template #content>
                            <div class="space-y-4">
                                <div>
                                    <AppLabel for="adult_count">Adults <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="adult_count"
                                        v-model="form.adult_count"
                                        type="number"
                                        min="1"
                                        @input="updateTravelers('adult')"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="child_count">Children</AppLabel>
                                    <AppInputText
                                        id="child_count"
                                        v-model="form.child_count"
                                        type="number"
                                        min="0"
                                        @input="updateTravelers('child')"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="infant_count">Infants</AppLabel>
                                    <AppInputText
                                        id="infant_count"
                                        v-model="form.infant_count"
                                        type="number"
                                        min="0"
                                        @input="updateTravelers('infant')"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard v-if="isEdit">
                         <template #title> Status </template>
                         <template #content>
                             <div class="space-y-4">
                                <div>
                                    <AppLabel for="status">Application Status</AppLabel>
                                    <AppCombobox
                                        v-model="form.status"
                                        :options="statusOptions"
                                        combo-label="Select Status"
                                        emit-value
                                    />
                                </div>
                                <div>
                                    <AppLabel for="status_note">Status Note</AppLabel>
                                    <AppTextArea
                                        id="status_note"
                                        v-model="form.status_note"
                                        rows="3"
                                        placeholder="Reason for status change..."
                                    />
                                </div>
                            </div>
                         </template>
                    </AppCard>
                </div>
            </div>
        </div>

        <!-- Travelers -->
        <div v-show="activeTab === 'travelers'">
            <div class="space-y-6">
                <div v-if="form.travelers.length === 0" class="text-center py-8 bg-neutral-1 rounded-xl border border-neutral-6 border-dashed">
                     <p class="text-neutral-9">Please set traveler counts in Application Info tab to generate traveler forms.</p>
                </div>

                <div v-for="(traveler, index) in form.travelers" :key="index">
                    <AppCard>
                        <template #title>
                            <div class="flex items-center justify-between">
                                <span class="text-primary-700 font-bold">
                                    Traveler {{ index + 1 }} ({{ capitalize(traveler.type) }})
                                </span>
                                <AppButton type="button" class="btn btn-icon btn-outline-destructive btn-sm" @click="removeTraveler(index)">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </div>
                        </template>
                        <template #content>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <AppLabel>First Name <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText v-model="traveler.first_name" :class="{'input-error': errorsFields.includes(`travelers.${index}.first_name`)}" />
                                </div>
                                <div>
                                    <AppLabel>Last Name <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText v-model="traveler.last_name" :class="{'input-error': errorsFields.includes(`travelers.${index}.last_name`)}" />
                                </div>
                                <div>
                                    <AppLabel>Date of Birth <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText type="date" v-model="traveler.date_of_birth" :class="{'input-error': errorsFields.includes(`travelers.${index}.date_of_birth`)}" />
                                </div>
                                <div>
                                    <AppLabel>Gender <span class="text-red-500">*</span></AppLabel>
                                    <AppCombobox v-model="traveler.gender" :options="genderOptions" combo-label="Select" emit-value :class="{'input-error': errorsFields.includes(`travelers.${index}.gender`)}" />
                                </div>
                                <div>
                                    <AppLabel>Nationality <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText v-model="traveler.nationality" :class="{'input-error': errorsFields.includes(`travelers.${index}.nationality`)}" />
                                </div>
                                <div>
                                    <AppLabel>Passport Number <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText v-model="traveler.passport_number" :class="{'input-error': errorsFields.includes(`travelers.${index}.passport_number`)}" />
                                </div>
                                <div>
                                    <AppLabel>Issue Date <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText type="date" v-model="traveler.passport_issue_date" :class="{'input-error': errorsFields.includes(`travelers.${index}.passport_issue_date`)}" />
                                </div>
                                <div>
                                    <AppLabel>Expiry Date <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText type="date" v-model="traveler.passport_expiry_date" :class="{'input-error': errorsFields.includes(`travelers.${index}.passport_expiry_date`)}" />
                                </div>
                                 <div>
                                    <AppLabel>Type</AppLabel>
                                    <AppCombobox v-model="traveler.type" :options="typeOptions" combo-label="Select Type" emit-value />
                                </div>
                            </div>
                        </template>
                    </AppCard>
                </div>
                
                <div class="flex justify-center">
                    <AppButton type="button" class="btn btn-secondary" @click="addTraveler">
                        <i class="ri-add-line mr-1"></i> Add Manual Traveler
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- Fees -->
        <div v-show="activeTab === 'fees'">
            <AppCard>
                <template #title> Fees & Discounts </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="express_fees">Express Fees</AppLabel>
                            <AppInputText
                                id="express_fees"
                                v-model="form.express_fees"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="additional_fees">Additional Fees</AppLabel>
                            <AppInputText
                                id="additional_fees"
                                v-model="form.additional_fees"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="discount_amount">Discount Amount</AppLabel>
                            <AppInputText
                                id="discount_amount"
                                v-model="form.discount_amount"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="coupon_code">Coupon Code</AppLabel>
                            <AppInputText
                                id="coupon_code"
                                v-model="form.coupon_code"
                                type="text"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <div class="mt-6 flex justify-end">
            <AppButton class="btn btn-primary" :disabled="form.processing" type="submit">
                <i class="ri-save-line mr-1"></i>
                {{ isCreate ? 'Submit Application' : 'Update Application' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppCombobox from '@/Components/Form/AppCombobox.vue'

const { isCreate, isEdit } = useFormContext()
const { title } = useTitle(isCreate.value ? 'New Visa Application' : 'Edit Application')

const props = defineProps({
    application: { type: Object, default: null },
    visas: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    agents: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visa Applications', href: route('visaApplication.index') },
    { label: title.value, last: true }
]

const form = useForm({
    visa_id: props.application?.visa_id ?? null,
    customer_id: props.application?.customer_id ?? null,
    agent_id: props.application?.agent_id ?? null,
    
    adult_count: props.application?.adult_count ?? 1,
    child_count: props.application?.child_count ?? 0,
    infant_count: props.application?.infant_count ?? 0,
    
    intended_travel_date: props.application?.intended_travel_date ?? '',
    intended_return_date: props.application?.intended_return_date ?? '',
    travel_purpose: props.application?.travel_purpose ?? '',
    
    contact_name: props.application?.contact_name ?? '',
    contact_email: props.application?.contact_email ?? '',
    contact_phone: props.application?.contact_phone ?? '',
    whatsapp: props.application?.whatsapp ?? '',
    contact_address: props.application?.contact_address ?? '',
    
    express_fees: props.application?.express_fees ?? 0,
    additional_fees: props.application?.additional_fees ?? 0,
    discount_amount: props.application?.discount_amount ?? 0,
    coupon_code: props.application?.coupon_code ?? '',
    
    status: props.application?.status ?? 'pending',
    status_note: props.application?.status_note ?? '',
    
    travelers: props.application?.travelers ?? [
        { first_name: '', last_name: '', date_of_birth: '', gender: 'male', nationality: '', passport_number: '', passport_issue_date: '', passport_expiry_date: '', type: 'adult' }
    ],
    
    _method: isEdit.value ? 'PUT' : 'POST'
})

const { errorsFields } = useFormErrors()

const activeTab = ref('info')

const tabs = [
    { id: 'info', label: 'Application Info', icon: 'ri-file-info-line' },
    { id: 'travelers', label: 'Travelers', icon: 'ri-group-line' },
    { id: 'fees', label: 'Fees & Payment', icon: 'ri-money-dollar-circle-line' }
]

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' }
]

const typeOptions = [
    { value: 'adult', label: 'Adult' },
    { value: 'child', label: 'Child' },
    { value: 'infant', label: 'Infant' }
]

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'pending', label: 'Pending' },
    { value: 'documents_pending', label: 'Documents Pending' },
    { value: 'documents_submitted', label: 'Documents Submitted' },
    { value: 'under_review', label: 'Under Review' },
    { value: 'processing', label: 'Processing' },
    { value: 'submitted_to_embassy', label: 'Submitted to Embassy' },
    { value: 'interview_scheduled', label: 'Interview Scheduled' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
]

const onCustomerChange = (id) => {
    const customer = props.customers.find(c => c.id === id)
    if (customer) {
        form.contact_name = customer.name || ''
        form.contact_email = customer.email || ''
        form.contact_phone = customer.phone || ''
        form.whatsapp = customer.whatsapp || ''
        form.contact_address = customer.address || ''
    }
}

const onVisaChange = (id) => {
    // Optionally fetch visa details if needed, but not strictly required for form
}

const updateTravelers = (type) => {
    // Logic to sync travelers array with counts could go here
    // But for now we'll rely on manual adding/removing to respect existing data
    // Or we could auto-expand. Let's keep it simple: Add Manual Traveler button.
    // If we wanted auto-sync:
    /*
    const currentCount = form.travelers.filter(t => t.type === type).length
    const targetCount = type === 'adult' ? form.adult_count : (type === 'child' ? form.child_count : form.infant_count)
    if (targetCount > currentCount) {
        for (let i = 0; i < targetCount - currentCount; i++) {
             form.travelers.push({ type: type, ...emptyTraveler })
        }
    }
    */
}

const addTraveler = () => {
    form.travelers.push({ 
        first_name: '', last_name: '', date_of_birth: '', gender: 'male', 
        nationality: '', passport_number: '', passport_issue_date: '', 
        passport_expiry_date: '', type: 'adult' 
    })
    form.adult_count = form.travelers.filter(t => t.type === 'adult').length
    form.child_count = form.travelers.filter(t => t.type === 'child').length
    form.infant_count = form.travelers.filter(t => t.type === 'infant').length
}

const removeTraveler = (index) => {
    form.travelers.splice(index, 1)
    form.adult_count = form.travelers.filter(t => t.type === 'adult').length
    form.child_count = form.travelers.filter(t => t.type === 'child').length
    form.infant_count = form.travelers.filter(t => t.type === 'infant').length
}

const capitalize = (s) => s && s[0].toUpperCase() + s.slice(1)

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visaApplication.store'))
    } else {
        form.post(route('visaApplication.update', props.application.id))
    }
}
</script>
