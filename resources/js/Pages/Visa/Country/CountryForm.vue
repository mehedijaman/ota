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
                                ? 'bg-linear-to-r from-blue-600 to-teal-600 text-white shadow-lg shadow-blue-500/25'
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
                                    <AppCombobox
                                        id="continent"
                                        v-model="form.continent"
                                        :options="continentOptions"
                                        placeholder="Select Continent"
                                        emit-value
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
                        <template #title> Settings & Ranking </template>
                        <template #content>
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-wrap gap-6">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <AppCheckbox v-model="form.is_active" />
                                        <span class="text-neutral-12 font-medium">Active</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <AppCheckbox v-model="form.is_popular" />
                                        <span class="text-neutral-12 font-medium">Popular Destination</span>
                                    </label>
                                </div>
                                <div class="w-full md:w-1/3">
                                    <AppLabel for="sort_order">Sort Order</AppLabel>
                                    <AppInputText
                                        id="sort_order"
                                        v-model="form.sort_order"
                                        type="number"
                                        min="0"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <AppCard>
                        <template #title> Flag / Thumbnail </template>
                        <template #content>
                            <div class="flex flex-col gap-4">
                                <div class="shrink-0">
                                    <div v-if="flagPreview || props.country?.flag_url" class="relative">
                                        <img
                                            :src="flagPreview || props.country?.flag_url"
                                            class="border-neutral-5 h-48 w-full rounded-xl border-2 object-cover shadow-lg"
                                            alt="Flag preview"
                                        />
                                        <button
                                            type="button"
                                            @click="removeFlag"
                                            class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-colors hover:bg-red-600"
                                        >
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                    <div
                                        v-else
                                        class="border-neutral-5 flex h-48 w-full items-center justify-center rounded-xl border-2 border-dashed bg-linear-to-br from-blue-50 to-cyan-50"
                                    >
                                        <div class="text-center">
                                            <i class="ri-image-add-line text-neutral-7 text-4xl"></i>
                                            <p class="text-neutral-9 mt-2 text-sm">No flag selected</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <label class="cursor-pointer">
                                        <span class="btn btn-secondary w-full justify-center">
                                            <i class="ri-upload-2-line mr-2"></i>
                                            {{ flagPreview || props.country?.flag_url ? 'Change Flag' : 'Upload Flag' }}
                                        </span>
                                        <input
                                            type="file"
                                            class="hidden"
                                            accept="image/*"
                                            @change="handleFlagChange"
                                        />
                                    </label>
                                    <p class="text-neutral-9 text-xs text-center">
                                        <i class="ri-information-line mr-1"></i>
                                        Recommended: 400x300 pixels
                                    </p>
                                </div>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard>
                        <template #title> Banner Image </template>
                        <template #content>
                            <div class="flex flex-col gap-4">
                                <div class="shrink-0">
                                    <div v-if="bannerPreview || props.country?.banner_url" class="relative">
                                        <img
                                            :src="bannerPreview || props.country?.banner_url"
                                            class="border-neutral-5 h-32 w-full rounded-xl border-2 object-cover shadow-lg"
                                            alt="Banner preview"
                                        />
                                        <button
                                            type="button"
                                            @click="removeBanner"
                                            class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-colors hover:bg-red-600"
                                        >
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                    <div
                                        v-else
                                        class="border-neutral-5 flex h-32 w-full items-center justify-center rounded-xl border-2 border-dashed bg-linear-to-br from-indigo-50 to-violet-50"
                                    >
                                        <div class="text-center">
                                            <i class="ri-image-add-line text-neutral-7 text-4xl"></i>
                                            <p class="text-neutral-9 mt-2 text-sm">No banner selected</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <label class="cursor-pointer">
                                        <span class="btn btn-secondary w-full justify-center">
                                            <i class="ri-upload-2-line mr-2"></i>
                                            {{ bannerPreview || props.country?.banner_url ? 'Change Banner' : 'Upload Banner' }}
                                        </span>
                                        <input
                                            type="file"
                                            class="hidden"
                                            accept="image/*"
                                            @change="handleBannerChange"
                                        />
                                    </label>
                                    <p class="text-neutral-9 text-xs text-center">
                                        <i class="ri-information-line mr-1"></i>
                                        Recommended: 1200x400 pixels
                                    </p>
                                </div>
                            </div>
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
                                row="3"
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
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Embassy Tab -->
        <div v-show="activeTab === 'embassy'">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                 <AppCard class="h-full">
                    <template #title> Embassy Details </template>
                    <template #content>
                        <div class="space-y-4">
                             <div>
                                <AppLabel for="embassy_name">Embassy Name</AppLabel>
                                <AppInputText
                                    id="embassy_name"
                                    v-model="form.embassy_name"
                                    type="text"
                                    placeholder="e.g. Embassy of the United States"
                                />
                            </div>
                            <div>
                                <AppLabel for="embassy_address">Address</AppLabel>
                                <AppTextArea
                                    id="embassy_address"
                                    v-model="form.embassy_address"
                                    rows="3"
                                    placeholder="Full address"
                                />
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                            </div>
                            <div>
                                <AppLabel for="embassy_website">Website</AppLabel>
                                <AppInputText
                                    id="embassy_website"
                                    v-model="form.embassy_website"
                                    type="url"
                                />
                            </div>
                             <div>
                                <AppLabel>Working Hours</AppLabel>
                                <div class="space-y-3">
                                    <div v-for="(item, index) in form.embassy_working_hours" :key="index" class="flex gap-2">
                                        <div class="flex-1">
                                            <AppInputText
                                                v-model="item.day"
                                                placeholder="Day (e.g. Mon-Fri)"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <AppInputText
                                                v-model="item.hours"
                                                placeholder="Hours (e.g. 9AM - 5PM)"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeWorkingHour(index)"
                                            class="text-neutral-500 hover:text-red-500"
                                        >
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        @click="addWorkingHour"
                                        class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1"
                                    >
                                        <i class="ri-add-line"></i> Add Working Hours
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </AppCard>

                <AppCard class="h-full">
                    <template #title> Consulate Details </template>
                    <template #content>
                         <div class="space-y-4">
                             <div>
                                <AppLabel for="consulate_name">Consulate Name</AppLabel>
                                <AppInputText
                                    id="consulate_name"
                                    v-model="form.consulate_name"
                                    type="text"
                                />
                            </div>
                            <div>
                                <AppLabel for="consulate_address">Address</AppLabel>
                                <AppTextArea
                                    id="consulate_address"
                                    v-model="form.consulate_address"
                                    rows="3"
                                />
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                        </div>
                    </template>
                </AppCard>
            </div>
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
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppCheckbox from '@/Components/Form/AppCheckbox.vue'

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
    flag_icon: props.country?.flag_icon ?? '', // Emoji
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
    embassy_working_hours: Array.isArray(props.country?.embassy_working_hours) ? props.country.embassy_working_hours : [],
    
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
    
    flag: null,
    banner: null,
    remove_flag: false,
    remove_banner: false,
    _method: isEdit.value ? 'PUT' : 'POST'
})
// Media Handling
const flagPreview = ref(null)
const bannerPreview = ref(null)

