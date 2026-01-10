<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full">
        <template #title>{{ isEdit ? 'Edit Tour' : 'Create Tour' }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />

            <!-- Tabs -->
            <div class="border-b mb-6">
                <nav class="flex gap-6 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'pb-3 px-1 font-medium text-sm border-b-2 transition-colors whitespace-nowrap flex items-center gap-2',
                            activeTab === tab.id
                                ? 'border-primary-600 text-primary-600'
                                : 'border-transparent text-slate-500 hover:text-slate-700'
                        ]"
                    >
                        <i :class="tab.icon"></i>
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <!-- Basic Info Tab -->
            <div v-show="activeTab === 'basic'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="name">Tour Name *</AppLabel>
                        <AppInputText
                            id="name"
                            v-model="form.name"
                            type="text"
                            @input="generateSlug"
                            :class="{ 'input-error': errorsFields.includes('name') }"
                        />
                    </div>

                    <div>
                        <AppLabel for="slug">Slug *</AppLabel>
                        <AppInputText
                            id="slug"
                            v-model="form.slug"
                            type="text"
                            :class="{ 'input-error': errorsFields.includes('slug') }"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <AppLabel for="code">Tour Code</AppLabel>
                        <AppInputText
                            id="code"
                            v-model="form.code"
                            type="text"
                            placeholder="e.g., CBX-001"
                        />
                    </div>

                    <div>
                        <AppLabel for="category_id">Category</AppLabel>
                        <AppCombobox
                            v-model="form.category_id"
                            :options="categories"
                            combo-label="Select Category"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="destination_id">Destination</AppLabel>
                        <AppCombobox
                            v-model="form.destination_id"
                            :options="destinations"
                            combo-label="Select Destination"
                            class="w-full"
                        />
                    </div>
                </div>

                <div>
                    <AppLabel for="excerpt">Short Description</AppLabel>
                    <AppTextArea
                        id="excerpt"
                        v-model="form.excerpt"
                        rows="2"
                        placeholder="Brief tour description for listings"
                    />
                </div>

                <div>
                    <AppLabel for="description">Full Description</AppLabel>
                    <AppTextArea
                        id="description"
                        v-model="form.description"
                        rows="6"
                        placeholder="Detailed tour description"
                    />
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <AppLabel for="duration_days">Days *</AppLabel>
                        <AppInputText
                            id="duration_days"
                            v-model="form.duration_days"
                            type="number"
                            min="1"
                            :class="{ 'input-error': errorsFields.includes('duration_days') }"
                        />
                    </div>

                    <div>
                        <AppLabel for="duration_nights">Nights *</AppLabel>
                        <AppInputText
                            id="duration_nights"
                            v-model="form.duration_nights"
                            type="number"
                            min="0"
                        />
                    </div>

                    <div>
                        <AppLabel for="difficulty_level">Difficulty</AppLabel>
                        <AppCombobox
                            v-model="form.difficulty_level"
                            :options="difficultyOptions"
                            combo-label="Select Difficulty"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="tour_type">Tour Type</AppLabel>
                        <AppCombobox
                            v-model="form.tour_type"
                            :options="tourTypeOptions"
                            combo-label="Select Tour Type"
                            class="w-full"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="start_location">Start Location</AppLabel>
                        <AppInputText
                            id="start_location"
                            v-model="form.start_location"
                            type="text"
                        />
                    </div>

                    <div>
                        <AppLabel for="end_location">End Location</AppLabel>
                        <AppInputText
                            id="end_location"
                            v-model="form.end_location"
                            type="text"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="valid_from">Valid From</AppLabel>
                        <AppInputText
                            id="valid_from"
                            v-model="form.valid_from"
                            type="date"
                        />
                    </div>

                    <div>
                        <AppLabel for="valid_to">Valid To</AppLabel>
                        <AppInputText
                            id="valid_to"
                            v-model="form.valid_to"
                            type="date"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <AppCheckbox
                        id="is_featured"
                        v-model="form.is_featured"
                        :checked="form.is_featured"
                        label="Featured Tour"
                    />
                    <AppCheckbox
                        id="is_bestseller"
                        v-model="form.is_bestseller"
                        :checked="form.is_bestseller"
                        label="Bestseller"
                    />
                    <AppCheckbox
                        id="is_active"
                        v-model="form.is_active"
                        :checked="form.is_active"
                        label="Active"
                    />
                </div>
            </div>

            <!-- Pricing Tab -->
            <div v-show="activeTab === 'pricing'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <AppLabel for="base_price">Base Price (৳) *</AppLabel>
                        <AppInputText
                            id="base_price"
                            v-model="form.base_price"
                            type="number"
                            min="0"
                            step="0.01"
                            :class="{ 'input-error': errorsFields.includes('base_price') }"
                        />
                    </div>

                    <div>
                        <AppLabel for="discount_price">Discount Price (৳)</AppLabel>
                        <AppInputText
                            id="discount_price"
                            v-model="form.discount_price"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>

                    <div>
                        <AppLabel for="child_price">Child Price (৳)</AppLabel>
                        <AppInputText
                            id="child_price"
                            v-model="form.child_price"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <AppLabel for="deposit_amount">Deposit Amount (৳)</AppLabel>
                        <AppInputText
                            id="deposit_amount"
                            v-model="form.deposit_amount"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>

                    <div>
                        <AppLabel for="deposit_type">Deposit Type</AppLabel>
                        <AppCombobox
                            v-model="form.deposit_type"
                            :options="depositTypeOptions"
                            combo-label="Select Deposit Type"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="cancellation_days">Free Cancellation Days</AppLabel>
                        <AppInputText
                            id="cancellation_days"
                            v-model="form.cancellation_days"
                            type="number"
                            min="0"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="min_group_size">Min Group Size</AppLabel>
                        <AppInputText
                            id="min_group_size"
                            v-model="form.min_group_size"
                            type="number"
                            min="1"
                        />
                    </div>

                    <div>
                        <AppLabel for="max_group_size">Max Group Size</AppLabel>
                        <AppInputText
                            id="max_group_size"
                            v-model="form.max_group_size"
                            type="number"
                            min="1"
                        />
                    </div>
                </div>
            </div>

            <!-- Itinerary Tab -->
            <div v-show="activeTab === 'itinerary'" class="space-y-4">
                <div
                    v-for="(day, index) in form.itineraries"
                    :key="index"
                    class="p-4 border rounded-lg bg-slate-50"
                >
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium">Day {{ day.day_number }}</h4>
                        <button
                            type="button"
                            @click="removeItinerary(index)"
                            class="text-red-500 hover:text-red-700"
                        >
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <AppLabel>Title</AppLabel>
                            <AppInputText v-model="day.title" type="text" />
                        </div>

                        <div>
                            <AppLabel>Description</AppLabel>
                            <AppTextArea v-model="day.description" rows="2" />
                        </div>

                        <div>
                            <AppLabel>Accommodation</AppLabel>
                            <AppInputText v-model="day.accommodation" type="text" placeholder="Hotel/Lodge name" />
                        </div>
                    </div>
                </div>

                <AppButton type="button" class="btn btn-secondary" @click="addItinerary">
                    <i class="ri-add-line mr-1"></i> Add Day
                </AppButton>
            </div>

            <!-- Inclusions Tab -->
            <div v-show="activeTab === 'inclusions'" class="space-y-6">
                <div>
                    <h4 class="font-medium mb-3 text-green-700">What's Included</h4>
                    <div class="space-y-2">
                        <div
                            v-for="(item, index) in form.inclusions"
                            :key="'inc-' + index"
                            class="flex gap-2"
                        >
                            <AppInputText
                                v-model="form.inclusions[index]"
                                type="text"
                                class="flex-1"
                                placeholder="e.g., Round-trip transport"
                            />
                            <button
                                type="button"
                                @click="form.inclusions.splice(index, 1)"
                                class="text-red-500 hover:text-red-700 px-2"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <AppButton type="button" class="btn btn-sm btn-secondary mt-2" @click="form.inclusions.push('')">
                        <i class="ri-add-line mr-1"></i> Add Inclusion
                    </AppButton>
                </div>

                <div>
                    <h4 class="font-medium mb-3 text-red-700">What's Not Included</h4>
                    <div class="space-y-2">
                        <div
                            v-for="(item, index) in form.exclusions"
                            :key="'exc-' + index"
                            class="flex gap-2"
                        >
                            <AppInputText
                                v-model="form.exclusions[index]"
                                type="text"
                                class="flex-1"
                                placeholder="e.g., Personal expenses"
                            />
                            <button
                                type="button"
                                @click="form.exclusions.splice(index, 1)"
                                class="text-red-500 hover:text-red-700 px-2"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <AppButton type="button" class="btn btn-sm btn-secondary mt-2" @click="form.exclusions.push('')">
                        <i class="ri-add-line mr-1"></i> Add Exclusion
                    </AppButton>
                </div>
            </div>

            <!-- Media Tab -->
            <div v-show="activeTab === 'media'" class="space-y-6">
                <!-- Thumbnail -->
                <div>
                    <AppLabel>Featured Image (Thumbnail)</AppLabel>
                    <div class="mt-2">
                        <div v-if="thumbnailPreview || props.tour?.thumbnail_url" class="relative inline-block mb-4">
                            <img
                                :src="thumbnailPreview || props.tour?.thumbnail_url"
                                class="h-48 w-64 rounded-xl object-cover border-2 border-slate-200"
                                alt="Thumbnail preview"
                            />
                            <button
                                type="button"
                                @click="removeThumbnail"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
                            >
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="cursor-pointer">
                                <span class="btn btn-secondary">
                                    <i class="ri-image-add-line mr-2"></i>
                                    {{ thumbnailPreview || props.tour?.thumbnail_url ? 'Change Image' : 'Upload Image' }}
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleThumbnailChange"
                                />
                            </label>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Recommended: 800x600 pixels, JPG or PNG</p>
                    </div>
                </div>

                <!-- Gallery -->
                <div>
                    <AppLabel>Gallery Images</AppLabel>
                    <div class="mt-2">
                        <div v-if="galleryPreviews.length > 0 || (props.tour?.gallery && props.tour.gallery.length > 0)" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                            <!-- Existing gallery images -->
                            <div
                                v-for="image in props.tour?.gallery || []"
                                :key="'existing-' + image.id"
                                class="relative group"
                                v-show="!removedGalleryIds.includes(image.id)"
                            >
                                <img
                                    :src="image.url"
                                    class="h-32 w-full rounded-xl object-cover border-2 border-slate-200"
                                    :alt="image.name"
                                />
                                <button
                                    type="button"
                                    @click="removeExistingGalleryImage(image.id)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <!-- New gallery previews -->
                            <div
                                v-for="(preview, index) in galleryPreviews"
                                :key="'new-' + index"
                                class="relative group"
                            >
                                <img
                                    :src="preview"
                                    class="h-32 w-full rounded-xl object-cover border-2 border-emerald-300"
                                    alt="New gallery image"
                                />
                                <button
                                    type="button"
                                    @click="removeNewGalleryImage(index)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <i class="ri-close-line"></i>
                                </button>
                                <span class="absolute bottom-2 left-2 bg-emerald-500 text-white text-xs px-2 py-0.5 rounded">New</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="cursor-pointer">
                                <span class="btn btn-secondary">
                                    <i class="ri-image-add-line mr-2"></i>
                                    Add Gallery Images
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    multiple
                                    @change="handleGalleryChange"
                                />
                            </label>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">You can select multiple images at once</p>
                    </div>
                </div>
            </div>

            <!-- SEO Tab -->
            <div v-show="activeTab === 'seo'" class="space-y-4">
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
                        placeholder="keyword1, keyword2, keyword3"
                    />
                </div>
            </div>
        </template>
        <template #footer>
            <AppButton class="btn btn-secondary mr-2" @click="$inertia.visit(route('tour.index'))">
                Cancel
            </AppButton>
            <AppButton class="btn btn-primary" @click="submitForm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Tour' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('Tour')

