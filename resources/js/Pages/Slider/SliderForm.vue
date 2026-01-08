<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('slider.index'))"
            >
                <i class="ri-arrow-left-s-line mr-1"></i>
                Back to List
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm" enctype="multipart/form-data">
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Form Section -->
            <div class="lg:col-span-2">
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-slideshow-3-line text-primary"></i>
                            Slider Details
                        </div>
                    </template>
                    <template #content>
                        <AppFormErrors class="mb-6" />

                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <AppLabel for="title">
                                    Title <span class="text-error">*</span>
                                </AppLabel>
                                <AppInputText
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    :class="{ 'input-error': form.errors.title }"
                                    placeholder="Enter an engaging slider title"
                                    autocomplete="off"
                                />
                                <p class="mt-1 text-xs text-base-content/60">
                                    The main heading displayed prominently on the slider
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <AppLabel for="description">Description</AppLabel>
                                <AppTextArea
                                    id="description"
                                    v-model="form.description"
                                    :class="{ 'input-error': form.errors.description }"
                                    placeholder="Add a compelling description to accompany the title"
                                    rows="4"
                                />
                                <p class="mt-1 text-xs text-base-content/60">
                                    Optional supporting text displayed below the title
                                </p>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <AppLabel>
                                    Slider Image <span v-if="!slider" class="text-error">*</span>
                                </AppLabel>
                                <div class="mt-2">
                                    <!-- Current Image Preview -->
                                    <div v-if="currentImageUrl && !form.remove_previous_image && !imagePreview" class="mb-4">
                                        <div class="relative inline-block">
                                            <img
                                                :src="currentImageUrl"
                                                alt="Current slider image"
                                                class="h-40 w-auto rounded-lg border border-base-300 object-cover shadow-sm"
                                            />
                                            <button
                                                type="button"
                                                class="absolute -top-2 -right-2 btn btn-circle btn-xs btn-error"
                                                @click="removeImage"
                                            >
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                        <p class="mt-2 text-xs text-base-content/60">Current image</p>
                                    </div>

                                    <!-- New Image Preview -->
                                    <div v-if="imagePreview" class="mb-4">
                                        <div class="relative inline-block">
                                            <img
                                                :src="imagePreview"
                                                alt="New slider image"
                                                class="h-40 w-auto rounded-lg border border-base-300 object-cover shadow-sm"
                                            />
                                            <button
                                                type="button"
                                                class="absolute -top-2 -right-2 btn btn-circle btn-xs btn-error"
                                                @click="clearNewImage"
                                            >
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                        <p class="mt-2 text-xs text-success">New image selected</p>
                                    </div>

                                    <!-- File Input -->
                                    <div
                                        v-show="!imagePreview && (!currentImageUrl || form.remove_previous_image)"
                                        class="flex items-center justify-center w-full"
                                    >
                                        <label
                                            for="image"
                                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                                            :class="form.errors.image ? 'border-error bg-error/5' : 'border-base-300 bg-base-200/50 hover:bg-base-200'"
                                        >
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <i class="ri-upload-cloud-2-line text-3xl text-base-content/40 mb-2"></i>
                                                <p class="mb-1 text-sm text-base-content/70">
                                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="text-xs text-base-content/50">PNG, JPG or WEBP (max 2MB)</p>
                                            </div>
                                            <input
                                                id="image"
                                                ref="imageInput"
                                                type="file"
                                                class="hidden"
                                                accept="image/*"
                                                @change="handleImageChange"
                                            />
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- URL and Button Text -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <AppLabel for="url">
                                        <i class="ri-link mr-1"></i>Link URL
                                    </AppLabel>
                                    <AppInputText
                                        id="url"
                                        v-model="form.url"
                                        type="text"
                                        :class="{ 'input-error': form.errors.url }"
                                        placeholder="https://example.com/page"
                                        autocomplete="off"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="button_text">
                                        <i class="ri-cursor-fill mr-1"></i>Button Text
                                    </AppLabel>
                                    <AppInputText
                                        id="button_text"
                                        v-model="form.button_text"
                                        type="text"
                                        :class="{ 'input-error': form.errors.button_text }"
                                        placeholder="e.g. Learn More, Get Started"
                                        autocomplete="off"
                                    />
                                </div>
                            </div>

                            <!-- Form Actions (Mobile) -->
                            <div class="flex gap-3 pt-4 border-t border-base-200 lg:hidden">
                                <AppButton
                                    type="submit"
                                    class="btn btn-primary flex-1"
                                    :disabled="form.processing"
                                >
                                    <i v-if="form.processing" class="ri-loader-4-line animate-spin mr-1"></i>
                                    <i v-else class="ri-save-line mr-1"></i>
                                    {{ slider ? 'Update Slider' : 'Create Slider' }}
                                </AppButton>
                                <AppButton
                                    type="button"
                                    class="btn btn-ghost"
                                    @click="$inertia.visit(route('slider.index'))"
                                >
                                    Cancel
                                </AppButton>
                            </div>
                        </div>
                    </template>
                </AppCard>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Settings Card -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-settings-3-line text-primary"></i>
                            Settings
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-5">
                            <!-- Display Order -->
                            <div>
                                <AppLabel for="order">
                                    <i class="ri-sort-asc mr-1"></i>Display Order
                                </AppLabel>
                                <AppInputText
                                    id="order"
                                    v-model="form.order"
                                    type="number"
                                    placeholder="1"
                                    min="0"
                                />
                                <p class="mt-1 text-xs text-base-content/60">
                                    Lower numbers appear first in the slider
                                </p>
                            </div>

                            <!-- Active Toggle -->
                            <div class="rounded-lg border border-base-300 bg-base-200/30 p-4">
                                <div class="flex items-start gap-3">
                                    <AppCheckbox
                                        id="is_active"
                                        v-model="form.is_active"
                                        :value="true"
                                    />
                                    <div class="flex-1">
                                        <AppLabel for="is_active" class="!mb-0 cursor-pointer font-medium">
                                            Active
                                        </AppLabel>
                                        <p class="text-xs text-base-content/60 mt-1">
                                            When enabled, this slider will be visible on the website
                                        </p>
                                    </div>
                                    <span
                                        class="badge badge-sm"
                                        :class="form.is_active ? 'badge-success' : 'badge-error'"
                                    >
                                        {{ form.is_active ? 'Visible' : 'Hidden' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </AppCard>

                <!-- Actions Card (Desktop) -->
                <AppCard class="hidden lg:block sticky top-4">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-checkbox-circle-line text-primary"></i>
                            Actions
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-3">
                            <AppButton
                                type="submit"
                                class="btn btn-primary w-full"
                                :disabled="form.processing"
                            >
                                <i v-if="form.processing" class="ri-loader-4-line animate-spin mr-1"></i>
                                <i v-else class="ri-save-line mr-1"></i>
                                {{ slider ? 'Update Slider' : 'Create Slider' }}
                            </AppButton>
                            <AppButton
                                type="button"
                                class="btn btn-ghost w-full"
                                @click="$inertia.visit(route('slider.index'))"
                            >
                                <i class="ri-close-line mr-1"></i>
                                Cancel
                            </AppButton>
                        </div>
                    </template>
                </AppCard>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const props = defineProps({
    slider: {
        type: Object,
        default: null
    }
})

const isEdit = computed(() => !!props.slider)
const { title } = useTitle(isEdit.value ? 'Edit Slider' : 'Create Slider')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Sliders', href: route('slider.index') },
    { label: title, last: true }
]

// Current image URL for edit mode
const currentImageUrl = computed(() => props.slider?.image_url || null)

// Form data
const form = useForm({
    title: props.slider?.title || '',
    description: props.slider?.description || '',
    image: null,
    url: props.slider?.url || '',
    button_text: props.slider?.button_text || '',
    order: props.slider?.order || null,
    is_active: props.slider?.is_active ?? true,
    remove_previous_image: false
})

// Image preview
const imagePreview = ref(null)
const imageInput = ref(null)

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.image = file
        form.remove_previous_image = false
        imagePreview.value = URL.createObjectURL(file)
    }
}

const removeImage = () => {
    form.remove_previous_image = true
    form.image = null
    imagePreview.value = null
}

const clearNewImage = () => {
    form.image = null
    imagePreview.value = null
    // Reset the file input
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}

const submitForm = () => {
    if (isEdit.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('slider.update', props.slider.id))
    } else {
        form.post(route('slider.store'))
    }
}
</script>
