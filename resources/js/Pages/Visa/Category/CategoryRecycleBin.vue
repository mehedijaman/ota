<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visaCategory.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="categories.data.length && can('visa-category-delete')"
                    class="btn btn-primary"
                    @click="confirmRestore(route('visaCategory.restoreRecycleBin'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="categories.data.length && can('visa-category-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('visaCategory.emptyRecycleBin'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div v-if="categories.data.length" class="border-neutral-6 bg-neutral-1 mb-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex items-center gap-4 p-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                <i class="ri-delete-bin-line text-xl text-white"></i>
            </div>
            <div>
                <p class="text-neutral-9 text-sm font-medium">Deleted Categories</p>
                <p class="text-neutral-12 text-2xl font-bold">{{ categories.total }}</p>
            </div>
        </div>
    </div>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in categories.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (categories.current_page - 1) * categories.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img
                                    v-if="item.thumbnail_url"
                                    :src="item.thumbnail_url"
                                    :alt="item.name"
                                    class="ring-neutral-3 h-10 w-10 rounded-lg object-cover opacity-60 ring-1 grayscale"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-slate-400 to-slate-500 text-white opacity-60 ring-1"
                                >
                                    <i class="ri-folder-line text-lg"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-11 flex items-center gap-1.5 text-sm">
                                <i class="ri-calendar-close-line text-red-400"></i>
                                {{ formatDate(item.deleted_at) }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <AppTooltip v-if="can('visa-category-delete')" text="Restore">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    size="sm"
                                    @click="confirmRestore(route('visaCategory.restore', item.id))"
                                >
                                    <i class="ri-recycle-fill"></i>
                                </AppButton>
                            </AppTooltip>

                            <AppTooltip v-if="can('visa-category-delete')" text="Delete Permanently">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    size="sm"
                                    @click="confirmDelete(route('visaCategory.destroyForce', item.id))"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="categories.data.length"
        :links="categories.links"
        :from="categories.from ?? 0"
        :to="categories.to ?? 0"
        :total="categories.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <div v-if="!categories.data.length" class="border-neutral-6 bg-neutral-1 mt-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex flex-col items-center justify-center py-16">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-teal-100">
                <i class="ri-checkbox-circle-line text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-neutral-12 mb-2 text-lg font-semibold">Recycle Bin is Empty</h3>
            <p class="text-neutral-9 mb-6 max-w-sm text-center text-sm">
                No deleted categories found.
            </p>
            <AppButton class="btn btn-primary" @click="$inertia.visit(route('visaCategory.index'))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Categories
            </AppButton>
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Category Recycle Bin')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Visa Categories', href: route('visaCategory.index') },
    { label: 'Recycle Bin', last: true }
]

defineProps({
    categories: {
        type: Object,
        default: () => ({})
    }
})

const headers = ['#', 'Category', 'Deleted At', 'Actions']

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.confirmRestore(restoreRoute)
}
</script>
