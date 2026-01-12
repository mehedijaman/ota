<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('tour-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('tour.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Tour
                </AppButton>

                <AppButton
                    v-if="can('tour-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tour.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Overview -->
    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <!-- Total Tours -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-blue-500': !filterForm.status && !filterForm.is_featured && !filterForm.is_bestseller }"
            @click="filterByStats('all')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-map-pin-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total Tours</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Active Tours -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-emerald-500': filterForm.status === 'active' }"
            @click="filterByStats('active')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="ri-checkbox-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Active</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.active || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Inactive Tours -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-red-500': filterForm.status === 'inactive' }"
            @click="filterByStats('inactive')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                    <i class="ri-close-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Inactive</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.inactive || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Featured Tours -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-amber-500': filterForm.is_featured === 'yes' }"
            @click="filterByStats('featured')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                    <i class="ri-star-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Featured</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.featured || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Bestseller Tours -->
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-purple-500': filterForm.is_bestseller === 'yes' }"
            @click="filterByStats('bestseller')"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
                    <i class="ri-fire-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Bestseller</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.bestseller || 0 }}</p>
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
                    <div
                        class="bg-primary-2 flex h-8 w-8 items-center justify-center rounded-lg"
                    >
                        <i class="ri-filter-line text-primary-9"></i>
                    </div>
                    <span>Filter Tours</span>
                    <span
                        v-if="hasActiveFilters"
                        class="bg-primary-9 inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-xs font-bold text-white"
                    >
                        {{ activeFilterCount }}
                    </span>
                </div>
                <div class="text-neutral-11 flex items-center gap-1 text-sm">
                    <span class="hidden sm:inline">{{
                        showFilters ? 'Hide' : 'Show'
                    }}</span>
                    <i
                        class="text-xl transition-transform duration-200"
                        :class="
                            showFilters
                                ? 'ri-arrow-up-s-line'
                                : 'ri-arrow-down-s-line'
                        "
                    ></i>
                </div>
            </div>
        </template>
        <template #content>
            <div v-show="showFilters" class="p-4 sm:p-5">
                <form
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
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

                    <!-- Destination Filter -->
                    <div>
                        <AppLabel for="destination_id">Destination</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.destination_id"
                            :options="destinations"
                            combo-label="All Destinations"
                            emit-value
                        />
                    </div>

                    <!-- Duration Filter -->
                    <div>
                        <AppLabel for="duration">Duration</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.duration"
                            :options="durationOptions"
                            combo-label="All Durations"
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

                    <!-- Bestseller Filter -->
                    <div>
                        <AppLabel for="is_bestseller">Bestseller</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="filterForm.is_bestseller"
                            :options="yesNoOptions"
                            combo-label="All"
                            emit-value
                        />
                    </div>

                    <!-- Valid From Filter -->
                    <div>
                        <AppLabel for="valid_from">Valid From</AppLabel>
                        <AppInputText
                            type="date"
                            v-model="filterForm.valid_from"
                            class="w-full"
                        />
                    </div>

                    <!-- Valid To Filter -->
                    <div>
                        <AppLabel for="valid_to">Valid To</AppLabel>
                        <AppInputText
                            type="date"
                            v-model="filterForm.valid_to"
                            class="w-full"
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
        v-if="tours.data.length || route().params.searchTerm"
        :url="route('tour.index')"
        fields-to-search="name,code"
    ></AppDataSearch>

    <AppDataTable v-if="tours.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in tours.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <!-- Serial Number -->
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{
                            (tours.current_page - 1) * tours.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <!-- Tour Info with Image -->
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
                                    <i class="ri-map-pin-line text-xl"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div
                                    class="text-neutral-12 truncate font-medium"
                                >
                                    {{ item.name }}
                                </div>
                                <div class="text-neutral-9 text-xs">
                                    {{ item.code }}
                                </div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Category & Destination -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span
                                v-if="item.category"
                                class="text-neutral-11 flex items-center gap-1.5 text-sm"
                            >
                                <i class="ri-folder-line text-neutral-9"></i>
                                {{ item.category }}
                            </span>
                            <span
                                v-if="item.destination"
                                class="text-neutral-10 flex items-center gap-1.5 text-xs"
                            >
                                <i class="ri-map-pin-2-line text-neutral-9"></i>
                                {{ item.destination }}
                            </span>
                            <span
                                v-if="!item.category && !item.destination"
                                class="text-neutral-9 text-sm"
                                >-</span
                            >
                        </div>
                    </AppDataTableData>

                    <!-- Duration -->
                    <AppDataTableData>
                        <span
                            class="text-neutral-11 flex items-center gap-1.5 text-sm"
                        >
                            <i class="ri-time-line text-neutral-9"></i>
                            {{ item.duration || '-' }}
                        </span>
                    </AppDataTableData>

                    <!-- Price -->
                    <AppDataTableData>
                        <div class="flex flex-col">
                            <template v-if="item.min_price">
                                <span
                                    class="text-lg font-bold text-emerald-600"
                                >
                                    ৳{{ formatNumber(item.min_price) }}
                                    <template v-if="item.max_price">
                                        <span
                                            class="text-neutral-9 text-sm font-normal"
                                            >-</span
                                        >
                                        ৳{{ formatNumber(item.max_price) }}
                                    </template>
                                </span>
                                <span class="text-neutral-9 text-xs"
                                    >per person</span
                                >
                            </template>
                            <span v-else class="text-neutral-9 text-sm"
                                >No pricing</span
                            >
                        </div>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-if="item.is_featured"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200"
                            >
                                <i class="ri-star-fill text-amber-500"></i>
                                Featured
                            </span>
                            <span
                                v-if="item.is_bestseller"
                                class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-purple-200"
                            >
                                <i class="ri-fire-fill text-purple-500"></i>
                                Bestseller
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
                                    :class="
                                        item.is_active
                                            ? 'bg-emerald-500'
                                            : 'bg-red-500'
                                    "
                                ></span>
                                {{ item.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData
                        class="overflow-visible text-right"
                        @click.stop
                    >
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton
                                    class="btn btn-icon btn-secondary"
                                    size="sm"
                                    aria-label="More actions"
                                >
                                    <i class="ri-more-2-fill"></i>
                                </AppButton>
                            </template>

                            <div class="py-1">
                                <AppDropdownItem
                                    v-if="can('tour-view')"
                                    @click="
                                        $inertia.visit(
                                            route('tour.show', item.id)
                                        )
                                    "
                                >
                                    <i
                                        class="ri-eye-line text-neutral-9 mr-2"
                                    ></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="can('tour-edit')"
                                    @click="
                                        $inertia.visit(
                                            route('tour.edit', item.id)
                                        )
                                    "
                                >
                                    <i
                                        class="ri-edit-line text-neutral-9 mr-2"
                                    ></i>
                                    Edit Tour
                                </AppDropdownItem>

                                <AppDropdownItem
                                    @click="copyTourLink(item.slug)"
                                >
                                    <i class="ri-link text-neutral-9 mr-2"></i>
                                    Copy Link
                                </AppDropdownItem>

                                <div
                                    class="border-neutral-4 my-1 border-t"
                                ></div>

                                <AppDropdownItem
                                    v-if="can('tour-delete')"
                                    class="text-danger hover:bg-danger-light"
                                    @click="
                                        confirmDelete(
                                            route('tour.destroy', item.id)
                                        )
                                    "
                                >
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete Tour
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
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

    <AppEmptyState
        v-if="!tours.data.length"
        icon="ri-map-pin-line"
        title="No tours found"
        description="Get started by creating your first tour package."
    >
        <template #actions>
            <AppButton
                v-if="can('tour-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('tour.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Tour
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
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'

const { can } = useAuthCan()
const { title } = useTitle('Tours')

const props = defineProps({
    tours: {
        type: Object,
        default: () => ({})
    },
    stats: {
        type: Object,
        default: () => ({})
    },
    categories: {
        type: Array,
        default: () => []
    },
    destinations: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tours', last: true }
]

const headers = [
    '#',
    'Tour',
    'Category / Destination',
    'Duration',
    'Price',
    'Status',
    'Actions'
]

// --- Collapsible Filter ---
const STORAGE_KEY = 'tour_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

// --- Filter Form ---
const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    category_id: currentParams.get('category_id') ?? null,
    destination_id: currentParams.get('destination_id') ?? null,
    status: currentParams.get('status') ?? null,
    is_featured: currentParams.get('is_featured') ?? null,
    is_bestseller: currentParams.get('is_bestseller') ?? null,
    duration: currentParams.get('duration') ?? null,
    valid_from: currentParams.get('valid_from') ?? null,
    valid_to: currentParams.get('valid_to') ?? null
})

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' }
]

const yesNoOptions = [
    { value: 'yes', label: 'Yes' },
    { value: 'no', label: 'No' }
]

const durationOptions = [
    { value: '1', label: '1 Day' },
    { value: '2-3', label: '2-3 Days' },
    { value: '4-7', label: '4-7 Days' },
    { value: '8+', label: '8+ Days' }
]

const hasActiveFilters = computed(() => {
    return !!(
        filterForm.category_id ||
        filterForm.destination_id ||
        filterForm.status ||
        filterForm.is_featured ||
        filterForm.is_bestseller ||
        filterForm.duration ||
        filterForm.valid_from ||
        filterForm.valid_to
    )
})

const activeFilterCount = computed(() => {
    let count = 0
    if (filterForm.category_id) count++
    if (filterForm.destination_id) count++
    if (filterForm.status) count++
    if (filterForm.is_featured) count++
    if (filterForm.is_bestseller) count++
    if (filterForm.duration) count++
    if (filterForm.valid_from) count++
    if (filterForm.valid_to) count++
    return count
})

onMounted(() => {
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

const applyFilter = () => {
    filterForm.get(route('tour.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const resetFilters = () => {
    filterForm.category_id = null
    filterForm.destination_id = null
    filterForm.status = null
    filterForm.is_featured = null
    filterForm.is_bestseller = null
    filterForm.duration = null
    filterForm.valid_from = null
    filterForm.valid_to = null
    filterForm.get(route('tour.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

const filterByStats = (type) => {
    filterForm.category_id = null
    filterForm.destination_id = null
    filterForm.status = null
    filterForm.is_featured = null
    filterForm.is_bestseller = null
    filterForm.duration = null
    filterForm.valid_from = null
    filterForm.valid_to = null

    if (type === 'active') {
        filterForm.status = 'active'
    } else if (type === 'inactive') {
        filterForm.status = 'inactive'
    } else if (type === 'featured') {
        filterForm.is_featured = 'yes'
    } else if (type === 'bestseller') {
        filterForm.is_bestseller = 'yes'
    }

    applyFilter()
}

const formatNumber = (num) => {
    if (!num) return '0'
    return new Intl.NumberFormat('en-BD').format(num)
}

const copyTourLink = async (slug) => {
    try {
        const url = route('site.tour.show', slug)
        await navigator.clipboard.writeText(window.location.origin + url)
    } catch (err) {
        console.error('Failed to copy:', err)
    }
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
