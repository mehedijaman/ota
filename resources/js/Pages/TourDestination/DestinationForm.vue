<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-2/3">
        <template #title>{{ isEdit ? 'Edit Destination' : 'Create Destination' }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="name">Name *</AppLabel>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="country">Country</AppLabel>
                        <AppInputText
                            id="country"
                            v-model="form.country"
                            type="text"
                        />
                    </div>

                    <div>
                        <AppLabel for="city">City</AppLabel>
                        <AppInputText
                            id="city"
                            v-model="form.city"
                            type="text"
                        />
                    </div>
                </div>

                <div>
                    <AppLabel for="description">Description</AppLabel>
                    <AppTextArea
                        id="description"
                        v-model="form.description"
                        rows="3"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="latitude">Latitude</AppLabel>
                        <AppInputText
                            id="latitude"
                            v-model="form.latitude"
                            type="number"
                            step="0.00000001"
                            min="-90"
                            max="90"
                        />
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
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <AppCheckbox
                        id="is_featured"
                        v-model="form.is_featured"
                        :checked="form.is_featured"
                        label="Featured Destination"
                    />
                    <AppCheckbox
                        id="is_active"
                        v-model="form.is_active"
                        :checked="form.is_active"
                        label="Active"
                    />
                </div>

                <!-- Media Section -->
                <div class="border-t pt-4">
                    <h4 class="font-medium mb-4">Media</h4>

                    <!-- Featured Image -->
                    <div class="mb-6">
                        <AppLabel>Featured Image</AppLabel>
                        <div class="mt-2">
                            <div v-if="imagePreview || props.destination?.image_url" class="relative inline-block mb-4">
                                <img
                                    :src="imagePreview || props.destination?.image_url"
                                    class="h-40 w-56 rounded-xl object-cover border-2 border-slate-200"
                                    alt="Destination image"
                                />
                                <button
                                    type="button"
                                    @click="removeImage"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
                                >
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="cursor-pointer">
                                    <span class="btn btn-secondary">
                                        <i class="ri-image-add-line mr-2"></i>
                                        {{ imagePreview || props.destination?.image_url ? 'Change Image' : 'Upload Image' }}
                                    </span>
                                    <input
                                        type="file"
                                        class="hidden"
                                        accept="image/*"
                                        @change="handleImageChange"
                                    />
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">Recommended: 800x600 pixels</p>
                        </div>
                    </div>

                    <!-- Gallery -->
                    <div>
                        <AppLabel>Gallery Images</AppLabel>
                        <div class="mt-2">
                            <div v-if="galleryPreviews.length > 0 || (props.destination?.gallery && props.destination.gallery.length > 0)" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                                <div
                                    v-for="image in props.destination?.gallery || []"
                                    :key="'existing-' + image.id"
                                    class="relative group"
                                    v-show="!removedGalleryIds.includes(image.id)"
                                >
                                    <img
                                        :src="image.url"
                                        class="h-28 w-full rounded-xl object-cover border-2 border-slate-200"
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
                                <div
                                    v-for="(preview, index) in galleryPreviews"
                                    :key="'new-' + index"
                                    class="relative group"
                                >
                                    <img
                                        :src="preview"
                                        class="h-28 w-full rounded-xl object-cover border-2 border-emerald-300"
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
                            <p class="text-xs text-slate-500 mt-2">Select multiple images at once</p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-medium mb-2">SEO Settings</h4>
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
                                rows="2"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-secondary mr-2" @click="$inertia.visit(route('tourDestination.index'))">
                Cancel
            </AppButton>
            <AppButton class="btn btn-primary" @click="submitForm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
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

const { errorsFields } = useFormErrors()

// Media handling
import { ref } from 'vue'
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
