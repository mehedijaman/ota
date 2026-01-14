<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('hotel.booking.create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('hotelBooking.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    New Booking
                </AppButton>

                <AppButton
                    v-if="can('hotel.booking.view')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('hotelBooking.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Overview -->
    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4 xl:grid-cols-7">
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-blue-500': !filterForm.status }"
            @click="filterByStats('all')"
        >
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600">
                    <i class="ri-calendar-check-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Total</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-yellow-500': filterForm.status === 'pending' }"
            @click="filterByStats('pending')"
        >
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600">
                    <i class="ri-time-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Pending</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.pending || 0 }}</p>
                </div>
            </div>
        </div>

        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-emerald-500': filterForm.status === 'confirmed' }"
            @click="filterByStats('confirmed')"
        >
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600">
                    <i class="ri-checkbox-circle-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Confirmed</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.confirmed || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600">
                    <i class="ri-flight-land-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Arriving Today</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.arriving_today || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600">
                    <i class="ri-flight-takeoff-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Departing Today</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.departing_today || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600">
                    <i class="ri-money-dollar-circle-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Revenue</p>
                    <p class="text-neutral-12 text-xl font-bold">৳{{ formatNumber(stats.total_revenue) }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-red-600">
                    <i class="ri-error-warning-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs font-medium">Unpaid</p>
                    <p class="text-neutral-12 text-xl font-bold">৳{{ formatNumber(stats.unpaid_amount) }}</p>
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
                </div>
                <i class="text-xl transition-transform duration-200" :class="showFilters ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
            </div>
        </template>
        <template #content>
            <div v-show="showFilters" class="p-4 sm:p-5">
                <form class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <AppLabel>Hotel</AppLabel>
                        <AppCombobox v-model="filterForm.hotel_id" :options="hotelOptions" combo-label="All Hotels" emit-value />
                    </div>
                    <div>
                        <AppLabel>Status</AppLabel>
                        <AppCombobox v-model="filterForm.status" :options="statusOptions" combo-label="All Status" emit-value />
                    </div>
                    <div>
                        <AppLabel>Payment Status</AppLabel>
                        <AppCombobox v-model="filterForm.payment_status" :options="paymentStatusOptions" combo-label="All" emit-value />
                    </div>
                    <div>
                        <AppLabel>Check-in From</AppLabel>
                        <AppInputText type="date" v-model="filterForm.date_from" class="w-full" />
                    </div>
                    <div>
                        <AppLabel>Check-out To</AppLabel>
                        <AppInputText type="date" v-model="filterForm.date_to" class="w-full" />
                    </div>
                </form>
                <div class="mt-4 flex justify-end gap-2">
                    <AppButton class="btn btn-secondary" @click="resetFilters">Reset</AppButton>
                    <AppButton class="btn btn-primary" @click="applyFilter">Apply</AppButton>
                </div>
            </div>
        </template>
    </AppCard>

    <!-- Bulk Actions -->
    <div v-if="selectedIds.length > 0" class="mt-4 p-4 bg-primary-50 rounded-lg flex items-center justify-between border border-primary-100 animate-fade-in-down">
        <div class="font-medium text-primary-900 flex items-center gap-2">
            <i class="ri-checkbox-multiple-line text-lg"></i>
            {{ selectedIds.length }} selected
        </div>
        <div class="flex gap-2 flex-wrap">
            <AppButton v-if="can('hotel.booking.edit')" class="btn btn-sm btn-white text-emerald-600 hover:bg-emerald-50" @click="executeBulkAction('confirmed')">Confirm</AppButton>
            <AppButton v-if="can('hotel.booking.edit')" class="btn btn-sm btn-white text-blue-600 hover:bg-blue-50" @click="executeBulkAction('checked_in')">Check In</AppButton>
            <AppButton v-if="can('hotel.booking.edit')" class="btn btn-sm btn-white text-purple-600 hover:bg-purple-50" @click="executeBulkAction('checked_out')">Check Out</AppButton>
            <div class="hidden sm:block h-8 w-px bg-primary-200 mx-1"></div>
            <AppButton v-if="can('hotel.booking.edit')" class="btn btn-sm btn-white text-red-600 hover:bg-red-50" @click="executeBulkAction('cancelled')">Cancel</AppButton>
            <AppButton v-if="can('hotel.booking.delete')" class="btn btn-sm btn-danger" @click="executeBulkAction('delete')">Delete</AppButton>
        </div>
    </div>

    <AppDataSearch
        class="mt-4"
        v-if="bookings.data.length || route().params.searchTerm"
        :url="route('hotelBooking.index')"
        fields-to-search="booking_number,contact_name,contact_email"
    ></AppDataSearch>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableHead>
            <thead class="bg-neutral-1 text-xs uppercase text-neutral-9 font-medium">
                <tr>
                    <th class="px-6 py-3 w-10 text-center">
                        <input type="checkbox" :checked="allSelected" @change="toggleAll" class="rounded border-neutral-4 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer" />
                    </th>
                    <th v-for="h in headers" :key="h" class="px-6 py-3">{{ h }}</th>
                </tr>
            </thead>
        </template>
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="item in bookings.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors cursor-pointer"
                    @click="$inertia.visit(route('hotelBooking.show', item.id))"
                >
                    <AppDataTableData class="w-10 text-center" @click.stop>
                         <input type="checkbox" :value="item.id" v-model="selectedIds" class="rounded border-neutral-4 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer" />
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="font-mono text-sm font-medium text-blue-600">{{ item.booking_number }}</div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="min-w-0">
                            <div class="text-neutral-12 font-medium">{{ item.hotel?.name }}</div>
                            <div class="text-neutral-9 text-xs">{{ item.contact_name }}</div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="text-sm">
                            <div>{{ formatDate(item.check_in_date) }}</div>
                            <div class="text-neutral-9 text-xs">to {{ formatDate(item.check_out_date) }}</div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-sm">{{ item.nights }} nights, {{ item.total_rooms }} room(s)</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="text-lg font-bold text-emerald-600">৳{{ formatNumber(item.total_amount) }}</div>
                        <div v-if="item.amount_due > 0" class="text-xs text-red-600">Due: ৳{{ formatNumber(item.amount_due) }}</div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span :class="statusClass(item.status)" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1">
                            {{ item.status }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton class="btn btn-icon btn-secondary" size="sm"><i class="ri-more-2-fill"></i></AppButton>
                            </template>
                            <div class="py-1">
                                <AppDropdownItem @click="$inertia.visit(route('hotelBooking.show', item.id))">
                                    <i class="ri-eye-line text-neutral-9 mr-2"></i> View
                                </AppDropdownItem>
                                <AppDropdownItem v-if="can('hotel.booking.edit')" @click="$inertia.visit(route('hotelBooking.edit', item.id))">
                                    <i class="ri-edit-line text-neutral-9 mr-2"></i> Edit
                                </AppDropdownItem>
                                <div class="border-neutral-4 my-1 border-t"></div>
                                <AppDropdownItem v-if="item.status === 'pending'" @click="updateStatus(item.id, 'confirmed')">
                                    <i class="ri-checkbox-circle-line text-emerald-600 mr-2"></i> Confirm
                                </AppDropdownItem>
                                <AppDropdownItem v-if="item.status === 'confirmed'" @click="updateStatus(item.id, 'checked_in')">
                                    <i class="ri-login-box-line text-blue-600 mr-2"></i> Check In
                                </AppDropdownItem>
                                <AppDropdownItem v-if="item.status === 'checked_in'" @click="updateStatus(item.id, 'checked_out')">
                                    <i class="ri-logout-box-line text-purple-600 mr-2"></i> Check Out
                                </AppDropdownItem>
                                <AppDropdownItem class="text-danger" @click="updateStatus(item.id, 'cancelled')">
                                    <i class="ri-close-circle-line mr-2"></i> Cancel
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="bookings.data.length" :links="bookings.links" :from="bookings.from ?? 0" :to="bookings.to ?? 0" :total="bookings.total ?? 0" class="mt-4 justify-center"></AppPaginator>

    <AppEmptyState v-if="!bookings.data.length" icon="ri-calendar-check-line" title="No bookings found" description="Create your first hotel booking.">
        <template #actions>
            <AppButton v-if="can('hotel.booking.create')" class="btn btn-primary" @click="$inertia.visit(route('hotelBooking.create'))">
                <i class="ri-add-fill mr-1"></i> New Booking
            </AppButton>
        </template>
    </AppEmptyState>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'

const { can } = useAuthCan()
const { title } = useTitle('Hotel Bookings')

const props = defineProps({
    bookings: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) },
    hotels: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotel Bookings', last: true }
]

const headers = ['Booking #', 'Hotel / Guest', 'Dates', 'Stay', 'Amount', 'Status', 'Actions']

const hotelOptions = computed(() => props.hotels.map(h => ({ value: h.id, label: h.name })))

const showFilters = ref(false)
const filterForm = useForm({
    hotel_id: null,
    status: null,
    payment_status: null,
    date_from: null,
    date_to: null
})

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'checked_in', label: 'Checked In' },
    { value: 'checked_out', label: 'Checked Out' },
    { value: 'cancelled', label: 'Cancelled' }
]

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'partial', label: 'Partial' },
    { value: 'paid', label: 'Paid' }
]

