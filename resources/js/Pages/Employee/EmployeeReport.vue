<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <!-- <div class="flex gap-2">
                <AppButton
                    v-if="can('Employee - Create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('employee.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Employee
                </AppButton>

                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('employee.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div> -->
        </template>
    </AppSectionHeader>

    <AppCard class="w-full">
        <template #title>
            <i class="ri-table-line"></i> View Employee Report
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="flex flex-col gap-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <AppLabel for="company">{{ __('Company') }}</AppLabel>
                        <AppCombobox
                            v-model="form.company"
                            :options="companies"
                            combo-label="Select Company"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="department"
                            >{{ __('Department') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.department"
                            :options="departments"
                            combo-label="Select Department"
                            class="w-full"
                        />
                    </div>

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
                        <AppLabel for="area">{{ __('Area') }} </AppLabel>
                        <AppCombobox
                            v-model="form.area"
                            :options="areas"
                            combo-label="Select Area"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="shift">{{ __('Shift') }} </AppLabel>
                        <AppCombobox
                            v-model="form.shift"
                            :options="shifts"
                            combo-label="Select Shift"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="section">{{ __('Section') }}</AppLabel>
                        <AppCombobox
                            v-model="form.section"
                            :options="sections"
                            combo-label="Select Section"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="type">{{ __('Type') }}</AppLabel>
                        <AppCombobox
                            v-model="form.type"
                            :options="types"
                            combo-label="Select type"
                            class="w-full"
                        />
                    </div>

                    <!-- <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="active"
                        v-model="form.active"
                        name="active"
                        :value="true"
                    />
                    <AppLabel for="active" class="ml-3"> Active </AppLabel>
                </div> -->
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex justify-center gap-4">
                <AppButton
                    class="btn btn-primary"
                    @click="submitForm('details')"
                >
                    <i class="ri-filter-line mr-1"></i>
                    {{ __('View Details Report') }}
                </AppButton>

                <AppButton
                    class="btn btn-primary"
                    @click="exportReport('xlsx')"
                >
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('XLSX') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="exportReport('xls')">
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('XLS') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="exportReport('csv')">
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('CSV') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="exportReport('tsv')">
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('TSV') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="exportReport('ods')">
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('ODS') }}
                </AppButton>

                <AppButton
                    class="btn btn-primary"
                    @click="exportReport('html')"
                >
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('HTML') }}
                </AppButton>

                <AppButton class="btn btn-primary" @click="exportReport('pdf')">
                    <i class="ri-file-excel-2-line mr-1"></i>
                    {{ __('PDF') }}
                </AppButton>

                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('employee.report'))"
                >
                    <i class="ri-filter-off-line mr-1"></i>
                    {{ __('Clear Filter') }}
                </AppButton>
            </div>
        </template>
    </AppCard>

    <EmployeeReportDetails v-if="reportType === 'details'" :report="report" />
</template>

<script setup>
import useAuthCan from '@/Composables/useAuthCan'
const { can } = useAuthCan()
import { useForm } from '@inertiajs/vue3'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import { onMounted } from 'vue'
import EmployeeReportDetails from '@/Pages/Employee/Partials/EmployeeReportDetails.vue'

const { title } = useTitle('Employee Report')

import { Head } from '@inertiajs/vue3'

const props = defineProps({
    companies: {
        type: Object,
        default: null
    },
    departments: {
        type: Object,
        default: null
    },
    designations: {
        type: Object,
        default: null
    },
    types: {
        type: Object,
        default: null
    },
    areas: {
        type: Object,
        default: null
    },
    shifts: {
        type: Object,
        default: null
    },
    sections: {
        type: Object,
        default: null
    },
    reportType: {
        type: String,
        default: null
    },
    report: {
        type: Object,
        default: null
    }
})

const getEmployeeStatusClass = (status) => {
    return status == true ? 'active' : 'inactive'
}

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Employees', href: route('employee.index') },
    { label: 'Report', last: true }
]

const headers = [
    'Code',
    'Name',
    'Designation',
    'Department',
    'Area',
    'Section',
    'Shift',
    'Company',
    'Joining',
    'Email',
    'Phone',
    'RFID No',
    'Status',
    'Actions'
]

const form = useForm({
    company: '',
    department: '',
    designation: '',
    area: '',
    shift: '',
    section: '',
    type: ''
})

onMounted(() => {
    if (form.company) {
        form.company = props.companies.find(
            (company) => company.value === form.company
        )
    }

    if (form.department) {
        form.department = props.departments.find(
            (department) => department.value === form.department
        )
    }

    if (form.designation) {
        form.designation = props.designations.find(
            (designation) => designation.value === form.designation
        )
    }

    if (form.area) {
        form.area = props.areas.find((area) => area.value === form.area)
    }

    if (form.shift) {
        form.shift = props.shifts.find((shift) => shift.value === form.shift)
    }

    if (form.section) {
        form.section = props.sections.find(
            (section) => section.value === form.section
        )
    }

    if (form.type != null) {
        form.type = props.types.find((type) => type.value === form.type)
    }

    // Extract company parameter from URL
    const urlParams = new URLSearchParams(window.location.search)

    // Set form values based on URL parameters
    form.company = urlParams.get('company') || ''
    form.department = urlParams.get('department') || ''
    form.designation = urlParams.get('designation') || ''
    form.area = urlParams.get('area') || ''
    form.shift = urlParams.get('shift') || ''
    form.section = urlParams.get('section') || ''
    form.type = urlParams.get('type') || ''
})

const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = (reportType) => {
    const postData = (data) => {
        const commonData = {
            ...data,
            company: getValueFromKey(data, 'company'),
            department: getValueFromKey(data, 'department'),
            designation: getValueFromKey(data, 'designation'),
            area: getValueFromKey(data, 'area'),
            shift: getValueFromKey(data, 'shift'),
            section: getValueFromKey(data, 'section'),
            type: getValueFromKey(data, 'type'),
            report_type: reportType
        }

        return commonData
    }

    form.transform(postData).get(route('employee.report'))
}

/**
 * Exports the employee report data to an Excel file.
 *
 * @param {string} reportType - The type of report to export.
 * @returns {void}
 */

const exportReport = (exportType) => {
    const postData = (data) => {
        const commonData = {
            ...data,
            company: getValueFromKey(data, 'company'),
            department: getValueFromKey(data, 'department'),
            designation: getValueFromKey(data, 'designation'),
            area: getValueFromKey(data, 'area'),
            shift: getValueFromKey(data, 'shift'),
            section: getValueFromKey(data, 'section'),
            type: getValueFromKey(data, 'type'),
            exportType: exportType
        }

        return commonData
    }

    const queryString = new URLSearchParams(postData(form.data())).toString()

    // Redirect to the export route with the query string
    window.location.href = `${route('employee.export')}?${queryString}`
}

const { errorsFields } = useFormErrors()
</script>
