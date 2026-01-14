<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton v-if="can('hotel.review.view')" class="btn btn-secondary" @click="$inertia.visit(route('hotelReview.recycleBin'))">
                <i class="ri-recycle-line mr-1"></i>
                Recycle Bin
            </AppButton>
        </template>
    </AppSectionHeader>

    <!-- Stats -->
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-blue-500': !filterForm.status }"
            @click="filterByStatus(null)"
        >
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600">
                    <i class="ri-chat-3-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs">Total</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>

        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-yellow-500': filterForm.status === 'pending' }"
            @click="filterByStatus('pending')"
        >
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600">
                    <i class="ri-time-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs">Pending</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.pending || 0 }}</p>
                </div>
            </div>
        </div>

        <div 
            class="border-neutral-6 bg-neutral-1 cursor-pointer overflow-hidden rounded-xl border shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            :class="{ 'ring-2 ring-emerald-500': filterForm.status === 'approved' }"
            @click="filterByStatus('approved')"
        >
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600">
                    <i class="ri-checkbox-circle-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs">Approved</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.approved || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
            <div class="flex items-center gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600">
                    <i class="ri-star-line text-lg text-white"></i>
                </div>
                <div>
                    <p class="text-neutral-9 text-xs">Avg Rating</p>
                    <p class="text-neutral-12 text-xl font-bold">{{ stats.avg_rating || '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <AppDataSearch
        v-if="reviews.data.length || route().params.searchTerm"
        :url="route('hotelReview.index')"
        fields-to-search="guest_name,comment"
    ></AppDataSearch>

    <AppDataTable v-if="reviews.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="item in reviews.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData>
                        <div class="min-w-0">
                            <div class="text-neutral-12 font-medium">{{ item.hotel?.name }}</div>
                            <div class="text-neutral-9 text-xs">{{ item.guest_name || item.customer?.name }}</div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-1">
                            <i v-for="n in 5" :key="n" class="ri-star-fill" :class="n <= item.overall_rating ? 'text-amber-500' : 'text-neutral-300'"></i>
                            <span class="text-neutral-11 ml-1 text-sm">{{ item.overall_rating }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <p class="text-neutral-11 line-clamp-2 text-sm">{{ item.comment?.substring(0, 100) }}...</p>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span 
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                            :class="statusClass(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-9 text-sm">{{ formatDate(item.created_at) }}</span>
                    </AppDataTableData>

                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton class="btn btn-icon btn-secondary" size="sm"><i class="ri-more-2-fill"></i></AppButton>
                            </template>
                            <div class="py-1">
                                <AppDropdownItem @click="$inertia.visit(route('hotelReview.show', item.id))">
                                    <i class="ri-eye-line text-neutral-9 mr-2"></i> View
                                </AppDropdownItem>
                                <div class="border-neutral-4 my-1 border-t"></div>
                                <AppDropdownItem v-if="item.status === 'pending'" @click="approveReview(item.id)">
                                    <i class="ri-checkbox-circle-line text-emerald-600 mr-2"></i> Approve
                                </AppDropdownItem>
                                <AppDropdownItem v-if="item.status === 'pending'" @click="rejectReview(item.id)">
                                    <i class="ri-close-circle-line text-red-600 mr-2"></i> Reject
                                </AppDropdownItem>
                                <AppDropdownItem @click="toggleFeatured(item.id)">
                                    <i class="ri-star-line text-amber-600 mr-2"></i> {{ item.is_featured ? 'Unfeature' : 'Feature' }}
                                </AppDropdownItem>
                                <div class="border-neutral-4 my-1 border-t"></div>
                                <AppDropdownItem class="text-danger" @click="confirmDelete(route('hotelReview.destroy', item.id))">
                                    <i class="ri-delete-bin-line mr-2"></i> Delete
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator v-if="reviews.data.length" :links="reviews.links" :from="reviews.from ?? 0" :to="reviews.to ?? 0" :total="reviews.total ?? 0" class="mt-4 justify-center"></AppPaginator>

    <AppEmptyState v-if="!reviews.data.length" icon="ri-chat-3-line" title="No reviews found" description="Reviews from guests will appear here."></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('Hotel Reviews')

const props = defineProps({
    reviews: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotel Reviews', last: true }
]

const headers = ['Hotel / Guest', 'Rating', 'Comment', 'Status', 'Date', 'Actions']

const filterForm = useForm({ status: null })

const filterByStatus = (status) => {
    filterForm.status = status
    filterForm.get(route('hotelReview.index'), { preserveScroll: true, replace: true })
}

const formatDate = (date) => new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        approved: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        rejected: 'bg-red-50 text-red-700 ring-red-200'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-200'
}

const approveReview = (id) => router.patch(route('hotelReview.approve', id), {}, { preserveScroll: true })
const rejectReview = (id) => router.patch(route('hotelReview.reject', id), {}, { preserveScroll: true })
const toggleFeatured = (id) => router.patch(route('hotelReview.toggleFeatured', id), {}, { preserveScroll: true })

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
