<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('faq-list')"
                class="btn btn-secondary mr-2"
                @click="$inertia.visit(route('faq.recycleBin'))"
            >
                <i class="ri-recycle-line mr-1"></i>
                Recycle Bin
            </AppButton>
            <AppButton
                v-if="can('faq-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('faq.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create FAQ
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="faqs.data.length || route().params.searchTerm"
        :url="route('faq.index')"
        fields-to-search="question"
    ></AppDataSearch>

    <AppDataTable v-if="faqs.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in faqs.data" :key="item.id">
                    <AppDataTableData>
                        <span class="font-medium">{{
                            truncate(item.question, 60)
                        }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-600">{{
                            item.category || '-'
                        }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                            :class="statusClass(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <i
                            v-if="item.is_featured"
                            class="ri-star-fill text-yellow-500"
                        ></i>
                        <span v-else class="text-neutral-400">-</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-600">{{
                            item.sort_order
                        }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.updated_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('faq-view')"
                            text="View FAQ"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="
                                    $inertia.visit(route('faq.show', item.uuid))
                                "
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('faq-edit')"
                            text="Edit FAQ"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(route('faq.edit', item.uuid))
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip v-if="can('faq-delete')" text="Delete FAQ">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('faq.destroy', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="faqs.data.length"
        :links="faqs.links"
        :from="faqs.from || 0"
        :to="faqs.to || 0"
        :total="faqs.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!faqs.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('FAQs')
const { can } = useAuthCan()

defineProps({
    faqs: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQs', last: true }
]

const headers = [
    'Question',
    'Category',
    'Status',
    'Featured',
    'Order',
    'Updated',
    'Actions'
]

const confirmDialogRef = ref(null)

const confirmDelete = (url) => {
    confirmDialogRef.value.openModal(url)
}

const truncate = (str, length) => {
    if (!str) return ''
    return str.length > length ? str.substring(0, length) + '...' : str
}

const statusClass = (status) => {
    const classes = {
        draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        published:
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        archived:
            'bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200'
    }
    return classes[status] || classes.draft
}
</script>
