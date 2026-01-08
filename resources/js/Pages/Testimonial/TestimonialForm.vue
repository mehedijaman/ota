<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('testimonial.index'))"
            >
                <i class="ri-arrow-left-s-line mr-1"></i>
                Back to List
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm" enctype="multipart/form-data">
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Testimonial Details Card -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-chat-quote-line text-primary"></i>
                            Testimonial Details
                        </div>
                    </template>
                    <template #content>
                        <AppFormErrors class="mb-6" />

                        <div class="space-y-6">
                            <!-- Person Info Row -->
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <AppLabel for="name">
                                        Name <span class="text-error">*</span>
                                    </AppLabel>
                                    <AppInputText
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        :class="{ 'input-error': form.errors.name }"
                                        placeholder="John Doe"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="designation">Designation</AppLabel>
                                    <AppInputText
                                        id="designation"
                                        v-model="form.designation"
                                        type="text"
                                        :class="{ 'input-error': form.errors.designation }"
                                        placeholder="CEO, Manager, etc."
                                    />
                                </div>

                                <div>
                                    <AppLabel for="company">Company</AppLabel>
                                    <AppInputText
                                        id="company"
                                        v-model="form.company"
                                        type="text"
                                        :class="{ 'input-error': form.errors.company }"
                                        placeholder="Company Name"
                                    />
                                </div>
                            </div>

                            <!-- Title -->
                            <div>
                                <AppLabel for="title">Title</AppLabel>
                                <AppInputText
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    :class="{ 'input-error': form.errors.title }"
                                    placeholder="Testimonial headline or title"
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <AppLabel for="description">
                                    Testimonial Content <span class="text-error">*</span>
                                </AppLabel>
                                <AppTextArea
                                    id="description"
                                    v-model="form.description"
                                    rows="5"
                                    :class="{ 'input-error': form.errors.description }"
                                    placeholder="What did they say about your product or service?"
                                />
                                <p class="mt-1 text-xs text-base-content/60">
                                    The main testimonial quote or review
                                </p>
                            </div>
                        </div>
                    </template>
                </AppCard>

                <!-- Photo Card -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-user-smile-line text-primary"></i>
                            Person Photo
                        </div>
                    </template>
                    <template #content>
                        <div>
                            <!-- Current Image Preview -->
                            <div v-if="currentImageUrl && !form.remove_previous_image && !imagePreview" class="mb-4">
                                <div class="relative inline-block">
                                    <img
                                        :src="currentImageUrl"
                                        alt="Current photo"
                                        class="h-32 w-32 rounded-full border-4 border-base-300 object-cover shadow-lg"
                                    />
                                    <button
                                        type="button"
                                        class="absolute -top-1 -right-1 btn btn-circle btn-xs btn-error"
                                        @click="removeImage"
                                    >
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-base-content/60">Current photo</p>
                            </div>

                            <!-- New Image Preview -->
                            <div v-if="imagePreview" class="mb-4">
                                <div class="relative inline-block">
                                    <img
                                        :src="imagePreview"
                                        alt="New photo"
                                        class="h-32 w-32 rounded-full border-4 border-success object-cover shadow-lg"
                                    />
                                    <button
                                        type="button"
                                        class="absolute -top-1 -right-1 btn btn-circle btn-xs btn-error"
                                        @click="clearNewImage"
                                    >
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-success">New photo selected</p>
                            </div>

                            <!-- File Input -->
                            <div
                                v-show="!imagePreview && (!currentImageUrl || form.remove_previous_image)"
                                class="flex items-center justify-center w-full"
                            >
                                <label
                                    for="image"
                                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                                    :class="form.errors.image ? 'border-error bg-error/5' : 'border-base-300 bg-base-200/50 hover:bg-base-200'"
                                >
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <div class="w-16 h-16 rounded-full bg-base-300 flex items-center justify-center mb-3">
                                            <i class="ri-user-add-line text-2xl text-base-content/40"></i>
                                        </div>
                                        <p class="mb-1 text-sm text-base-content/70">
                                            <span class="font-semibold">Upload photo</span>
                                        </p>
                                        <p class="text-xs text-base-content/50">PNG, JPG (max 2MB)</p>
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
                    </template>
                </AppCard>

                <!-- Mobile Actions -->
                <div class="flex gap-3 lg:hidden">
                    <AppButton
                        type="submit"
                        class="btn btn-primary flex-1"
                        :disabled="form.processing"
                    >
                        <i v-if="form.processing" class="ri-loader-4-line animate-spin mr-1"></i>
                        <i v-else class="ri-save-line mr-1"></i>
                        {{ testimonial ? 'Update' : 'Create' }}
                    </AppButton>
                </div>
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
                            <!-- Featured Toggle -->
                            <div class="rounded-lg border border-warning/30 bg-warning/5 p-4">
                                <div class="flex items-start gap-3">
                                    <AppCheckbox
                                        id="featured"
                                        v-model="form.featured"
                                        :value="true"
                                    />
                                    <div class="flex-1">
                                        <AppLabel for="featured" class="!mb-0 cursor-pointer font-medium">
                                            <i class="ri-star-line text-warning mr-1"></i>
                                            Featured
                                        </AppLabel>
                                        <p class="text-xs text-base-content/60 mt-1">
                                            Show prominently on the homepage
                                        </p>
                                    </div>
                                    <span v-if="form.featured" class="badge badge-warning badge-sm">
                                        Featured
                                    </span>
                                </div>
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
                                            Display on the website
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

                <!-- Actions Card -->
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
                                {{ testimonial ? 'Update Testimonial' : 'Create Testimonial' }}
                            </AppButton>
                            <AppButton
                                type="button"
                                class="btn btn-ghost w-full"
                                @click="$inertia.visit(route('testimonial.index'))"
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
    testimonial: {
        type: Object,
        default: null
    }
})

const isEdit = computed(() => !!props.testimonial)
const { title } = useTitle(isEdit.value ? 'Edit Testimonial' : 'Create Testimonial')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Testimonials', href: route('testimonial.index') },
    { label: title, last: true }
]

// Current image URL for edit mode
const currentImageUrl = computed(() => props.testimonial?.image_url || null)

// Form data
const form = useForm({
    name: props.testimonial?.name || '',
    designation: props.testimonial?.designation || '',
    company: props.testimonial?.company || '',
    title: props.testimonial?.title || '',
    description: props.testimonial?.description || '',
    image: null,
    featured: props.testimonial?.featured ?? false,
    is_active: props.testimonial?.is_active ?? true,
    remove_previous_image: false
})

// Image handling
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
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}

// Submit form
const submitForm = () => {
    if (isEdit.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('testimonial.update', props.testimonial.id))
    } else {
        form.post(route('testimonial.store'))
    }
}
</script>