const applyFilter = () => filterForm.get(route('hotelBooking.index'), { preserveScroll: true, replace: true })
const resetFilters = () => {
    Object.keys(filterForm.data()).forEach(k => filterForm[k] = null)
    applyFilter()
}
const filterByStats = (type) => {
    resetFilters()
    if (type !== 'all') filterForm.status = type
    applyFilter()
}

const formatNumber = (num) => num ? new Intl.NumberFormat('en-BD').format(num) : '0'
const formatDate = (date) => new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        checked_in: 'bg-blue-50 text-blue-700 ring-blue-200',
        checked_out: 'bg-purple-50 text-purple-700 ring-purple-200',
        cancelled: 'bg-red-50 text-red-700 ring-red-200',
        no_show: 'bg-gray-50 text-gray-700 ring-gray-200'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-200'
}

const updateStatus = (id, status) => {
    router.patch(route('hotelBooking.updateStatus', { id, status }), {}, { preserveScroll: true })
}

// Bulk Actions
const selectedIds = ref([])
const allSelected = computed(() => props.bookings.data && props.bookings.data.length > 0 && selectedIds.value.length === props.bookings.data.length)

const toggleAll = () => {
    if (allSelected.value) {
        selectedIds.value = []
    } else {
        selectedIds.value = props.bookings.data.map(b => b.id)
    }
}

const executeBulkAction = (action) => {
    if (action === 'delete') {
        if (!confirm(`Are you sure you want to delete ${selectedIds.value.length} bookings?`)) return
        router.post(route('hotelBooking.bulkDestroy'), { ids: selectedIds.value }, { 
            onSuccess: () => selectedIds.value = [] 
        })
    } else {
        if (!confirm(`Mark ${selectedIds.value.length} bookings as ${action}?`)) return
        router.post(route('hotelBooking.bulkUpdateStatus'), { ids: selectedIds.value, status: action }, {
            onSuccess: () => selectedIds.value = []
        })
    }
}
</script>