const { errorsFields } = useFormErrors()

const activeTab = ref('basic')

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-information-line' },
    { id: 'details', label: 'Description', icon: 'ri-file-text-line' },
    { id: 'embassy', label: 'Embassy & Consulate', icon: 'ri-building-line' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-line' }
]

const continentOptions = [
    { label: 'Asia', value: 'Asia' },
    { label: 'Europe', value: 'Europe' },
    { label: 'Africa', value: 'Africa' },
    { label: 'North America', value: 'North America' },
    { label: 'South America', value: 'South America' },
    { label: 'Oceania', value: 'Oceania' },
    { label: 'Antarctica', value: 'Antarctica' }
]

const generateSlug = () => {
    if (isCreate.value && form.name) {
        form.slug = form.name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')
    }
}

const handleFlagChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.flag = file
        form.remove_flag = false
        flagPreview.value = URL.createObjectURL(file)
    }
}

const removeFlag = () => {
    form.flag = null
    form.remove_flag = true
    flagPreview.value = null
}

const handleBannerChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.banner = file
        form.remove_banner = false
        bannerPreview.value = URL.createObjectURL(file)
    }
}

const removeBanner = () => {
    form.banner = null
    form.remove_banner = true
    bannerPreview.value = null
}

const addWorkingHour = () => {
    if (!Array.isArray(form.embassy_working_hours)) {
        form.embassy_working_hours = []
    }
    form.embassy_working_hours.push({ day: '', hours: '' })
}

const removeWorkingHour = (index) => {
    form.embassy_working_hours.splice(index, 1)
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visaCountry.store'), {
            forceFormData: true
        })
    } else {
        form.post(route('visaCountry.update', props.country.id), {
            forceFormData: true
        })
    }
}
</script>
