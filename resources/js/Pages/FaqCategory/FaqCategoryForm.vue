<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>{{
            isEdit ? 'Edit Category' : 'Create Category'
        }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <AppLabel for="name"
                            >Name <span class="text-red-500">*</span></AppLabel
                        >
                        <AppInputText
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Enter category name"
                            :class="{
                                'input-error': errorsFields.includes('name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="slug">Slug</AppLabel>
                        <AppInputText
                            id="slug"
                            v-model="form.slug"
                            type="text"
                            placeholder="Auto-generated from name if left empty"
                            :class="{
                                'input-error': errorsFields.includes('slug')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="description">Description</AppLabel>
                        <AppTextArea
                            id="description"
                            v-model="form.description"
                            placeholder="Enter category description"
                            :rows="3"
                            :class="{
                                'input-error':
                                    errorsFields.includes('description')
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <AppLabel for="sort_order">Sort Order</AppLabel>
                            <AppInputText
                                id="sort_order"
                                v-model="form.sort_order"
                                type="number"
                                placeholder="0"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('sort_order')
                                }"
                            />
                        </div>

                        <div>
                            <AppLabel for="is_active">Status</AppLabel>
                            <AppSelect
                                id="is_active"
                                v-model="form.is_active"
                                :options="statusOptions"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('is_active')
                                }"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                class="btn btn-primary"
                @click="submitForm"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Saving...' : 'Save' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('FAQ Category')

const props = defineProps({
    category: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQ Categories', href: route('faqCategory.index') },
    { label: props.category ? 'Edit' : 'Create', last: true }
]

const statusOptions = [
    { value: true, label: 'Active' },
    { value: false, label: 'Inactive' }
]

const form = useForm({
    name: props.category?.name ?? '',
    slug: props.category?.slug ?? '',
    description: props.category?.description ?? '',
    sort_order: props.category?.sort_order ?? 0,
    is_active: props.category?.is_active ?? true
})

const { isCreate, isEdit } = useFormContext()
const { errorsFields } = useFormErrors()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('faqCategory.store'))
    } else {
        form.put(route('faqCategory.update', props.category.uuid))
    }
}
</script>
