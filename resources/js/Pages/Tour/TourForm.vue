<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('tour.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm">
        <!-- Progress Tabs -->
        <div class="border-neutral-6 bg-neutral-1 mb-6 overflow-hidden rounded-xl border shadow-sm">
            <div class="overflow-x-auto p-4">
                <nav class="flex min-w-max gap-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all',
                            activeTab === tab.id
                                ? 'bg-gradient-to-r from-blue-600 to-teal-600 text-white shadow-lg shadow-blue-500/25'
                                : 'bg-neutral-3 text-neutral-11 hover:bg-neutral-4 hover:text-neutral-12'
                        ]"
                    >
                        <i :class="[tab.icon, activeTab === tab.id ? 'text-white' : 'text-neutral-9']"></i>
                        <span class="hidden sm:inline">{{ tab.label }}</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Basic Info Tab -->
        <div v-show="activeTab === 'basic'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-information-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Basic Information</span>
                            <p class="text-neutral-9 text-xs">Tour name, category, and general details</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <AppFormErrors class="mb-4" />

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="name">
                                Tour Name <span class="text-red-500">*</span>
                            </AppLabel>
                            <AppInputText
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter tour name"
                                @input="generateSlug"
                                :class="{ 'input-error': errorsFields.includes('name') }"
                            />
                        </div>

                        <div>
                            <AppLabel for="slug">
                                URL Slug <span class="text-red-500">*</span>
                            </AppLabel>
                            <div class="relative">
                                <span class="text-neutral-9 pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm">/tours/</span>
                                <AppInputText
                                    id="slug"
                                    v-model="form.slug"
                                    type="text"
                                    class="pl-14"
                                    :class="{ 'input-error': errorsFields.includes('slug') }"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <AppLabel for="code">Tour Code</AppLabel>
                            <AppInputText
                                id="code"
                                v-model="form.code"
                                type="text"
                                placeholder="e.g., CBX-001"
                            />
                        </div>

                        <div>
                            <AppLabel for="category_id">Category</AppLabel>
                            <AppCombobox
                                v-model="form.category_id"
                                :options="categories"
                                combo-label="Select Category"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <AppLabel for="destination_id">Destination</AppLabel>
                            <AppCombobox
                                v-model="form.destination_id"
                                :options="destinations"
                                combo-label="Select Destination"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <div class="mt-4">
                        <AppLabel for="excerpt">Short Description</AppLabel>
                        <AppTextArea
                            id="excerpt"
                            v-model="form.excerpt"
                            rows="2"
                            placeholder="Brief tour description for listings (max 200 characters)"
                        />
                    </div>

                    <div class="mt-4">
                        <AppLabel for="description">Full Description</AppLabel>
                        <AppTextArea
                            id="description"
                            v-model="form.description"
                            rows="6"
                            placeholder="Detailed tour description with highlights and important information"
                        />
                    </div>
                </template>
            </AppCard>

            <!-- Duration & Location -->
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-lg shadow-teal-500/25">
                            <i class="ri-time-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Duration & Location</span>
                            <p class="text-neutral-9 text-xs">Tour duration, dates, and locations</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div>
                            <AppLabel for="duration_days">
                                Days <span class="text-red-500">*</span>
                            </AppLabel>
                            <AppInputText
                                id="duration_days"
                                v-model="form.duration_days"
                                type="number"
                                min="1"
                                :class="{ 'input-error': errorsFields.includes('duration_days') }"
                            />
                        </div>

                        <div>
                            <AppLabel for="duration_nights">
                                Nights <span class="text-red-500">*</span>
                            </AppLabel>
                            <AppInputText
                                id="duration_nights"
                                v-model="form.duration_nights"
                                type="number"
                                min="0"
                            />
                        </div>

                        <div>
                            <AppLabel for="valid_from">Valid From</AppLabel>
                            <AppInputText
                                id="valid_from"
                                v-model="form.valid_from"
                                type="date"
                            />
                        </div>

                        <div>
                            <AppLabel for="valid_to">Valid To</AppLabel>
                            <AppInputText
                                id="valid_to"
                                v-model="form.valid_to"
                                type="date"
                            />
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="start_location">
                                <i class="ri-map-pin-line mr-1 text-emerald-500"></i>
                                Start Location
                            </AppLabel>
                            <AppInputText
                                id="start_location"
                                v-model="form.start_location"
                                type="text"
                                placeholder="Where the tour begins"
                            />
                        </div>

                        <div>
                            <AppLabel for="end_location">
                                <i class="ri-flag-line mr-1 text-red-500"></i>
                                End Location
                            </AppLabel>
                            <AppInputText
                                id="end_location"
                                v-model="form.end_location"
                                type="text"
                                placeholder="Where the tour ends"
                            />
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="languages">Languages Available</AppLabel>
                            <AppInputText
                                id="languages"
                                v-model="languagesInput"
                                type="text"
                                placeholder="English, Bengali, Hindi (comma separated)"
                            />
                        </div>
                        <div>
                            <AppLabel>Age Restrictions</AppLabel>
                            <div class="grid grid-cols-2 gap-2">
                                <AppInputText
                                    v-model="form.age_restrictions.min_age"
                                    type="number"
                                    min="0"
                                    placeholder="Min Age"
                                />
                                <AppInputText
                                    v-model="form.age_restrictions.max_age"
                                    type="number"
                                    min="0"
                                    placeholder="Max Age"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Status & Visibility -->
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-toggle-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Status & Visibility</span>
                            <p class="text-neutral-9 text-xs">Control how the tour appears on site</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-wrap gap-6">
                        <label class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors">
                            <input
                                type="checkbox"
                                v-model="form.is_active"
                                class="text-primary-600 h-5 w-5 rounded"
                            />
                            <div>
                                <span class="text-neutral-12 font-medium">Active</span>
                                <p class="text-neutral-9 text-xs">Tour is visible and bookable</p>
                            </div>
                        </label>

                        <label class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors">
                            <input
                                type="checkbox"
                                v-model="form.is_featured"
                                class="h-5 w-5 rounded text-amber-500"
                            />
                            <div>
                                <span class="text-neutral-12 font-medium">
                                    <i class="ri-star-fill mr-1 text-amber-500"></i>Featured
                                </span>
                                <p class="text-neutral-9 text-xs">Show on homepage featured section</p>
                            </div>
                        </label>

                        <label class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors">
                            <input
                                type="checkbox"
                                v-model="form.is_bestseller"
                                class="h-5 w-5 rounded text-purple-500"
                            />
                            <div>
                                <span class="text-neutral-12 font-medium">
                                    <i class="ri-fire-fill mr-1 text-purple-500"></i>Bestseller
                                </span>
                                <p class="text-neutral-9 text-xs">Mark as bestselling tour</p>
                            </div>
                        </label>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Pricing Tab -->
        <div v-show="activeTab === 'pricing'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-money-dollar-circle-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Pricing Packages</span>
                            <p class="text-neutral-9 text-xs">Define different pricing tiers for this tour</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.pricings.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-price-tag-3-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No pricing packages yet</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add at least one pricing package for customers to book</p>
                        <AppButton type="button" class="btn btn-primary" @click="addPricing">
                            <i class="ri-add-line mr-1"></i>
                            Add First Package
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(pricing, index) in form.pricings"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-emerald-600/10 to-teal-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-sm font-bold text-emerald-700">
                                        {{ index + 1 }}
                                    </span>
                                    <span class="text-neutral-12 font-medium">{{ pricing.name || 'Untitled Package' }}</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removePricing(index)"
                                    class="text-neutral-9 hover:text-red-600 transition-colors"
                                >
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="p-4">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <AppLabel>Package Name <span class="text-red-500">*</span></AppLabel>
                                        <AppInputText
                                            v-model="pricing.name"
                                            type="text"
                                            placeholder="e.g., Standard, Premium, Budget"
                                        />
                                    </div>
                                    <div>
                                        <AppLabel>Description</AppLabel>
                                        <AppInputText
                                            v-model="pricing.description"
                                            type="text"
                                            placeholder="What's included in this package"
                                        />
                                    </div>
                                </div>

                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <AppLabel>
                                            <i class="ri-user-line mr-1 text-blue-500"></i>
                                            Adult Price (৳) <span class="text-red-500">*</span>
                                        </AppLabel>
                                        <AppInputText
                                            v-model="pricing.adult_price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>
                                    <div>
                                        <AppLabel>
                                            <i class="ri-user-smile-line mr-1 text-teal-500"></i>
                                            Child Price (৳)
                                        </AppLabel>
                                        <AppInputText
                                            v-model="pricing.child_price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>
                                    <div>
                                        <AppLabel>
                                            <i class="ri-emotion-happy-line mr-1 text-pink-500"></i>
                                            Infant Price (৳)
                                        </AppLabel>
                                        <AppInputText
                                            v-model="pricing.infant_price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="pricing.is_active" class="h-4 w-4 rounded text-emerald-500" />
                                        <span class="text-neutral-11 text-sm">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <AppButton type="button" class="btn btn-secondary" @click="addPricing">
                            <i class="ri-add-line mr-1"></i>
                            Add Another Package
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Itinerary Tab -->
        <div v-show="activeTab === 'itinerary'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-lg shadow-teal-500/25">
                            <i class="ri-route-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Day-by-Day Itinerary</span>
                            <p class="text-neutral-9 text-xs">Detailed plan for each day of the tour</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.itineraries.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-map-2-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No itinerary added</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add daily activities to help travelers understand the journey</p>
                        <AppButton type="button" class="btn btn-primary" @click="addItinerary">
                            <i class="ri-add-line mr-1"></i>
                            Add Day 1
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(day, index) in form.itineraries"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-teal-600/10 to-emerald-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-700">
                                        Day {{ index + 1 }}
                                    </span>
                                    <span class="text-neutral-12 font-medium">{{ day.title || 'Untitled Day' }}</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeItinerary(index)"
                                    class="text-neutral-9 hover:text-red-600 transition-colors"
                                >
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="space-y-4 p-4">
                                <div>
                                    <AppLabel>Day Title <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText v-model="day.title" type="text" placeholder="e.g., Arrival & City Tour" />
                                </div>

                                <div>
                                    <AppLabel>Description</AppLabel>
                                    <AppTextArea v-model="day.description" rows="3" placeholder="Describe the day's activities in detail" />
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <AppLabel>
                                            <i class="ri-hotel-line mr-1 text-blue-500"></i>
                                            Accommodation
                                        </AppLabel>
                                        <AppInputText v-model="day.accommodation" type="text" placeholder="Hotel/Lodge name" />
                                    </div>
                                    <div>
                                        <AppLabel>
                                            <i class="ri-pin-distance-line mr-1 text-teal-500"></i>
                                            Distance (km)
                                        </AppLabel>
                                        <AppInputText v-model="day.distance_km" type="number" min="0" step="0.1" placeholder="0" />
                                    </div>
                                </div>

                                <div>
                                    <AppLabel>
                                        <i class="ri-restaurant-line mr-1 text-orange-500"></i>
                                        Meals Included
                                    </AppLabel>
                                    <div class="flex flex-wrap gap-3">
                                        <label v-for="meal in ['Breakfast', 'Lunch', 'Dinner']" :key="meal" class="flex cursor-pointer items-center gap-2">
                                            <input
                                                type="checkbox"
                                                :value="meal"
                                                v-model="day.meals_included"
                                                class="h-4 w-4 rounded text-orange-500"
                                            />
                                            <span class="text-neutral-11 text-sm">{{ meal }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <AppButton type="button" class="btn btn-secondary" @click="addItinerary">
                            <i class="ri-add-line mr-1"></i>
                            Add Day {{ form.itineraries.length + 1 }}
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Inclusions Tab -->
        <div v-show="activeTab === 'inclusions'">
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Inclusions -->
                <AppCard class="mb-4 w-full">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg shadow-emerald-500/25">
                                <i class="ri-check-line text-lg text-white"></i>
                            </div>
                            <div>
                                <span class="text-neutral-12 font-semibold">What's Included</span>
                                <p class="text-neutral-9 text-xs">Services included in the tour price</p>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-2">
                            <div
                                v-for="(item, index) in form.inclusions"
                                :key="'inc-' + index"
                                class="group flex items-center gap-2"
                            >
                                <i class="ri-check-line shrink-0 text-emerald-500"></i>
                                <AppInputText
                                    v-model="form.inclusions[index]"
                                    type="text"
                                    class="flex-1"
                                    placeholder="e.g., Round-trip transport"
                                />
                                <button
                                    type="button"
                                    @click="form.inclusions.splice(index, 1)"
                                    class="text-neutral-9 opacity-0 transition-opacity hover:text-red-600 group-hover:opacity-100"
                                >
                                    <i class="ri-close-line text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-sm btn-secondary mt-4" @click="form.inclusions.push('')">
                            <i class="ri-add-line mr-1"></i> Add Inclusion
                        </AppButton>
                    </template>
                </AppCard>

                <!-- Exclusions -->
                <AppCard class="mb-4 w-full">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/25">
                                <i class="ri-close-line text-lg text-white"></i>
                            </div>
                            <div>
                                <span class="text-neutral-12 font-semibold">What's Not Included</span>
                                <p class="text-neutral-9 text-xs">Services NOT included in the price</p>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-2">
                            <div
                                v-for="(item, index) in form.exclusions"
                                :key="'exc-' + index"
                                class="group flex items-center gap-2"
                            >
                                <i class="ri-close-line shrink-0 text-red-500"></i>
                                <AppInputText
                                    v-model="form.exclusions[index]"
                                    type="text"
                                    class="flex-1"
                                    placeholder="e.g., Personal expenses"
                                />
                                <button
                                    type="button"
                                    @click="form.exclusions.splice(index, 1)"
                                    class="text-neutral-9 opacity-0 transition-opacity hover:text-red-600 group-hover:opacity-100"
                                >
                                    <i class="ri-close-line text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-sm btn-secondary mt-4" @click="form.exclusions.push('')">
                            <i class="ri-add-line mr-1"></i> Add Exclusion
                        </AppButton>
                    </template>
                </AppCard>
            </div>
        </div>

        <!-- Highlights Tab -->
        <div v-show="activeTab === 'highlights'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-star-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Tour Highlights</span>
                            <p class="text-neutral-9 text-xs">Key attractions and experiences of this tour</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.highlights.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-star-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No highlights added</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add highlights to showcase what makes this tour special</p>
                        <AppButton type="button" class="btn btn-primary" @click="addHighlight">
                            <i class="ri-add-line mr-1"></i>
                            Add First Highlight
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(highlight, index) in form.highlights"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-amber-600/10 to-orange-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                        <i :class="highlight.icon || 'ri-star-line'"></i>
                                    </span>
                                    <span class="text-neutral-12 font-medium">{{ highlight.title || 'Untitled Highlight' }}</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeHighlight(index)"
                                    class="text-neutral-9 hover:text-red-600 transition-colors"
                                >
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="p-4">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <AppLabel>Title <span class="text-red-500">*</span></AppLabel>
                                        <AppInputText v-model="highlight.title" type="text" placeholder="Highlight title" />
                                    </div>
                                    <div>
                                        <AppLabel>Icon (Remix Icon class)</AppLabel>
                                        <AppInputText v-model="highlight.icon" type="text" placeholder="e.g., ri-star-line" />
                                    </div>
                                </div>
                                <div>
                                    <AppLabel>Description</AppLabel>
                                    <AppTextArea v-model="highlight.description" rows="2" placeholder="Describe this highlight" />
                                </div>
                            </div>
                        </div>

                        <AppButton type="button" class="btn btn-secondary" @click="addHighlight">
                            <i class="ri-add-line mr-1"></i>
                            Add Another Highlight
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- FAQs Tab -->
        <div v-show="activeTab === 'faqs'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 shadow-lg shadow-sky-500/25">
                            <i class="ri-question-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Frequently Asked Questions</span>
                            <p class="text-neutral-9 text-xs">Common questions travelers might have</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.faqs.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-question-answer-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No FAQs added</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add frequently asked questions to help travelers</p>
                        <AppButton type="button" class="btn btn-primary" @click="addFaq">
                            <i class="ri-add-line mr-1"></i>
                            Add First FAQ
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(faq, index) in form.faqs"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-sky-600/10 to-blue-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sm font-bold text-sky-700">
                                        Q{{ index + 1 }}
                                    </span>
                                    <span class="text-neutral-12 line-clamp-1 font-medium">{{ faq.question || 'New Question' }}</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeFaq(index)"
                                    class="text-neutral-9 hover:text-red-600 transition-colors"
                                >
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="space-y-4 p-4">
                                <div>
                                    <AppLabel>Question <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText v-model="faq.question" type="text" placeholder="What would travelers ask?" />
                                </div>
                                <div>
                                    <AppLabel>Answer <span class="text-red-500">*</span></AppLabel>
                                    <AppTextArea v-model="faq.answer" rows="3" placeholder="Provide a helpful answer" />
                                </div>
                                <div class="flex items-center gap-4">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="faq.is_active" class="h-4 w-4 rounded text-sky-500" />
                                        <span class="text-neutral-11 text-sm">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <AppButton type="button" class="btn btn-secondary" @click="addFaq">
                            <i class="ri-add-line mr-1"></i>
                            Add Another FAQ
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Extras Tab -->
        <div v-show="activeTab === 'extras'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                            <i class="ri-add-box-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Add-ons & Extras</span>
                            <p class="text-neutral-9 text-xs">Optional services travelers can add to their booking</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.extras.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-add-circle-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No extras added</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add optional services like airport transfers, meals, etc.</p>
                        <AppButton type="button" class="btn btn-primary" @click="addExtra">
                            <i class="ri-add-line mr-1"></i>
                            Add First Extra
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(extra, index) in form.extras"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-violet-600/10 to-purple-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-violet-700">
                                        <i class="ri-add-circle-line"></i>
                                    </span>
                                    <span class="text-neutral-12 font-medium">{{ extra.name || 'Untitled Extra' }}</span>
                                    <span v-if="extra.is_required" class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Required</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeExtra(index)"
                                    class="text-neutral-9 hover:text-red-600 transition-colors"
                                >
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="p-4">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <AppLabel>Name <span class="text-red-500">*</span></AppLabel>
                                        <AppInputText v-model="extra.name" type="text" placeholder="e.g., Airport Transfer" />
                                    </div>
                                    <div>
                                        <AppLabel>Description</AppLabel>
                                        <AppInputText v-model="extra.description" type="text" placeholder="Brief description" />
                                    </div>
                                </div>
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <AppLabel>Price (৳)</AppLabel>
                                        <AppInputText v-model="extra.price" type="number" min="0" step="0.01" placeholder="0.00" />
                                    </div>
                                    <div>
                                        <AppLabel>Price Type</AppLabel>
                                        <AppCombobox v-model="extra.price_type" :options="priceTypeOptions" combo-label="Select Type" class="w-full" />
                                    </div>
                                    <div>
                                        <AppLabel>Max Quantity</AppLabel>
                                        <AppInputText v-model="extra.max_quantity" type="number" min="1" placeholder="Unlimited" />
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-4">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="extra.is_required" class="h-4 w-4 rounded text-red-500" />
                                        <span class="text-neutral-11 text-sm">Required</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="extra.is_active" class="h-4 w-4 rounded text-violet-500" />
                                        <span class="text-neutral-11 text-sm">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <AppButton type="button" class="btn btn-secondary" @click="addExtra">
                            <i class="ri-add-line mr-1"></i>
                            Add Another Extra
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Cancellation Policies Tab -->
        <div v-show="activeTab === 'cancellation'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-shield-check-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Cancellation Policies</span>
                            <p class="text-neutral-9 text-xs">Define refund rules for booking cancellations</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="form.cancellation_policies.length === 0" class="border-neutral-5 rounded-xl border-2 border-dashed py-12 text-center">
                        <i class="ri-shield-check-line text-neutral-7 mb-3 text-5xl"></i>
                        <h4 class="text-neutral-11 mb-1 font-medium">No cancellation policies</h4>
                        <p class="text-neutral-9 mb-4 text-sm">Add policies to define refund rules for cancellations</p>
                        <AppButton type="button" class="btn btn-primary" @click="addCancellationPolicy">
                            <i class="ri-add-line mr-1"></i>
                            Add First Policy
                        </AppButton>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(policy, index) in form.cancellation_policies"
                            :key="index"
                            class="border-neutral-5 bg-neutral-2/50 relative overflow-hidden rounded-xl border"
                        >
                            <div class="flex items-center justify-between border-b bg-gradient-to-r from-amber-600/10 to-orange-600/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                        <i class="ri-calendar-check-line"></i>
                                    </span>
                                    <span class="text-neutral-12 font-medium">{{ policy.name || 'Untitled Policy' }}</span>
                                    <span v-if="policy.is_default" class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Default</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeCancellationPolicy(index)"
                                    class="text-neutral-9 hover:text-red-600 transition-colors"
                                >
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>

                            <div class="p-4">
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <AppLabel>Policy Name <span class="text-red-500">*</span></AppLabel>
                                        <AppInputText v-model="policy.name" type="text" placeholder="e.g., Full Refund" />
                                    </div>
                                    <div>
                                        <AppLabel>Description</AppLabel>
                                        <AppInputText v-model="policy.description" type="text" placeholder="Policy description" />
                                    </div>
                                </div>
                                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <AppLabel>
                                            <i class="ri-calendar-line mr-1 text-teal-500"></i>
                                            Days Before Departure
                                        </AppLabel>
                                        <AppInputText v-model="policy.days_before_departure" type="number" min="0" placeholder="0" />
                                    </div>
                                    <div>
                                        <AppLabel>
                                            <i class="ri-percent-line mr-1 text-emerald-500"></i>
                                            Refund Percentage
                                        </AppLabel>
                                        <AppInputText v-model="policy.refund_percentage" type="number" min="0" max="100" step="0.01" placeholder="100" />
                                    </div>
                                    <div>
                                        <AppLabel>
                                            <i class="ri-money-dollar-circle-line mr-1 text-red-500"></i>
                                            Cancellation Fee (৳)
                                        </AppLabel>
                                        <AppInputText v-model="policy.cancellation_fee" type="number" min="0" step="0.01" placeholder="0.00" />
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-4">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="policy.is_default" class="h-4 w-4 rounded text-blue-500" />
                                        <span class="text-neutral-11 text-sm">Default Policy</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="policy.is_active" class="h-4 w-4 rounded text-amber-500" />
                                        <span class="text-neutral-11 text-sm">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <AppButton type="button" class="btn btn-secondary" @click="addCancellationPolicy">
                            <i class="ri-add-line mr-1"></i>
                            Add Another Policy
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Media Tab -->
        <div v-show="activeTab === 'media'">
            <!-- Thumbnail -->
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-image-2-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Featured Image</span>
                            <p class="text-neutral-9 text-xs">Main image shown in tour listings</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-col items-start gap-6 sm:flex-row">
                        <div class="shrink-0">
                            <div v-if="thumbnailPreview || props.tour?.thumbnail_url" class="relative">
                                <img
                                    :src="thumbnailPreview || props.tour?.thumbnail_url"
                                    class="border-neutral-5 h-48 w-64 rounded-xl border-2 object-cover shadow-lg"
                                    alt="Thumbnail preview"
                                />
                                <button
                                    type="button"
                                    @click="removeThumbnail"
                                    class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-colors hover:bg-red-600"
                                >
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <div
                                v-else
                                class="border-neutral-5 flex h-48 w-64 items-center justify-center rounded-xl border-2 border-dashed bg-gradient-to-br from-blue-50 to-cyan-50"
                            >
                                <div class="text-center">
                                    <i class="ri-image-add-line text-neutral-7 text-4xl"></i>
                                    <p class="text-neutral-9 mt-2 text-sm">No image selected</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3">
                            <label class="cursor-pointer">
                                <span class="btn btn-secondary">
                                    <i class="ri-upload-2-line mr-2"></i>
                                    {{ thumbnailPreview || props.tour?.thumbnail_url ? 'Change Image' : 'Upload Image' }}
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleThumbnailChange"
                                />
                            </label>
                            <p class="text-neutral-9 text-xs">
                                <i class="ri-information-line mr-1"></i>
                                Recommended: 800x600 pixels, JPG or PNG
                            </p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Gallery -->
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/25">
                            <i class="ri-gallery-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">Photo Gallery</span>
                            <p class="text-neutral-9 text-xs">Additional images to showcase the tour</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div v-if="galleryPreviews.length > 0 || (props.tour?.gallery && props.tour.gallery.length > 0)" class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                        <!-- Existing gallery images -->
                        <div
                            v-for="image in props.tour?.gallery || []"
                            :key="'existing-' + image.id"
                            class="group relative aspect-[4/3] overflow-hidden rounded-xl"
                            v-show="!removedGalleryIds.includes(image.id)"
                        >
                            <img
                                :src="image.url"
                                class="h-full w-full object-cover transition-transform group-hover:scale-110"
                                :alt="image.name"
                            />
                            <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            <button
                                type="button"
                                @click="removeExistingGalleryImage(image.id)"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-lg transition-all hover:bg-red-600 group-hover:opacity-100"
                            >
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <!-- New gallery previews -->
                        <div
                            v-for="(preview, index) in galleryPreviews"
                            :key="'new-' + index"
                            class="group relative aspect-[4/3] overflow-hidden rounded-xl ring-2 ring-emerald-400"
                        >
                            <img
                                :src="preview"
                                class="h-full w-full object-cover transition-transform group-hover:scale-110"
                                alt="New gallery image"
                            />
                            <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            <button
                                type="button"
                                @click="removeNewGalleryImage(index)"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-lg transition-all hover:bg-red-600 group-hover:opacity-100"
                            >
                                <i class="ri-close-line"></i>
                            </button>
                            <span class="absolute bottom-2 left-2 rounded-full bg-emerald-500 px-2 py-0.5 text-xs font-medium text-white shadow">New</span>
                        </div>
                    </div>

                    <div v-else class="border-neutral-5 mb-4 flex items-center justify-center rounded-xl border-2 border-dashed py-12">
                        <div class="text-center">
                            <i class="ri-gallery-line text-neutral-7 text-5xl"></i>
                            <p class="text-neutral-9 mt-2">No gallery images yet</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="cursor-pointer">
                            <span class="btn btn-secondary">
                                <i class="ri-image-add-line mr-2"></i>
                                Add Gallery Images
                            </span>
                            <input
                                type="file"
                                class="hidden"
                                accept="image/*"
                                multiple
                                @change="handleGalleryChange"
                            />
                        </label>
                        <p class="text-neutral-9 text-xs">
                            <i class="ri-information-line mr-1"></i>
                            You can select multiple images at once
                        </p>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- SEO Tab -->
        <div v-show="activeTab === 'seo'">
            <AppCard class="mb-4 w-full">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg shadow-rose-500/25">
                            <i class="ri-search-eye-line text-lg text-white"></i>
                        </div>
                        <div>
                            <span class="text-neutral-12 font-semibold">SEO Settings</span>
                            <p class="text-neutral-9 text-xs">Optimize how this tour appears in search engines</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <!-- Preview Card -->
                    <div class="border-neutral-5 bg-neutral-2/50 mb-6 rounded-xl border p-4">
                        <p class="text-neutral-9 mb-2 text-xs font-medium uppercase tracking-wider">Search Preview</p>
                        <div class="text-blue-700 text-lg font-medium">
                            {{ form.meta_title || form.name || 'Tour Title' }}
                        </div>
                        <div class="text-emerald-700 text-sm">
                            {{ siteUrl }}/tours/{{ form.slug || 'tour-slug' }}
                        </div>
                        <div class="text-neutral-10 mt-1 line-clamp-2 text-sm">
                            {{ form.meta_description || form.excerpt || 'Tour description will appear here...' }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <AppLabel for="meta_title">
                                <i class="ri-heading mr-1 text-blue-500"></i>
                                Meta Title
                            </AppLabel>
                            <AppInputText
                                id="meta_title"
                                v-model="form.meta_title"
                                type="text"
                                placeholder="Leave empty to use tour name"
                            />
                            <p class="text-neutral-9 mt-1 text-xs">
                                {{ (form.meta_title || form.name || '').length }}/60 characters (recommended)
                            </p>
                        </div>

                        <div>
                            <AppLabel for="meta_description">
                                <i class="ri-file-text-line mr-1 text-teal-500"></i>
                                Meta Description
                            </AppLabel>
                            <AppTextArea
                                id="meta_description"
                                v-model="form.meta_description"
                                rows="3"
                                placeholder="Leave empty to use short description"
                            />
                            <p class="text-neutral-9 mt-1 text-xs">
                                {{ (form.meta_description || form.excerpt || '').length }}/160 characters (recommended)
                            </p>
                        </div>

                        <div>
                            <AppLabel for="meta_keywords">
                                <i class="ri-price-tag-3-line mr-1 text-violet-500"></i>
                                Meta Keywords
                            </AppLabel>
                            <AppInputText
                                id="meta_keywords"
                                v-model="form.meta_keywords"
                                type="text"
                                placeholder="tour, travel, adventure, destination (comma separated)"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sticky Footer -->
        <div class="border-neutral-6 bg-neutral-1/95 sticky bottom-0 z-10 -mx-4 mt-6 border-t px-4 py-4 backdrop-blur-sm sm:-mx-6 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="text-neutral-9 hidden text-sm sm:block">
                    <i class="ri-information-line mr-1"></i>
                    {{ isEdit ? 'Editing existing tour' : 'Creating new tour' }}
                </div>
                <div class="flex gap-2">
                    <AppButton type="button" class="btn btn-secondary" @click="$inertia.visit(route('tour.index'))">
                        <i class="ri-close-line mr-1"></i>
                        Cancel
                    </AppButton>
                    <AppButton type="submit" class="btn btn-primary" :disabled="form.processing">
                        <i :class="form.processing ? 'ri-loader-4-line animate-spin' : 'ri-save-line'" class="mr-1"></i>
                        {{ form.processing ? 'Saving...' : 'Save Tour' }}
                    </AppButton>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('Tour')

const props = defineProps({
    tour: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    destinations: { type: Array, default: () => [] }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tours', href: route('tour.index') },
    { label: props.tour ? 'Edit Tour' : 'Create Tour', last: true }
]

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-information-line' },
    { id: 'pricing', label: 'Pricing', icon: 'ri-money-dollar-circle-line' },
    { id: 'itinerary', label: 'Itinerary', icon: 'ri-route-line' },
    { id: 'inclusions', label: 'Inclusions', icon: 'ri-checkbox-circle-line' },
    { id: 'highlights', label: 'Highlights', icon: 'ri-star-line' },
    { id: 'faqs', label: 'FAQs', icon: 'ri-question-line' },
    { id: 'extras', label: 'Extras', icon: 'ri-add-box-line' },
    { id: 'cancellation', label: 'Cancellation', icon: 'ri-shield-check-line' },
    { id: 'media', label: 'Media', icon: 'ri-image-line' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-eye-line' }
]

const activeTab = ref('basic')

// Site URL for SEO preview
const siteUrl = computed(() => {
    const page = usePage()
    return page.props?.app?.url || window.location.origin
})

const form = useForm({
    name: props.tour?.name || '',
    slug: props.tour?.slug || '',
    code: props.tour?.code || '',
    category_id: props.tour?.category_id || null,
    destination_id: props.tour?.destination_id || null,
    excerpt: props.tour?.excerpt || '',
    description: props.tour?.description || '',
    duration_days: props.tour?.duration_days || 1,
    duration_nights: props.tour?.duration_nights || 0,
    start_location: props.tour?.start_location || '',
    end_location: props.tour?.end_location || '',
    valid_from: props.tour?.valid_from || null,
    valid_to: props.tour?.valid_to || null,
    languages: props.tour?.languages || [],
    age_restrictions: props.tour?.age_restrictions || {},
    is_featured: props.tour?.is_featured ?? false,
    is_bestseller: props.tour?.is_bestseller ?? false,
    is_active: props.tour?.is_active ?? true,
    meta_title: props.tour?.meta_title || '',
    meta_description: props.tour?.meta_description || '',
    meta_keywords: props.tour?.meta_keywords || '',
    pricings: props.tour?.pricings || [],
    itineraries: props.tour?.itineraries || [],
    inclusions: props.tour?.inclusions || [],
    exclusions: props.tour?.exclusions || [],
    highlights: props.tour?.highlights || [],
    faqs: props.tour?.faqs || [],
    extras: props.tour?.extras || [],
    cancellation_policies: props.tour?.cancellation_policies || [],
    thumbnail: null,
    gallery: [],
    remove_thumbnail: false,
    remove_gallery_ids: []
})

const { isCreate, isEdit } = useFormContext()

// Options for extras price type
const priceTypeOptions = [
    { value: 'per_person', label: 'Per Person' },
    { value: 'per_booking', label: 'Per Booking' },
    { value: 'per_day', label: 'Per Day' }
]

// Languages input handling
const languagesInput = ref(props.tour?.languages?.join(', ') || '')

const generateSlug = () => {
    if (isCreate.value) {
        form.slug = form.name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '')
    }
}

const addPricing = () => {
    form.pricings.push({
        name: '',
        description: '',
        adult_price: 0,
        child_price: null,
        infant_price: null,
        is_active: true
    })
}

const removePricing = (index) => {
    form.pricings.splice(index, 1)
}

const addItinerary = () => {
    const nextDay = form.itineraries.length + 1
    form.itineraries.push({
        title: `Day ${nextDay}`,
        description: '',
        accommodation: '',
        meals_included: [],
        distance_km: null,
        activities: [],
        highlights: []
    })
}

const removeItinerary = (index) => {
    form.itineraries.splice(index, 1)
}

const addHighlight = () => {
    form.highlights.push({
        title: '',
        description: '',
        icon: ''
    })
}

const removeHighlight = (index) => {
    form.highlights.splice(index, 1)
}

const addFaq = () => {
    form.faqs.push({
        question: '',
        answer: '',
        is_active: true
    })
}

const removeFaq = (index) => {
    form.faqs.splice(index, 1)
}

const addExtra = () => {
    form.extras.push({
        name: '',
        description: '',
        price: 0,
        price_type: 'per_person',
        max_quantity: null,
        is_required: false,
        is_active: true
    })
}

const removeExtra = (index) => {
    form.extras.splice(index, 1)
}

const addCancellationPolicy = () => {
    form.cancellation_policies.push({
        name: '',
        description: '',
        days_before_departure: 0,
        refund_percentage: 100,
        cancellation_fee: 0,
        is_default: false,
        is_active: true
    })
}

const removeCancellationPolicy = (index) => {
    form.cancellation_policies.splice(index, 1)
}

const submitForm = () => {
    // Convert languages input to array
    form.languages = languagesInput.value.split(',').map(l => l.trim()).filter(l => l)
    
    // Filter out empty inclusions/exclusions
    form.inclusions = form.inclusions.filter(i => typeof i === 'string' ? i.trim() : i?.title?.trim())
    form.exclusions = form.exclusions.filter(e => typeof e === 'string' ? e.trim() : e?.title?.trim())

    if (isCreate.value) {
        form.post(route('tour.store'), {
            forceFormData: true
        })
    }
    if (isEdit.value) {
        form.transform(data => ({
            ...data,
            _method: 'PUT'
        })).post(route('tour.update', props.tour.id), {
            forceFormData: true
        })
    }
}

const { errorsFields } = useFormErrors()

// Media handling
const thumbnailPreview = ref(null)
const galleryPreviews = ref([])
const galleryFiles = ref([])
const removedGalleryIds = ref([])

const handleThumbnailChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.thumbnail = file
        form.remove_thumbnail = false
        thumbnailPreview.value = URL.createObjectURL(file)
    }
}

const removeThumbnail = () => {
    form.thumbnail = null
    form.remove_thumbnail = true
    thumbnailPreview.value = null
}

const handleGalleryChange = (event) => {
    const files = Array.from(event.target.files)
    files.forEach(file => {
        form.gallery.push(file)
        galleryPreviews.value.push(URL.createObjectURL(file))
    })
}

const removeNewGalleryImage = (index) => {
    form.gallery.splice(index, 1)
    galleryPreviews.value.splice(index, 1)
}

const removeExistingGalleryImage = (id) => {
    removedGalleryIds.value.push(id)
    form.remove_gallery_ids.push(id)
}
</script>