const props = defineProps({
    tour: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    destinations: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tours', href: route('tour.index') },
    { label: props.tour ? 'Edit' : 'Create', last: true }
]

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-information-line' },
    { id: 'pricing', label: 'Pricing', icon: 'ri-money-dollar-circle-line' },
    { id: 'itinerary', label: 'Itinerary', icon: 'ri-route-line' },
    { id: 'inclusions', label: 'Inclusions', icon: 'ri-checkbox-circle-line' },
    { id: 'media', label: 'Media', icon: 'ri-image-line' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-eye-line' }
]

const activeTab = ref('basic')

const difficultyOptions = [
    { value: 'easy', label: 'Easy' },
    { value: 'moderate', label: 'Moderate' },
    { value: 'challenging', label: 'Challenging' },
    { value: 'extreme', label: 'Extreme' }
]

const tourTypeOptions = [
    { value: 'group', label: 'Group Tour' },
    { value: 'private', label: 'Private Tour' },
    { value: 'custom', label: 'Custom Tour' }
]

const depositTypeOptions = [
    { value: 'fixed', label: 'Fixed Amount' },
    { value: 'percentage', label: 'Percentage' }
]

const form = useForm({
    name: props.tour?.name || '',
    slug: props.tour?.slug || '',
    code: props.tour?.code || '',
    category_id: props.tour?.category_id || null,
    destination_id: props.tour?.destination_id || null,
    excerpt: props.tour?.excerpt || '',
    description: props.tour?.description || '',
    duration_days: props.tour?.duration_days || 1,
    duration_nights: props.tour?.duration_nights || 0,
    difficulty_level: props.tour?.difficulty_level || 'easy',
    tour_type: props.tour?.tour_type || 'group',
    base_price: props.tour?.base_price || 0,
    discount_price: props.tour?.discount_price || null,
    child_price: props.tour?.child_price || null,
    deposit_amount: props.tour?.deposit_amount || null,
    deposit_type: props.tour?.deposit_type || 'fixed',
    cancellation_days: props.tour?.cancellation_days || 0,
    min_group_size: props.tour?.min_group_size || 1,
    max_group_size: props.tour?.max_group_size || null,
    start_location: props.tour?.start_location || 'Dhaka',
    end_location: props.tour?.end_location || 'Dhaka',
    valid_from: props.tour?.valid_from || null,
    valid_to: props.tour?.valid_to || null,
    is_featured: props.tour?.is_featured ?? false,
    is_bestseller: props.tour?.is_bestseller ?? false,
    is_active: props.tour?.is_active ?? true,
    meta_title: props.tour?.meta_title || '',
    meta_description: props.tour?.meta_description || '',
    meta_keywords: props.tour?.meta_keywords || '',
    itineraries: props.tour?.itineraries || [],
    inclusions: props.tour?.inclusions || [''],
    exclusions: props.tour?.exclusions || [''],
    thumbnail: null,
    gallery: [],
    remove_thumbnail: false,
    remove_gallery_ids: []
})

const { isCreate, isEdit } = useFormContext()

const generateSlug = () => {
    if (isCreate.value) {
        form.slug = form.name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '')
    }
}

