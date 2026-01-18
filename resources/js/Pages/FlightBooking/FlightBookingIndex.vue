<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('flight.booking.create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('flightBooking.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Booking
                </AppButton>

                <AppButton
                    v-if="can('flight.booking.view')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('flightBooking.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Overview -->
    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <!-- Total Bookings -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-blue-500': !filterForm.status }"
            @click="filterByStats('all')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-plane-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-amber-500': filterForm.status === 'pending' }"
            @click="filterByStats('pending')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                    <i class="ri-hourglass-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Pending</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.pending || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Confirmed -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-emerald-500': filterForm.status === 'confirmed' }"
            @click="filterByStats('confirmed')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="ri-checkbox-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Confirmed</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.confirmed || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Ticketed -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-purple-500': filterForm.ticketing_status === 'ticketed' }"
            @click="filterByStats('ticketed')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
                    <i class="ri-ticket-2-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Ticketed</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.ticketed || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Cancelled -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-red-500': filterForm.status === 'cancelled' }"
            @click="filterByStats('cancelled')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                    <i class="ri-close-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Cancelled</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.cancelled || 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <AppCard class="w-full" no-padding>
        <template #title>
            <div
                class="hover:bg-neutral-3/50 -mx-2 -my-1 flex w-full cursor-pointer select-none items-center justify-between rounded-lg px-2 py-1 transition-colors"
                @click="showFilters = !showFilters"
            >
                <div class="flex items-center gap-3">
                    <div class="bg-primary-2 flex h-8 w-8 items-center justify-center rounded-lg">
                        <i class="ri-filter-line text-primary-9"></i>
                    </div>
                    <span>Filter Bookings</span>
                    <span
                        v-if="hasActiveFilters"
                        class="bg-primary-9 inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-xs font-bold text-white"
                    >
                        {{ activeFilterCount }}
                    </span>
                </div>
                <div class="text-neutral-11 flex items-center gap-1 text-sm">
                    <span class="hidden sm:inline">{{ showFilters ? 'Hide' : 'Show' }}</span>
                    <i class="text-xl transition-transform duration-200" :class="showFilters ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                </div>
            </div>
        </template>
        <template #content>
            <div v-show="showFilters" class="p-4 sm:p-5">
                <form class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Status Filter -->
                    <div>
                        <AppLabel for="status">Status</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.status"
                            :options="statusOptions"
                            combo-label="All Status"
                            emit-value
                        />
                    </div>

                    <!-- Ticketing Status -->
                    <div>
                        <AppLabel for="ticketing_status">Ticketing</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.ticketing_status"
                            :options="ticketingOptions"
                            combo-label="All"
                            emit-value
                        />
                    </div>

                    <!-- Departure Date -->
                    <div>
                        <AppLabel for="departure_date">Departure Date</AppLabel>
                        <AppInput
                            type="date"
                            v-model="filterForm.departure_date"
                        />
                    </div>

                    <!-- Booking Source -->
                    <div>
                        <AppLabel for="booking_source">Source</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.booking_source"
                            :options="sourceOptions"
                            combo-label="All Sources"
                            emit-value
                        />
                    </div>
                </form>

                <div class="mt-4 flex justify-end gap-2">
                    <AppButton class="btn btn-secondary" @click="resetFilters">
                        <i class="ri-refresh-line mr-1"></i>
                        Reset
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="applyFilter">
                        <i class="ri-filter-line mr-1"></i>
                        Apply
                    </AppButton>
                </div>
            </div>
        </template>
    </AppCard>

    <AppDataSearch
        class="mt-4"
        v-if="bookings.data.length || route().params.searchTerm"
        :url="route('flightBooking.index')"
        fields-to-search="booking_number,pnr,contact_email"
    ></AppDataSearch>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in bookings.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <!-- Serial Number -->
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (bookings.current_page - 1) * bookings.per_page + (index + 1) }}
                    </AppDataTableData>

                    <!-- Booking Info -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-12 font-mono font-medium">{{ item.booking_number }}</span>
                            <span v-if="item.pnr" class="text-neutral-9 text-xs">PNR: {{ item.pnr }}</span>
                        </div>
                    </AppDataTableData>

                    <!-- Route -->
                    <AppDataTableData>
                        <div class="flex items-center gap-2">
                            <span class="text-neutral-12 font-medium">{{ item.origin_code }}</span>
                            <i class="ri-arrow-right-line text-neutral-9"></i>
                            <span class="text-neutral-12 font-medium">{{ item.destination_code }}</span>
                        </div>
                        <div class="text-neutral-9 text-xs">{{ item.departure_date }}</div>
                    </AppDataTableData>

                    <!-- Passengers -->
                    <AppDataTableData>
                        <span class="text-neutral-11 flex items-center gap-1.5 text-sm">
                            <i class="ri-user-line text-neutral-9"></i>
                            {{ item.total_passengers }} pax
                        </span>
                    </AppDataTableData>

                    <!-- Amount -->
                    <AppDataTableData>
                        <div class="text-neutral-12 font-bold">৳{{ Number(item.total_amount).toLocaleString() }}</div>
                        <div v-if="item.amount_due > 0" class="text-xs text-red-600">Due: ৳{{ Number(item.amount_due).toLocaleString() }}</div>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <div class="flex flex-wrap gap-1">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                                :class="getStatusClass(item.status)"
                            >
                                {{ item.status }}
                            </span>
                            <span
                                v-if="item.ticketing_status === 'ticketed'"
                                class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-purple-200"
                            >
                                <i class="ri-ticket-2-line"></i> Ticketed
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton class="btn btn-icon btn-secondary" size="sm" aria-label="More actions">
                                    <i class="ri-more-2-fill"></i>
                                </AppButton>
                            </template>

                            <div class="py-1">
                                <AppDropdownItem v-if="can('flight.booking.view')" @click="$inertia.visit(route('flightBooking.show', item.id))">
                                    <i class="ri-eye-line text-neutral-9 mr-2"></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem v-if="can('flight.booking.edit')" @click="$inertia.visit(route('flightBooking.edit', item.id))">
                                    <i class="ri-edit-line text-neutral-9 mr-2"></i>
                                    Edit Booking
                                </AppDropdownItem>

                                <AppDropdownItem v-if="can('flight.ticketing.issue') && item.status === 'confirmed'" @click="issueTicket(item.id)">
                                    <i class="ri-ticket-2-line text-neutral-9 mr-2"></i>
                                    Issue Ticket
                                </AppDropdownItem>

                                <div class="border-neutral-4 my-1 border-t"></div>

                                <AppDropdownItem
                                    v-if="can('flight.booking.delete')"
                                    class="text-danger hover:bg-danger-light"
                                    @click="confirmDelete(route('flightBooking.destroy', item.id))"
                                >
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="bookings.data.length"
        :links="bookings.links"
        :from="bookings.from ?? 0"
        :to="bookings.to ?? 0"
        :total="bookings.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!bookings.data.length"
        icon="ri-plane-line"
        title="No flight bookings found"
        description="Get started by adding your first flight booking."
    >
        <template #actions>
            <AppButton
                v-if="can('flight.booking.create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('flightBooking.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Booking
            </AppButton>
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Flight Bookings')

