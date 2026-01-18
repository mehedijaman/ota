<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('flight.airline.create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('flight.airline.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Airline
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        class="mb-4"
        v-if="airlines.data.length || route().params.searchTerm"
        :url="route('flight.airline.index')"
        fields-to-search="iata_code,name"
    ></AppDataSearch>

    <AppDataTable v-if="airlines.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in airlines.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (airlines.current_page - 1) * airlines.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img v-if="item.logo_url" :src="item.logo_url" :alt="item.name" class="h-10 w-10 rounded-lg object-contain" />
                                <div v-else class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 font-bold text-white">
                                    {{ item.iata_code }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                                <div class="text-neutral-9 text-xs">{{ item.iata_code }} / {{ item.icao_code }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11">{{ item.country?.name }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span v-if="item.alliance" class="inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-700 ring-1 ring-blue-200">
                            {{ item.alliance }}
                        </span>
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
                                <AppButton class="btn btn-icon btn-secondary" size="sm">
                                    <i class="ri-more-2-fill"></i>
                                </AppButton>
                            </template>
                            <div class="py-1">
                                <AppDropdownItem v-if="can('flight.airline.edit')" @click="$inertia.visit(route('flight.airline.edit', item.id))">
                                    <i class="ri-edit-line text-neutral-9 mr-2"></i> Edit
                                </AppDropdownItem>
                                <AppDropdownItem
                                    v-if="can('flight.airline.delete')"
                                    class="text-danger"
                                    @click="confirmDelete(route('flight.airline.destroy', item.id))"
                                >
                                    <i class="ri-delete-bin-line mr-2"></i> Delete
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="airlines.data.length"
        :links="airlines.links"
        :from="airlines.from ?? 0"
        :to="airlines.to ?? 0"
        :total="airlines.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!airlines.data.length"
        icon="ri-flight-takeoff-line"
        title="No airlines found"
        description="Add airlines to use in flight bookings."
    >
        <template #actions>
            <AppButton v-if="can('flight.airline.create')" class="btn btn-primary" @click="$inertia.visit(route('flight.airline.create'))">
                <i class="ri-add-fill mr-1"></i> Add Airline
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

const { can } = useAuthCan()
const { title } = useTitle('Airlines')

defineProps({
    airlines: { type: Object, default: () => ({}) }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Airlines', last: true }
]

const headers = ['#', 'Airline', 'Country', 'Alliance', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
