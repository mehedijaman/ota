<template>
    <Head :title="title"></Head>
    
    <AppSectionHeader :title="'Review #' + review.id" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap gap-2">
                <AppButton
                    v-if="!review.is_approved && can('tour-review-edit')"
                    class="btn btn-success"
                    @click="approve"
                >
                    <i class="ri-check-line mr-1"></i> Approve
                </AppButton>
                <AppButton
                    v-if="review.is_approved && can('tour-review-edit')"
                    class="btn btn-warning"
                    @click="reject"
                >
                    <i class="ri-close-line mr-1"></i> Reject
                </AppButton>
                <AppButton
                    v-if="can('tour-review-edit')"
                    class="btn btn-secondary"
                    @click="toggleFeatured"
                >
                    <i :class="review.is_featured ? 'ri-star-fill' : 'ri-star-line'" class="mr-1"></i>
                    {{ review.is_featured ? 'Unfeature' : 'Feature' }}
                </AppButton>
                <AppButton
                    v-if="can('tour-review-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('tourReview.destroy', review.id))"
                >
                    <i class="ri-delete-bin-line mr-1"></i> Delete
                </AppButton>
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tourReview.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i> Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Review Hero Card -->
    <div class="border-neutral-6 bg-neutral-1 relative mb-6 overflow-hidden rounded-2xl border shadow-lg">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 via-transparent to-purple-500/5"></div>
        <div class="absolute right-0 top-0 h-64 w-64 translate-x-1/3 -translate-y-1/3 rounded-full bg-gradient-to-br from-amber-500/10 to-purple-500/10 blur-3xl"></div>
        
        <div class="relative p-6 lg:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <!-- Left: Review Info -->
                <div class="flex items-start gap-5">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-xl shadow-amber-500/30 ring-4 ring-white dark:ring-neutral-800">
                        <i class="ri-star-smile-line text-4xl text-white"></i>
                    </div>
                    <div>
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span v-if="review.is_verified" class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-800">
                                <i class="ri-verified-badge-line text-xs"></i>
                                Verified
                            </span>
                            <span :class="review.is_approved ? 'bg-green-100 text-green-700 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-800' : 'bg-amber-100 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-800'" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold ring-1 ring-inset">
                                <i :class="review.is_approved ? 'ri-check-line' : 'ri-time-line'" class="text-xs"></i>
                                {{ review.is_approved ? 'Approved' : 'Pending' }}
                            </span>
                            <span v-if="review.is_featured" class="inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-700 ring-1 ring-inset ring-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:ring-purple-800">
                                <i class="ri-star-fill text-xs"></i>
                                Featured
                            </span>
                        </div>
                        <h2 class="text-neutral-12 text-2xl font-bold lg:text-3xl">{{ review.title || 'Untitled Review' }}</h2>
                        <div class="text-neutral-9 mt-2 flex flex-wrap items-center gap-4 text-sm">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-user-line"></i>
                                {{ review.reviewer_name || review.user?.name || 'Anonymous' }}
                            </span>
                            <span v-if="review.reviewer_email" class="flex items-center gap-1.5">
                                <i class="ri-mail-line"></i>
                                {{ review.reviewer_email }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="ri-calendar-line"></i>
                                {{ formatDate(review.created_at) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Rating -->
                <div class="bg-neutral-2 flex flex-col items-center rounded-xl border border-dashed border-neutral-300 p-6 text-center dark:border-neutral-700">
                    <p class="text-neutral-9 mb-2 text-xs font-medium uppercase tracking-wider">Rating</p>
                    <div class="flex items-center gap-1">
                        <i v-for="n in 5" :key="n" :class="n <= review.rating ? 'ri-star-fill text-amber-400' : 'ri-star-line text-slate-300'" class="text-2xl"></i>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ review.rating }}<span class="text-neutral-9 text-lg">/5</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Review Content -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-chat-quote-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Review Content</h3>
                            <p class="text-neutral-9 text-xs">What the reviewer said</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <p class="text-neutral-11 whitespace-pre-wrap leading-relaxed">{{ review.content || 'No review content provided.' }}</p>
                </div>
            </div>

            <!-- Pros & Cons -->
            <div v-if="(review.pros && review.pros.length) || (review.cons && review.cons.length)" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Pros -->
                <div v-if="review.pros && review.pros.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-thumb-up-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Pros</h3>
                            <p class="text-neutral-9 text-xs">What they liked</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2">
                            <li v-for="(pro, index) in review.pros" :key="index" class="text-neutral-11 flex items-start gap-2 text-sm">
                                <i class="ri-check-line mt-0.5 shrink-0 text-emerald-500"></i>
                                {{ pro }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Cons -->
                <div v-if="review.cons && review.cons.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                            <i class="ri-thumb-down-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Cons</h3>
                            <p class="text-neutral-9 text-xs">What could improve</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2">
                            <li v-for="(con, index) in review.cons" :key="index" class="text-neutral-11 flex items-start gap-2 text-sm">
                                <i class="ri-close-line mt-0.5 shrink-0 text-red-500"></i>
                                {{ con }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tour Information -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/25">
                        <i class="ri-map-pin-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Tour Information</h3>
                        <p class="text-neutral-9 text-xs">About the reviewed tour</p>
                    </div>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Tour Name</span>
                        <span class="text-neutral-12 max-w-[60%] text-right text-sm font-medium">{{ review.tour?.name || '-' }}</span>
                    </div>
                    <div v-if="review.travel_date" class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Travel Date</span>
                        <span class="text-neutral-12 text-sm font-medium">{{ formatDate(review.travel_date) }}</span>
                    </div>
                    <div v-if="review.booking_id" class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Booking ID</span>
                        <AppButton class="btn btn-sm btn-secondary" @click="$inertia.visit(route('tourBooking.show', review.booking_id))">
                            View Booking
                        </AppButton>
                    </div>
                </div>
            </div>

            <!-- Reviewer Details -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                        <i class="ri-user-3-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Reviewer Details</h3>
                        <p class="text-neutral-9 text-xs">About the reviewer</p>
                    </div>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Name</span>
                        <span class="text-neutral-12 text-sm font-medium">{{ review.reviewer_name || review.user?.name || 'Anonymous' }}</span>
                    </div>
                    <div v-if="review.reviewer_email || review.user?.email" class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Email</span>
                        <span class="text-neutral-12 text-sm font-medium">{{ review.reviewer_email || review.user?.email }}</span>
                    </div>
                    <div v-if="review.user_id" class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">User Account</span>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-blue-600">
                            <i class="ri-user-line"></i>
                            Registered User
                        </span>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-500 to-slate-600 shadow-lg shadow-slate-500/25">
                        <i class="ri-time-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Timestamps</h3>
                        <p class="text-neutral-9 text-xs">Important dates</p>
                    </div>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Created</span>
                        <span class="text-neutral-12 text-sm font-medium">{{ formatDateTime(review.created_at) }}</span>
                    </div>
                    <div v-if="review.approved_at" class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Approved</span>
                        <span class="text-sm font-medium text-green-600">{{ formatDateTime(review.approved_at) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                        <span class="text-neutral-9 text-sm">Updated</span>
                        <span class="text-neutral-12 text-sm font-medium">{{ formatDateTime(review.updated_at) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Review Details')

const props = defineProps({
    review: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Tour Reviews', href: route('tourReview.index') },
    { label: 'Review #' + props.review.id, last: true }
]

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

const formatDateTime = (dateString) => {
    if (!dateString) {
        return '-'
    }
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const approve = () => {
    router.patch(route('tourReview.approve', props.review.id))
}

const reject = () => {
    router.patch(route('tourReview.reject', props.review.id))
}

const toggleFeatured = () => {
    router.patch(route('tourReview.toggleFeatured', props.review.id))
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
