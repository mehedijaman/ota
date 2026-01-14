<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotelReview.index'))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <!-- Review Content -->
            <AppCard>
                <template #title>
                    <div class="flex items-center justify-between">
                        <span>Review Details</span>
                        <span 
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                            :class="statusClass(review.status)"
                        >
                            {{ review.status }}
                        </span>
                    </div>
                </template>
                <template #content>
                    <div class="mb-4 flex items-center gap-4">
                        <div class="flex items-center gap-1">
                            <i v-for="n in 5" :key="n" class="ri-star-fill text-xl" :class="n <= review.overall_rating ? 'text-amber-500' : 'text-neutral-300'"></i>
                        </div>
                        <span class="text-xl font-bold text-neutral-12">{{ review.overall_rating }}/5</span>
                    </div>

                    <div class="prose max-w-none text-neutral-11">
                        <p>{{ review.comment }}</p>
                    </div>

                    <div v-if="review.pros || review.cons" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div v-if="review.pros" class="bg-emerald-50 rounded-lg p-4">
                            <h4 class="flex items-center gap-2 font-medium text-emerald-700 mb-2">
                                <i class="ri-thumb-up-line"></i> Pros
                            </h4>
                            <p class="text-emerald-900 text-sm">{{ review.pros }}</p>
                        </div>
                        <div v-if="review.cons" class="bg-red-50 rounded-lg p-4">
                            <h4 class="flex items-center gap-2 font-medium text-red-700 mb-2">
                                <i class="ri-thumb-down-line"></i> Cons
                            </h4>
                            <p class="text-red-900 text-sm">{{ review.cons }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Rating Breakdown -->
            <AppCard v-if="review.cleanliness_rating">
                <template #title>Rating Breakdown</template>
                <template #content>
                    <div class="space-y-3">
                        <div v-for="(value, label) in ratingBreakdown" :key="label" class="flex items-center gap-4">
                            <span class="w-32 text-sm text-neutral-9 capitalize">{{ label.replace('_', ' ') }}</span>
                            <div class="flex-1 bg-neutral-200 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" :style="{ width: (value / 5 * 100) + '%' }"></div>
                            </div>
                            <span class="w-8 text-sm font-medium text-neutral-12">{{ value }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Management Response -->
            <AppCard>
                <template #title>Management Response</template>
                <template #content>
                    <div v-if="review.response" class="bg-blue-50 rounded-lg p-4">
                        <p class="text-blue-900">{{ review.response.response }}</p>
                        <p class="text-blue-700 text-xs mt-2">
                            Responded by {{ review.response.user?.name }} on {{ formatDate(review.response.created_at) }}
                        </p>
                    </div>

                    <form v-else @submit.prevent="submitResponse" class="mt-4">
                        <AppLabel>Write a response</AppLabel>
                        <AppTextArea v-model="responseForm.response" rows="3" placeholder="Thank you for your feedback..." />
                        <AppButton type="submit" class="btn btn-primary mt-3" :loading="responseForm.processing">
                            <i class="ri-send-plane-line mr-1"></i> Send Response
                        </AppButton>
                    </form>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <!-- Hotel Info -->
            <AppCard>
                <template #title>Hotel</template>
                <template #content>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 text-white">
                            <i class="ri-hotel-line text-xl"></i>
                        </div>
                        <div>
                            <p class="font-medium text-neutral-12">{{ review.hotel?.name }}</p>
                            <p class="text-xs text-neutral-9">{{ review.hotel?.city?.name }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Guest Info -->
            <AppCard>
                <template #title>Guest</template>
                <template #content>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-9">Name</span>
                            <span class="font-medium text-neutral-12">{{ review.guest_name || review.customer?.name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-9">Date</span>
                            <span class="text-neutral-11">{{ formatDate(review.created_at) }}</span>
                        </div>
                        <div v-if="review.stay_date" class="flex items-center justify-between">
                            <span class="text-neutral-9">Stay Date</span>
                            <span class="text-neutral-11">{{ formatDate(review.stay_date) }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Actions -->
            <AppCard>
                <template #title>Actions</template>
                <template #content>
                    <div class="space-y-2">
                        <AppButton v-if="review.status === 'pending'" class="btn btn-success w-full" @click="approveReview">
                            <i class="ri-checkbox-circle-line mr-1"></i> Approve
                        </AppButton>
                        <AppButton v-if="review.status === 'pending'" class="btn btn-destructive w-full" @click="rejectReview">
                            <i class="ri-close-circle-line mr-1"></i> Reject
                        </AppButton>
                        <AppButton class="btn btn-secondary w-full" @click="toggleFeatured">
                            <i class="ri-star-line mr-1"></i> {{ review.is_featured ? 'Remove Featured' : 'Mark Featured' }}
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </aside>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'

const props = defineProps({
    review: { type: Object, required: true }
})

const { title } = useTitle('Review Details')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotel Reviews', href: route('hotelReview.index') },
    { label: 'View Review', last: true }
]

const ratingBreakdown = computed(() => {
    const breakdown = {}
    if (props.review.cleanliness_rating) breakdown.cleanliness = props.review.cleanliness_rating
    if (props.review.comfort_rating) breakdown.comfort = props.review.comfort_rating
    if (props.review.location_rating) breakdown.location = props.review.location_rating
    if (props.review.facilities_rating) breakdown.facilities = props.review.facilities_rating
    if (props.review.staff_rating) breakdown.staff = props.review.staff_rating
    if (props.review.value_rating) breakdown.value = props.review.value_rating
    return breakdown
})

const statusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        approved: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        rejected: 'bg-red-50 text-red-700 ring-red-200'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-200'
}

const formatDate = (date) => new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })

const responseForm = useForm({ response: '' })
const submitResponse = () => {
    responseForm.post(route('hotelReview.addResponse', props.review.id), { preserveScroll: true })
}

const approveReview = () => router.patch(route('hotelReview.approve', props.review.id), {}, { preserveScroll: true })
const rejectReview = () => router.patch(route('hotelReview.reject', props.review.id), {}, { preserveScroll: true })
const toggleFeatured = () => router.patch(route('hotelReview.toggleFeatured', props.review.id), {}, { preserveScroll: true })
</script>
