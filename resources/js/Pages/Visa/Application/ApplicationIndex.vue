<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('visa-application-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visaApplication.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    New Application
                </AppButton>

                <AppButton
                    v-if="can('visa-application-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visaApplication.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats -->
    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-file-list-3-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total Applications</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                    <i class="ri-time-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Processing</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.processing || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="ri-checkbox-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Approved</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.approved || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
             <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                    <i class="ri-close-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Rejected</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.rejected || 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
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
                    <span>Filter Applications</span>
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
                <form class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                     <!-- Country Filter -->
                    <div>
                        <AppLabel for="country_id">Country</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.country_id"
                            :options="countries"
                            combo-label="All Countries"
                            emit-value
                        />
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <AppLabel for="status">Application Status</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.status"
                            :options="statusOptions"
                            combo-label="All Status"
                            emit-value
                        />
                    </div>

                    <!-- Payment Status Filter -->
                    <div>
                        <AppLabel for="payment_status">Payment Status</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.payment_status"
                            :options="paymentStatusOptions"
                            combo-label="All Payments"
                            emit-value
                        />
                    </div>
                    
                    <div>
                         <AppLabel for="date_range">Date Range</AppLabel>
                         <div class="flex gap-2">
                             <AppInputText type="date" v-model="filterForm.start_date" class="w-full" />
                             <AppInputText type="date" v-model="filterForm.end_date" class="w-full" />
                         </div>
                    </div>
                </form>

                <div class="mt-4 flex justify-end gap-2">
                    <AppButton class="btn btn-secondary" @click="resetFilters">
                        <i class="ri-refresh-line mr-1"></i> Reset
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="applyFilter">
                        <i class="ri-filter-line mr-1"></i> Apply
                    </AppButton>
                </div>
            </div>
        </template>
    </AppCard>

    <AppDataSearch
        class="mt-4"
        v-if="applications.data.length || route().params.searchTerm"
        :url="route('visaApplication.index')"
        fields-to-search="application_number,customer:name"
    ></AppDataSearch>

    <AppDataTable v-if="applications.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in applications.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (applications.current_page - 1) * applications.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-12 font-mono font-medium">{{ item.application_number }}</span>
                        <div class="text-neutral-9 text-xs mt-0.5">
                            {{ formatDate(item.application_date) }}
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="text-neutral-12 font-medium">{{ item.customer?.name || 'N/A' }}</span>
                            <span class="text-neutral-9 text-xs">{{ item.customer?.phone || '' }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="text-neutral-12 font-medium">{{ item.visa?.name || 'N/A' }}</span>
                            <span v-if="item.visa?.country" class="text-neutral-9 text-xs">{{ item.visa.country.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1 capitalize"
                            :class="getStatusColor(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </AppDataTableData>
                    
                     <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1 capitalize"
                            :class="getPaymentStatusColor(item.payment_status)"
                        >
                            {{ item.payment_status }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                         <div class="flex flex-col items-end">
                            <span class="font-bold text-neutral-12">Total: ৳{{ formatNumber(item.total_amount) }}</span>
                            <span v-if="item.due_amount > 0" class="text-xs text-red-500">Due: ৳{{ formatNumber(item.due_amount) }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData class="text-right">
                         <AppTooltip text="View / Edit" class="mr-2">
                            <AppButton
                                v-if="can('visa-application-view')"
                                class="btn btn-icon btn-primary"
                                size="sm"
                                @click="$inertia.visit(route('visaApplication.show', item.id))"
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete Application">
                            <AppButton
                                v-if="can('visa-application-delete')"
                                class="btn btn-icon btn-destructive"
                                size="sm"
                                @click="confirmDelete(route('visaApplication.destroy', item.id))"
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
        v-if="applications.data.length"
        :links="applications.links"
        :from="applications.from ?? 0"
        :to="applications.to ?? 0"
        :total="applications.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!applications.data.length"
        icon="ri-file-list-3-line"
        title="No applications found"
        description="No visa applications have been submitted yet."
    >
        <template #actions>
            <AppButton
                v-if="can('visa-application-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('visaApplication.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                New Application
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
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppCombobox from '@/Components/Form/AppCombobox.vue'

const { can } = useAuthCan()
const { title } = useTitle('Visa Applications')

const props = defineProps({
    applications: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) },
    countries: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visa Applications', last: true }
]

const headers = ['#', 'Details', 'Customer', 'Visa', 'Status', 'Payment', 'Amount', 'Actions']

// Filter Logic
const STORAGE_KEY = 'visa_app_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    country_id: currentParams.get('country_id') ?? null,
    status: currentParams.get('status') ?? null,
    payment_status: currentParams.get('payment_status') ?? null,
    start_date: currentParams.get('start_date') ?? null,
    end_date: currentParams.get('end_date') ?? null,
})

const statusOptions = [
    { value: 'submitted', label: 'Submitted' },
    { value: 'processing', label: 'Processing' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
]

const paymentStatusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'partial', label: 'Partial' },
    { value: 'paid', label: 'Paid' },
    { value: 'refunded', label: 'Refunded' }
]

const hasActiveFilters = computed(() => {
    return !!(
        filterForm.country_id ||
        filterForm.status ||
        filterForm.payment_status ||
        filterForm.start_date ||
        filterForm.end_date
    )
})

const activeFilterCount = computed(() => {
    let count = 0
    if (filterForm.country_id) count++
    if (filterForm.status) count++
    if (filterForm.payment_status) count++
    if (filterForm.start_date || filterForm.end_date) count++
    return count
})

onMounted(() => {
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

const applyFilter = () => {
    filterForm.get(route('visaApplication.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const resetFilters = () => {
    filterForm.country_id = null
    filterForm.status = null
    filterForm.payment_status = null
    filterForm.start_date = null
    filterForm.end_date = null
    filterForm.get(route('visaApplication.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const formatNumber = (num) => {
    if (!num) return '0'
    return new Intl.NumberFormat('en-BD').format(num)
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getStatusColor = (status) => {
    switch (status) {
        case 'submitted': return 'bg-blue-50 text-blue-700 ring-blue-200'
        case 'processing': return 'bg-amber-50 text-amber-700 ring-amber-200'
        case 'approved': return 'bg-emerald-50 text-emerald-700 ring-emerald-200'
        case 'rejected': return 'bg-red-50 text-red-700 ring-red-200'
        case 'completed': return 'bg-purple-50 text-purple-700 ring-purple-200'
        case 'cancelled': return 'bg-neutral-50 text-neutral-700 ring-neutral-200'
        default: return 'bg-neutral-50 text-neutral-700 ring-neutral-200'
    }
}

const getPaymentStatusColor = (status) => {
     switch (status) {
        case 'paid': return 'bg-emerald-50 text-emerald-700 ring-emerald-200'
        case 'partial': return 'bg-amber-50 text-amber-700 ring-amber-200'
        case 'pending': return 'bg-red-50 text-red-700 ring-red-200'
        default: return 'bg-neutral-50 text-neutral-700 ring-neutral-200'
    }
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
