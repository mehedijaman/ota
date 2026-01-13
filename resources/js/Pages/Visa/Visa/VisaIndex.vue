<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('visa-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visa.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Visa
                </AppButton>

                <AppButton
                    v-if="can('visa-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visa.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Overview -->
    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-passport-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total Visas</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="ri-checkbox-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Active</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.active || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                    <i class="ri-star-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Featured</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.featured || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
             <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
                    <i class="ri-fire-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Popular</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.popular || 0 }}</p>
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
                    <span>Filter Visas</span>
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

                    <!-- Category Filter -->
                    <div>
                        <AppLabel for="category_id">Category</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.category_id"
                            :options="categories"
                            combo-label="All Categories"
                            emit-value
                        />
                    </div>

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

                    <!-- Featured Filter -->
                    <div>
                        <AppLabel for="is_featured">Featured</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.is_featured"
                            :options="yesNoOptions"
                            combo-label="All"
                            emit-value
                        />
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
        v-if="visas.data.length || route().params.searchTerm"
        :url="route('visa.index')"
        fields-to-search="name,code"
    ></AppDataSearch>

    <AppDataTable v-if="visas.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in visas.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (visas.current_page - 1) * visas.per_page + (index + 1) }}
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
                                    <i class="ri-passport-line text-xl"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                                <div class="text-neutral-9 text-xs">{{ item.code }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span v-if="item.country" class="text-neutral-11 flex items-center gap-1.5 text-sm">
                                <i class="ri-flag-line text-neutral-9"></i>
                                {{ item.country.name }}
                            </span>
                            <span v-if="item.category" class="text-neutral-10 flex items-center gap-1.5 text-xs">
                                <i class="ri-folder-line text-neutral-9"></i>
                                {{ item.category.name }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-emerald-600">৳{{ formatNumber(item.base_price) }}</span>
                            <span v-if="item.service_fee > 0" class="text-neutral-9 text-xs">+ ৳{{ formatNumber(item.service_fee) }} fee</span>
                        </div>
                    </AppDataTableData>

                     <AppDataTableData>
                        <span class="text-neutral-11 capitalize">{{ item.entry_type }} Entry</span>
                         <div class="text-neutral-9 text-xs mt-0.5">
                            {{ item.validity_days || 0 }} Days Validity
                         </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-if="item.is_featured"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-500 ring-1 ring-amber-200"
                            >
                                <i class="ri-star-fill"></i>
                            </span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                                :class="
                                    item.is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                        : 'bg-red-50 text-red-700 ring-red-200'
                                "
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="item.is_active ? 'bg-emerald-500' : 'bg-red-500'"
                                ></span>
                                {{ item.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData class="text-right">
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton class="btn btn-icon btn-secondary" size="sm">
                                    <i class="ri-more-2-fill"></i>
                                </AppButton>
                            </template>

                            <div class="py-1">
                                <AppDropdownItem v-if="can('visa-view')" @click="$inertia.visit(route('visa.show', item.id))">
                                    <i class="ri-eye-line text-neutral-9 mr-2"></i> View Details
                                </AppDropdownItem>
                                
                                <AppDropdownItem v-if="can('visa-edit')" @click="$inertia.visit(route('visa.edit', item.id))">
                                    <i class="ri-edit-line text-neutral-9 mr-2"></i> Edit Visa
                                </AppDropdownItem>
                                
                                <div class="border-neutral-4 my-1 border-t"></div>
                                
                                <AppDropdownItem
                                    v-if="can('visa-delete')"
                                    class="text-danger hover:bg-danger-light"
                                    @click="confirmDelete(route('visa.destroy', item.id))"
                                >
                                    <i class="ri-delete-bin-line mr-2"></i> Delete Visa
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="visas.data.length"
        :links="visas.links"
        :from="visas.from ?? 0"
        :to="visas.to ?? 0"
        :total="visas.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!visas.data.length"
        icon="ri-passport-line"
        title="No visas found"
        description="Get started by creating your first visa package."
    >
        <template #actions>
            <AppButton
                v-if="can('visa-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('visa.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Visa
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
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppCombobox from '@/Components/Form/AppCombobox.vue'

const { can } = useAuthCan()
const { title } = useTitle('Visas')

const props = defineProps({
    visas: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) },
    countries: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visas', last: true }
]

const headers = ['#', 'Visa', 'Country / Category', 'Price', 'Validity', 'Status', 'Actions']

// Filter Logic
const STORAGE_KEY = 'visa_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    country_id: currentParams.get('country_id') ?? null,
    category_id: currentParams.get('category_id') ?? null,
    status: currentParams.get('status') ?? null,
    is_featured: currentParams.get('is_featured') ?? null,
})

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' }
]

const yesNoOptions = [
    { value: 'yes', label: 'Yes' },
    { value: 'no', label: 'No' }
]

const hasActiveFilters = computed(() => {
    return !!(
        filterForm.country_id ||
        filterForm.category_id ||
        filterForm.status ||
        filterForm.is_featured
    )
})

const activeFilterCount = computed(() => {
    let count = 0
    if (filterForm.country_id) count++
    if (filterForm.category_id) count++
    if (filterForm.status) count++
    if (filterForm.is_featured) count++
    return count
})

onMounted(() => {
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

const applyFilter = () => {
    filterForm.get(route('visa.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const resetFilters = () => {
    filterForm.country_id = null
    filterForm.category_id = null
    filterForm.status = null
    filterForm.is_featured = null
    filterForm.get(route('visa.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const formatNumber = (num) => {
    if (!num) return '0'
    return new Intl.NumberFormat('en-BD').format(num)
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
