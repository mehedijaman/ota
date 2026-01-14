<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotel.index'))">
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
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-hotel-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Basic Information</span>
                            <p class="text-neutral-9 text-xs">Hotel name, location, and general details</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <AppFormErrors class="mb-4" />

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="name">
                                Hotel Name <span class="text-red-500">*</span>
                            </AppLabel>
                            <AppInputText
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter hotel name"
                                @input="generateSlug"
                            />
                        </div>

                        <div>
                            <AppLabel for="slug">
                                URL Slug <span class="text-red-500">*</span>
                            </AppLabel>
                            <div class="relative">
                                <span class="text-neutral-9 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm">/hotels/</span>
                                <AppInputText
                                    id="slug"
                                    v-model="form.slug"
                                    type="text"
                                    class="pl-16"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <AppLabel for="code">Hotel Code</AppLabel>
                            <AppInputText
                                id="code"
                                v-model="form.code"
                                type="text"
                                placeholder="e.g., HTL-001"
                            />
                        </div>

                        <div>
                            <AppLabel for="star_rating">Star Rating <span class="text-red-500">*</span></AppLabel>
                            <AppCombobox
                                v-model="form.star_rating"
                                :options="starRatings"
                                combo-label="Select Rating"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <AppLabel for="property_type">Property Type <span class="text-red-500">*</span></AppLabel>
                            <AppCombobox
                                v-model="form.property_type"
                                :options="propertyTypes"
                                combo-label="Select Type"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <AppLabel for="excerpt">Short Description</AppLabel>
                        <AppTextArea
                            id="excerpt"
                            v-model="form.excerpt"
                            rows="2"
                            placeholder="Brief hotel description for listings"
                        />
                    </div>

                    <div class="mt-4">
                        <AppLabel for="description">Full Description</AppLabel>
                        <AppTextArea
                            id="description"
                            v-model="form.description"
                            rows="5"
                            placeholder="Detailed hotel description"
                        />
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Location Tab -->
        <div v-show="activeTab === 'location'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-lg shadow-teal-500/25">
                            <i class="ri-map-pin-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Location Details</span>
                            <p class="text-neutral-9 text-xs">Address and map coordinates</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel>Country <span class="text-red-500">*</span></AppLabel>
                            <AppCombobox
                                v-model="form.country_id"
                                :options="countries"
                                combo-label="Select Country"
                                class="w-full"
                                @update:modelValue="loadCities"
                            />
                        </div>

                        <div>
                            <AppLabel>City <span class="text-red-500">*</span></AppLabel>
                            <AppCombobox
                                v-model="form.city_id"
                                :options="cityOptions"
                                combo-label="Select City"
                                class="w-full"
                                :disabled="!form.country_id"
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <AppLabel>Street Address <span class="text-red-500">*</span></AppLabel>
                        <AppInputText
                            v-model="form.address_line_1"
                            type="text"
                            placeholder="Street address"
                        />
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <AppLabel>Postal Code</AppLabel>
                            <AppInputText v-model="form.postal_code" type="text" placeholder="e.g., 1000" />
                        </div>
                        <div>
                            <AppLabel>Latitude</AppLabel>
                            <AppInputText v-model="form.latitude" type="text" placeholder="23.8103" />
                        </div>
                        <div>
                            <AppLabel>Longitude</AppLabel>
                            <AppInputText v-model="form.longitude" type="text" placeholder="90.4125" />
                        </div>
                    </div>
                </template>
            </AppCard>

            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-phone-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Contact Information</span>
                            <p class="text-neutral-9 text-xs">Hotel contact details</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <AppLabel>Phone</AppLabel>
                            <AppInputText v-model="form.phone" type="text" placeholder="+880 1234 567890" />
                        </div>
                        <div>
                            <AppLabel>Email</AppLabel>
                            <AppInputText v-model="form.email" type="email" placeholder="info@hotel.com" />
                        </div>
                        <div>
                            <AppLabel>Website</AppLabel>
                            <AppInputText v-model="form.website" type="url" placeholder="https://hotel.com" />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Timings Tab -->
        <div v-show="activeTab === 'timings'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-time-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Check-in / Check-out</span>
                            <p class="text-neutral-9 text-xs">Arrival and departure timings</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel>Check-in Time <span class="text-red-500">*</span></AppLabel>
                            <AppInputText v-model="form.check_in_time" type="time" />
                        </div>
                        <div>
                            <AppLabel>Check-out Time <span class="text-red-500">*</span></AppLabel>
                            <AppInputText v-model="form.check_out_time" type="time" />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Amenities Tab -->
        <div v-show="activeTab === 'amenities'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-service-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Hotel Amenities</span>
                            <p class="text-neutral-9 text-xs">Select amenities available at this hotel</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-for="(amenities, category) in groupedAmenities" :key="category" class="mb-6 last:mb-0">
                        <h4 class="text-neutral-11 mb-3 font-medium capitalize">{{ category.replace('_', ' ') }}</h4>
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                            <label
                                v-for="amenity in amenities"
                                :key="amenity.id"
                                class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
                                :class="{ 'border-emerald-500 bg-emerald-50': form.amenity_ids.includes(amenity.id) }"
                            >
                                <input
                                    type="checkbox"
                                    :value="amenity.id"
                                    v-model="form.amenity_ids"
                                    class="h-4 w-4 rounded text-emerald-500"
                                />
                                <span class="text-neutral-11 text-sm">{{ amenity.name }}</span>
                            </label>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Status Tab -->
        <div v-show="activeTab === 'status'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-toggle-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Status & Visibility</span>
                            <p class="text-neutral-9 text-xs">Control how the hotel appears on site</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-wrap gap-6">
                        <label class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors">
                            <input type="checkbox" v-model="form.is_active" class="text-primary-600 h-5 w-5 rounded" />
                            <div>
                                <span class="text-neutral-12 font-medium">Active</span>
                                <p class="text-neutral-9 text-xs">Hotel is visible and bookable</p>
                            </div>
                        </label>

                        <label class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors">
                            <input type="checkbox" v-model="form.is_featured" class="h-5 w-5 rounded text-amber-500" />
                            <div>
                                <span class="text-neutral-12 font-medium">
                                    <i class="ri-star-fill mr-1 text-amber-500"></i>Featured
                                </span>
                                <p class="text-neutral-9 text-xs">Show on homepage featured section</p>
                            </div>
                        </label>
                    </div>
                </template>
            </AppCard>

            <AppCard class="mb-4 w-full">
                <template #title>SEO Settings</template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <AppLabel>Meta Title</AppLabel>
                            <AppInputText v-model="form.meta_title" type="text" placeholder="SEO title (leave blank for auto)" />
                        </div>
                        <div>
                            <AppLabel>Meta Description</AppLabel>
                            <AppTextArea v-model="form.meta_description" rows="2" placeholder="SEO description" />
                        </div>
                        <div>
                            <AppLabel>Meta Keywords</AppLabel>
                            <AppInputText v-model="form.meta_keywords" type="text" placeholder="keyword1, keyword2, keyword3" />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Submit Button -->
        <div class="bg-neutral-1 border-neutral-6 sticky bottom-0 z-10 -mx-4 border-t px-4 py-4 shadow-lg sm:-mx-6 sm:px-6">
            <div class="flex justify-end gap-3">
                <AppButton type="button" class="btn btn-secondary" @click="$inertia.visit(route('hotel.index'))">
                    Cancel
                </AppButton>
                <AppButton type="submit" class="btn btn-primary" :loading="form.processing">
                    <i class="ri-save-line mr-1"></i>
                    {{ isEditing ? 'Update Hotel' : 'Create Hotel' }}
                </AppButton>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'
