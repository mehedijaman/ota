<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('visaCountry.index'))">
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

        <!-- Basic Info Tab -->
        <div v-show="activeTab === 'basic'">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <AppCard class="mb-4">
                        <template #title> Basic Information </template>
                        <template #content>
                            <AppFormErrors class="mb-4" />
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <AppLabel for="name">Country Name <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Enter country name"
                                        @input="generateSlug"
                                        :class="{ 'input-error': errorsFields.includes('name') }"
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
                                    <AppLabel for="code">ISO Code (2/3 chars) <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        placeholder="e.g. US, USA"
                                        uppercase
                                        :class="{ 'input-error': errorsFields.includes('code') }"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="flag_icon">Flag Icon (Emoji)</AppLabel>
                                    <AppInputText
                                        id="flag_icon"
                                        v-model="form.flag_icon"
                                        type="text"
                                        placeholder="e.g. 🇺🇸"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="continent">Continent</AppLabel>
                                    <AppInputText
                                        id="continent"
                                        v-model="form.continent"
                                        type="text"
                                        placeholder="e.g. North America"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="region">Region</AppLabel>
                                    <AppInputText
                                        id="region"
                                        v-model="form.region"
                                        type="text"
                                        placeholder="e.g. Western Europe"
                                    />
                                </div>
                                <div>
                                    <AppLabel for="capital">Capital City</AppLabel>
                                    <AppInputText
                                        id="capital"
                                        v-model="form.capital"
                                        type="text"
                                        placeholder="e.g. Washington D.C."
                                    />
                                </div>
                                <div>
                                    <AppLabel for="timezone">Timezone</AppLabel>
                                    <AppInputText
                                        id="timezone"
                                        v-model="form.timezone"
                                        type="text"
                                        placeholder="e.g. GMT-5"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard class="mb-4">
                        <template #title> Settings </template>
                        <template #content>
                            <div class="flex flex-wrap gap-6">
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
                                        v-model="form.is_popular"
                                        class="h-4 w-4 rounded text-amber-500"
                                    />
                                    <span class="text-neutral-12 font-medium">Popular Destination</span>
                                </label>
                            </div>
                            <div class="mt-4">
                                <AppLabel for="sort_order">Sort Order</AppLabel>
                                <AppInputText
                                    id="sort_order"
                                    v-model="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="w-full md:w-1/3"
                                />
                            </div>
                        </template>
                    </AppCard>
                </div>

                <div class="lg:col-span-1">
                    <AppCard>
                        <template #title> Flag / Image </template>
                        <template #content>
                             <AppImage
                                v-model="form.thumbnail"
                                :image-url="imageUrl"
                                label="Country Image"
                            />
                        </template>
                    </AppCard>
                </div>
            </div>
        </div>

        <!-- Details Tab -->
        <div v-show="activeTab === 'details'">
            <AppCard class="mb-4">
                <template #title> Description & Visa Info </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="description">Short Description</AppLabel>
                            <AppTextArea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Brief overview of the country"
                            />
                        </div>
                        <div>
                            <AppLabel for="visa_info">General Visa Information</AppLabel>
                            <AppTextArea
                                id="visa_info"
                                v-model="form.visa_info"
                                rows="10"
                                placeholder="Detailed visa policy information..."
                            />
                            <!-- Could use a rich text editor here if available -->
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Embassy Tab -->
        <div v-show="activeTab === 'embassy'">
            <AppCard class="mb-4">
                <template #title> Embassy Details </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <AppLabel for="embassy_name">Embassy Name</AppLabel>
                            <AppInputText
                                id="embassy_name"
                                v-model="form.embassy_name"
                                type="text"
                                placeholder="e.g. Embassy of the United States"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <AppLabel for="embassy_address">Address</AppLabel>
                            <AppTextArea
                                id="embassy_address"
                                v-model="form.embassy_address"
                                rows="2"
                                placeholder="Full address"
                            />
                        </div>
                        <div>
                            <AppLabel for="embassy_phone">Phone</AppLabel>
                            <AppInputText
                                id="embassy_phone"
                                v-model="form.embassy_phone"
                                type="text"
                            />
                        </div>
                        <div>
                            <AppLabel for="embassy_email">Email</AppLabel>
                            <AppInputText
                                id="embassy_email"
                                v-model="form.embassy_email"
                                type="email"
                            />
                        </div>
                        <div>
                            <AppLabel for="embassy_website">Website</AppLabel>
                            <AppInputText
                                id="embassy_website"
                                v-model="form.embassy_website"
                                type="url"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>

            <AppCard class="mb-4">
                <template #title> Consulate Details </template>
                <template #content>
                     <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <AppLabel for="consulate_name">Consulate Name</AppLabel>
                            <AppInputText
                                id="consulate_name"
                                v-model="form.consulate_name"
                                type="text"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <AppLabel for="consulate_address">Address</AppLabel>
                            <AppTextArea
                                id="consulate_address"
                                v-model="form.consulate_address"
                                rows="2"
                            />
                        </div>
                        <div>
                            <AppLabel for="consulate_phone">Phone</AppLabel>
                            <AppInputText
                                id="consulate_phone"
                                v-model="form.consulate_phone"
                                type="text"
                            />
                        </div>
                        <div>
                            <AppLabel for="consulate_email">Email</AppLabel>
                            <AppInputText
                                id="consulate_email"
                                v-model="form.consulate_email"
                                type="email"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- SEO Tab -->
        <div v-show="activeTab === 'seo'">
            <AppCard class="mb-4">
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
                {{ isCreate ? 'Create Country' : 'Update Country' }}
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

