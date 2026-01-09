<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('tour-view')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tour.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    {{ __('Back') }}
                </AppButton>

                <AppButton
                    v-if="can('tour-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('tour.edit', tour.id))"
                >
                    <i class="ri-edit-line mr-1"></i>
                    {{ __('Edit') }}
                </AppButton>

                <AppButton
                    v-if="can('tour-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('tour.destroy', tour.id))"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    {{ __('Delete') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Hero Section -->
    <div class="relative mb-6 overflow-hidden rounded-2xl shadow-xl">
        <div class="bg-linear-to-br absolute inset-0 from-orange-600 via-amber-600 to-yellow-500"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px); background-size: 60px 60px;"></div>

        <div class="relative z-10 p-8">
            <div class="flex flex-col items-center gap-8 md:flex-row">
                <!-- Thumbnail -->
                <div class="relative">
                    <div class="absolute -inset-2 rounded-2xl bg-white/20 blur-lg"></div>
                    <div
                        v-if="tour.thumbnail_url"
                        class="relative h-48 w-48 overflow-hidden rounded-2xl border-4 border-white/40 shadow-2xl"
                    >
                        <img :src="tour.thumbnail_url" :alt="tour.name" class="h-full w-full object-cover" />
                    </div>
                    <div
                        v-else
                        class="relative flex h-48 w-48 items-center justify-center rounded-2xl border-4 border-white/40 bg-white/20 text-6xl text-white shadow-2xl backdrop-blur-sm"
                    >
                        <i class="ri-compass-3-line"></i>
                    </div>
                </div>

                <!-- Tour Info -->
                <div class="flex-1 text-center text-white md:text-left">
                    <div class="mb-2 flex flex-wrap items-center justify-center gap-2 md:justify-start">
                        <span v-if="tour.is_featured" class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            <i class="ri-star-fill mr-1"></i>Featured
                        </span>
                        <span v-if="tour.is_bestseller" class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            <i class="ri-fire-fill mr-1"></i>Bestseller
                        </span>
                        <span :class="tour.is_active ? 'bg-emerald-400/30' : 'bg-red-400/30'" class="rounded-full px-3 py-1 text-xs font-bold backdrop-blur-sm">
                            {{ tour.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight md:text-4xl">{{ tour.name }}</h1>
                    <p class="mt-2 flex items-center justify-center gap-2 text-lg text-orange-100 md:justify-start">
                        <i class="ri-map-pin-line"></i>
                        {{ tour.destination?.name || 'No Destination' }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3 md:justify-start">
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-hashtag text-orange-200"></i>
                            <span class="font-mono font-medium">{{ tour.code }}</span>
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-time-line text-orange-200"></i>
                            {{ tour.duration }}
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-folder-line text-orange-200"></i>
                            {{ tour.category?.name || 'Uncategorized' }}
                        </span>
                    </div>
                </div>

                <!-- Price Card -->
                <div class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm">
                    <p class="text-xs font-medium uppercase tracking-wider text-orange-200">Starting From</p>
                    <p class="text-3xl font-bold text-white">৳{{ formatPrice(tour.discount_price || tour.base_price) }}</p>
                    <p v-if="tour.discount_price" class="text-sm text-orange-200 line-through">৳{{ formatPrice(tour.base_price) }}</p>
                    <div class="mt-2 flex items-center gap-1 text-amber-300">
                        <i class="ri-star-fill"></i>
                        <span class="font-bold">{{ tour.average_rating || 0 }}</span>
                        <span class="text-orange-200">({{ tour.reviews_count }} reviews)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Gallery -->
            <div v-if="tour.gallery && tour.gallery.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-orange-500 to-amber-600 shadow-lg shadow-orange-500/25">
                            <i class="ri-image-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Photo Gallery</span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                        <div v-for="image in tour.gallery" :key="image.id" class="group relative aspect-square overflow-hidden rounded-xl">
                            <img :src="image.url" :alt="image.name" class="h-full w-full object-cover transition-transform group-hover:scale-110" />
                            <a :href="image.url" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                                <i class="ri-zoom-in-line text-3xl text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-file-text-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Description</span>
                    </div>
                </div>
                <div class="p-5">
                    <p v-if="tour.excerpt" class="text-neutral-11 mb-4 font-medium">{{ tour.excerpt }}</p>
                    <div v-if="tour.description" class="prose prose-neutral max-w-none" v-html="tour.description"></div>
                    <p v-else class="text-neutral-9 text-center py-4">No description available</p>
                </div>
            </div>

            <!-- Itinerary -->
            <div v-if="tour.itineraries && tour.itineraries.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-purple-500 to-violet-600 shadow-lg shadow-purple-500/25">
                            <i class="ri-route-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Day-by-Day Itinerary</span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <div v-for="day in tour.itineraries" :key="day.day_number" class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 font-bold text-orange-600">
                                    Day {{ day.day_number }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-neutral-12 font-semibold">{{ day.title }}</h4>
                                    <p class="text-neutral-10 mt-1 text-sm">{{ day.description }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span v-if="day.accommodation" class="text-neutral-11 flex items-center gap-1 text-xs">
                                            <i class="ri-hotel-line text-orange-500"></i>{{ day.accommodation }}
                                        </span>
                                        <span v-if="day.meals_included?.length" class="text-neutral-11 flex items-center gap-1 text-xs">
                                            <i class="ri-restaurant-line text-orange-500"></i>{{ day.meals_included.join(', ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inclusions & Exclusions -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 shadow-lg">
                                <i class="ri-check-line text-lg text-emerald-600"></i>
                            </div>
                            <span class="text-neutral-12 text-lg font-semibold">Inclusions</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul v-if="tour.inclusions?.length" class="space-y-2">
                            <li v-for="item in tour.inclusions" :key="item" class="flex items-start gap-2 text-sm">
                                <i class="ri-check-line mt-0.5 text-emerald-500"></i>
                                <span class="text-neutral-11">{{ item }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-neutral-9 text-center py-4">No inclusions listed</p>
                    </div>
                </div>

                <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                    <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 shadow-lg">
                                <i class="ri-close-line text-lg text-red-600"></i>
                            </div>
                            <span class="text-neutral-12 text-lg font-semibold">Exclusions</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul v-if="tour.exclusions?.length" class="space-y-2">
                            <li v-for="item in tour.exclusions" :key="item" class="flex items-start gap-2 text-sm">
                                <i class="ri-close-line mt-0.5 text-red-500"></i>
                                <span class="text-neutral-11">{{ item }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-neutral-9 text-center py-4">No exclusions listed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Pricing Details -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-money-dollar-circle-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Pricing</span>
                    </div>
                </div>
                <div class="divide-neutral-5 divide-y p-5">
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">Adult Price</span>
                        <span class="text-neutral-12 font-semibold">৳{{ formatPrice(tour.base_price) }}</span>
                    </div>
                    <div v-if="tour.discount_price" class="flex justify-between py-2">
                        <span class="text-neutral-10">Discounted Price</span>
                        <span class="font-semibold text-emerald-600">৳{{ formatPrice(tour.discount_price) }}</span>
                    </div>
                    <div v-if="tour.child_price" class="flex justify-between py-2">
                        <span class="text-neutral-10">Child Price</span>
                        <span class="text-neutral-12 font-semibold">৳{{ formatPrice(tour.child_price) }}</span>
                    </div>
                    <div v-if="tour.infant_price !== null" class="flex justify-between py-2">
                        <span class="text-neutral-10">Infant Price</span>
                        <span class="text-neutral-12 font-semibold">৳{{ formatPrice(tour.infant_price) }}</span>
                    </div>
                    <div v-if="tour.deposit_amount" class="flex justify-between py-2">
                        <span class="text-neutral-10">Deposit</span>
                        <span class="text-neutral-12 font-semibold">৳{{ formatPrice(tour.deposit_amount) }} ({{ tour.deposit_type }})</span>
                    </div>
                </div>
            </div>

            <!-- Tour Details -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/25">
                            <i class="ri-information-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Tour Details</span>
                    </div>
                </div>
                <div class="divide-neutral-5 divide-y p-5 text-sm">
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">Duration</span>
                        <span class="text-neutral-12 font-medium">{{ tour.duration }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">Difficulty</span>
                        <span class="text-neutral-12 font-medium capitalize">{{ tour.difficulty_level }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">Tour Type</span>
                        <span class="text-neutral-12 font-medium capitalize">{{ tour.tour_type }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">Group Size</span>
                        <span class="text-neutral-12 font-medium">{{ tour.min_group_size }} - {{ tour.max_group_size }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">Start Location</span>
                        <span class="text-neutral-12 font-medium">{{ tour.start_location || '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">End Location</span>
                        <span class="text-neutral-12 font-medium">{{ tour.end_location || '-' }}</span>
                    </div>
                    <div v-if="tour.cancellation_days" class="flex justify-between py-2">
                        <span class="text-neutral-10">Free Cancellation</span>
                        <span class="text-neutral-12 font-medium">{{ tour.cancellation_days }} days before</span>
                    </div>
                    <div v-if="tour.languages?.length" class="flex justify-between py-2">
                        <span class="text-neutral-10">Languages</span>
                        <span class="text-neutral-12 font-medium">{{ tour.languages.join(', ') }}</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Departures -->
            <div v-if="tour.departure_dates?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-calendar-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Upcoming Departures</span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="space-y-2">
                        <div v-for="dep in tour.departure_dates" :key="dep.id" class="border-neutral-5 flex items-center justify-between rounded-lg border p-3">
                            <div>
                                <p class="text-neutral-12 font-medium">{{ dep.date }}</p>
                                <p class="text-neutral-9 text-xs">{{ dep.available }} slots available</p>
                            </div>
                            <span :class="dep.status === 'available' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'" class="rounded-full px-2 py-1 text-xs font-medium capitalize">
                                {{ dep.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Highlights -->
            <div v-if="tour.highlights?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-lightbulb-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Highlights</span>
                    </div>
                </div>
                <div class="p-5">
                    <ul class="space-y-2">
                        <li v-for="item in tour.highlights" :key="item" class="flex items-start gap-2 text-sm">
                            <i class="ri-star-line mt-0.5 text-amber-500"></i>
                            <span class="text-neutral-11">{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import { ref, getCurrentInstance } from 'vue'

const instance = getCurrentInstance()
const __ = instance?.appContext?.config?.globalProperties?.__ || window.__ || ((key) => key)

const { can } = useAuthCan()

const props = defineProps({
    tour: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: __('Dashboard'), href: route('dashboard.index') },
    { label: __('Tours'), href: route('tour.index') },
    { label: props.tour.name, last: true }
]

const { title } = useTitle(props.tour.name)

const formatPrice = (price) => {
    return Number(price || 0).toLocaleString()
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
