<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('visa-category-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visaCategory.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Category
                </AppButton>

                <AppButton
                    v-if="can('visa-category-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visaCategory.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer rounded-xl border p-4 shadow-sm transition-all hover:border-blue-500 hover:shadow-md"
            @click="filterByStats('total')"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-folders-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total Categories</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer rounded-xl border p-4 shadow-sm transition-all hover:border-emerald-500 hover:shadow-md"
            @click="filterByStats('active')"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="ri-checkbox-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Active</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.active || 0 }}</p>
                </div>
            </div>
        </div>

        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer rounded-xl border p-4 shadow-sm transition-all hover:border-red-500 hover:shadow-md"
            @click="filterByStats('inactive')"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                    <i class="ri-close-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Inactive</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.inactive || 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <AppDataSearch
        v-if="categories.data.length || route().params.searchTerm"
        :url="route('visaCategory.index')"
        fields-to-search="name"
    ></AppDataSearch>

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
                                    class="ring-neutral-3 h-10 w-10 rounded-lg object-cover ring-1"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 text-white ring-1"
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
                        <span class="text-neutral-11">{{ item.slug }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11">{{ item.visas_count || 0 }} Visas</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                            :class="
                                item.is_active
                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                    : 'bg-red-50 text-red-700 ring-red-200'
                            "
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full"
                                :class="item.is_active ? 'bg-emerald-500' : 'bg-red-500'"
                            ></span>
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData class="text-right">
                        <AppTooltip text="Edit Category" class="mr-2">
                            <AppButton
                                v-if="can('visa-category-edit')"
                                class="btn btn-icon btn-primary"
                                size="sm"
                                @click="$inertia.visit(route('visaCategory.edit', item.id))"
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete Category">
                            <AppButton
                                v-if="can('visa-category-delete')"
                                class="btn btn-icon btn-destructive"
                                size="sm"
                                @click="confirmDelete(route('visaCategory.destroy', item.id))"
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
        :from="categories.from ?? 0"
        :to="categories.to ?? 0"
        :total="categories.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!categories.data.length"
        icon="ri-folders-line"
        title="No categories found"
        description="Get started by creating your first visa category."
    >
        <template #actions>
            <AppButton
                v-if="can('visa-category-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('visaCategory.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Category
            </AppButton>
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('Visa Categories')

const props = defineProps({
    categories: {
        type: Object,
        default: () => ({})
    },
    stats: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visa Management', href: '#' },
    { label: 'Categories', last: true }
]

const headers = ['#', 'Name', 'Slug', 'Visas', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const filterByStats = (type) => {
    const params = {}
    if (type === 'active') params.status = 'active'
    if (type === 'inactive') params.status = 'inactive'
    
    router.visit(route('visaCategory.index'), {
        data: params,
        preserveState: true,
        preserveScroll: true
    })
}
</script>
