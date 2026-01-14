<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton v-if="can('hotel.room.create')" class="btn btn-primary" @click="$inertia.visit(route('hotel.room.create', hotelId))">
                    <i class="ri-add-fill mr-1"></i>
                    Add Room
                </AppButton>
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotel.show', hotelId))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back to Hotel
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="rooms.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in rooms.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (rooms.current_page - 1) * rooms.per_page + (index + 1) }}
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
                                    class="ring-neutral-3 flex h-12 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 text-white ring-1"
                                >
                                    <i class="ri-door-line text-xl"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                                <div class="text-neutral-9 text-xs">{{ item.room_type?.name }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11 text-sm">
                            <i class="ri-bed-line mr-1 text-neutral-9"></i>
                            {{ item.bed_count }} {{ item.bed_type }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11 text-sm">
                            <i class="ri-user-line mr-1 text-neutral-9"></i>
                            {{ item.max_occupancy }} guests
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="text-lg font-bold text-emerald-600">৳{{ formatNumber(item.base_rate) }}</div>
                        <div class="text-xs text-neutral-9">per night</div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                            :class="item.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-red-50 text-red-700 ring-red-200'"
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton class="btn btn-icon btn-secondary" size="sm"><i class="ri-more-2-fill"></i></AppButton>
                            </template>
                            <div class="py-1">
                                <AppDropdownItem v-if="can('hotel.room.view')" @click="$inertia.visit(route('hotel.room.show', [hotelId, item.id]))">
                                    <i class="ri-eye-line text-neutral-9 mr-2"></i> View
                                </AppDropdownItem>
                                <AppDropdownItem v-if="can('hotel.room.edit')" @click="$inertia.visit(route('hotel.room.edit', [hotelId, item.id]))">
                                    <i class="ri-edit-line text-neutral-9 mr-2"></i> Edit
                                </AppDropdownItem>
                                <div class="border-neutral-4 my-1 border-t"></div>
                                <AppDropdownItem v-if="can('hotel.room.delete')" class="text-danger" @click="confirmDelete(route('hotel.room.destroy', [hotelId, item.id]))">
                                    <i class="ri-delete-bin-line mr-2"></i> Delete
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="rooms.data.length" :links="rooms.links" :from="rooms.from ?? 0" :to="rooms.to ?? 0" :total="rooms.total ?? 0" class="mt-4 justify-center"></AppPaginator>

    <AppEmptyState v-if="!rooms.data.length" icon="ri-door-line" title="No rooms found" description="Add rooms to this hotel.">
        <template #actions>
            <AppButton v-if="can('hotel.room.create')" class="btn btn-primary" @click="$inertia.visit(route('hotel.room.create', hotelId))">
                <i class="ri-add-fill mr-1"></i> Add Room
            </AppButton>
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('Hotel Rooms')

const props = defineProps({
    hotel: { type: Object, required: true },
    rooms: { type: Object, default: () => ({}) }
})

const hotelId = props.hotel.id

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotels', href: route('hotel.index') },
    { label: props.hotel.name, href: route('hotel.show', hotelId) },
    { label: 'Rooms', last: true }
]

const headers = ['#', 'Room', 'Bed', 'Capacity', 'Rate', 'Status', 'Actions']

const formatNumber = (num) => num ? new Intl.NumberFormat('en-BD').format(num) : '0'

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