import AppFormErrors from '@/Components/Form/AppFormErrors.vue'

const props = defineProps({
    hotel: { type: Object, default: null },
    countries: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
    propertyTypes: { type: Array, default: () => [] },
    starRatings: { type: Array, default: () => [] },
    amenities: { type: Array, default: () => [] }
})

const isEditing = computed(() => !!props.hotel?.id)
const { title } = useTitle(isEditing.value ? 'Edit Hotel' : 'Create Hotel')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotels', href: route('hotel.index') },
    { label: isEditing.value ? 'Edit' : 'Create', last: true }
]

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-hotel-line' },
    { id: 'location', label: 'Location', icon: 'ri-map-pin-line' },
    { id: 'timings', label: 'Timings', icon: 'ri-time-line' },
    { id: 'amenities', label: 'Amenities', icon: 'ri-service-line' },
    { id: 'status', label: 'Status & SEO', icon: 'ri-toggle-line' }
]

const activeTab = ref('basic')
const cityOptions = ref(props.cities || [])

const form = useForm({
    name: props.hotel?.name || '',
    slug: props.hotel?.slug || '',
    code: props.hotel?.code || '',
    star_rating: props.hotel?.star_rating || null,
    property_type: props.hotel?.property_type || null,
    excerpt: props.hotel?.excerpt || '',
    description: props.hotel?.description || '',
    country_id: props.hotel?.country_id || null,
    city_id: props.hotel?.city_id || null,
    address_line_1: props.hotel?.address_line_1 || '',
    postal_code: props.hotel?.postal_code || '',
    latitude: props.hotel?.latitude || '',
    longitude: props.hotel?.longitude || '',
    phone: props.hotel?.phone || '',
    email: props.hotel?.email || '',
    website: props.hotel?.website || '',
    check_in_time: props.hotel?.check_in_time || '14:00',
    check_out_time: props.hotel?.check_out_time || '11:00',
    is_active: props.hotel?.is_active ?? true,
    is_featured: props.hotel?.is_featured ?? false,
    meta_title: props.hotel?.meta_title || '',
    meta_description: props.hotel?.meta_description || '',
    meta_keywords: props.hotel?.meta_keywords || '',
    amenity_ids: props.hotel?.amenities?.map(a => a.id) || []
})

const groupedAmenities = computed(() => {
    const grouped = {}
    props.amenities.forEach(a => {
        if (!grouped[a.category]) grouped[a.category] = []
        grouped[a.category].push(a)
    })
    return grouped
})

const generateSlug = () => {
    if (!isEditing.value) {
        form.slug = form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')
    }
}

const loadCities = async () => {
    if (!form.country_id) {
        cityOptions.value = []
        return
    }
    try {
        const response = await fetch(route('hotel.api.cities', form.country_id))
        const data = await response.json()
        cityOptions.value = data.map(c => ({ value: c.id, label: c.name }))
    } catch (e) {
        console.error('Failed to load cities', e)
    }
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('hotel.update', props.hotel.id))
    } else {
        form.post(route('hotel.store'))
    }
}
</script>
