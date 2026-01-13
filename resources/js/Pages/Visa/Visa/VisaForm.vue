<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('visa.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm">
        <!-- Progress Tabs -->
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

        <!-- Basic Info -->
        <div v-show="activeTab === 'basic'">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <AppCard class="mb-4">
                        <template #title> Basic Information </template>
                        <template #content>
                            <AppFormErrors class="mb-4" />
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <AppLabel for="name">Visa Name <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="e.g. US Tourist Visa B1/B2"
                                        @input="generateSlug"
                                        :class="{ 'input-error': errorsFields.includes('name') }"
                                    />
                                </div>
                                
                                <div>
                                    <AppLabel for="country_id">Country <span class="text-red-500">*</span></AppLabel>
                                    <AppCombobox
                                        v-model="form.country_id"
                                        :options="countries"
                                        combo-label="Select Country"
                                        emit-value
                                        :class="{ 'input-error': errorsFields.includes('country_id') }"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="category_id">Category</AppLabel>
                                    <AppCombobox
                                        v-model="form.category_id"
                                        :options="categories"
                                        combo-label="Select Category"
                                        emit-value
                                    />
                                </div>

                                <div>
                                    <AppLabel for="slug">Slug <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        :class="{ 'input-error': errorsFields.includes('slug') }"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="code">Visa Code</AppLabel>
                                    <AppInputText
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        placeholder="Internal code e.g. US-B1"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard class="mb-4">
                        <template #title> Description </template>
                        <template #content>
                            <div class="space-y-4">
                                <div>
                                    <AppLabel for="excerpt">Short Excerpt</AppLabel>
                                    <AppTextArea
                                        id="excerpt"
                                        v-model="form.excerpt"
                                        rows="2"
                                        placeholder="Brief summary for listings..."
                                    />
                                </div>
                                <div>
                                    <AppLabel for="description">Full Description</AppLabel>
                                    <AppRichTextEditor
                                        id="description"
                                        v-model="form.description"
                                        height="300px"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>
                </div>
                
                <div class="lg:col-span-1">
                     <AppCard class="mb-4">
                        <template #title> Settings </template>
                        <template #content>
                            <div class="flex flex-col gap-4">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="h-4 w-4 rounded text-primary-600"
                                    />
                                    <span class="text-neutral-12 font-medium">Active</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_featured"
                                        class="h-4 w-4 rounded text-amber-500"
                                    />
                                    <span class="text-neutral-12 font-medium">Featured</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_popular"
                                        class="h-4 w-4 rounded text-purple-500"
                                    />
                                    <span class="text-neutral-12 font-medium">Popular</span>
                                </label>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard>
                        <template #title> Thumbnail </template>
                        <template #content>
                             <AppImage
                                v-model="form.thumbnail"
                                :image-url="imageUrl"
                                label="Visa Thumbnail"
                            />
                        </template>
                    </AppCard>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div v-show="activeTab === 'details'">
            <AppCard class="mb-4">
                <template #title> Visa Specifics </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                         <div>
                            <AppLabel for="entry_type">Entry Type</AppLabel>
                            <AppCombobox
                                v-model="form.entry_type"
                                :options="entryTypeOptions"
                                combo-label="Select Entry Type"
                                emit-value
                            />
                        </div>
                        <div>
                            <AppLabel for="validity_days">Validity (Days)</AppLabel>
                            <AppInputText
                                id="validity_days"
                                v-model="form.validity_days"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="stay_days">Max Stay (Days)</AppLabel>
                            <AppInputText
                                id="stay_days"
                                v-model="form.stay_days"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                             <AppLabel>Processing Time (Days)</AppLabel>
                             <div class="flex items-center gap-2">
                                <AppInputText
                                    v-model="form.processing_days_min"
                                    type="number"
                                    min="0"
                                    placeholder="Min"
                                />
                                <span class="text-neutral-9">-</span>
                                <AppInputText
                                    v-model="form.processing_days_max"
                                    type="number"
                                    min="0"
                                    placeholder="Max"
                                />
                             </div>
                        </div>
                    </div>
                </template>
            </AppCard>

             <AppCard class="mb-4">
                <template #title> Requirements & Rules </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_express_available" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Express Service Available</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_interview_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Interview Required</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_biometric_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Biometric Required</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_appointment_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Appointment Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_travel_insurance_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Travel Insurance Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_bank_statement_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Bank Statement Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_invitation_letter_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Invitation Letter Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_hotel_booking_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Hotel Booking Required</span>
                        </label>
                    </div>
                </template>
            </AppCard>
            
            <AppCard>
                <template #title> Criteria & Terms </template>
                <template #content>
                     <div class="space-y-4">
                        <div>
                            <AppLabel for="eligibility_criteria">Eligibility Criteria</AppLabel>
                            <AppTextArea
                                id="eligibility_criteria"
                                v-model="form.eligibility_criteria"
                                rows="4"
                            />
                        </div>
                        <div>
                            <AppLabel for="terms_conditions">Terms & Conditions</AppLabel>
                            <AppTextArea
                                id="terms_conditions"
                                v-model="form.terms_conditions"
                                rows="4"
                            />
                        </div>
                         <div>
                            <AppLabel for="important_notes">Important Notes</AppLabel>
                            <AppTextArea
                                id="important_notes"
                                v-model="form.important_notes"
                                rows="3"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Pricing -->
        <div v-show="activeTab === 'pricing'">
            <AppCard>
                <template #title> Pricing Breakdown </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="base_price">Base Price <span class="text-red-500">*</span></AppLabel>
                            <AppInputText
                                id="base_price"
                                v-model="form.base_price"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="service_fee">Service Fee</AppLabel>
                            <AppInputText
                                id="service_fee"
                                v-model="form.service_fee"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="embassy_fee">Embassy Fee</AppLabel>
                            <AppInputText
                                id="embassy_fee"
                                v-model="form.embassy_fee"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="vfs_fee">VFS Fee</AppLabel>
                            <AppInputText
                                id="vfs_fee"
                                v-model="form.vfs_fee"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                    </div>
                    
                    <div class="bg-neutral-2 border-neutral-4 mt-6 flex justify-between rounded-lg border p-4">
                        <span class="text-neutral-12 font-medium">Total Cost:</span>
                        <span class="font-bold text-emerald-600 text-xl">
                            ৳{{ (Number(form.base_price || 0) + Number(form.service_fee || 0) + Number(form.embassy_fee || 0) + Number(form.vfs_fee || 0)).toFixed(2) }}
                        </span>
                    </div>
                </template>
            </AppCard>
        </div>
        
        <!-- Services -->
        <div v-show="activeTab === 'services'">
             <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <AppCard>
                    <template #title> Included Services </template>
                    <template #content>
                         <div class="space-y-2">
                            <div v-for="(item, index) in form.included_services" :key="index" class="flex gap-2">
                                <AppInputText
                                    v-model="form.included_services[index]"
                                    placeholder="Service included"
                                />
                                <AppButton type="button" class="btn btn-icon btn-destructive" size="sm" @click="removeIncluded(index)">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </div>
                            <AppButton type="button" class="btn btn-sm btn-secondary w-full" @click="addIncluded">
                                <i class="ri-add-line mr-1"></i> Add Service
                            </AppButton>
                        </div>
                    </template>
                </AppCard>
                
                <AppCard>
                    <template #title> Excluded Services </template>
                    <template #content>
                         <div class="space-y-2">
                            <div v-for="(item, index) in form.excluded_services" :key="index" class="flex gap-2">
                                <AppInputText
                                    v-model="form.excluded_services[index]"
                                    placeholder="Service excluded"
                                />
                                <AppButton type="button" class="btn btn-icon btn-destructive" size="sm" @click="removeExcluded(index)">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </div>
                            <AppButton type="button" class="btn btn-sm btn-secondary w-full" @click="addExcluded">
                                <i class="ri-add-line mr-1"></i> Add Exclusion
                            </AppButton>
                        </div>
                    </template>
                </AppCard>
             </div>
        </div>

        <!-- SEO -->
        <div v-show="activeTab === 'seo'">
            <AppCard>
                <template #title> SEO Configuration </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="meta_title">Meta Title</AppLabel>
                            <AppInputText
                                id="meta_title"
                                v-model="form.meta_title"
                                type="text"
                            />
                        </div>
                        <div>
                            <AppLabel for="meta_description">Meta Description</AppLabel>
                            <AppTextArea
                                id="meta_description"
                                v-model="form.meta_description"
                                rows="3"
                            />
                        </div>
                        <div>
                            <AppLabel for="meta_keywords">Meta Keywords</AppLabel>
                            <AppInputText
                                id="meta_keywords"
                                v-model="form.meta_keywords"
                                type="text"
                                placeholder="comma, separated, keywords"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <div class="mt-6 flex justify-end">
            <AppButton class="btn btn-primary" :disabled="form.processing" type="submit">
                <i class="ri-save-line mr-1"></i>
                {{ isCreate ? 'Create Visa' : 'Update Visa' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import AppRichTextEditor from '@/Components/Form/AppRichTextEditor.vue'

const { isCreate, isEdit } = useFormContext()
const { title } = useTitle(isCreate.value ? 'Create Visa' : 'Edit Visa')

const props = defineProps({
    visa: { type: Object, default: null },
    countries: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visas', href: route('visa.index') },
    { label: title.value, last: true }
]

const form = useForm({
    name: props.visa?.name ?? '',
    country_id: props.visa?.country_id ?? null,
    category_id: props.visa?.category_id ?? null,
    slug: props.visa?.slug ?? '',
    code: props.visa?.code ?? '',
    excerpt: props.visa?.excerpt ?? '',
    description: props.visa?.description ?? '',
    
    entry_type: props.visa?.entry_type ?? 'single',
    validity_days: props.visa?.validity_days ?? null,
    stay_days: props.visa?.stay_days ?? null,
    processing_days_min: props.visa?.processing_days_min ?? null,
    processing_days_max: props.visa?.processing_days_max ?? null,
    
    base_price: props.visa?.base_price ?? 0,
    service_fee: props.visa?.service_fee ?? 0,
    embassy_fee: props.visa?.embassy_fee ?? 0,
    vfs_fee: props.visa?.vfs_fee ?? 0,
    
    is_express_available: props.visa?.is_express_available ?? false,
    is_interview_required: props.visa?.is_interview_required ?? false,
    is_biometric_required: props.visa?.is_biometric_required ?? false,
    is_appointment_required: props.visa?.is_appointment_required ?? false,
    is_travel_insurance_required: props.visa?.is_travel_insurance_required ?? false,
    is_bank_statement_required: props.visa?.is_bank_statement_required ?? true,
    is_invitation_letter_required: props.visa?.is_invitation_letter_required ?? false,
    is_hotel_booking_required: props.visa?.is_hotel_booking_required ?? false,
    
    eligibility_criteria: props.visa?.eligibility_criteria ?? '',
    terms_conditions: props.visa?.terms_conditions ?? '',
    important_notes: props.visa?.important_notes ?? '',
    
    included_services: props.visa?.included_services ?? [],
    excluded_services: props.visa?.excluded_services ?? [],
    
    is_active: props.visa?.is_active ?? true,
    is_featured: props.visa?.is_featured ?? false,
    is_popular: props.visa?.is_popular ?? false,
    
    meta_title: props.visa?.meta_title ?? '',
    meta_description: props.visa?.meta_description ?? '',
    meta_keywords: props.visa?.meta_keywords ?? '',
    
    thumbnail: null,
    _method: isEdit.value ? 'PUT' : 'POST'
})

const imageUrl = computed(() => props.visa?.thumbnail_url)
const { errorsFields } = useFormErrors()

const activeTab = ref('basic')

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-information-line' },
    { id: 'details', label: 'Details', icon: 'ri-file-text-line' },
    { id: 'pricing', label: 'Pricing', icon: 'ri-money-dollar-circle-line' },
    { id: 'services', label: 'Services', icon: 'ri-list-check' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-line' }
]

const entryTypeOptions = [
    { value: 'single', label: 'Single Entry' },
    { value: 'double', label: 'Double Entry' },
    { value: 'multiple', label: 'Multiple Entry' }
]

const generateSlug = () => {
    if (isCreate.value && form.name) {
        form.slug = form.name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')
    }
}

const addIncluded = () => form.included_services.push('')
const removeIncluded = (index) => form.included_services.splice(index, 1)

const addExcluded = () => form.excluded_services.push('')
const removeExcluded = (index) => form.excluded_services.splice(index, 1)

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visa.store'))
    } else {
        form.post(route('visa.update', props.visa.id))
    }
}
</script>
