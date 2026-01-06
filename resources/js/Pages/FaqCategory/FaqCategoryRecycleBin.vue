<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('faq-category-delete') && categories.data.length"
                class="btn btn-secondary mr-2"
                @click="confirmRestoreAll"
            >
                <i class="ri-refresh-line mr-1"></i>
                Restore All
            </AppButton>
            <AppButton
                v-if="can('faq-category-delete') && categories.data.length"
                class="btn btn-destructive mr-2"
                @click="confirmEmptyRecycleBin"
            >
                <i class="ri-delete-bin-line mr-1"></i>
                Empty Recycle Bin
            </AppButton>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('faqCategory.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Categories
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="categories.data.length || route().params.searchTerm"
        :url="route('faqCategory.recycleBin')"
        fields-to-search="name,slug"
    ></AppDataSearch>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in categories.data" :key="item.id">
                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="font-medium">{{ item.name }}</span>
                            <span class="text-xs text-neutral-500">{{
                                item.slug
                            }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            :class="
                                item.is_active
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deleted_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('faq-category-delete')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="
                                    confirmRestore(
                                        route('faqCategory.restore', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-refresh-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('faq-category-delete')"
                            text="Delete Permanently"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmForceDelete(
                                        route(
                                            'faqCategory.destroyForce',
                                            item.uuid
                                        )
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
        v-if="categories.data.length"
        :links="categories.links"
        :from="categories.from || 0"
        :to="categories.to || 0"
        :total="categories.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!categories.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('FAQ Categories - Recycle Bin')
const { can } = useAuthCan()

defineProps({
    categories: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQ Categories', href: route('faqCategory.index') },
    { label: 'Recycle Bin', last: true }
]

const headers = ['Name', 'Status', 'Deleted At', 'Actions']

const confirmDialogRef = ref(null)

const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.openCustomModal({
        route: restoreRoute,
        title: 'Restore Confirmation',
        message: 'Are you sure you want to restore this category?',
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
    confirmDialogRef.value.openModal(route('faqCategory.emptyRecycleBin'))
}

const confirmRestoreAll = () => {
    confirmDialogRef.value.openCustomModal({
        route: route('faqCategory.restoreRecycleBin'),
        title: 'Restore All Confirmation',
        message:
            'Are you sure you want to restore all categories in the recycle bin?',
        buttonClass: 'btn btn-primary',
        buttonText: 'Restore All',
        method: 'put',
        modalType: 'primary'
    })
}
</script>
