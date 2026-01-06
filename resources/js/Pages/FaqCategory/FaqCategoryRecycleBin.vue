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
                                @click="confirmRestore(item.uuid)"
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
        v-if="categories.data.length"
        :links="categories.links"
        :from="categories.from || 0"
        :to="categories.to || 0"
        :total="categories.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!categories.data.length" class="mt-4">
        Recycle bin is empty.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

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

const confirmRestore = (uuid) => {
    confirmDialogRef.value.openModal(
        route('faqCategory.restore', uuid),
        'put',
        'Restore Category',
        'Are you sure you want to restore this category?'
    )
}

const confirmForceDelete = (uuid) => {
    confirmDialogRef.value.openModal(
        route('faqCategory.destroyForce', uuid),
        'delete',
        'Delete Permanently',
        'Are you sure you want to permanently delete this category? This action cannot be undone.'
    )
}

const confirmEmptyRecycleBin = () => {
    confirmDialogRef.value.openModal(
        route('faqCategory.emptyRecycleBin'),
        'delete',
        'Empty Recycle Bin',
        'Are you sure you want to permanently delete all categories in the recycle bin? This action cannot be undone.'
    )
}

const confirmRestoreAll = () => {
    confirmDialogRef.value.openModal(
        route('faqCategory.restoreRecycleBin'),
        'put',
        'Restore All Categories',
        'Are you sure you want to restore all categories in the recycle bin?'
    )
}
</script>
