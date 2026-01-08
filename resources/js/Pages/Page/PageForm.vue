<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('page.index'))"
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
                <!-- Page Content Card -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-file-text-line text-primary"></i>
                            Page Content
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
                                    placeholder="Enter page title"
                                    autocomplete="off"
                                />
                                <p v-if="form.title" class="mt-1 text-xs text-base-content/60">
                                    Slug: <code class="text-primary">{{ generateSlug(form.title) }}</code>
                                </p>
                            </div>

                            <!-- Content Editor -->
                            <div>
                                <AppLabel for="content">
                                    Content <span class="text-error">*</span>
                                </AppLabel>
                                <AppTipTapEditor
                                    v-model="form.content"
                                    editor-id="page-content"
                                    :editor-class="form.errors.content ? 'app-tip-tap-error' : ''"
                                    :file-upload-url="route('page.uploadEditorImage')"
                                />
                            </div>
                        </div>
                    </template>
                </AppCard>

                <!-- Featured Image Card -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-image-line text-primary"></i>
                            Featured Image
                        </div>
                    </template>
                    <template #content>
                        <div>
                            <!-- Current Image Preview -->
                            <div v-if="currentImageUrl && !form.remove_previous_image && !imagePreview" class="mb-4">
                                <div class="relative inline-block">
                                    <img
                                        :src="currentImageUrl"
                                        alt="Current featured image"
                                        class="h-48 w-auto rounded-lg border border-base-300 object-cover shadow-sm"
                                    />
                                    <button
                                        type="button"
                                        class="absolute -top-2 -right-2 btn btn-circle btn-xs btn-error"
                                        @click="removeImage"
                                    >
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-base-content/60">Current featured image</p>
                            </div>

                            <!-- New Image Preview -->
                            <div v-if="imagePreview" class="mb-4">
                                <div class="relative inline-block">
                                    <img
                                        :src="imagePreview"
                                        alt="New featured image"
                                        class="h-48 w-auto rounded-lg border border-base-300 object-cover shadow-sm"
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
                                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                                    :class="form.errors.image ? 'border-error bg-error/5' : 'border-base-300 bg-base-200/50 hover:bg-base-200'"
                                >
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <i class="ri-image-add-line text-4xl text-base-content/30 mb-3"></i>
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
                    </template>
                </AppCard>

                <!-- SEO Card -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-seo-line text-primary"></i>
                            SEO Settings
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-5">
                            <!-- SEO Preview -->
                            <div class="p-4 rounded-lg bg-base-200/50 border border-base-300">
                                <p class="text-xs text-base-content/50 mb-2">Search Preview</p>
                                <p class="text-primary text-lg font-medium line-clamp-1">
                                    {{ form.meta_tag_title || form.title || 'Page Title' }}
                                </p>
                                <p class="text-success text-sm">{{ baseUrl }}/{{ generateSlug(form.title) || 'page-url' }}</p>
                                <p class="text-base-content/70 text-sm line-clamp-2 mt-1">
                                    {{ form.meta_tag_description || stripHtml(form.content).substring(0, 160) || 'Page description will appear here...' }}
                                </p>
                            </div>

                            <!-- Meta Title -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <AppLabel for="meta_tag_title" class="!mb-0">Meta Title</AppLabel>
                                    <span
                                        class="text-xs"
                                        :class="(form.meta_tag_title?.length || 0) > 60 ? 'text-error' : 'text-base-content/50'"
                                    >
                                        {{ form.meta_tag_title?.length || 0 }}/60
                                    </span>
                                </div>
                                <AppInputText
                                    id="meta_tag_title"
                                    v-model="form.meta_tag_title"
                                    type="text"
                                    :class="{ 'input-error': form.errors.meta_tag_title }"
                                    placeholder="SEO-optimized title (max 60 characters)"
                                    maxlength="60"
                                />
                            </div>

                            <!-- Meta Description -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <AppLabel for="meta_tag_description" class="!mb-0">Meta Description</AppLabel>
                                    <span
                                        class="text-xs"
                                        :class="(form.meta_tag_description?.length || 0) > 160 ? 'text-error' : 'text-base-content/50'"
                                    >
                                        {{ form.meta_tag_description?.length || 0 }}/160
                                    </span>
                                </div>
                                <AppTextArea
                                    id="meta_tag_description"
                                    v-model="form.meta_tag_description"
                                    rows="3"
                                    :class="{ 'input-error': form.errors.meta_tag_description }"
                                    placeholder="Brief description for search engines (max 160 characters)"
                                    maxlength="160"
                                />
                            </div>

                            <!-- Auto-generate Button -->
                            <AppButton
                                type="button"
                                class="btn btn-ghost btn-sm"
                                @click="autoGenerateSeo"
                            >
                                <i class="ri-magic-line mr-1"></i>
                                Auto-generate from content
                            </AppButton>
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
                        {{ page ? 'Update Page' : 'Create Page' }}
                    </AppButton>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Publish Settings -->
                <AppCard>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="ri-calendar-check-line text-primary"></i>
                            Publish Settings
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-5">
                            <!-- Status Badge -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-base-200/50">
                                <span class="text-sm font-medium">Status</span>
                                <span
                                    class="badge"
                                    :class="isPublished ? 'badge-success' : 'badge-warning'"
                                >
                                    {{ isPublished ? 'Published' : 'Draft' }}
                                </span>
                            </div>

                            <!-- Publish Date -->
                            <div>
                                <AppLabel for="published_at">Publish Date</AppLabel>
                                <AppInputText
                                    id="published_at"
                                    v-model="form.published_at"
                                    type="date"
                                    :class="{ 'input-error': form.errors.published_at }"
                                />
                                <p class="mt-1 text-xs text-base-content/60">
                                    Leave empty to save as draft
                                </p>
                            </div>

                            <!-- Active Toggle -->
                            <div class="rounded-lg border border-base-300 bg-base-200/30 p-4">
                                <div class="flex items-start gap-3">
                                    <AppCheckbox
                                        id="active"
                                        v-model="form.active"
                                        :value="true"
                                    />
                                    <div class="flex-1">
                                        <AppLabel for="active" class="!mb-0 cursor-pointer font-medium">
                                            Active
                                        </AppLabel>
                                        <p class="text-xs text-base-content/60 mt-1">
                                            Show this page on the website
                                        </p>
                                    </div>
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
                                {{ page ? 'Update Page' : 'Create Page' }}
                            </AppButton>
                            <AppButton
                                type="button"
                                class="btn btn-ghost w-full"
                                @click="$inertia.visit(route('page.index'))"
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
    page: {
        type: Object,
        default: null
    }
})

