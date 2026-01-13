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
                        <AppImage
                            v-model="form.thumbnail"
                            :image-url="imageUrl"
                            label="Category Thumbnail"
                        />
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
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
    thumbnail: null,
    _method: isEdit.value ? 'PUT' : 'POST'
})

const imageUrl = computed(() => props.category?.thumbnail_url)

const { errorsFields } = useFormErrors()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visaCategory.store'))
    } else {
        form.post(route('visaCategory.update', props.category.id))
    }
}
</script>
