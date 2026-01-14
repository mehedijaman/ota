<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-primary" @click="$inertia.visit(route('hotel.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back to Hotels
                </AppButton>
                <AppButton v-if="can('hotel.delete')" class="btn btn-secondary" @click="$inertia.visit(route('hotel.destroy', hotel.id), { method: 'delete' })">
                    <i class="ri-refresh-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="hotels.data.length || route().params.searchTerm"
        :url="route('hotel.recycleBin')"
        fields-to-search="name,code"
    ></AppDataSearch>

    <AppDataTable v-if="hotels.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in hotels.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (hotels.current_page - 1) * hotels.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img
                                    v-if="item.thumbnail_url"
                                    :src="item.thumbnail_url"
                                    :alt="item.name"
                                    class="ring-neutral-3 h-12 w-16 rounded-lg object-cover ring-1"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-12 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 text-white ring-1"
                                >
                                    <i class="ri-hotel-line text-xl"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                                <div class="text-neutral-9 text-xs">{{ item.code }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-9 text-sm">{{ formatDate(item.deleted_at) }}</span>
                    </AppDataTableData>

                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <div class="flex justify-end gap-2">
                            <AppTooltip text="Restore">
                                <AppButton
                                    class="btn btn-icon btn-secondary"
                                    @click="restoreHotel(item.id)"
                                >
                                    <i class="ri-refresh-line"></i>
                                </AppButton>
                            </AppTooltip>
                            <AppTooltip text="Delete Permanently">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('hotel.destroyForce', item.id))"
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
        v-if="hotels.data.length"
        :links="hotels.links"
        :from="hotels.from ?? 0"
        :to="hotels.to ?? 0"
        :total="hotels.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!hotels.data.length"
        icon="ri-recycle-line"
        title="Recycle bin is empty"
        description="Deleted hotels will appear here."
    >
        <template #actions>
            <AppButton class="btn btn-primary" @click="$inertia.visit(route('hotel.index'))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Hotels
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
import AppTooltip from '@/Components/Message/AppTooltip.vue'

const { can } = useAuthCan()
const { title } = useTitle('Hotel Recycle Bin')

const props = defineProps({
    hotels: { type: Object, default: () => ({}) }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotels', href: route('hotel.index') },
    { label: 'Recycle Bin', last: true }
]

const headers = ['#', 'Hotel', 'Deleted At', 'Actions']

const formatDate = (date) => new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })

const restoreHotel = (id) => {
    router.post(route('hotel.restore', id), {}, { preserveScroll: true })
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
