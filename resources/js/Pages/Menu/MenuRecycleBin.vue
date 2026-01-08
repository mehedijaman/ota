<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 md:flex-row">
                <AppButton
                    v-if="can('menu-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('menu.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="can('menu-recycle-bin-restore') && menus.data.length"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('menu.recycleBin.restoreAll'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="can('menu-recycle-bin-delete') && menus.data.length"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('menu.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Info Banner -->
    <div v-if="menus.data.length" class="mb-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-900/20">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/50">
            <i class="ri-delete-bin-line text-xl text-amber-600 dark:text-amber-400"></i>
        </div>
        <div>
            <p class="font-medium text-amber-800 dark:text-amber-200">
                {{ menus.total }} menu(s) in recycle bin
            </p>
            <p class="text-sm text-amber-600 dark:text-amber-400">
                Items here will be permanently deleted after 30 days
            </p>
        </div>
    </div>

    <AppDataSearch
        v-if="menus.data.length || route().params.searchTerm"
        :url="route('menu.recycleBin.index')"
        fields-to-search="name"
    ></AppDataSearch>

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
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                <i class="ri-menu-2-line text-gray-400"></i>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ item.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <i class="ri-time-line"></i>
                            {{ item.deleted_at }}
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div v-if="item.deletedBy" class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                {{ item.deletedBy.name?.charAt(0)?.toUpperCase() || '?' }}
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ item.deletedBy.name }}</span>
                        </div>
                        <span v-else class="text-gray-400">-</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-1">
                            <!-- Restore -->
                            <AppTooltip
                                v-if="can('menu-recycle-bin-restore')"
                                text="Restore"
                                class="mr-1"
                            >
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('menu.recycleBin.restore', item.id))"
                                >
                                    <i class="ri-recycle-fill"></i>
                                </AppButton>
                            </AppTooltip>

                            <!-- Delete -->
                            <AppTooltip
                                v-if="can('menu-recycle-bin-delete')"
                                text="Permanently Delete"
                            >
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('menu.recycleBin.destroyForce', item.id))"
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
        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-green-100 dark:bg-green-900/30">
            <i class="ri-checkbox-circle-line text-5xl text-green-400 dark:text-green-500"></i>
        </div>
        <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Recycle bin is empty</h3>
        <p class="mb-6 text-gray-500 dark:text-gray-400">No deleted menus to show</p>
        <AppButton class="btn btn-secondary" @click="$inertia.visit(route('menu.index'))">
            <i class="ri-arrow-left-line mr-1"></i>
            Back to Menus
        </AppButton>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Menu Recycle Bin')

const props = defineProps({
    menus: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Menus', href: route('menu.index') },
    { label: 'Recycle Bin', last: true }
]

const headers = ['SL', 'Menu Name', 'Deleted At', 'Deleted By', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
