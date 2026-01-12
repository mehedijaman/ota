<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('tourDestination.index'))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-information-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Basic Information</span>
                            <p class="text-neutral-9 text-xs">Destination name and details</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <AppFormErrors class="mb-4" />

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <AppLabel for="name">
                                    Name <span class="text-red-500">*</span>
                                </AppLabel>
                                <AppInputText
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Enter destination name"
                                    @input="generateSlug"
                                    :class="{ 'input-error': errorsFields.includes('name') }"
                                />
                            </div>

                            <div>
                                <AppLabel for="slug">
                                    URL Slug <span class="text-red-500">*</span>
                                </AppLabel>
                                <div class="relative">
                                    <span class="text-neutral-9 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm">/destination/</span>
                                    <AppInputText
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        class="pl-24"
                                        :class="{ 'input-error': errorsFields.includes('slug') }"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <AppLabel for="country">
                                    <i class="ri-earth-line mr-1 text-neutral-9"></i>
                                    Country
                                </AppLabel>
                                <AppInputText
                                    id="country"
                                    v-model="form.country"
                                    type="text"
                                    placeholder="e.g., Bangladesh"
                                />
                            </div>

                            <div>
                                <AppLabel for="city">
                                    <i class="ri-building-line mr-1 text-neutral-9"></i>
                                    City
                                </AppLabel>
                                <AppInputText
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    placeholder="e.g., Cox's Bazar"
                                />
                            </div>
                        </div>

                        <div>
                            <AppLabel for="description">Description</AppLabel>
                            <AppTextArea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Brief description of this destination"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Location -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg shadow-rose-500/25">
                            <i class="ri-map-pin-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Location Coordinates</span>
                            <p class="text-neutral-9 text-xs">GPS coordinates for map display</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="latitude">Latitude</AppLabel>
                            <AppInputText
                                id="latitude"
                                v-model="form.latitude"
                                type="number"
                                step="0.00000001"
                                min="-90"
                                max="90"
                                placeholder="e.g., 21.4272"
                            />
                            <p class="text-neutral-9 mt-1 text-xs">Range: -90 to 90</p>
                        </div>

                        <div>
                            <AppLabel for="longitude">Longitude</AppLabel>
                            <AppInputText
                                id="longitude"
                                v-model="form.longitude"
                                type="number"
                                step="0.00000001"
                                min="-180"
                                max="180"
                                placeholder="e.g., 92.0058"
                            />
                            <p class="text-neutral-9 mt-1 text-xs">Range: -180 to 180</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Gallery -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-gallery-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Gallery</span>
                            <p class="text-neutral-9 text-xs">Additional destination images</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-if="galleryPreviews.length > 0 || (props.destination?.gallery && props.destination.gallery.length > 0)" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                            <div
                                v-for="image in props.destination?.gallery || []"
                                :key="'existing-' + image.id"
                                class="group relative"
                                v-show="!removedGalleryIds.includes(image.id)"
                            >
                                <img
                                    :src="image.url"
                                    class="border-neutral-6 aspect-square w-full rounded-xl border-2 object-cover"
                                    :alt="image.name"
                                />
                                <div class="absolute inset-0 flex items-center justify-center rounded-xl bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        type="button"
                                        @click="removeExistingGalleryImage(image.id)"
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-white transition-transform hover:scale-110"
                                    >
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div
                                v-for="(preview, index) in galleryPreviews"
                                :key="'new-' + index"
                                class="group relative"
                            >
                                <img
                                    :src="preview"
                                    class="aspect-square w-full rounded-xl border-2 border-emerald-400 object-cover"
                                    alt="New gallery image"
                                />
                                <span class="absolute left-2 top-2 rounded bg-emerald-500 px-2 py-0.5 text-xs text-white">New</span>
                                <div class="absolute inset-0 flex items-center justify-center rounded-xl bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button
                                        type="button"
                                        @click="removeNewGalleryImage(index)"
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-white transition-transform hover:scale-110"
                                    >
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border-neutral-6 bg-neutral-2/50 hover:bg-neutral-3/50 rounded-xl border-2 border-dashed p-6 text-center transition-colors">
                            <label class="cursor-pointer">
                                <div class="bg-neutral-3 mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl">
                                    <i class="ri-image-add-line text-neutral-9 text-2xl"></i>
                                </div>
                                <p class="text-neutral-11 mb-1 text-sm font-medium">Click to add gallery images</p>
                                <p class="text-neutral-9 text-xs">Select multiple images at once</p>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    multiple
                                    @change="handleGalleryChange"
                                />
                            </label>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- SEO Settings -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/25">
                            <i class="ri-search-eye-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">SEO Settings</span>
                            <p class="text-neutral-9 text-xs">Search engine optimization</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="meta_title">Meta Title</AppLabel>
                            <AppInputText
                                id="meta_title"
                                v-model="form.meta_title"
                                type="text"
                                placeholder="SEO title (leave empty to use destination name)"
                            />
                            <p class="text-neutral-9 mt-1 text-xs">{{ form.meta_title?.length || 0 }}/60 characters</p>
                        </div>

                        <div>
                            <AppLabel for="meta_description">Meta Description</AppLabel>
                            <AppTextArea
                                id="meta_description"
                                v-model="form.meta_description"
                                rows="2"
                                placeholder="SEO description for search results"
                            />
                            <p class="text-neutral-9 mt-1 text-xs">{{ form.meta_description?.length || 0 }}/160 characters</p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Publish Settings -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-settings-3-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Settings</span>
                            <p class="text-neutral-9 text-xs">Visibility options</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div
                            class="border-neutral-6 bg-neutral-2/50 flex items-center justify-between rounded-xl border p-4"
                            :class="{ 'border-emerald-500/50 bg-emerald-50/50 dark:bg-emerald-900/20': form.is_active }"
                        >
                            <div class="flex items-center gap-3">
                                <div :class="form.is_active ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-neutral-3 text-neutral-9'" class="flex h-9 w-9 items-center justify-center rounded-lg transition-colors">
                                    <i :class="form.is_active ? 'ri-eye-line' : 'ri-eye-off-line'" class="text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-neutral-12 text-sm font-medium">Active</p>
                                    <p class="text-neutral-9 text-xs">Show on website</p>
                                </div>
                            </div>
                            <AppCheckbox
                                id="is_active"
                                v-model="form.is_active"
                                :checked="form.is_active"
                            />
                        </div>

                        <div
                            class="border-neutral-6 bg-neutral-2/50 flex items-center justify-between rounded-xl border p-4"
                            :class="{ 'border-amber-500/50 bg-amber-50/50 dark:bg-amber-900/20': form.is_featured }"
                        >
                            <div class="flex items-center gap-3">
                                <div :class="form.is_featured ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400' : 'bg-neutral-3 text-neutral-9'" class="flex h-9 w-9 items-center justify-center rounded-lg transition-colors">
                                    <i :class="form.is_featured ? 'ri-star-fill' : 'ri-star-line'" class="text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-neutral-12 text-sm font-medium">Featured</p>
                                    <p class="text-neutral-9 text-xs">Highlight on homepage</p>
                                </div>
                            </div>
                            <AppCheckbox
                                id="is_featured"
                                v-model="form.is_featured"
                                :checked="form.is_featured"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-2">
                        <AppButton class="btn btn-primary w-full" @click="submitForm" :disabled="form.processing">
                            <i class="ri-save-line mr-2"></i>
                            {{ form.processing ? 'Saving...' : (isEdit ? 'Update Destination' : 'Create Destination') }}
                        </AppButton>
                        <AppButton class="btn btn-secondary w-full" @click="$inertia.visit(route('tourDestination.index'))">
                            Cancel
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <!-- Featured Image -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-image-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Featured Image</span>
                            <p class="text-neutral-9 text-xs">Main destination image</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div
                            v-if="imagePreview || props.destination?.image_url"
                            class="group relative overflow-hidden rounded-xl"
                        >
                            <img
                                :src="imagePreview || props.destination?.image_url"
                                class="aspect-video w-full object-cover"
                                alt="Destination image"
                            />
                            <div class="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                                <label class="cursor-pointer">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-slate-700 transition-transform hover:scale-110">
                                        <i class="ri-pencil-line"></i>
                                    </span>
                                    <input
                                        type="file"
                                        class="hidden"
                                        accept="image/*"
                                        @change="handleImageChange"
                                    />
                                </label>
                                <button
                                    type="button"
                                    @click="removeImage"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-white transition-transform hover:scale-110"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>

                        <div v-else class="border-neutral-6 bg-neutral-2/50 hover:bg-neutral-3/50 rounded-xl border-2 border-dashed p-6 text-center transition-colors">
                            <label class="cursor-pointer">
                                <div class="bg-neutral-3 mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl">
                                    <i class="ri-image-add-line text-neutral-9 text-2xl"></i>
                                </div>
                                <p class="text-neutral-11 mb-1 text-sm font-medium">Click to upload</p>
                                <p class="text-neutral-9 text-xs">PNG, JPG up to 2MB</p>
                                <p class="text-neutral-9 mt-1 text-xs">Recommended: 800×600</p>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleImageChange"
                                />
                            </label>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('Tour Destination')