const addItinerary = () => {
    const nextDay = form.itineraries.length + 1
    form.itineraries.push({
        day_number: nextDay,
        title: `Day ${nextDay}`,
        description: '',
        accommodation: '',
        meals_included: []
    })
}

const removeItinerary = (index) => {
    form.itineraries.splice(index, 1)
    // Renumber days
    form.itineraries.forEach((item, i) => {
        item.day_number = i + 1
    })
}

const submitForm = () => {
    // Filter out empty inclusions/exclusions
    form.inclusions = form.inclusions.filter(i => i.trim())
    form.exclusions = form.exclusions.filter(e => e.trim())

    if (isCreate.value) {
        form.post(route('tour.store'), {
            forceFormData: true
        })
    }
    if (isEdit.value) {
        form.transform(data => ({
            ...data,
            _method: 'PUT'
        })).post(route('tour.update', props.tour.id), {
            forceFormData: true
        })
    }
}

const { errorsFields } = useFormErrors()

// Media handling
const thumbnailPreview = ref(null)
const galleryPreviews = ref([])
const galleryFiles = ref([])
const removedGalleryIds = ref([])

const handleThumbnailChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.thumbnail = file
        form.remove_thumbnail = false
        thumbnailPreview.value = URL.createObjectURL(file)
    }
}

const removeThumbnail = () => {
    form.thumbnail = null
    form.remove_thumbnail = true
    thumbnailPreview.value = null
}

const handleGalleryChange = (event) => {
    const files = Array.from(event.target.files)
    files.forEach(file => {
        form.gallery.push(file)
        galleryPreviews.value.push(URL.createObjectURL(file))
    })
}

const removeNewGalleryImage = (index) => {
    form.gallery.splice(index, 1)
    galleryPreviews.value.splice(index, 1)
}

const removeExistingGalleryImage = (id) => {
    removedGalleryIds.value.push(id)
    form.remove_gallery_ids.push(id)
}
</script>
