<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('designation-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('designation.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Designation
                </AppButton>

                <AppButton
                    v-if="can('designation-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('designation.import'))"
                >
                    <i class="ri-file-excel-2-line mr-1"></i>
                    Bulk Import
                </AppButton>

                <AppButton
                    v-if="can('designation-export')"
                    class="btn btn-primary"
                    @click="exportReport"
                >
                    <i class="ri-download-2-line mr-1"></i>
                    Export Excel
                </AppButton>

                <AppButton
                    v-if="can('designation-recycle-bin')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('designation.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="designations.data.length || route().params.searchTerm"
        :url="route('designation.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="designations.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in designations.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{
                            (designations.current_page - 1) *
                                designations.per_page +
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
                            :class="getDesignationStatusClass(item.is_active)"
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('designation-edit')"
                            text="Edit Designation"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('designation.edit', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('designation-delete')"
                            text="Delete Designation"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('designation.destroy', item.uuid)
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
        v-if="designations.data.length"
        :links="designations.links"
        :from="designations.from ?? 0"
        :to="designations.to ?? 0"
        :total="designations.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>
    

    <AppEmptyState v-if="!designations.data.length" class="mt-4">
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
const { title } = useTitle('Designations')

const props = defineProps({
    designations: {
        type: Object,
        default: () => {}
    }
})

const getDesignationStatusClass = (status) => {
    return status == true ? 'active' : 'inactive'
}

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Designations', last: true }
]

const headers = ['SL', 'Code', 'Name', 'Employees', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const exportReport = () => {
    window.location.href = route('designation.export')
}
</script>
