<template>
    <AppDataTable v-if="report" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in report" :key="item.id">
                    <AppDataTableData>
                        {{ ++index }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.code }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.designation?.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.department?.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.area?.name }}
                    </AppDataTableData>                    

                    <AppDataTableData>
                        {{ item.joining_date }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.phone }}
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
const { title } = useTitle('Leave Report')
import { useForm } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import useFormErrors from '@/Composables/useFormErrors'

import useAuthCan from '@/Composables/useAuthCan'
const { can } = useAuthCan()

const props = defineProps({
    report: {
        type: Object,
        default: () => {}
    }
})

const getStatusClass = (status) => {
    return status == true ? 'active' : 'inactive'
}

const headers = [
    'SL',
    'Code',
    'Name',
    'Designation',
    'Department',
    'Area',
    'Joining',
    'Email',
    'Phone'
]
</script>
