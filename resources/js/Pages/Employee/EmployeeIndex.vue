<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('employee-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('employee.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Employee
                </AppButton>

                <AppButton
                    v-if="can('employee-create')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('employee.import'))"
                >
                    <i class="ri-upload-line mr-1"></i>
                    Bulk Import
                </AppButton>

                <AppButton
                    v-if="can('employee-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('employee.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <EmployeeFilterCard 
    :designations="designations"
    :statuses="statuses"
    :departments="departments"
    :areas="areas"
    >
    </EmployeeFilterCard>

    <EmployeeList :employees="employees"></EmployeeList>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import EmployeeFilterCard from './Partials/EmployeeFilterCard.vue'
import EmployeeList from './Partials/EmployeeList.vue'

const { can } = useAuthCan()
const { title } = useTitle('Employees')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: title, last: true }
]

const props = defineProps({
    employees: {
        type: Object,
        default: () => ({})
    },
    designations: {
        type: Object,
        default: null
    },
    departments: {
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
</script>
