<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('flightBooking.index'))">
                <i class="ri-arrow-left-line mr-1"></i> Back to Bookings
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in bookings.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (bookings.current_page - 1) * bookings.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-12 font-mono font-medium">{{ item.booking_number }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11">{{ item.contact_first_name }} {{ item.contact_last_name }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11">{{ item.departure_date }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-9 text-sm">{{ new Date(item.deleted_at).toLocaleDateString() }}</span>
                    </AppDataTableData>

                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <div class="flex justify-end gap-2">
                            <AppButton
                                v-if="can('flight.booking.edit')"
                                class="btn btn-secondary"
                                size="sm"
                                @click="restore(item.id)"
                            >
                                <i class="ri-refresh-line mr-1"></i> Restore
                            </AppButton>
                            <AppButton
                                v-if="can('flight.booking.delete')"
                                class="btn btn-danger"
                                size="sm"
                                @click="forceDelete(item.id)"
                            >
                                <i class="ri-delete-bin-line mr-1"></i> Delete
                            </AppButton>
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

    <AppEmptyState
        v-if="!bookings.data.length"
        icon="ri-delete-bin-line"
        title="Recycle bin is empty"
        description="No deleted bookings found."
    ></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Booking Recycle Bin')

defineProps({
    bookings: { type: Object, default: () => ({}) }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Bookings', href: route('flightBooking.index') },
    { label: 'Recycle Bin', last: true }
]

const headers = ['#', 'Booking', 'Contact', 'Departure', 'Deleted', 'Actions']

const restore = (id) => {
    if (confirm('Restore this booking?')) {
        router.post(route('flightBooking.restore', id))
    }
}

const confirmDialogRef = ref(null)
const forceDelete = (id) => {
    if (confirm('Permanently delete this booking? This cannot be undone.')) {
        router.delete(route('flightBooking.forceDelete', id))
    }
}
</script>
