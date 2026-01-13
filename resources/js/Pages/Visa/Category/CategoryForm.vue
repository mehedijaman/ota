<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('visaCategory.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Input Form -->
        <div class="lg:col-span-2">
            <AppCard>
                <template #title> {{ title }} </template>
                <template #content>
                    <AppFormErrors class="mb-4" />
                    <form @submit.prevent="submitForm">
                        <div class="space-y-4">
                            <div>
                                <AppLabel for="name">Name <span class="text-red-500">*</span></AppLabel>
                                <AppInputText
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Enter category name"
                                    :class="{ 'input-error': errorsFields.includes('name') }"
                                />
                            </div>

                            <div>
                                <AppLabel for="slug">Slug</AppLabel>
                                <AppInputText
                                    id="slug"
                                    v-model="form.slug"
                                    type="text"
                                    placeholder="Enter category slug (optional)"
                                />
                            </div>

                            <div>
                                <AppLabel for="description">Description</AppLabel>
                                <AppTextArea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Enter category description"
                                />
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="h-4 w-4 rounded text-primary-600"
                                    />
                                    <span class="text-neutral-11">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <AppButton class="btn btn-primary" :disabled="form.processing" @click="submitForm">
                                <i class="ri-save-line mr-1"></i>
                                {{ isCreate ? 'Create Category' : 'Update Category' }}
                            </AppButton>
                        </div>
                    </form>
                </template>
            </AppCard>
        </div>

        <!-- Image Upload -->
        <div class="lg:col-span-1">
            <AppCard>
                <template #title> Thumbnail </template>
                <template #content>
                    <div class="space-y-4">
                    <div class="flex flex-col gap-4">
                        <div class="shrink-0">
                            <div v-if="imagePreview || props.category?.image_url" class="relative">
                                <img
                                    :src="imagePreview || props.category?.image_url"
                                    class="border-neutral-5 h-48 w-full rounded-xl border-2 object-cover shadow-lg"
                                    alt="Category image"
                                />
                                <button
                                    type="button"
                                    @click="removeImage"
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
                                    <p class="text-neutral-9 mt-2 text-sm">No image selected</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3">
                            <label class="cursor-pointer">
                                <span class="btn btn-secondary w-full justify-center">
                                    <i class="ri-upload-2-line mr-2"></i>
                                    {{ imagePreview || props.category?.image_url ? 'Change Image' : 'Upload Image' }}
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleImageChange"
                                />
                            </label>
                            <p class="text-neutral-9 text-xs text-center">
                                <i class="ri-information-line mr-1"></i>
                                Recommended: 800x600 pixels
                            </p>
                        </div>
                    </div>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { isCreate, isEdit } = useFormContext()
const { title } = useTitle(isCreate.value ? 'Create Category' : 'Edit Category')

const props = defineProps({
    category: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visas', href: route('visa.index') },
    { label: 'Categories', href: route('visaCategory.index') },
    { label: title.value, last: true }
]

const form = useForm({
    name: props.category?.name ?? '',
    slug: props.category?.slug ?? '',
    description: props.category?.description ?? '',
    is_active: props.category?.is_active ?? true,
    image: null,
    remove_image: false,
    _method: isEdit.value ? 'PUT' : 'POST'
})

const imagePreview = ref(null)

const { errorsFields } = useFormErrors()

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



const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visaCategory.store'), {
            forceFormData: true
        })
    } else {
        form.post(route('visaCategory.update', props.category.id), {
            forceFormData: true
        })
    }
}
</script>
