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
        form.post(route('tourCategory.store'))
    }
    if (isEdit.value) {
        form.put(route('tourCategory.update', props.category.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
