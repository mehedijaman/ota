<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('tour-booking-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tourBooking.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="bookings.data.length && can('tour-booking-delete')"
                    class="btn btn-primary"
                    @click="confirmRestore(route('tourBooking.restoreRecycleBin'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="bookings.data.length && can('tour-booking-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('tourBooking.emptyRecycleBin'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Stats Card -->
    <div v-if="bookings.data.length" class="border-neutral-6 bg-neutral-1 mb-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex items-center gap-4 p-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                <i class="ri-delete-bin-line text-xl text-white"></i>
            </div>
            <div>
                <p class="text-neutral-9 text-sm font-medium">Deleted Bookings</p>
                <p class="text-neutral-12 text-2xl font-bold">{{ bookings.total }}</p>
            </div>
            <div class="text-neutral-9 ml-auto hidden text-sm sm:block">
                <i class="ri-information-line mr-1"></i>
                Items will be permanently deleted after 30 days
            </div>
        </div>
    </div>

    <AppDataSearch
        v-if="bookings.data.length || route().params.searchTerm"
        :url="route('tourBooking.recycleBin')"
        fields-to-search="booking_number,contact_name,contact_email"
    ></AppDataSearch>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in bookings.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <!-- Serial Number -->
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (bookings.current_page - 1) * bookings.per_page + (index + 1) }}
                    </AppDataTableData>

                    <!-- Booking Number -->
                    <AppDataTableData>
                        <span class="font-mono font-semibold text-primary-600 opacity-60">
                            {{ item.booking_number }}
                        </span>
                    </AppDataTableData>

                    <!-- Tour & Date -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-12 font-medium line-through opacity-60">
                                {{ item.tour || '-' }}
                            </span>
                            <span class="text-neutral-9 flex items-center gap-1.5 text-xs">
                                <i class="ri-calendar-line"></i>
                                {{ item.travel_date || '-' }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Contact -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-11 text-sm">{{ item.contact_name }}</span>
                            <span class="text-neutral-9 text-xs">{{ item.contact_email }}</span>
                        </div>
                    </AppDataTableData>

                    <!-- Amount -->
                    <AppDataTableData>
                        <span class="text-neutral-11 font-semibold">
                            ৳{{ formatNumber(item.total_amount) }}
                        </span>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-1">
                            <span :class="getStatusClass(item.status)" class="px-2 py-0.5 rounded-full text-xs font-medium w-fit opacity-60">
                                {{ formatStatus(item.status) }}
                            </span>
                            <span :class="getPaymentStatusClass(item.payment_status)" class="px-2 py-0.5 rounded-full text-xs font-medium w-fit opacity-60">
                                {{ formatStatus(item.payment_status) }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Deleted At -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-11 flex items-center gap-1.5 text-sm">
                                <i class="ri-calendar-close-line text-red-400"></i>
                                {{ formatDate(item.deleted_at) }}
                            </span>
                            <span class="text-neutral-9 text-xs">
                                {{ formatTimeAgo(item.deleted_at) }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <AppTooltip v-if="can('tour-booking-delete')" text="Restore">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="confirmRestore(route('tourBooking.restore', item.id))"
                                >
                                    <i class="ri-recycle-fill"></i>
                                </AppButton>
                            </AppTooltip>

                            <AppTooltip v-if="can('tour-booking-delete')" text="Delete Permanently">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('tourBooking.destroyForce', item.id))"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="bookings.data.length"
        :links="bookings.links"
        :from="bookings.from ?? 0"
        :to="bookings.to ?? 0"
        :total="bookings.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <!-- Empty State -->
    <div v-if="!bookings.data.length" class="border-neutral-6 bg-neutral-1 mt-4 overflow-hidden rounded-xl border shadow-sm">
        <div class="flex flex-col items-center justify-center py-16">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-teal-100">
                <i class="ri-checkbox-circle-line text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-neutral-12 mb-2 text-lg font-semibold">Recycle Bin is Empty</h3>
            <p class="text-neutral-9 mb-6 max-w-sm text-center text-sm">
                Great job! There are no deleted bookings. All your bookings are safe and sound.
            </p>
            <AppButton class="btn btn-primary" @click="$inertia.visit(route('tourBooking.index'))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Bookings
            </AppButton>
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Booking Recycle Bin')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Tour Bookings', href: route('tourBooking.index') },
    { label: title, last: true }
]

defineProps({
    bookings: {
        type: Object,
        default: () => ({})
    }
})

const headers = ['SL', 'Booking #', 'Tour / Date', 'Contact', 'Amount', 'Status', 'Deleted At', 'Actions']

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

const formatDate = (dateString) => {
    if (!dateString) {
        return '-'
    }
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTimeAgo = (dateString) => {
    if (!dateString) {
        return ''
    }
    const date = new Date(dateString)
    const now = new Date()
    const diffInSeconds = Math.floor((now - date) / 1000)

    if (diffInSeconds < 60) {
        return 'just now'
    }
    if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60)
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
    }
    if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600)
        return `${hours} hour${hours > 1 ? 's' : ''} ago`
    }
    const days = Math.floor(diffInSeconds / 86400)
    return `${days} day${days > 1 ? 's' : ''} ago`
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.confirmRestore(restoreRoute)
}
</script>