const props = defineProps({
    bookings: {
        type: Object,
        default: () => ({})
    },
    stats: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Bookings', last: true }
]

const headers = ['#', 'Booking', 'Route', 'Passengers', 'Amount', 'Status', 'Actions']

// --- Status Options ---
const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'ticketing', label: 'Ticketing' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'refunded', label: 'Refunded' }
]

const ticketingOptions = [
    { value: 'not_ticketed', label: 'Not Ticketed' },
    { value: 'ticketed', label: 'Ticketed' },
    { value: 'void', label: 'Void' }
]

const sourceOptions = [
    { value: 'website', label: 'Website' },
    { value: 'admin', label: 'Admin' },
    { value: 'agent_portal', label: 'Agent' }
]

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200',
        confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        ticketing: 'bg-blue-50 text-blue-700 ring-blue-200',
        cancelled: 'bg-red-50 text-red-700 ring-red-200',
        refunded: 'bg-purple-50 text-purple-700 ring-purple-200'
    }
    return classes[status] || 'bg-neutral-50 text-neutral-700 ring-neutral-200'
}

// --- Collapsible Filter ---
const STORAGE_KEY = 'flight_booking_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

// --- Filter Form ---
const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    status: currentParams.get('status') ?? null,
    ticketing_status: currentParams.get('ticketing_status') ?? null,
    departure_date: currentParams.get('departure_date') ?? null,
    booking_source: currentParams.get('booking_source') ?? null
})

const hasActiveFilters = computed(() => {
    return !!(filterForm.status || filterForm.ticketing_status || filterForm.departure_date || filterForm.booking_source)
})

const activeFilterCount = computed(() => {
    let count = 0
    if (filterForm.status) count++
    if (filterForm.ticketing_status) count++
    if (filterForm.departure_date) count++
    if (filterForm.booking_source) count++
    return count
})

onMounted(() => {
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

const applyFilter = () => {
    filterForm.get(route('flightBooking.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const resetFilters = () => {
    filterForm.status = null
    filterForm.ticketing_status = null
    filterForm.departure_date = null
    filterForm.booking_source = null
    filterForm.get(route('flightBooking.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const filterByStats = (type) => {
    filterForm.status = null
    filterForm.ticketing_status = null
    filterForm.departure_date = null
    filterForm.booking_source = null

    if (type === 'pending') {
        filterForm.status = 'pending'
    } else if (type === 'confirmed') {
        filterForm.status = 'confirmed'
    } else if (type === 'ticketed') {
        filterForm.ticketing_status = 'ticketed'
    } else if (type === 'cancelled') {
        filterForm.status = 'cancelled'
    }

    applyFilter()
}

const issueTicket = (id) => {
    if (confirm('Issue ticket for this booking?')) {
        // Ticket issuance logic
    }
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
