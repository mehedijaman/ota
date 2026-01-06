<template>
    <AppCard class="w-full" no-padding>
        <template #title>
            <div 
                class="flex w-full items-center justify-between cursor-pointer select-none -my-1 -mx-2 py-1 px-2 rounded-lg hover:bg-neutral-3/50 transition-colors"
                @click="showFilters = !showFilters"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-2">
                        <i class="ri-filter-line text-primary-9"></i>
                    </div>
                    <span>{{ __('Filter Employees') }}</span>
                    <span 
                        v-if="hasActiveFilters" 
                        class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-9 px-1.5 text-xs font-bold text-white"
                    >
                        {{ activeFilterCount }}
                    </span>
                </div>
                <div class="flex items-center gap-1 text-sm text-neutral-11">
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
                <AppFormErrors class="mb-4" />

                <form class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <AppLabel for="designation">{{ __('Designation') }}</AppLabel>
                        <AppCombobox
                            v-model="form.designation"
                            :options="designations"
                            combo-label="Select Designation"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <AppLabel for="department">{{ __('Department') }}</AppLabel>
                        <AppCombobox
                            v-model="form.department"
                            :options="departments"
                            combo-label="Select Department"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <AppLabel for="area">{{ __('Area') }}</AppLabel>
                        <AppCombobox
                            v-model="form.area"
                            :options="areas"
                            combo-label="Select Area"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <AppLabel for="status">{{ __('Status') }}</AppLabel>
                        <AppCombobox
                            v-model="form.active"
                            :options="statuses"
                            combo-label="Select Status"
                            class="w-full"
                        />
                    </div>
                </form>

                <div class="mt-4 flex justify-end gap-2">
                    <AppButton
                        class="btn btn-secondary"
                        @click="$inertia.visit(route('employee.index'))"
                    >
                        <i class="ri-refresh-line mr-1"></i>
                        {{ __('Reset') }}
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="viewList">
                        <i class="ri-filter-line mr-1"></i>
                        {{ __('Apply') }}
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="printList">
                        <i class="ri-printer-line mr-1"></i>
                        {{ __('Print') }}
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="exportList">
                        <i class="ri-download-line mr-1"></i>
                        {{ __('Export') }}
                    </AppButton>
                </div>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppCombobox from '@/Components/Form/AppCombobox.vue'

const props = defineProps({
    departments: {
        type: Object,
        default: null
    },
    designations: {
        type: Object,
        default: null
    },
    areas: {
        type: Object,
        default: null
    },
    statuses: {
        type: Object,
        default: null
    }
})

const STORAGE_KEY = 'employee_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

// Persist state to localStorage
watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

function getQueryParams() {
    const urlParams = new URLSearchParams(window.location.search)
    return {
        department: urlParams.get('department'),
        designation: urlParams.get('designation'),
        area: urlParams.get('area'),
        active: urlParams.get('active')
    }
}

const queryParams = getQueryParams()

const form = useForm({
    department: null,
    designation: null,
    area: null,
    active: null
})

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return form.department || form.designation || form.area || form.active
})

const activeFilterCount = computed(() => {
    let count = 0
    if (form.department) count++
    if (form.designation) count++
    if (form.area) count++
    if (form.active) count++
    return count
})

onMounted(() => {
    const fields = [
        { key: 'department', source: props.departments },
        { key: 'designation', source: props.designations },
        { key: 'area', source: props.areas },
        { key: 'active', source: props.statuses }
    ]

    fields.forEach(({ key, source }) => {
        let queryValue = queryParams[key]

        if (key === 'active') {
            queryValue =
                queryValue === 'true'
                    ? true
                    : queryValue === 'false'
                      ? false
                      : null
        }

        if (queryValue && source?.length) {
            const matchingItem = source.find((item) => item.value == queryValue)

            if (matchingItem) {
                form[key] = matchingItem
            } else {
                console.warn(
                    `No match found for key: "${key}" with value: "${queryValue}" in source`,
                    source
                )
            }
        }
    })

    // Show filters if any are active (override localStorage)
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const viewList = () => {
    const postData = (data) => {
        const commonData = {
            ...data,
            department: getValueFromKey(data, 'department'),
            designation: getValueFromKey(data, 'designation'),
            area: getValueFromKey(data, 'area'),
            active: getValueFromKey(data, 'active')
        }

        return commonData
    }

    form.transform(postData).get(route('employee.index'))
}

const printList = () => {
    const postData = (data) => {
        const commonData = {
            ...data,
            department: getValueFromKey(data, 'department'),
            designation: getValueFromKey(data, 'designation'),
            area: getValueFromKey(data, 'area'),
            active: getValueFromKey(data, 'active'),
            reportType: 'employee-list'
        }

        return commonData
    }

    const transformedData = postData(form.data())
    const url = route('employee.report.index', transformedData)

    window.open(url, '_blank')
}

const exportList = () => {
    const getQueryParam = (data, key) => {
        const value = getValueFromKey(data, key)
        return value !== null && value !== undefined
            ? `${key}=${encodeURIComponent(value)}`
            : ''
    }

    const buildQueryString = (data) => {
        const keys = ['department', 'designation', 'area', 'active']
        return keys
            .map((key) => getQueryParam(data, key))
            .filter((param) => param)
            .join('&')
    }

    const queryString = buildQueryString(form.data())
    const baseUrl = route('employee.export')
    const url = queryString ? `${baseUrl}?${queryString}` : baseUrl

    window.location.href = url
}
</script>
