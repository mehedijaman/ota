<template>
    <AppCard class="w-full">
        <template #title>
            <i class="ri-user-3-line"></i> Employee Filter
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="flex flex-col gap-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <AppLabel for="designation">{{
                            __('Designation')
                        }}</AppLabel>
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
                            combo-label="Select status"
                            class="w-full"
                        />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex justify-center gap-4">
                <AppButton class="btn btn-primary" @click="viewList">
                    <i class="ri-file-list-line mr-1"></i>
                    {{ __('View List') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="printList">
                    <i class="ri-printer-line mr-1"></i>
                    {{ __('Print List') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="exportList">
                    <i class="ri-download-line mr-1"></i>
                    {{ __('Export') }}
                </AppButton>

                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('employee.index'))"
                >
                    <i class="ri-filter-off-line mr-1"></i>
                    {{ __('Clear Filter') }}
                </AppButton>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import { onMounted } from 'vue'

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

onMounted(() => {
    const fields = [
        { key: 'department', source: props.departments },
        { key: 'designation', source: props.designations },
        { key: 'area', source: props.areas },
        { key: 'active', source: props.statuses } // Assuming active maps to statuses
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

    // Open the route in a new tab
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
            .filter((param) => param) // Remove empty parameters
            .join('&')
    }

    const queryString = buildQueryString(form.data())
    const baseUrl = route('employee.export')
    const url = queryString ? `${baseUrl}?${queryString}` : baseUrl

    // Redirect the user to the constructed URL
    window.location.href = url
}
</script>
