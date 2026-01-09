<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>{{ isEdit ? 'Edit Category' : 'Create Category' }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="space-y-4">
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

                <div>
                    <AppLabel for="icon">Icon</AppLabel>
                    <AppInputText
                        id="icon"
                        v-model="form.icon"
                        type="text"
                        placeholder="e.g., sun, compass, mountain"
                    />
                    <p class="text-xs text-slate-500 mt-1">Remix icon name (without ri- prefix)</p>
                </div>

                <div>
                    <AppLabel for="description">Description</AppLabel>
                    <AppTextArea
                        id="description"
                        v-model="form.description"
                        rows="3"
                    />
                </div>

                <div>
                    <AppLabel for="sort_order">Sort Order</AppLabel>
                    <AppInputText
                        id="sort_order"
                        v-model="form.sort_order"
                        type="number"
                        min="0"
                    />
                </div>

                <div class="flex items-center gap-4">
                    <AppCheckbox
                        id="is_active"
                        v-model="form.is_active"
                        :checked="form.is_active"
                        label="Active"
                    />
                </div>

                <!-- Image Upload -->
                <div class="border-t pt-4">
                    <h4 class="font-medium mb-2">Category Image</h4>
                    <div>
                        <div v-if="imagePreview || props.category?.image_url" class="relative inline-block mb-4">
                            <img
                                :src="imagePreview || props.category?.image_url"
                                class="h-40 w-56 rounded-xl object-cover border-2 border-slate-200"
                                alt="Category image"
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
                                    {{ imagePreview || props.category?.image_url ? 'Change Image' : 'Upload Image' }}
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleImageChange"
                                />
                            </label>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Recommended: 800x600 pixels, JPG or PNG</p>
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
            <AppButton class="btn btn-secondary mr-2" @click="$inertia.visit(route('tourCategory.index'))">
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

const { title } = useTitle('Tour Category')

const props = defineProps({
    category: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Categories', href: route('tourCategory.index') },
    { label: props.category ? 'Edit' : 'Create', last: true }
]

const form = useForm({
    name: props.category?.name || '',
    slug: props.category?.slug || '',
    description: props.category?.description || '',
    icon: props.category?.icon || '',
    is_active: props.category?.is_active ?? true,
    sort_order: props.category?.sort_order || 0,
    meta_title: props.category?.meta_title || '',
    meta_description: props.category?.meta_description || '',
    image: null,
    remove_image: false,
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
        form.post(route('tourCategory.store'), {
            forceFormData: true
        })
    }
    if (isEdit.value) {
        form.transform(data => ({
            ...data,
            _method: 'PUT'
        })).post(route('tourCategory.update', props.category.id), {
            forceFormData: true
        })
    }
}

const { errorsFields } = useFormErrors()

// Image handling
import { ref } from 'vue'
const imagePreview = ref(null)

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
</script>
