<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('visa-view')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visa.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>

                <AppButton
                    v-if="can('visa-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visa.edit', visa.id))"
                >
                    <i class="ri-edit-line mr-1"></i>
                    Edit
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Hero Section -->
    <div class="relative mb-6 overflow-hidden rounded-2xl shadow-xl">
        <div class="bg-gradient-to-br absolute inset-0 from-blue-600 via-indigo-600 to-purple-600"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px); background-size: 60px 60px;"></div>

        <div class="relative z-10 p-8">
            <div class="flex flex-col items-center gap-8 md:flex-row">
                <!-- Thumbnail -->
                <div class="relative">
                    <div class="absolute -inset-2 rounded-2xl bg-white/20 blur-lg"></div>
                    <div
                        v-if="visa.thumbnail_url"
                        class="relative h-48 w-48 overflow-hidden rounded-2xl border-4 border-white/40 shadow-2xl"
                    >
                        <img :src="visa.thumbnail_url" :alt="visa.name" class="h-full w-full object-cover" />
                    </div>
                    <div
                        v-else
                        class="relative flex h-48 w-48 items-center justify-center rounded-2xl border-4 border-white/40 bg-white/20 text-6xl text-white shadow-2xl backdrop-blur-sm"
                    >
                        <i class="ri-passport-line"></i>
                    </div>
                </div>

                <!-- Visa Info -->
                <div class="flex-1 text-center text-white md:text-left">
                    <div class="mb-2 flex flex-wrap items-center justify-center gap-2 md:justify-start">
                        <span v-if="visa.is_featured" class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            <i class="ri-star-fill mr-1"></i>Featured
                        </span>
                        <span v-if="visa.is_popular" class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            <i class="ri-fire-fill mr-1"></i>Popular
                        </span>
                        <span :class="visa.is_active ? 'bg-emerald-400/30' : 'bg-red-400/30'" class="rounded-full px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            {{ visa.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight md:text-4xl">{{ visa.name }}</h1>
                    
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3 md:justify-start">
                         <span v-if="visa.country" class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-flag-line text-indigo-200"></i>
                            {{ visa.country.name }}
                        </span>
                        <span v-if="visa.category" class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-folder-line text-indigo-200"></i>
                            {{ visa.category.name }}
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-barcode-line text-indigo-200"></i>
                            <span class="font-mono font-medium">{{ visa.code || '-' }}</span>
                        </span>
                         <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-door-open-line text-indigo-200"></i>
                            <span class="capitalize">{{ visa.entry_type }} Entry</span>
                        </span>
                    </div>
                </div>

                <!-- Price Card -->
                <div class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm min-w-[200px]">
                    <p class="text-xs font-medium uppercase tracking-wider text-indigo-200">Total Price</p>
                    <p class="text-3xl font-bold text-white">৳{{ formatPrice(totalPrice) }}</p>
                    <p class="text-indigo-200 text-sm">Processing: {{ visa.processing_days_min }}-{{ visa.processing_days_max }} Days</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Description -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br flex h-10 w-10 items-center justify-center rounded-xl from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/25">
                            <i class="ri-file-text-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Description</span>
                    </div>
                </div>
                <div class="p-5">
                    <p v-if="visa.excerpt" class="text-neutral-11 mb-4 font-medium italic border-l-4 border-indigo-500 pl-4 py-1 bg-neutral-2 rounded-r-lg">{{ visa.excerpt }}</p>
                    <div v-if="visa.description" class="prose prose-neutral max-w-none" v-html="visa.description"></div>
                    <p v-else class="text-neutral-9 py-4 text-center">No description available</p>
                </div>
            </div>

            <!-- Eligibility & Terms -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 shadow-lg">
                                <i class="ri-shield-check-line text-lg text-emerald-600"></i>
                            </div>
                            <span class="text-neutral-12 text-lg font-semibold">Eligibility</span>
                        </div>
                    </div>
                    <div class="p-5">
                         <div v-if="visa.eligibility_criteria" class="prose prose-sm prose-neutral" v-html="visa.eligibility_criteria"></div>
                         <p v-else class="text-neutral-9 text-sm">No criteria specified.</p>
                    </div>
                </div>

                <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 shadow-lg">
                                <i class="ri-file-list-line text-lg text-amber-600"></i>
                            </div>
                            <span class="text-neutral-12 text-lg font-semibold">Terms & Conditions</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div v-if="visa.terms_conditions" class="prose prose-sm prose-neutral" v-html="visa.terms_conditions"></div>
                        <p v-else class="text-neutral-9 text-sm">No terms specified.</p>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="grid gap-6 md:grid-cols-2">
                 <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                                <i class="ri-check-line text-emerald-600"></i>
                            </div>
                            <span class="text-neutral-12 font-semibold">Included Services</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul v-if="visa.included_services?.length" class="space-y-2">
                            <li v-for="(item, idx) in visa.included_services" :key="idx" class="flex items-start gap-2 text-sm">
                                <i class="ri-checkbox-circle-fill mt-0.5 text-emerald-500"></i>
                                <span class="text-neutral-11">{{ item }}</span>
                            </li>
                        </ul>
                         <p v-else class="text-neutral-9 text-sm">No included services listed.</p>
                    </div>
                </div>
                
                 <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                                <i class="ri-close-line text-red-600"></i>
                            </div>
                            <span class="text-neutral-12 font-semibold">Excluded Services</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul v-if="visa.excluded_services?.length" class="space-y-2">
                            <li v-for="(item, idx) in visa.excluded_services" :key="idx" class="flex items-start gap-2 text-sm">
                                <i class="ri-close-circle-fill mt-0.5 text-red-500"></i>
                                <span class="text-neutral-11">{{ item }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-neutral-9 text-sm">No excluded services listed.</p>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Right Column -->
        <div class="space-y-6 lg:col-span-1">
             <!-- Requirements -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br flex h-10 w-10 items-center justify-center rounded-xl from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-list-check text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Requirements</span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-neutral-3">
                        <span class="text-neutral-11 text-sm">Validity Days</span>
                        <span class="font-medium">{{ visa.validity_days || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-neutral-3">
                        <span class="text-neutral-11 text-sm">Max Stay</span>
                        <span class="font-medium">{{ visa.stay_days || '-' }}</span>
                    </div>
                    
                    <div v-for="(req, key) in requirementsList" :key="key" class="flex justify-between items-center py-2 border-b border-neutral-3 last:border-0">
                        <span class="text-neutral-11 text-sm">{{ req.label }}</span>
                        <span v-if="visa[req.key]" class="text-emerald-600 text-sm font-medium"><i class="ri-check-line"></i> Required</span>
                        <span v-else class="text-neutral-9 text-sm">Optional / No</span>
                    </div>
                </div>
            </div>

            <!-- Pricing Breakdown -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                 <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br flex h-10 w-10 items-center justify-center rounded-xl from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-money-dollar-circle-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Cost Breakdown</span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-11">Base Price</span>
                        <span class="font-medium">৳{{ formatPrice(visa.base_price) }}</span>
                    </div>
                    <div v-if="visa.service_fee > 0" class="flex justify-between items-center">
                        <span class="text-neutral-11">Service Fee</span>
                        <span class="font-medium">৳{{ formatPrice(visa.service_fee) }}</span>
                    </div>
                    <div v-if="visa.embassy_fee > 0" class="flex justify-between items-center">
                        <span class="text-neutral-11">Embassy Fee</span>
                        <span class="font-medium">৳{{ formatPrice(visa.embassy_fee) }}</span>
                    </div>
                     <div v-if="visa.vfs_fee > 0" class="flex justify-between items-center">
                        <span class="text-neutral-11">VFS Fee</span>
                        <span class="font-medium">৳{{ formatPrice(visa.vfs_fee) }}</span>
                    </div>
                    <div class="border-t border-neutral-4 pt-3 flex justify-between items-center">
                        <span class="text-neutral-12 font-bold">Total</span>
                        <span class="text-emerald-600 font-bold text-lg">৳{{ formatPrice(totalPrice) }}</span>
                    </div>
                </div>
            </div>
            
             <!-- SEO Information -->
            <div v-if="visa.meta_title || visa.meta_description" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br flex h-10 w-10 items-center justify-center rounded-xl from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-seo-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">SEO Information</span>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                     <div v-if="visa.meta_title">
                         <p class="text-xs uppercase text-neutral-9 font-medium mb-1">Meta Title</p>
                         <p class="text-sm text-neutral-12">{{ visa.meta_title }}</p>
                     </div>
                     <div v-if="visa.meta_description">
                         <p class="text-xs uppercase text-neutral-9 font-medium mb-1">Meta Description</p>
                         <p class="text-sm text-neutral-11">{{ visa.meta_description }}</p>
                     </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    visa: {
        type: Object,
        required: true
    }
})

const { title } = useTitle(props.visa.name)

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Visas', href: route('visa.index') },
    { label: props.visa.name, last: true }
]

const requirementsList = [
    { key: 'is_express_available', label: 'Express Service' },
    { key: 'is_interview_required', label: 'Interview' },
    { key: 'is_biometric_required', label: 'Biometrics' },
    { key: 'is_appointment_required', label: 'Appointment' },
    { key: 'is_travel_insurance_required', label: 'Travel Insurance' },
    { key: 'is_bank_statement_required', label: 'Bank Statement' },
    { key: 'is_invitation_letter_required', label: 'Invitation Letter' },
    { key: 'is_hotel_booking_required', label: 'Hotel Booking' }
]

const totalPrice = computed(() => {
    return Number(props.visa.base_price || 0) + 
           Number(props.visa.service_fee || 0) + 
           Number(props.visa.embassy_fee || 0) +
           Number(props.visa.vfs_fee || 0)
})

const formatPrice = (price) => {
    return Number(price || 0).toLocaleString('en-BD', { minimumFractionDigits: 2 })
}
</script>
