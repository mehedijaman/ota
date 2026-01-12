<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('tour-booking-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tourBooking.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
                <AppButton
                    v-if="can('tour-booking-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('tourBooking.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    New Booking
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Overview -->
    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-6">
        <!-- Total Bookings -->
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
            :class="{ 'ring-2 ring-blue-500': !filterForm.status && !filterForm.payment_status }"
            @click="filterByStats('all')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-calendar-check-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
            :class="{ 'ring-2 ring-amber-500': filterForm.status === 'pending' }"
            @click="filterByStats('pending')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                    <i class="ri-time-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Pending</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.pending || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Confirmed -->
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
            :class="{ 'ring-2 ring-teal-500': filterForm.status === 'confirmed' }"
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

        <!-- Paid -->
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
            :class="{ 'ring-2 ring-emerald-500': filterForm.payment_status === 'paid' }"
            @click="filterByStats('paid')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg shadow-green-500/25">
                    <i class="ri-checkbox-circle-fill text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Paid</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.paid || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Partial Paid -->
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
            :class="{ 'ring-2 ring-blue-500': filterForm.payment_status === 'partial' }"
            @click="filterByStats('partial')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-funds-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Partial</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.partial || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Unpaid -->
        <div
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
            :class="{ 'ring-2 ring-red-500': filterForm.payment_status === 'unpaid' }"
            @click="filterByStats('unpaid')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                    <i class="ri-close-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Unpaid</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.unpaid || 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <AppCard class="mb-6 w-full" no-padding>
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
                    <i
                        class="text-xl transition-transform duration-200"
                        :class="showFilters ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                    ></i>
                </div>
            </div>
        </template>
        <template #content>
            <div v-show="showFilters" class="p-4 sm:p-5">
                <form class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <!-- Search -->
                    <div>
                        <AppLabel>Search</AppLabel>
                        <AppInputText
                            v-model="filterForm.search"
                            placeholder="Booking #, name, email..."
                            class="w-full"
                        >
                            <template #iconLeft>
                                <i class="ri-search-line"></i>
                            </template>
                        </AppInputText>
                    </div>

                    <!-- Status -->
                    <div>
                        <AppLabel>Status</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.status"
                            :options="statusOptions"
                            combo-label="All Statuses"
                            emit-value
                        />
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <AppLabel>Payment</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.payment_status"
                            :options="paymentStatusOptions"
                            combo-label="All Payments"
                            emit-value
                        />
                    </div>

                    <!-- Travel Date From -->
                    <div>
                        <AppLabel>Travel From</AppLabel>
                        <AppInputText
                            v-model="filterForm.travel_from"
                            type="date"
                            class="w-full"
                        />
                    </div>

                    <!-- Travel Date To -->
                    <div>
                        <AppLabel>Travel To</AppLabel>
                        <AppInputText
                            v-model="filterForm.travel_to"
                            type="date"
                            class="w-full"
                        />
                    </div>
                </form>

                <div class="mt-4 flex justify-end gap-2">
                    <AppButton class="btn btn-secondary" @click="resetFilters">
                        <i class="ri-refresh-line mr-1"></i>
                        Reset
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="applyFilters">
                        <i class="ri-filter-line mr-1"></i>
                        Apply
                    </AppButton>
                </div>
            </div>
        </template>
    </AppCard>

    <AppDataSearch
        v-if="bookings.data.length || route().params.searchTerm"
        :url="route('tourBooking.index')"
        fields-to-search="booking_number,contact_name,contact_email"
    ></AppDataSearch>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in bookings.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group cursor-pointer transition-colors"
                    @click="$inertia.visit(route('tourBooking.show', item.id))"
                >
                    <!-- Serial Number -->
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (bookings.current_page - 1) * bookings.per_page + (index + 1) }}
                    </AppDataTableData>

                    <!-- Booking Number -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-md shadow-primary-500/25">
                                <i class="ri-ticket-line text-lg text-white"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-primary-11 block font-mono text-sm font-bold">
                                    {{ item.booking_number }}
                                </span>
                                <span class="text-neutral-9 text-xs">
                                    {{ formatDate(item.created_at) }}
                                </span>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Tour & Travel Date -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-12 font-medium">{{ item.tour || '-' }}</span>
                            <span class="text-neutral-10 flex items-center gap-1.5 text-xs">
                                <i class="ri-calendar-event-line text-neutral-9"></i>
                                {{ item.travel_date || 'Not set' }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Contact -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-11 flex items-center gap-1.5 text-sm">
                                <i class="ri-user-line text-neutral-9"></i>
                                {{ item.contact_name }}
                            </span>
                            <span class="text-neutral-10 flex items-center gap-1.5 text-xs">
                                <i class="ri-mail-line text-neutral-9"></i>
                                {{ item.contact_email }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Travelers -->
                    <AppDataTableData>
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="ri-group-line"></i>
                            </div>
                            <span class="text-neutral-11 font-medium">{{ item.total_travelers }} pax</span>
                        </div>
                    </AppDataTableData>

                    <!-- Amount -->
                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                ৳{{ formatNumber(item.total_amount) }}
                            </span>
                            <span v-if="item.amount_paid > 0" class="text-neutral-9 text-xs">
                                Paid: ৳{{ formatNumber(item.amount_paid) }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-1.5">
                            <span :class="getStatusBadgeClass(item.status)" class="inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
                                <i :class="getStatusIcon(item.status)"></i>
                                {{ formatStatus(item.status) }}
                            </span>
                            <span :class="getPaymentBadgeClass(item.payment_status)" class="inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
                                <i :class="getPaymentIcon(item.payment_status)"></i>
                                {{ formatStatus(item.payment_status) }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData @click.stop>
                        <div class="flex justify-end">
                            <AppDropdown placement="bottom-end">
                                <template #trigger>
                                    <AppButton class="btn btn-icon btn-secondary">
                                        <i class="ri-more-2-fill"></i>
                                    </AppButton>
                                </template>

                                <AppDropdownItem
                                    v-if="can('tour-booking-view')"
                                    @click="$inertia.visit(route('tourBooking.show', item.id))"
                                >
                                    <i class="ri-eye-line mr-2 text-blue-500"></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="can('tour-booking-edit')"
                                    @click="$inertia.visit(route('tourBooking.edit', item.id))"
                                >
                                    <i class="ri-edit-line mr-2 text-primary-500"></i>
                                    Edit Booking
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="can('tour-booking-delete')"
                                    @click="confirmDelete(route('tourBooking.destroy', item.id))"
                                >
                                    <i class="ri-delete-bin-line mr-2 text-red-500"></i>
                                    Delete
                                </AppDropdownItem>
                            </AppDropdown>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="bookings.data.length"
        :links="bookings.links"
        :from="bookings.from || 0"
        :to="bookings.to || 0"
        :total="bookings.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <!-- Empty State -->
    <div v-if="!bookings.data.length && !route().params.searchTerm" class="border-neutral-6 bg-neutral-1 mt-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex flex-col items-center justify-center py-16">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30">
                <i class="ri-calendar-check-line text-4xl text-blue-500"></i>
            </div>
            <h3 class="text-neutral-12 mb-2 text-lg font-semibold">No Bookings Yet</h3>
            <p class="text-neutral-9 mb-6 max-w-sm text-center text-sm">
                Start by creating your first tour booking. Track travelers, payments, and manage all booking details.
            </p>
            <AppButton v-if="can('tour-booking-create')" class="btn btn-primary" @click="$inertia.visit(route('tourBooking.create'))">
                <i class="ri-add-line mr-1"></i>
                Create First Booking
            </AppButton>
        </div>
    </div>

    <!-- Search Empty State -->
    <AppAlert v-if="!bookings.data.length && route().params.searchTerm" class="mt-4">
        No bookings found matching your search criteria.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppCombobox from '@/Components/Form/AppCombobox.vue'

const { can } = useAuthCan()
const { title } = useTitle('Tour Bookings')

const props = defineProps({
    bookings: {
        type: Object,
        default: () => ({})
    },
    stats: {
        type: Object,
        default: () => null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', last: true }
]

const headers = ['SL', 'Booking', 'Tour / Date', 'Contact', 'Travelers', 'Amount', 'Status', 'Actions']

// --- Collapsible Filter ---
const STORAGE_KEY = 'booking_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

// --- Filter Form ---
const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    search: currentParams.get('search') ?? null,
    status: currentParams.get('status') ?? null,
    payment_status: currentParams.get('payment_status') ?? null,
    travel_from: currentParams.get('travel_from') ?? null,
    travel_to: currentParams.get('travel_to') ?? null
})

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
]

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'pending', label: 'Pending' },
    { value: 'partial', label: 'Partial' },
    { value: 'paid', label: 'Paid' },
    { value: 'refunded', label: 'Refunded' }
]

const hasActiveFilters = computed(() => {
    return !!(
        filterForm.search ||
        filterForm.status ||
        filterForm.payment_status ||
        filterForm.travel_from ||
        filterForm.travel_to
    )
})

const activeFilterCount = computed(() => {
    let count = 0
    if (filterForm.search) count++
    if (filterForm.status) count++
    if (filterForm.payment_status) count++
    if (filterForm.travel_from) count++
    if (filterForm.travel_to) count++
    return count
})

onMounted(() => {
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

const applyFilters = () => {
    filterForm.get(route('tourBooking.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const resetFilters = () => {
    filterForm.search = null
    filterForm.status = null
    filterForm.payment_status = null
    filterForm.travel_from = null
    filterForm.travel_to = null
    filterForm.get(route('tourBooking.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const filterByStats = (type) => {
    filterForm.search = null
    filterForm.status = null
    filterForm.payment_status = null
    filterForm.travel_from = null
    filterForm.travel_to = null

    if (type === 'pending') {
        filterForm.status = 'pending'
    } else if (type === 'confirmed') {
        filterForm.status = 'confirmed'
    } else if (type === 'paid') {
        filterForm.payment_status = 'paid'
    } else if (type === 'partial') {
        filterForm.payment_status = 'partial'
    } else if (type === 'unpaid') {
        filterForm.payment_status = 'unpaid'
    }

    applyFilters()
}

const formatNumber = (num) => new Intl.NumberFormat('en-BD').format(num || 0)

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

const formatStatus = (status) => {
    return status?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) || '-'
}

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800',
        processing: 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800',
        confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800',
        completed: 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-800',
        cancelled: 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-800',
    }
    return classes[status] || 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700'
}

const getStatusIcon = (status) => {
    const icons = {
        pending: 'ri-time-line',
        processing: 'ri-loader-4-line',
        confirmed: 'ri-checkbox-circle-line',
        completed: 'ri-check-double-line',
        cancelled: 'ri-close-circle-line',
    }
    return icons[status] || 'ri-question-line'
}

const getPaymentBadgeClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800',
        partial: 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800',
        paid: 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800',
        refunded: 'bg-purple-50 text-purple-700 ring-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:ring-purple-800',
        unpaid: 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-800',
    }
    return classes[status] || 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700'
}

const getPaymentIcon = (status) => {
    const icons = {
        pending: 'ri-time-line',
        partial: 'ri-funds-line',
        paid: 'ri-checkbox-circle-fill',
        refunded: 'ri-refund-line',
        unpaid: 'ri-close-circle-line',
    }
    return icons[status] || 'ri-money-dollar-circle-line'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
