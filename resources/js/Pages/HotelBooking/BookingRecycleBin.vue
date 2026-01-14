<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotelBooking.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back to Bookings
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="bookings.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="item in bookings.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData>
                        <span class="font-medium text-neutral-12">{{ item.booking_number }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="min-w-0">
                            <div class="text-neutral-12 font-medium">{{ item.hotel?.name }}</div>
                            <div class="text-neutral-9 text-xs">{{ item.contact_name }}</div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-9 text-sm">{{ formatDate(item.deleted_at) }}</span>
                    </AppDataTableData>

                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <div class="flex justify-end gap-2">
                            <AppButton class="btn btn-icon btn-secondary" @click="restoreBooking(item.id)">
                                <i class="ri-refresh-line"></i>
                            </AppButton>
                            <AppButton class="btn btn-icon btn-destructive" @click="confirmDelete(route('hotelBooking.destroyForce', item.id))">
                                <i class="ri-delete-bin-line"></i>
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
        icon="ri-recycle-line"
        title="Recycle bin is empty"
        description="Deleted bookings will appear here."
    >
        <template #actions>
            <AppButton class="btn btn-primary" @click="$inertia.visit(route('hotelBooking.index'))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Bookings
            </AppButton>
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('Booking Recycle Bin')

const props = defineProps({
    bookings: { type: Object, default: () => ({}) }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotel Bookings', href: route('hotelBooking.index') },
    { label: 'Recycle Bin', last: true }
]

const headers = ['Booking #', 'Hotel / Guest', 'Deleted At', 'Actions']

const formatDate = (date) => new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })

const restoreBooking = (id) => {
    router.post(route('hotelBooking.restore', id), {}, { preserveScroll: true })
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
