<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('visa-country-view')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visaCountry.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>

                <AppButton
                    v-if="can('visa-country-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visaCountry.edit', country.id))"
                >
                    <i class="ri-edit-line mr-1"></i>
                    Edit
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Hero Section -->
    <div class="relative mb-6 overflow-hidden rounded-2xl shadow-xl">
        <div class="absolute inset-0 bg-linear-to-br from-blue-600 via-indigo-600 to-purple-600"></div>
         <div v-if="country.banner_url" class="absolute inset-0">
             <img :src="country.banner_url" class="h-full w-full object-cover mix-blend-overlay opacity-40" alt="Banner" />
        </div>
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px); background-size: 60px 60px;"></div>

        <div class="relative z-10 p-8">
            <div class="flex flex-col items-center gap-8 md:flex-row">
                <!-- Flag/Thumbnail -->
                <div class="relative">
                    <div class="absolute -inset-2 rounded-2xl bg-white/20 blur-lg"></div>
                    <div
                        v-if="country.flag_url"
                        class="relative h-32 w-48 overflow-hidden rounded-2xl border-4 border-white/40 shadow-2xl"
                    >
                        <img :src="country.flag_url" :alt="country.name" class="h-full w-full object-cover" />
                    </div>
                    <div
                        v-else
                        class="relative flex h-32 w-48 items-center justify-center rounded-2xl border-4 border-white/40 bg-white/20 text-6xl text-white shadow-2xl backdrop-blur-sm"
                    >
                        <span v-if="country.flag_icon" class="text-6xl">{{ country.flag_icon }}</span>
                        <i v-else class="ri-flag-line"></i>
                    </div>
                </div>

                <!-- Country Info -->
                <div class="flex-1 text-center text-white md:text-left">
                    <div class="mb-2 flex flex-wrap items-center justify-center gap-2 md:justify-start">
                        <span v-if="country.is_popular" class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            <i class="ri-star-fill mr-1"></i>Popular Destination
                        </span>
                        <span :class="country.is_active ? 'bg-emerald-400/30' : 'bg-red-400/30'" class="rounded-full px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            {{ country.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight md:text-4xl">{{ country.name }}</h1>
                    
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3 md:justify-start">
                         <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-earth-line text-indigo-200"></i>
                            {{ country.continent }} / {{ country.region || 'Unknown Region' }}
                        </span>
                        <span v-if="country.capital" class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-government-line text-indigo-200"></i>
                            {{ country.capital }}
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-time-line text-indigo-200"></i>
                            <span class="font-mono font-medium">{{ country.timezone || '-' }}</span>
                        </span>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm min-w-[200px]">
                    <p class="text-xs font-medium uppercase tracking-wider text-indigo-200">Available Visas</p>
                    <p class="text-3xl font-bold text-white">{{ country.visas_count || 0 }}</p>
                    <p class="text-indigo-200 text-sm">Packages</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Tabs -->
             <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-b border-neutral-4 bg-neutral-2">
                    <div class="flex overflow-x-auto">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            class="flex items-center gap-2 px-6 py-4 text-sm font-medium transition-colors hover:bg-neutral-3"
                            :class="activeTab === tab.id ? 'border-b-2 border-indigo-600 bg-neutral-1 text-indigo-600' : 'text-neutral-11'"
                        >
                            <i :class="tab.icon"></i>
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Overview Tab -->
                    <div v-show="activeTab === 'overview'" class="space-y-6">
                        <div v-if="country.description">
                            <h3 class="text-neutral-12 mb-2 font-semibold flex items-center gap-2">
                                <i class="ri-information-line text-indigo-500"></i> About {{ country.name }}
                            </h3>
                            <p class="text-neutral-11 leading-relaxed whitespace-pre-line">{{ country.description }}</p>
                        </div>
                        
                        <div v-if="country.visa_info">
                            <h3 class="text-neutral-12 mb-2 font-semibold flex items-center gap-2">
                                <i class="ri-passport-line text-indigo-500"></i> Visa Information
                            </h3>
                            <p class="text-neutral-11 leading-relaxed whitespace-pre-line">{{ country.visa_info }}</p>
                        </div>

                         <AppEmptyState
                            v-if="!country.description && !country.visa_info"
                            title="No Information Available"
                            description="Country description and visa information has not been added yet."
                            icon="ri-file-text-line"
                        />
                    </div>

                    <!-- Embassy Tab -->
                    <div v-show="activeTab === 'embassy'" class="space-y-8">
                         <!-- Embassy -->
                        <div v-if="country.embassy_name">
                             <div class="flex items-center gap-3 mb-4">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                                    <i class="ri-building-line text-xl"></i>
                                </div>
                                <h3 class="text-neutral-12 text-lg font-semibold">Embassy Details</h3>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6 p-4 bg-neutral-2 rounded-xl border border-neutral-4">
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Name</p>
                                    <p class="text-neutral-12 font-medium">{{ country.embassy_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Working Hours</p>
                                    <div v-if="country.embassy_working_hours && Array.isArray(country.embassy_working_hours) && country.embassy_working_hours.length > 0" class="space-y-1">
                                        <div v-for="(work, idx) in country.embassy_working_hours" :key="idx" class="flex justify-between text-sm gap-2">
                                            <span class="text-neutral-11">{{ work.day }}</span>
                                            <span class="text-neutral-12 font-medium">{{ work.hours }}</span>
                                        </div>
                                    </div>
                                    <p v-else class="text-neutral-12">-</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Address</p>
                                    <p class="text-neutral-12">{{ country.embassy_address || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Phone</p>
                                    <p class="text-neutral-12 font-mono">{{ country.embassy_phone || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Email</p>
                                    <p class="text-neutral-12">{{ country.embassy_email || '-' }}</p>
                                </div>
                                <div v-if="country.embassy_website" class="md:col-span-2">
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Website</p>
                                     <a :href="country.embassy_website" target="_blank" class="text-blue-600 hover:underline break-all">
                                        {{ country.embassy_website }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Consulate -->
                        <div v-if="country.consulate_name">
                             <div class="flex items-center gap-3 mb-4">
                                <div class="bg-purple-100 text-purple-600 p-2 rounded-lg">
                                    <i class="ri-community-line text-xl"></i>
                                </div>
                                <h3 class="text-neutral-12 text-lg font-semibold">Consulate Details</h3>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6 p-4 bg-neutral-2 rounded-xl border border-neutral-4">
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Name</p>
                                    <p class="text-neutral-12 font-medium">{{ country.consulate_name }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Address</p>
                                    <p class="text-neutral-12">{{ country.consulate_address || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Phone</p>
                                    <p class="text-neutral-12 font-mono">{{ country.consulate_phone || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-9 uppercase mb-1">Email</p>
                                    <p class="text-neutral-12">{{ country.consulate_email || '-' }}</p>
                                </div>
                            </div>
                        </div>
                        
                         <AppEmptyState
                            v-if="!country.embassy_name && !country.consulate_name"
                            title="No Embassy Info"
                            description="Embassy and consulate contact details are currently unavailable."
                            icon="ri-building-2-line"
                        />
                    </div>

                    <!-- SEO Tab -->
                    <div v-show="activeTab === 'seo'" class="space-y-6">
                        <div class="p-4 bg-neutral-2 rounded-xl border border-neutral-4 space-y-4">
                             <div>
                                 <p class="text-xs uppercase text-neutral-9 font-medium mb-1">Meta Title</p>
                                 <p class="text-sm text-neutral-12 font-medium">{{ country.meta_title || '-' }}</p>
                             </div>
                             <div>
                                 <p class="text-xs uppercase text-neutral-9 font-medium mb-1">Meta Description</p>
                                 <p class="text-sm text-neutral-11">{{ country.meta_description || '-' }}</p>
                             </div>
                             <div>
                                 <p class="text-xs uppercase text-neutral-9 font-medium mb-1">Keywords</p>
                                 <div class="flex flex-wrap gap-2">
                                     <span 
                                        v-for="keyword in (country.meta_keywords ? country.meta_keywords.split(',') : [])" 
                                        :key="keyword"
                                        class="px-2 py-1 bg-neutral-3 rounded text-xs text-neutral-11"
                                     >
                                         {{ keyword.trim() }}
                                     </span>
                                     <span v-if="!country.meta_keywords" class="text-sm text-neutral-11">-</span>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Visas List -->
             <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-border border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-passport-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Available Visa Packages</span>
                    </div>
                </div>
                <div class="p-5">
                    <div v-if="country.visas && country.visas.length > 0" class="grid gap-4 md:grid-cols-2">
                        <div 
                            v-for="visa in country.visas" 
                            :key="visa.id"
                            class="group relative flex flex-col justify-between rounded-xl border border-neutral-4 bg-neutral-2 p-4 transition-all hover:border-indigo-500 hover:shadow-md cursor-pointer"
                            @click="$inertia.visit(route('visa.show', visa.id))"
                        >
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="rounded bg-neutral-3 px-2 py-0.5 text-xs font-medium text-neutral-11 uppercase">{{ visa.entry_type }} Entry</span>
                                    <i class="ri-arrow-right-line text-neutral-9 group-hover:text-indigo-500 transition-colors"></i>
                                </div>
                                <h4 class="text-neutral-12 font-semibold group-hover:text-indigo-600 transition-colors">{{ visa.name }}</h4>
                                <div class="mt-2 text-sm text-neutral-11 flex items-center gap-4">
                                    <span title="Validity"><i class="ri-calendar-check-line mr-1"></i> {{ visa.validity_days }} Days</span>
                                    <span title="Processing"><i class="ri-history-line mr-1"></i> {{ visa.processing_days_min }}-{{ visa.processing_days_max }} Days</span>
                                </div>
                            </div>
                            <div class="mt-4 border-t border-neutral-4 pt-3 flex items-center justify-between">
                                <span class="text-xs text-neutral-9">Total Cost</span>
                                <span class="text-emerald-600 font-bold">৳{{ formatPrice(Number(visa.base_price) + Number(visa.service_fee) + Number(visa.embassy_fee) + Number(visa.vfs_fee)) }}</span>
                            </div>
                        </div>
                    </div>
                    <AppEmptyState
                        v-else
                        title="No Visa Packages"
                        description="There are currently no active visa packages listed for this country."
                        icon="ri-passport-line"
                        class="py-8"
                    >
                         <template #actions>
                            <AppButton
                                v-if="can('visa-create')"
                                class="btn btn-primary btn-sm"
                                @click="$inertia.visit(route('visa.create', { country_id: country.id }))"
                            >
                                <i class="ri-add-line mr-1"></i> Add Visa
                            </AppButton>
                        </template>
                    </AppEmptyState>
                </div>
            </div>
        </div>

        <!-- Right Sidebar (Could be used for additional info or quick links) -->
         <div class="space-y-6 lg:col-span-1">
             <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm sticky top-6">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <span class="text-neutral-12 font-semibold">Quick Information</span>
                </div>
                 <div class="p-5 space-y-4">
                     <div class="flex items-center justify-between">
                         <span class="text-neutral-11 text-sm">ISO Code</span>
                         <span class="font-mono font-medium text-neutral-12">{{ country.code }}</span>
                     </div>
                     <div class="flex items-center justify-between">
                         <span class="text-neutral-11 text-sm">Continent</span>
                         <span class="font-medium text-neutral-12">{{ country.continent }}</span>
                     </div>
                      <div class="flex items-center justify-between">
                         <span class="text-neutral-11 text-sm">Region</span>
                         <span class="font-medium text-neutral-12">{{ country.region || '-' }}</span>
                     </div>
                     <div class="flex items-center justify-between">
                         <span class="text-neutral-11 text-sm">Capital</span>
                         <span class="font-medium text-neutral-12">{{ country.capital || '-' }}</span>
                     </div>
                     <div class="flex items-center justify-between">
                         <span class="text-neutral-11 text-sm">Timezone</span>
                         <span class="font-medium text-neutral-12">{{ country.timezone || '-' }}</span>
                     </div>
                 </div>
             </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()

const props = defineProps({
    country: {
        type: Object,
        required: true
    }
})

const { title } = useTitle(props.country.name)

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Visas', href: route('visa.index') },
    { label: 'Countries', href: route('visaCountry.index') },
    { label: props.country.name, last: true }
]

const activeTab = ref('overview')

const tabs = [
    { id: 'overview', label: 'Overview', icon: 'ri-file-text-line' },
    { id: 'embassy', label: 'Embassy Info', icon: 'ri-building-line' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-line' }
]

const formatPrice = (price) => {
    return Number(price || 0).toLocaleString('en-BD', { minimumFractionDigits: 2 })
}
</script>