const { isCreate, isEdit } = useFormContext()
const { title } = useTitle(isCreate.value ? 'Create Country' : 'Edit Country')

const props = defineProps({
    country: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visas', href: route('visa.index') },
    { label: 'Countries', href: route('visaCountry.index') },
    { label: title.value, last: true }
]

const form = useForm({
    name: props.country?.name ?? '',
    slug: props.country?.slug ?? '',
    code: props.country?.code ?? '',
    flag_icon: props.country?.flag_icon ?? '',
    continent: props.country?.continent ?? '',
    region: props.country?.region ?? '',
    capital: props.country?.capital ?? '',
    timezone: props.country?.timezone ?? '',
    description: props.country?.description ?? '',
    visa_info: props.country?.visa_info ?? '',
    
    embassy_name: props.country?.embassy_name ?? '',
    embassy_address: props.country?.embassy_address ?? '',
    embassy_phone: props.country?.embassy_phone ?? '',
    embassy_email: props.country?.embassy_email ?? '',
    embassy_website: props.country?.embassy_website ?? '',
    
    consulate_name: props.country?.consulate_name ?? '',
    consulate_address: props.country?.consulate_address ?? '',
    consulate_phone: props.country?.consulate_phone ?? '',
    consulate_email: props.country?.consulate_email ?? '',
    
    is_active: props.country?.is_active ?? true,
    is_popular: props.country?.is_popular ?? false,
    sort_order: props.country?.sort_order ?? 0,
    
    meta_title: props.country?.meta_title ?? '',
    meta_description: props.country?.meta_description ?? '',
    meta_keywords: props.country?.meta_keywords ?? '',
    
    thumbnail: null,
    _method: isEdit.value ? 'PUT' : 'POST'
})

const imageUrl = computed(() => props.country?.thumbnail_url)
const { errorsFields } = useFormErrors()

const activeTab = ref('basic')

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-information-line' },
    { id: 'details', label: 'Description', icon: 'ri-file-text-line' },
    { id: 'embassy', label: 'Embassy & Consulate', icon: 'ri-building-line' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-line' }
]

const generateSlug = () => {
    if (isCreate.value && form.name) {
        form.slug = form.name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')
    }
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visaCountry.store'))
    } else {
        form.post(route('visaCountry.update', props.country.id))
    }
}
</script>
