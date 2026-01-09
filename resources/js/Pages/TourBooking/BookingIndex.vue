<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('tourBooking.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                New Booking
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="bookings.data.length || route().params.searchTerm"
        :url="route('tourBooking.index')"
        fields-to-search="booking_number,contact_name,contact_email"
    ></AppDataSearch>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in bookings.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        <span class="font-mono font-semibold text-primary-600">{{ item.booking_number }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="font-medium">{{ item.tour }}</span>
                            <span class="text-xs text-slate-500">{{ item.departure_date }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span>{{ item.contact_name }}</span>
                            <span class="text-xs text-slate-500">{{ item.contact_email }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.total_travelers }} pax
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="font-semibold">৳{{ formatNumber(item.total_amount) }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span :class="getStatusClass(item.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                            {{ formatStatus(item.status) }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span :class="getPaymentStatusClass(item.payment_status)" class="px-2 py-1 rounded-full text-xs font-medium">
                            {{ formatStatus(item.payment_status) }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip text="View" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="$inertia.visit(route('tourBooking.show', item.id))"
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Edit" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="$inertia.visit(route('tourBooking.edit', item.id))"
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('tourBooking.destroy', item.id))"
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
        v-if="bookings.data.length"
        :links="bookings.links"
        :from="bookings.from || 0"
        :to="bookings.to || 0"
        :total="bookings.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!bookings.data.length" class="mt-4">
        No bookings found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Tour Bookings')

defineProps({
    bookings: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', last: true }
]

const headers = ['Booking #', 'Tour', 'Contact', 'Travelers', 'Amount', 'Status', 'Payment', 'Actions']

const formatNumber = (num) => new Intl.NumberFormat('en-BD').format(num)

const formatStatus = (status) => {
    return status?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) || '-'
}

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 text-amber-700',
        confirmed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
        completed: 'bg-blue-100 text-blue-700',
    }
    return classes[status] || 'bg-slate-100 text-slate-700'
}

const getPaymentStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 text-amber-700',
        partial: 'bg-blue-100 text-blue-700',
        paid: 'bg-green-100 text-green-700',
        refunded: 'bg-red-100 text-red-700',
    }
    return classes[status] || 'bg-slate-100 text-slate-700'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
