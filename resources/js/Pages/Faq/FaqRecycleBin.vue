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
                                @click="
                                    confirmRestore(
                                        route('faq.restore', item.uuid)
                                    )
                                "
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
                                @click="
                                    confirmForceDelete(
                                        route('faq.destroyForce', item.uuid)
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

const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.openCustomModal({
        route: restoreRoute,
        title: 'Restore Confirmation',
        message: 'Are you sure you want to restore this FAQ?',
        buttonClass: 'btn btn-primary',
        buttonText: 'Restore',
        method: 'put',
        modalType: 'primary'
    })
}

const confirmForceDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const confirmEmptyRecycleBin = () => {
    confirmDialogRef.value.openModal(route('faq.emptyRecycleBin'))
}

const confirmRestoreAll = () => {
    confirmDialogRef.value.openCustomModal({
        route: route('faq.restoreRecycleBin'),
        title: 'Restore All Confirmation',
        message:
            'Are you sure you want to restore all FAQs in the recycle bin?',
        buttonClass: 'btn btn-primary',
        buttonText: 'Restore All',
        method: 'put',
        modalType: 'primary'
    })
}
</script>
