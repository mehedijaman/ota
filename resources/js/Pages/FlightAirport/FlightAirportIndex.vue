<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('flight.airport.create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('flight.airport.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Airport
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        class="mb-4"
        v-if="airports.data.length || route().params.searchTerm"
        :url="route('flight.airport.index')"
        fields-to-search="iata_code,name,city"
    ></AppDataSearch>

    <AppDataTable v-if="airports.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in airports.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (airports.current_page - 1) * airports.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 font-bold text-white">
                                {{ item.iata_code }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                                <div v-if="item.icao_code" class="text-neutral-9 text-xs">ICAO: {{ item.icao_code }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="text-neutral-12">{{ item.city }}</span>
                            <span class="text-neutral-9 text-xs">{{ item.country?.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span v-if="item.is_popular" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200">
                            <i class="ri-star-fill text-amber-500"></i> Popular
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                            :class="item.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-red-50 text-red-700 ring-red-200'"
                        >
                            <span class="h-1.5 w-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
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
                                <AppDropdownItem v-if="can('flight.airport.edit')" @click="$inertia.visit(route('flight.airport.edit', item.id))">
                                    <i class="ri-edit-line text-neutral-9 mr-2"></i> Edit
                                </AppDropdownItem>
                                <AppDropdownItem
                                    v-if="can('flight.airport.delete')"
                                    class="text-danger"
                                    @click="confirmDelete(route('flight.airport.destroy', item.id))"
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
        v-if="airports.data.length"
        :links="airports.links"
        :from="airports.from ?? 0"
        :to="airports.to ?? 0"
        :total="airports.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!airports.data.length"
        icon="ri-plane-line"
        title="No airports found"
        description="Add airports to use in flight bookings."
    >
        <template #actions>
            <AppButton v-if="can('flight.airport.create')" class="btn btn-primary" @click="$inertia.visit(route('flight.airport.create'))">
                <i class="ri-add-fill mr-1"></i> Add Airport
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
const { title } = useTitle('Airports')

defineProps({
    airports: { type: Object, default: () => ({}) }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Airports', last: true }
]

const headers = ['#', 'Airport', 'Location', 'Popular', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
