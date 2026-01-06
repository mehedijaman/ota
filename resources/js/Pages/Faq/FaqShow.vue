<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('faq-edit')"
                class="btn btn-primary mr-2"
                @click="$inertia.visit(route('faq.edit', faq.uuid))"
            >
                <i class="ri-edit-line mr-1"></i>
                Edit
            </AppButton>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('faq.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <span>Question & Answer</span>
                        <span
                            v-if="faq.is_featured"
                            class="inline-flex items-center text-yellow-500"
                        >
                            <i class="ri-star-fill"></i>
                        </span>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-6">
                        <div>
                            <h3
                                class="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100"
                            >
                                {{ faq.question }}
                            </h3>
                        </div>

                        <div class="border-t pt-4">
                            <h4
                                class="mb-2 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Answer
                            </h4>
                            <div
                                class="prose dark:prose-invert max-w-none whitespace-pre-wrap text-neutral-700 dark:text-neutral-300"
                            >
                                {{ faq.answer }}
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- SEO Section -->
            <AppCard v-if="faq.meta_title || faq.meta_description" class="mt-6">
                <template #title>SEO Information</template>
                <template #content>
                    <div class="space-y-4">
                        <div v-if="faq.meta_title">
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Meta Title
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ faq.meta_title }}
                            </p>
                        </div>
                        <div v-if="faq.meta_description">
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Meta Description
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ faq.meta_description }}
                            </p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <AppCard>
                <template #title>Details</template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Category
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ faq.category?.name || 'Uncategorized' }}
                            </p>
                        </div>

                        <div>
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Status
                            </h4>
                            <span
                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                :class="statusClass"
                            >
                                {{ faq.status }}
                            </span>
                        </div>

                        <div>
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Target Audience
                            </h4>
                            <p
                                class="capitalize text-neutral-900 dark:text-neutral-100"
                            >
                                {{ audienceLabel }}
                            </p>
                        </div>

                        <div>
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Sort Order
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ faq.sort_order }}
                            </p>
                        </div>

                        <div>
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Featured
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ faq.is_featured ? 'Yes' : 'No' }}
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Created
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ formatDate(faq.created_at) }}
                            </p>
                        </div>

                        <div>
                            <h4
                                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                            >
                                Last Updated
                            </h4>
                            <p class="text-neutral-900 dark:text-neutral-100">
                                {{ formatDate(faq.updated_at) }}
                            </p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('View FAQ')
const { can } = useAuthCan()

const props = defineProps({
    faq: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQs', href: route('faq.index') },
    { label: 'View', last: true }
]

const statusClass = computed(() => {
    const classes = {
        draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        published:
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        archived:
            'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200'
    }
    return classes[props.faq.status] || classes.draft
})

const audienceLabel = computed(() => {
    const labels = {
        all: 'All',
        b2c: 'B2C (Consumers)',
        b2b: 'B2B (Business)'
    }
    return labels[props.faq.target_audience] || 'All'
})

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>
