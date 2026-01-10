<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('tour-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tour.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="tours.data.length && can('tour-delete')"
                    class="btn btn-primary"
                    @click="confirmRestore(route('tour.restoreRecycleBin'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="tours.data.length && can('tour-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('tour.emptyRecycleBin'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="tours.data.length || route().params.searchTerm"
        :url="route('tour.recycleBin')"
        fields-to-search="name,code"
    ></AppDataSearch>

    <AppDataTable v-if="tours.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in tours.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ (tours.current_page - 1) * tours.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="font-medium text-slate-900">{{ item.name }}</span>
                            <span class="text-xs text-slate-500">{{ item.code }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.category || '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.destination || '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.duration }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-xs text-slate-500">{{ item.deleted_at }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('tour-delete')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="confirmRestore(route('tour.restore', item.id))"
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('tour-delete')"
                            text="Permanently Delete"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('tour.destroyForce', item.id))"
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
        v-if="tours.data.length"
        :links="tours.links"
        :from="tours.from ?? 0"
        :to="tours.to ?? 0"
        :total="tours.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!tours.data.length">
        <template #title>Recycle Bin is Empty</template>
        <template #description>Deleted tours will appear here</template>
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
const { title } = useTitle('Tour Recycle Bin')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Tours', href: route('tour.index') },
    { label: title, last: true }
]

defineProps({
    tours: {
        type: Object,
        default: () => ({})
    }
})

const headers = [
    'SL',
    'Tour',
    'Category',
    'Destination',
    'Duration',
    'Deleted At',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.confirmRestore(restoreRoute)
}
</script>
