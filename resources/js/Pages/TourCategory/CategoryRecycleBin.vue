<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('tour-category-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tourCategory.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="categories.data.length && can('tour-category-delete')"
                    class="btn btn-primary"
                    @click="confirmRestore(route('tourCategory.restoreRecycleBin'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="categories.data.length && can('tour-category-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('tourCategory.emptyRecycleBin'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Card -->
    <div v-if="categories.data.length" class="border-neutral-6 bg-neutral-1 mb-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex items-center gap-4 p-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                <i class="ri-delete-bin-line text-xl text-white"></i>
            </div>
            <div>
                <p class="text-neutral-9 text-sm font-medium">Deleted Categories</p>
                <p class="text-neutral-12 text-2xl font-bold">{{ categories.total }}</p>
            </div>
            <div class="text-neutral-9 ml-auto hidden text-sm sm:block">
                <i class="ri-information-line mr-1"></i>
                Items will be permanently deleted after 30 days
            </div>
        </div>
    </div>

    <AppDataSearch
        v-if="categories.data.length || route().params.searchTerm"
        :url="route('tourCategory.recycleBin')"
        fields-to-search="name,slug"
    ></AppDataSearch>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in categories.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <!-- Serial Number -->
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (categories.current_page - 1) * categories.per_page + (index + 1) }}
                    </AppDataTableData>

                    <!-- Category Info with Image -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img
                                    v-if="item.image_url"
                                    :src="item.image_url"
                                    :alt="item.name"
                                    class="ring-neutral-3 h-12 w-16 rounded-lg object-cover opacity-60 ring-1 grayscale"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-12 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-slate-400 to-slate-500 text-white opacity-60 ring-1"
                                >
                                    <i class="ri-folder-line text-xl"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 flex items-center gap-2 truncate font-medium">
                                    <i :class="'ri-' + (item.icon || 'folder') + '-line'" class="text-primary-600"></i>
                                    {{ item.name }}
                                </div>
                                <div class="text-neutral-9 font-mono text-xs">
                                    {{ item.slug }}
                                </div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Tours Count -->
                    <AppDataTableData>
                        <span class="text-neutral-11 flex items-center gap-1.5 text-sm">
                            <i class="ri-map-pin-line text-neutral-9"></i>
                            {{ item.tours_count }} tour{{ item.tours_count !== 1 ? 's' : '' }}
                        </span>
                    </AppDataTableData>

                    <!-- Deleted At -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-11 flex items-center gap-1.5 text-sm">
                                <i class="ri-calendar-close-line text-red-400"></i>
                                {{ formatDate(item.deleted_at) }}
                            </span>
                            <span class="text-neutral-9 text-xs">
                                {{ formatTimeAgo(item.deleted_at) }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <AppTooltip v-if="can('tour-category-delete')" text="Restore">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="confirmRestore(route('tourCategory.restore', item.id))"
                                >
                                    <i class="ri-recycle-fill"></i>
                                </AppButton>
                            </AppTooltip>

                            <AppTooltip v-if="can('tour-category-delete')" text="Delete Permanently">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('tourCategory.destroyForce', item.id))"
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

    <!-- Empty State -->
    <div v-if="!categories.data.length" class="border-neutral-6 bg-neutral-1 mt-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex flex-col items-center justify-center py-16">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-teal-100">
                <i class="ri-checkbox-circle-line text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-neutral-12 mb-2 text-lg font-semibold">Recycle Bin is Empty</h3>
            <p class="text-neutral-9 mb-6 max-w-sm text-center text-sm">
                Great job! There are no deleted categories. All your categories are safe and sound.
            </p>
            <AppButton class="btn btn-primary" @click="$inertia.visit(route('tourCategory.index'))">
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
    { label: 'Tour Categories', href: route('tourCategory.index') },
    { label: title, last: true }
]

defineProps({
    categories: {
        type: Object,
        default: () => ({})
    }
})

const headers = ['SL', 'Category', 'Tours', 'Deleted At', 'Actions']

const formatDate = (dateString) => {
    if (!dateString) {
        return '-'
    }
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTimeAgo = (dateString) => {
    if (!dateString) {
        return ''
    }
    const date = new Date(dateString)
    const now = new Date()
    const diffInSeconds = Math.floor((now - date) / 1000)

    if (diffInSeconds < 60) {
        return 'just now'
    }
    if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60)
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
    }
    if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600)
        return `${hours} hour${hours > 1 ? 's' : ''} ago`
    }
    const days = Math.floor(diffInSeconds / 86400)
    return `${days} day${days > 1 ? 's' : ''} ago`
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.confirmRestore(restoreRoute)
}
</script>