const isEdit = computed(() => !!props.page)
const { title } = useTitle(isEdit.value ? 'Edit Page' : 'Create Page')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Pages', href: route('page.index') },
    { label: title, last: true }
]

// Base URL for SEO preview
const baseUrl = window.location.origin

// Helper to strip HTML
const stripHtml = (html) => {
    if (!html) return ''
    return html.replace(/<\/?[^>]+(>|$)/g, '')
}

// Generate slug from title
const generateSlug = (text) => {
    if (!text) return ''
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
}

// Current image URL for edit mode
const currentImageUrl = computed(() => props.page?.image_url || null)

// Published status
const isPublished = computed(() => {
    if (!form.published_at) return false
    return new Date(form.published_at) <= new Date()
})

// Form data
const form = useForm({
    title: props.page?.title || '',
    content: props.page?.content || '',
    image: null,
    meta_tag_title: props.page?.meta_tag_title || '',
    meta_tag_description: props.page?.meta_tag_description || '',
    published_at: props.page?.published_at ? new Date(props.page.published_at).toISOString().split('T')[0] : '',
    active: props.page?.active ?? true,
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

// Auto-generate SEO
const autoGenerateSeo = () => {
    form.meta_tag_title = form.title.substring(0, 60)
    form.meta_tag_description = stripHtml(form.content).substring(0, 160)
}

// Submit form
const submitForm = () => {
    if (isEdit.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('page.update', props.page.id))
    } else {
        form.post(route('page.store'))
    }
}
</script>
