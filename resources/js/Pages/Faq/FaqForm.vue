<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full xl:w-3/4">
        <template #title>{{ isEdit ? 'Edit FAQ' : 'Create FAQ' }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Category and Status Row -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="faq_category_id">Category</AppLabel>
                            <AppSelect
                                id="faq_category_id"
                                v-model="form.faq_category_id"
                                :options="categoryOptions"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('faq_category_id')
                                }"
                            />
                        </div>

                        <div>
                            <AppLabel for="status"
                                >Status
                                <span class="text-red-500">*</span></AppLabel
                            >
                            <AppSelect
                                id="status"
                                v-model="form.status"
                                :options="statusOptions"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('status')
                                }"
                            />
                        </div>
                    </div>

                    <!-- Question -->
                    <div>
                        <AppLabel for="question"
                            >Question
                            <span class="text-red-500">*</span></AppLabel
                        >
                        <AppTextArea
                            id="question"
                            v-model="form.question"
                            placeholder="Enter the FAQ question"
                            :rows="2"
                            :class="{
                                'input-error': errorsFields.includes('question')
                            }"
                        />
                    </div>

                    <!-- Answer -->
                    <div>
                        <AppLabel for="answer"
                            >Answer
                            <span class="text-red-500">*</span></AppLabel
                        >
                        <AppTextArea
                            id="answer"
                            v-model="form.answer"
                            placeholder="Enter the FAQ answer"
                            :rows="6"
                            :class="{
                                'input-error': errorsFields.includes('answer')
                            }"
                        />
                    </div>

                    <!-- Options Row -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <AppLabel for="target_audience"
                                >Target Audience</AppLabel
                            >
                            <AppSelect
                                id="target_audience"
                                v-model="form.target_audience"
                                :options="audienceOptions"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('target_audience')
                                }"
                            />
                        </div>

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

                        <div class="flex items-end">
                            <AppCheckbox
                                id="is_featured"
                                v-model="form.is_featured"
                                label="Featured FAQ"
                            />
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="mt-2 border-t pt-4">
                        <h3
                            class="mb-3 text-sm font-medium text-neutral-700 dark:text-neutral-300"
                        >
                            SEO Settings (Optional)
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <AppLabel for="meta_title">Meta Title</AppLabel>
                                <AppInputText
                                    id="meta_title"
                                    v-model="form.meta_title"
                                    type="text"
                                    placeholder="SEO meta title"
                                    :class="{
                                        'input-error':
                                            errorsFields.includes('meta_title')
                                    }"
                                />
                            </div>

                            <div>
                                <AppLabel for="meta_description"
                                    >Meta Description</AppLabel
                                >
                                <AppTextArea
                                    id="meta_description"
                                    v-model="form.meta_description"
                                    placeholder="SEO meta description"
                                    :rows="2"
                                    :class="{
                                        'input-error':
                                            errorsFields.includes(
                                                'meta_description'
                                            )
                                    }"
                                />
                            </div>
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
import { computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('FAQ')

const props = defineProps({
    faq: {
        type: Object,
        default: null
    },
    categories: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQs', href: route('faq.index') },
    { label: props.faq ? 'Edit' : 'Create', last: true }
]

const categoryOptions = computed(() => [
    { value: '', label: 'Select Category' },
    ...props.categories
])

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'published', label: 'Published' },
    { value: 'archived', label: 'Archived' }
]

const audienceOptions = [
    { value: 'all', label: 'All' },
    { value: 'b2c', label: 'B2C (Consumers)' },
    { value: 'b2b', label: 'B2B (Business)' }
]

const form = useForm({
    faq_category_id: props.faq?.faq_category_id ?? '',
    question: props.faq?.question ?? '',
    answer: props.faq?.answer ?? '',
    status: props.faq?.status ?? 'draft',
    sort_order: props.faq?.sort_order ?? 0,
    is_featured: props.faq?.is_featured ?? false,
    target_audience: props.faq?.target_audience ?? 'all',
    meta_title: props.faq?.meta_title ?? '',
    meta_description: props.faq?.meta_description ?? ''
})

const { isCreate, isEdit } = useFormContext()
const { errorsFields } = useFormErrors()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('faq.store'))
    } else {
        form.put(route('faq.update', props.faq.uuid))
    }
}
</script>
