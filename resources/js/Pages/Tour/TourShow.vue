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
        <div class="bg-linear-to-br absolute inset-0 from-blue-600 via-teal-600 to-emerald-500"></div>
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
                    <p class="mt-1 flex items-center justify-center gap-2 font-mono text-sm text-teal-200 md:justify-start">
                        <i class="ri-link"></i>
                        /{{ tour.slug }}
                    </p>
                    <p class="mt-2 flex items-center justify-center gap-2 text-lg text-teal-100 md:justify-start">
                        <i class="ri-map-pin-line"></i>
                        {{ tour.destination?.name || 'No Destination' }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3 md:justify-start">
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-hashtag text-teal-200"></i>
                            <span class="font-mono font-medium">{{ tour.code }}</span>
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-time-line text-teal-200"></i>
                            {{ tour.duration }}
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-folder-line text-teal-200"></i>
                            {{ tour.category?.name || 'Uncategorized' }}
                        </span>
                    </div>
                </div>

                <!-- Price Card -->
                <div class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm">
                    <p class="text-xs font-medium uppercase tracking-wider text-teal-200">Starting From</p>
                    <p class="text-3xl font-bold text-white">৳{{ formatPrice(minPrice) }}</p>
                    <p v-if="maxPrice > minPrice" class="text-sm text-teal-200">up to ৳{{ formatPrice(maxPrice) }}</p>
                    <div class="mt-2 flex items-center gap-1 text-emerald-300">
                        <i class="ri-star-fill"></i>
                        <span class="font-bold">{{ tour.average_rating || 0 }}</span>
                        <span class="text-teal-200">({{ tour.reviews_count }} reviews)</span>
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
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-image-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Photo Gallery</span>
                    </div>
                </div>
                <div class="p-5">
                    <TourPhotoGallery :images="tour.gallery" />
                </div>
            </div>

            <!-- Description -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/25">
                            <i class="ri-file-text-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Description</span>
                    </div>
                </div>
                <div class="p-5">
                    <p v-if="tour.excerpt" class="text-neutral-11 mb-4 font-medium">{{ tour.excerpt }}</p>
                    <div v-if="tour.description" class="prose prose-neutral max-w-none" v-html="tour.description"></div>
                    <p v-else class="text-neutral-9 py-4 text-center">No description available</p>
                </div>
            </div>

            <!-- Itinerary -->
            <div v-if="tour.itineraries && tour.itineraries.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-teal-500 to-emerald-600 shadow-lg shadow-teal-500/25">
                            <i class="ri-route-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Day-by-Day Itinerary</span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <div v-for="day in tour.itineraries" :key="day.day_number" class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-600">
                                    Day {{ day.day_number }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-neutral-12 font-semibold">{{ day.title }}</h4>
                                    <p class="text-neutral-10 mt-1 text-sm">{{ day.description }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span v-if="day.accommodation" class="text-neutral-11 flex items-center gap-1 text-xs">
                                            <i class="ri-hotel-line text-teal-500"></i>{{ day.accommodation }}
                                        </span>
                                        <span v-if="day.meals_included?.length" class="text-neutral-11 flex items-center gap-1 text-xs">
                                            <i class="ri-restaurant-line text-teal-500"></i>{{ day.meals_included.join(', ') }}
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
                        <p v-else class="text-neutral-9 py-4 text-center">No exclusions listed</p>
                    </div>
                </div>
            </div>

            <!-- FAQs -->
            <div v-if="tour.faqs?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-sky-500 to-blue-600 shadow-lg shadow-sky-500/25">
                            <i class="ri-question-answer-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Frequently Asked Questions</span>
                    </div>
                </div>
                <div class="divide-neutral-5 divide-y p-5">
                    <div v-for="(faq, index) in tour.faqs" :key="index" class="py-4 first:pt-0 last:pb-0">
                        <h4 class="text-neutral-12 mb-2 flex items-start gap-2 font-semibold">
                            <i class="ri-question-line mt-0.5 shrink-0 text-blue-500"></i>
                            {{ faq.question }}
                        </h4>
                        <p class="text-neutral-10 ml-6 text-sm leading-relaxed">{{ faq.answer }}</p>
                    </div>
                </div>
            </div>

            <!-- Cancellation Policy -->
            <div v-if="tour.cancellation_policies?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-shield-check-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Cancellation Policy</span>
                    </div>
                </div>
                <div class="divide-neutral-5 divide-y p-5">
                    <div v-for="policy in tour.cancellation_policies" :key="policy.id" class="py-3 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-neutral-12 font-semibold">{{ policy.name }}</h4>
                                    <span v-if="policy.is_default" class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Default</span>
                                </div>
                                <p class="text-neutral-10 mt-1 text-sm">
                                    Cancel <span class="font-bold text-teal-600">{{ policy.days_before_departure }}+ days</span> before departure
                                </p>
                                <p v-if="policy.description" class="text-neutral-9 mt-1 text-xs">{{ policy.description }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p v-if="policy.refund_percentage" class="font-bold text-emerald-600">{{ policy.refund_percentage }}% Refund</p>
                                <p v-if="policy.cancellation_fee" class="text-neutral-10 text-sm">Fee: ৳{{ formatPrice(policy.cancellation_fee) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Information -->
            <div v-if="tour.meta_title || tour.meta_description || tour.meta_keywords" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-seo-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">SEO Information</span>
                    </div>
                </div>
                <div class="divide-neutral-5 divide-y p-5">
                    <div class="py-3 first:pt-0">
                        <label class="text-neutral-9 mb-1 block text-xs font-medium uppercase tracking-wider">URL Slug</label>
                        <p class="text-neutral-12 font-mono text-sm">/tours/{{ tour.slug }}</p>
                    </div>
                    <div v-if="tour.meta_title" class="py-3">
                        <label class="text-neutral-9 mb-1 block text-xs font-medium uppercase tracking-wider">Meta Title</label>
                        <p class="text-neutral-12 text-sm font-medium">{{ tour.meta_title }}</p>
                    </div>
                    <div v-if="tour.meta_description" class="py-3">
                        <label class="text-neutral-9 mb-1 block text-xs font-medium uppercase tracking-wider">Meta Description</label>
                        <p class="text-neutral-10 text-sm leading-relaxed">{{ tour.meta_description }}</p>
                    </div>
                    <div v-if="tour.meta_keywords" class="py-3 last:pb-0">
                        <label class="text-neutral-9 mb-1 block text-xs font-medium uppercase tracking-wider">Meta Keywords</label>
                        <p class="text-neutral-10 text-sm">{{ tour.meta_keywords }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Pricing Packages -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-price-tag-3-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Pricing Packages</span>
                    </div>
                </div>
                <div class="p-5">
                    <div v-if="tour.pricings?.length" class="space-y-4">
                        <div
                            v-for="pricing in tour.pricings"
                            :key="pricing.id"
                            class="border-neutral-5 relative overflow-hidden rounded-xl border"
                            :class="pricing.is_active ? 'bg-gradient-to-br from-emerald-50/50 to-teal-50/50 dark:from-emerald-900/10 dark:to-teal-900/10' : 'bg-neutral-3 opacity-60'"
                        >
                            <div class="border-neutral-5 border-b bg-gradient-to-r from-blue-600 to-teal-600 px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-white">{{ pricing.name }}</h4>
                                    <span
                                        v-if="!pricing.is_active"
                                        class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white"
                                    >
                                        Inactive
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <p v-if="pricing.description" class="text-neutral-10 mb-3 text-sm">{{ pricing.description }}</p>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-neutral-10 flex items-center gap-2 text-sm">
                                            <i class="ri-user-line text-blue-500"></i>
                                            Adult
                                        </span>
                                        <span class="font-bold text-emerald-600">৳{{ formatPrice(pricing.adult_price) }}</span>
                                    </div>
                                    <div v-if="pricing.child_price" class="flex items-center justify-between">
                                        <span class="text-neutral-10 flex items-center gap-2 text-sm">
                                            <i class="ri-user-smile-line text-teal-500"></i>
                                            Child
                                        </span>
                                        <span class="text-neutral-12 font-semibold">৳{{ formatPrice(pricing.child_price) }}</span>
                                    </div>
                                    <div v-if="pricing.infant_price !== null && pricing.infant_price !== undefined" class="flex items-center justify-between">
                                        <span class="text-neutral-10 flex items-center gap-2 text-sm">
                                            <i class="ri-emotion-happy-line text-cyan-500"></i>
                                            Infant
                                        </span>
                                        <span class="text-neutral-12 font-semibold">৳{{ formatPrice(pricing.infant_price) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-8 text-center">
                        <i class="ri-price-tag-3-line text-neutral-7 mb-2 text-4xl"></i>
                        <p class="text-neutral-9">No pricing packages available</p>
                    </div>
                </div>
            </div>

            <!-- Tour Extras -->
            <div v-if="tour.extras?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-add-circle-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Add-ons & Extras</span>
                    </div>
                </div>
                <div class="divide-neutral-5 divide-y p-5">
                    <div v-for="extra in tour.extras" :key="extra.id" class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-neutral-12 font-medium">{{ extra.name }}</p>
                            <p v-if="extra.description" class="text-neutral-9 text-xs">{{ extra.description }}</p>
                            <span class="text-neutral-10 mt-1 inline-block text-xs capitalize">{{ extra.price_type }}</span>
                        </div>
                        <span class="font-bold text-violet-600">+৳{{ formatPrice(extra.price) }}</span>
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
                        <span class="text-neutral-10">Start Location</span>
                        <span class="text-neutral-12 font-medium">{{ tour.start_location || '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-neutral-10">End Location</span>
                        <span class="text-neutral-12 font-medium">{{ tour.end_location || '-' }}</span>
                    </div>
                    <div v-if="tour.valid_from || tour.valid_to" class="flex justify-between py-2">
                        <span class="text-neutral-10">Validity Period</span>
                        <span class="text-neutral-12 font-medium">
                            {{ tour.valid_from || 'Any' }} - {{ tour.valid_to || 'Any' }}
                        </span>
                    </div>
                    <div v-if="tour.languages?.length" class="flex justify-between py-2">
                        <span class="text-neutral-10">Languages</span>
                        <span class="text-neutral-12 font-medium">{{ tour.languages.join(', ') }}</span>
                    </div>
                    <div v-if="tour.age_restrictions?.length" class="py-2">
                        <span class="text-neutral-10 mb-1 block">Age Restrictions</span>
                        <div class="flex flex-wrap gap-1">
                            <span v-for="restriction in tour.age_restrictions" :key="restriction" class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">
                                {{ restriction }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Departures -->
            <div v-if="tour.departure_dates?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 bg-neutral-2 border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25">
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
                        <div class="bg-linear-to-br flex h-10 w-10 items-center justify-center rounded-xl from-teal-500 to-green-600 shadow-lg shadow-teal-500/25">
                            <i class="ri-lightbulb-line text-lg text-white"></i>
                        </div>
                        <span class="text-neutral-12 text-lg font-semibold">Highlights</span>
                    </div>
                </div>
                <div class="p-5">
                    <ul class="space-y-2">
                        <li v-for="item in tour.highlights" :key="item" class="flex items-start gap-2 text-sm">
                            <i class="ri-checkbox-circle-fill mt-0.5 text-emerald-500"></i>
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
import TourPhotoGallery from '@/Components/Misc/TourPhotoGallery.vue'
import { ref, computed, getCurrentInstance } from 'vue'

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

const minPrice = computed(() => {
    if (!props.tour.pricings?.length) {
        return 0
    }
    return Math.min(...props.tour.pricings.map(p => p.adult_price || 0))
})

const maxPrice = computed(() => {
    if (!props.tour.pricings?.length) {
        return 0
    }
    return Math.max(...props.tour.pricings.map(p => p.adult_price || 0))
})

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
