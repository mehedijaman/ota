<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('manufacturer-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('manufacturer.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add manufacturer
                </AppButton>

                <AppButton
                    v-if="can('manufacturer-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="
                        $inertia.visit(route('manufacturer.recycleBin.index'))
                    "
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>
    <AppDataSearch
        v-if="manufacturers.data.length || route().params.searchTerm"
        :url="route('manufacturer.index')"
        fields-to-search="name"
    ></AppDataSearch>
    <AppDataTable v-if="manufacturers.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in manufacturers.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{
                            (manufacturers.current_page - 1) *
                                manufacturers.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getStatusClass(item.active)"
                        >
                            {{ item.active ? 'Active' : 'Inactive' }}
                        </span>

                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getStatusClass(item.featured)"
                        >
                            {{ item.featured ? 'Featured' : '' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit -->
                        <AppTooltip
                            v-if="can('manufacturer-edit')"
                            text="Edit"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('manufacturer.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('manufacturer-delete')"
                            text="Delete"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('manufacturer.destroy', item.id)
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
        :links="manufacturers.links"
        :from="manufacturers.from ?? 0"
        :to="manufacturers.to ?? 0"
        :total="manufacturers.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!manufacturers.data.length" class="mt-4">
        No data found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Manufacturers')
const { can } = useAuthCan()

const props = defineProps({
    manufacturers: {
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

const headers = ['SL', 'Manufacturer Name', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
