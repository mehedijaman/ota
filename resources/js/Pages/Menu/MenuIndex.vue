<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('menu-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('menu.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add menu
                </AppButton>

                <AppButton
                    v-if="can('menu-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('menu.recycleBin.index'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>
    <AppDataSearch
        v-if="menus.data.length || route().params.searchTerm"
        :url="route('menu.index')"
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
                        {{ item.position }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getStatusClass(item.active)"
                        >
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('menu-edit')"
                            text="Items"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('menu.items.index', item.id)
                                    )
                                "
                            >
                                <i class="ri-menu-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('menu-edit')"
                            text="Edit"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(route('menu.edit', item.id))
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip v-if="can('menu-delete')" text="Delete">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('menu.destroy', item.id)
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

const { title } = useTitle('menus')
const { can } = useAuthCan()

const props = defineProps({
    menus: {
        type: Object,
        default: () => {}
    }
})

const getStatusClass = (status) => {
    return status == true ? 'active' : 'inactive'
}

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
