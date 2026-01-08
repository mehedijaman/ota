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
                    v-if="can('menu-recycle-bin-restore')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('menu.recycleBin.restoreAll'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore Recycle Bin
                </AppButton>

                <AppButton
                    v-if="can('menu-recycle-bin-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('menu.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

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
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deleted_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deletedBy?.name ?? '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Restore -->
                        <AppTooltip
                            v-if="can('menu-recycle-bin-restore')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'menu.recycleBin.restore',
                                            item.id
                                        )
                                    )
                                "
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
                                @click="
                                    confirmDelete(
                                        route(
                                            'menu.recycleBin.destroyForce',
                                            item.id
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
        :links="menus.links"
        :from="menus.from ?? 0"
        :to="menus.to ?? 0"
        :total="menus.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!menus.data.length" class="mt-4"> No data found. </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('menu Recycle Bin')

const props = defineProps({
    menus: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'menus', href: route('menu.index') },
    { label: title, last: true }
]

const headers = ['SL', 'menu Name', 'Deleted At', 'Deleted By', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
