<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('faq-delete') && faqs.data.length"
                class="btn btn-secondary mr-2"
                @click="confirmRestoreAll"
            >
                <i class="ri-refresh-line mr-1"></i>
                Restore All
            </AppButton>
            <AppButton
                v-if="can('faq-delete') && faqs.data.length"
                class="btn btn-destructive mr-2"
                @click="confirmEmptyRecycleBin"
            >
                <i class="ri-delete-bin-line mr-1"></i>
                Empty Recycle Bin
            </AppButton>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('faq.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back to FAQs
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="faqs.data.length || route().params.searchTerm"
        :url="route('faq.recycleBin')"
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
                        {{ item.deleted_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('faq-delete')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="confirmRestore(item.uuid)"
                            >
                                <i class="ri-refresh-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('faq-delete')"
                            text="Delete Permanently"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmForceDelete(item.uuid)"
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

    <AppAlert v-if="!faqs.data.length" class="mt-4">
        Recycle bin is empty.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('FAQs - Recycle Bin')
const { can } = useAuthCan()

defineProps({
    faqs: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQs', href: route('faq.index') },
    { label: 'Recycle Bin', last: true }
]

const headers = ['Question', 'Category', 'Status', 'Deleted At', 'Actions']

const confirmDialogRef = ref(null)

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

const confirmRestore = (uuid) => {
    confirmDialogRef.value.openModal(
        route('faq.restore', uuid),
        'put',
        'Restore FAQ',
        'Are you sure you want to restore this FAQ?'
    )
}

const confirmForceDelete = (uuid) => {
    confirmDialogRef.value.openModal(
        route('faq.destroyForce', uuid),
        'delete',
        'Delete Permanently',
        'Are you sure you want to permanently delete this FAQ? This action cannot be undone.'
    )
}

const confirmEmptyRecycleBin = () => {
    confirmDialogRef.value.openModal(
        route('faq.emptyRecycleBin'),
        'delete',
        'Empty Recycle Bin',
        'Are you sure you want to permanently delete all FAQs in the recycle bin? This action cannot be undone.'
    )
}

const confirmRestoreAll = () => {
    confirmDialogRef.value.openModal(
        route('faq.restoreRecycleBin'),
        'put',
        'Restore All FAQs',
        'Are you sure you want to restore all FAQs in the recycle bin?'
    )
}
</script>