const props = defineProps({
    destination: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Destinations', href: route('tourDestination.index') },
    { label: props.destination ? 'Edit' : 'Create', last: true }
]

const form = useForm({
    name: props.destination?.name || '',
    slug: props.destination?.slug || '',
    country: props.destination?.country || 'Bangladesh',
    city: props.destination?.city || '',
    description: props.destination?.description || '',
    latitude: props.destination?.latitude || null,
    longitude: props.destination?.longitude || null,
    is_featured: props.destination?.is_featured ?? false,
    is_active: props.destination?.is_active ?? true,
    meta_title: props.destination?.meta_title || '',
    meta_description: props.destination?.meta_description || '',
    image: null,
    remove_image: false,
    gallery: [],
    remove_gallery_ids: [],
})

const { isCreate, isEdit } = useFormContext()
const { errorsFields } = useFormErrors()

const generateSlug = () => {
    if (isCreate.value) {
        form.slug = form.name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '')
    }
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('tourDestination.store'), {
            forceFormData: true
        })
    }
    if (isEdit.value) {
        form.transform(data => ({
            ...data,
            _method: 'PUT'
        })).post(route('tourDestination.update', props.destination.id), {
            forceFormData: true
        })
    }
}

// Media handling
const imagePreview = ref(null)
const galleryPreviews = ref([])
const removedGalleryIds = ref([])

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.image = file
        form.remove_image = false
        imagePreview.value = URL.createObjectURL(file)
    }
}

const removeImage = () => {
    form.image = null
    form.remove_image = true
    imagePreview.value = null
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
