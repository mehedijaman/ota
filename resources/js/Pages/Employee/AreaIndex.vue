<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('employee-area-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('area.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Area
                </AppButton>

                <AppButton
                    v-if="can('employee-area-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('area.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="areas.data.length || route().params.searchTerm"
        :url="route('area.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="areas.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in areas.data"
                    :key="item.uuid"
                >
                    <AppDataTableData>
                        {{
                            (areas.current_page - 1) *
                                areas.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>
                    <AppDataTableData>
                        {{ item.code }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.employee_count }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getStatusClass(item.is_active)"
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('employee-area-edit')"
                            text="Edit Area"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('area.edit', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('employee-area-delete')"
                            text="Delete Area"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('area.destroy', item.uuid)
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
        v-if="areas.data.length"
        :links="areas.links"
        :from="areas.from ?? 0"
        :to="areas.to ?? 0"
        :total="areas.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!areas.data.length" class="mt-4">
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('Areas')

const props = defineProps({
    areas: {
        type: Object,
        default: () => {}
    }
})

const getStatusClass = (status) => {
    return status == true ? 'active' : 'inactive'
}

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Areas', last: true }
]

const headers = ['SL', 'Code', 'Name', 'Employees', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
