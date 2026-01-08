<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex items-center gap-3">
                <AppButton
                    v-if="can('menu-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('menu.recycleBin.index'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
                <AppButton
                    v-if="can('menu-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('menu.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Menu
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Cards -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                <i class="ri-menu-2-line text-xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ menus.total || 0 }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Menus</p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                <i class="ri-checkbox-circle-line text-xl text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ activeCount }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                <i class="ri-pause-circle-line text-xl text-amber-600 dark:text-amber-400"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ inactiveCount }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inactive</p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30">
                <i class="ri-layout-grid-line text-xl text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ positionCount }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Positions</p>
            </div>
        </div>
    </div>

    <AppDataSearch
        v-if="menus.data.length || route().params.searchTerm"
        :url="route('menu.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <!-- Data Table -->
    <AppDataTable v-if="menus.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in menus.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{
                            (menus.current_page - 1) * menus.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                <i class="ri-menu-2-line text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ item.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400">
                            <i class="ri-map-pin-line text-sm"></i>
                            {{ item.position || '-' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="item.active
                                ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                        >
                            <span class="h-1.5 w-1.5 rounded-full" :class="item.active ? 'bg-green-500' : 'bg-gray-400'"></span>
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-1">
                            <!-- Items -->
                            <AppTooltip
                                v-if="can('menu-edit')"
                                text="Manage Items"
                                class="mr-1"
                            >
                                <AppButton
                                    class="btn btn-icon btn-secondary"
                                    @click="$inertia.visit(route('menu.items.index', item.id))"
                                >
                                    <i class="ri-menu-line"></i>
                                </AppButton>
                            </AppTooltip>

                            <!-- Edit -->
                            <AppTooltip
                                v-if="can('menu-edit')"
                                text="Edit"
                                class="mr-1"
                            >
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('menu.edit', item.id))"
                                >
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>

                            <!-- Delete -->
                            <AppTooltip v-if="can('menu-delete')" text="Delete">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('menu.destroy', item.id))"
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
        v-if="menus.data.length"
        :links="menus.links"
        :from="menus.from ?? 0"
        :to="menus.to ?? 0"
        :total="menus.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <!-- Empty State -->
    <div v-if="!menus.data.length" class="py-16 text-center">
        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-gray-100 dark:bg-gray-700">
            <i class="ri-menu-add-line text-5xl text-gray-300 dark:text-gray-600"></i>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">No menus yet</h3>
        <p class="mb-6 text-gray-500 dark:text-gray-400">Get started by creating your first navigation menu</p>
        <AppButton v-if="can('menu-create')" class="btn btn-primary" @click="$inertia.visit(route('menu.create'))">
            <i class="ri-add-fill mr-1"></i>
            Create First Menu
        </AppButton>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('menus')
const { can } = useAuthCan()

const props = defineProps({
    menus: {
        type: Object,
        default: () => ({})
    }
})

const activeCount = computed(() => {
    return props.menus.data?.filter(m => m.active).length || 0
})

const inactiveCount = computed(() => {
    return props.menus.data?.filter(m => !m.active).length || 0
})

const positionCount = computed(() => {
    const positions = new Set(props.menus.data?.map(m => m.position).filter(p => p))
    return positions.size
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: title, last: true }
]

const headers = ['SL', 'Menu Name', 'Position', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
