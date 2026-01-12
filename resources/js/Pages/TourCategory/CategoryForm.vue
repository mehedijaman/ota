<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('tourCategory.index'))">
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
                            <p class="text-neutral-9 text-xs">Category name and details</p>
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
                                    placeholder="Enter category name"
                                    @input="generateSlug"
                                    :class="{ 'input-error': errorsFields.includes('name') }"
                                />
                            </div>

                            <div>
                                <AppLabel for="slug">
                                    URL Slug <span class="text-red-500">*</span>
                                </AppLabel>
                                <div class="relative">
                                    <span class="text-neutral-9 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm">/category/</span>
                                    <AppInputText
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        class="pl-20"
                                        :class="{ 'input-error': errorsFields.includes('slug') }"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <AppLabel for="icon">Icon</AppLabel>
                                <div class="flex gap-3">
                                    <div v-if="form.icon" class="bg-neutral-3 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg">
                                        <i :class="'ri-' + form.icon + '-line'" class="text-neutral-11 text-xl"></i>
                                    </div>
                                    <AppInputText
                                        id="icon"
                                        v-model="form.icon"
                                        type="text"
                                        placeholder="e.g., sun, compass, mountain"
                                        class="w-full"
                                    />
                                </div>
                                <p class="text-neutral-9 mt-1 text-xs">Remix icon name (without ri- prefix)</p>
                            </div>

                            <div>
                                <AppLabel for="sort_order">Sort Order</AppLabel>
                                <AppInputText
                                    id="sort_order"
                                    v-model="form.sort_order"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <div>
                            <AppLabel for="description">Description</AppLabel>
                            <AppTextArea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Brief description of this category"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- SEO Settings -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
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
                                placeholder="SEO title (leave empty to use category name)"
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
                    <div class="space-y-4">
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
                    </div>

                    <div class="mt-6 flex flex-col gap-2">
                        <AppButton class="btn btn-primary w-full" @click="submitForm" :disabled="form.processing">
                            <i class="ri-save-line mr-2"></i>
                            {{ form.processing ? 'Saving...' : (isEdit ? 'Update Category' : 'Create Category') }}
                        </AppButton>
                        <AppButton class="btn btn-secondary w-full" @click="$inertia.visit(route('tourCategory.index'))">
                            Cancel
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <!-- Category Image -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-image-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Category Image</span>
                            <p class="text-neutral-9 text-xs">Featured image</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div
                            v-if="imagePreview || props.category?.image_url"
                            class="group relative overflow-hidden rounded-xl"
                        >
                            <img
                                :src="imagePreview || props.category?.image_url"
                                class="aspect-video w-full object-cover"
                                alt="Category image"
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

// Image handling
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
