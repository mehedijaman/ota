<template>
    <Head :title="title"></Head>
    
    <!-- Header with Actions -->
    <AppSectionHeader :title="'Booking #' + booking.booking_number" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap gap-2">
                <AppButton
                    v-if="booking.status === 'pending' && can('tour-booking-edit')"
                    class="btn btn-success"
                    @click="updateStatus('confirmed')"
                >
                    <i class="ri-check-line mr-1"></i> Confirm
                </AppButton>
                <AppButton
                    v-if="['pending', 'confirmed'].includes(booking.status) && can('tour-booking-edit')"
                    class="btn btn-destructive"
                    @click="showCancelModal = true"
                >
                    <i class="ri-close-line mr-1"></i> Cancel
                </AppButton>
                <AppButton
                    v-if="booking.status !== 'cancelled' && can('tour-booking-edit')"
                    class="btn btn-secondary"
                    @click="showStatusModal = true"
                >
                    <i class="ri-refresh-line mr-1"></i> Change Status
                </AppButton>
                <AppButton
                    v-if="can('tour-booking-edit')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tourBooking.edit', booking.id))"
                >
                    <i class="ri-edit-line mr-1"></i> Edit
                </AppButton>
                <AppButton
                    class="btn btn-secondary"
                    @click="handlePrint"
                >
                    <i class="ri-printer-line mr-1"></i> Print
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Quick Stats Overview -->
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- Total Amount -->
        <div class="border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/10 to-primary-600/5"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/30">
                    <i class="ri-money-dollar-circle-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Total Amount</p>
                    <p class="text-primary-11 mt-1 text-2xl font-bold">৳{{ formatNumber(booking.total_amount) }}</p>
                </div>
            </div>
        </div>

        <!-- Amount Paid -->
        <div class="border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30">
                    <i class="ri-check-double-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Paid</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ formatNumber(booking.amount_paid) }}</p>
                </div>
            </div>
        </div>

        <!-- Amount Due -->
        <div class="border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-red-600/5"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/30">
                    <i class="ri-error-warning-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Due</p>
                    <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">৳{{ formatNumber(booking.amount_due) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Travelers -->
        <div class="border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/5"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/30">
                    <i class="ri-group-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Travelers</p>
                    <p class="text-neutral-12 mt-1 text-2xl font-bold">{{ totalTravelers }}</p>
                    <p class="text-neutral-9 text-xs">{{ booking.adult_count }} adults, {{ booking.child_count || 0 }} children</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Tour Information -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-md">
                        <i class="ri-map-pin-line text-lg text-white"></i>
                    </div>
                    <h3 class="text-neutral-12 text-lg font-semibold">Tour Information</h3>
                </div>
                <div class="p-5">
                    <!-- Tour Name with Image -->
                    <div class="bg-neutral-2 mb-5 flex items-start gap-4 rounded-xl p-4">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg">
                            <i class="ri-map-2-line text-2xl text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-neutral-12 text-xl font-bold">{{ booking.tour?.name }}</h4>
                            <p v-if="booking.tour?.code" class="text-neutral-9 mt-1 font-mono text-sm">
                                Code: {{ booking.tour.code }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30">
                                <i class="ri-calendar-line text-lg text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Travel Date</label>
                                <p class="text-neutral-12 font-semibold">{{ formatDate(booking.travel_date) }}</p>
                            </div>
                        </div>

                        <div v-if="booking.pricing" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                <i class="ri-price-tag-3-line text-lg text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Pricing Plan</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.pricing.name }}</p>
                            </div>
                        </div>

                        <div class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                <i class="ri-user-line text-lg text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Adults</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.adult_count }}</p>
                            </div>
                        </div>

                        <div v-if="booking.child_count > 0" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                                <i class="ri-user-smile-line text-lg text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Children</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.child_count }}</p>
                            </div>
                        </div>

                        <div v-if="booking.infant_count > 0" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                                <i class="ri-user-heart-line text-lg text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Infants</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.infant_count }}</p>
                            </div>
                        </div>

                        <div v-if="booking.customer" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                                <i class="ri-user-star-line text-lg text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Customer</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.customer.name }}</p>
                            </div>
                        </div>

                        <div v-if="booking.agent" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-pink-100 dark:bg-pink-900/30">
                                <i class="ri-user-settings-line text-lg text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <div>
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Agent</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.agent.name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-teal-500 to-cyan-600 shadow-md">
                        <i class="ri-contacts-line text-lg text-white"></i>
                    </div>
                    <h3 class="text-neutral-12 text-lg font-semibold">Contact Information</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30">
                                <i class="ri-user-line text-lg text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Name</label>
                                <p class="text-neutral-12 truncate font-semibold">{{ booking.contact_name }}</p>
                            </div>
                        </div>

                        <div class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                <i class="ri-mail-line text-lg text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Email</label>
                                <p class="text-neutral-12 truncate font-semibold">{{ booking.contact_email }}</p>
                            </div>
                        </div>

                        <div v-if="booking.contact_phone" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                                <i class="ri-phone-line text-lg text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Phone</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.contact_phone }}</p>
                            </div>
                        </div>

                        <div v-if="booking.whatsapp" class="bg-neutral-2 flex items-center gap-3 rounded-xl p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                                <i class="ri-whatsapp-line text-lg text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">WhatsApp</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.whatsapp }}</p>
                            </div>
                        </div>

                        <div v-if="booking.contact_address" class="bg-neutral-2 flex items-start gap-3 rounded-xl p-4 md:col-span-2">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                                <i class="ri-map-pin-line text-lg text-orange-600 dark:text-orange-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Address</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.contact_address }}</p>
                            </div>
                        </div>

                        <div v-if="booking.emergency_contact" class="flex items-center gap-3 rounded-xl border-2 border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                                <i class="ri-alarm-warning-line text-lg text-red-600 dark:text-red-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-xs font-medium uppercase tracking-wide text-red-600 dark:text-red-400">Emergency Contact</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.emergency_contact }}</p>
                            </div>
                        </div>

                        <div v-if="booking.emergency_phone" class="flex items-center gap-3 rounded-xl border-2 border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                                <i class="ri-phone-line text-lg text-red-600 dark:text-red-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-xs font-medium uppercase tracking-wide text-red-600 dark:text-red-400">Emergency Phone</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.emergency_phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Travelers -->
            <div v-if="booking.travelers?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md">
                            <i class="ri-group-line text-lg text-white"></i>
                        </div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Traveler Details</h3>
                    </div>
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ booking.travelers.length }} {{ booking.travelers.length === 1 ? 'Traveler' : 'Travelers' }}
                    </span>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div
                        v-for="(traveler, index) in booking.travelers"
                        :key="traveler.id"
                        class="py-5 first:pt-0 last:pb-0"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-slate-600 to-slate-700 text-lg font-bold text-white shadow-md">
                                    {{ traveler.first_name?.charAt(0) }}{{ traveler.last_name?.charAt(0) }}
                                </div>
                                <div>
                                    <h4 class="text-neutral-12 text-lg font-bold">
                                        {{ traveler.first_name }} {{ traveler.last_name }}
                                    </h4>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span :class="getTravelerTypeBadge(traveler.type)" class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1">
                                            {{ traveler.type }}
                                        </span>
                                        <span v-if="traveler.is_lead" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800">
                                            <i class="ri-star-fill"></i> Lead
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-neutral-2 grid grid-cols-2 gap-3 rounded-xl p-4 text-sm md:grid-cols-3 lg:grid-cols-4">
                            <div v-if="traveler.email" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Email</span>
                                <p class="text-neutral-12 truncate font-medium">{{ traveler.email }}</p>
                            </div>
                            <div v-if="traveler.phone" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Phone</span>
                                <p class="text-neutral-12 font-medium">{{ traveler.phone }}</p>
                            </div>
                            <div v-if="traveler.date_of_birth" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Date of Birth</span>
                                <p class="text-neutral-12 font-medium">{{ formatDate(traveler.date_of_birth) }}</p>
                            </div>
                            <div v-if="traveler.gender" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Gender</span>
                                <p class="text-neutral-12 font-medium capitalize">{{ traveler.gender }}</p>
                            </div>
                            <div v-if="traveler.nationality" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Nationality</span>
                                <p class="text-neutral-12 font-medium">{{ traveler.nationality }}</p>
                            </div>
                            <div v-if="traveler.passport_number" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Passport</span>
                                <p class="text-neutral-12 font-mono font-medium">{{ traveler.passport_number }}</p>
                            </div>
                            <div v-if="traveler.passport_expiry" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">Passport Expiry</span>
                                <p class="text-neutral-12 font-medium">{{ formatDate(traveler.passport_expiry) }}</p>
                            </div>
                            <div v-if="traveler.nid_number" class="flex flex-col gap-1">
                                <span class="text-neutral-9 text-xs uppercase tracking-wide">NID</span>
                                <p class="text-neutral-12 font-mono font-medium">{{ traveler.nid_number }}</p>
                            </div>
                        </div>

                        <div v-if="traveler.dietary_requirements || traveler.medical_conditions || traveler.special_requests" class="mt-4 space-y-2">
                            <div v-if="traveler.dietary_requirements" class="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/20">
                                <i class="ri-restaurant-line mt-0.5 text-green-600"></i>
                                <div>
                                    <span class="text-xs font-medium text-green-700 dark:text-green-300">Dietary:</span>
                                    <p class="text-neutral-11 text-sm">{{ traveler.dietary_requirements }}</p>
                                </div>
                            </div>
                            <div v-if="traveler.medical_conditions" class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                                <i class="ri-heart-pulse-line mt-0.5 text-red-600"></i>
                                <div>
                                    <span class="text-xs font-medium text-red-700 dark:text-red-300">Medical:</span>
                                    <p class="text-neutral-11 text-sm">{{ traveler.medical_conditions }}</p>
                                </div>
                            </div>
                            <div v-if="traveler.special_requests" class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/20">
                                <i class="ri-message-3-line mt-0.5 text-blue-600"></i>
                                <div>
                                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Requests:</span>
                                    <p class="text-neutral-11 text-sm">{{ traveler.special_requests }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Extras -->
            <div v-if="booking.extras?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-pink-500 to-rose-600 shadow-md">
                            <i class="ri-gift-line text-lg text-white"></i>
                        </div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Tour Extras</h3>
                    </div>
                    <span class="rounded-full bg-pink-100 px-3 py-1 text-sm font-medium text-pink-700 dark:bg-pink-900/30 dark:text-pink-300">
                        {{ booking.extras.length }} {{ booking.extras.length === 1 ? 'Item' : 'Items' }}
                    </span>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div
                        v-for="extra in booking.extras"
                        :key="extra.id"
                        class="flex items-center justify-between py-4 first:pt-0 last:pb-0"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-pink-100 to-rose-100 dark:from-pink-900/30 dark:to-rose-900/30">
                                <i class="ri-gift-line text-xl text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <div>
                                <p class="text-neutral-12 font-semibold">{{ extra.extra?.name }}</p>
                                <p class="text-neutral-9 text-sm">{{ extra.quantity }} × ৳{{ formatNumber(extra.unit_price) }}</p>
                            </div>
                        </div>
                        <p class="text-xl font-bold text-pink-600 dark:text-pink-400">৳{{ formatNumber(extra.total_price) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div v-if="booking.payments?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md">
                            <i class="ri-bank-card-line text-lg text-white"></i>
                        </div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Payment History</h3>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        {{ booking.payments.length }} {{ booking.payments.length === 1 ? 'Payment' : 'Payments' }}
                    </span>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div
                        v-for="payment in booking.payments"
                        :key="payment.id"
                        class="py-4 first:pt-0 last:pb-0"
                    >
                        <div class="mb-3 flex items-start justify-between">
                            <div>
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ formatNumber(payment.amount) }}</p>
                                <p class="text-neutral-9 mt-1 flex items-center gap-1 text-sm">
                                    <i class="ri-calendar-line"></i>
                                    {{ formatDateTime(payment.paid_at || payment.created_at) }}
                                </p>
                            </div>
                            <span :class="getPaymentStatusClass(payment.status)" class="rounded-full px-3 py-1 text-xs font-medium">
                                {{ formatStatus(payment.status) }}
                            </span>
                        </div>
                        <div class="bg-neutral-2 grid grid-cols-2 gap-4 rounded-xl p-4 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="ri-bank-card-line text-neutral-9"></i>
                                <div>
                                    <span class="text-neutral-9 text-xs">Method</span>
                                    <p class="text-neutral-12 font-medium capitalize">{{ payment.payment_method }}</p>
                                </div>
                            </div>
                            <div v-if="payment.transaction_id" class="flex items-center gap-2">
                                <i class="ri-hashtag text-neutral-9"></i>
                                <div>
                                    <span class="text-neutral-9 text-xs">Transaction ID</span>
                                    <p class="text-neutral-12 font-mono text-xs font-medium">{{ payment.transaction_id }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="payment.notes" class="border-neutral-4 text-neutral-11 mt-3 border-t pt-3 text-sm">
                            {{ payment.notes }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Requests & Requirements -->
            <div v-if="booking.special_requests || booking.dietary_requirements || booking.medical_conditions" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 shadow-md">
                        <i class="ri-file-list-line text-lg text-white"></i>
                    </div>
                    <h3 class="text-neutral-12 text-lg font-semibold">Special Requests & Requirements</h3>
                </div>
                <div class="space-y-4 p-5">
                    <div v-if="booking.special_requests" class="rounded-xl border-2 border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                        <div class="flex items-start gap-3">
                            <i class="ri-message-3-line mt-0.5 text-xl text-blue-600"></i>
                            <div class="flex-1">
                                <label class="text-sm font-semibold text-blue-700 dark:text-blue-300">Special Requests</label>
                                <p class="text-neutral-11 mt-1">{{ booking.special_requests }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="booking.dietary_requirements" class="rounded-xl border-2 border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                        <div class="flex items-start gap-3">
                            <i class="ri-restaurant-line mt-0.5 text-xl text-green-600"></i>
                            <div class="flex-1">
                                <label class="text-sm font-semibold text-green-700 dark:text-green-300">Dietary Requirements</label>
                                <p class="text-neutral-11 mt-1">{{ booking.dietary_requirements }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-if="booking.medical_conditions" class="rounded-xl border-2 border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div class="flex items-start gap-3">
                            <i class="ri-heart-pulse-line mt-0.5 text-xl text-red-600"></i>
                            <div class="flex-1">
                                <label class="text-sm font-semibold text-red-700 dark:text-red-300">Medical Conditions</label>
                                <p class="text-neutral-11 mt-1">{{ booking.medical_conditions }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-slate-600 to-slate-700 shadow-md">
                        <i class="ri-information-line text-lg text-white"></i>
                    </div>
                    <h3 class="text-neutral-12 text-lg font-semibold">Booking Status</h3>
                </div>
                <div class="space-y-4 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-9 font-medium">Status</span>
                        <span :class="getStatusClass(booking.status)" class="rounded-full px-3 py-1 text-sm font-semibold">
                            {{ formatStatus(booking.status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-9 font-medium">Payment</span>
                        <span :class="getPaymentStatusClass(booking.payment_status)" class="rounded-full px-3 py-1 text-sm font-semibold">
                            {{ formatStatus(booking.payment_status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-9 font-medium">Source</span>
                        <span class="text-neutral-12 font-semibold capitalize">{{ booking.booking_source }}</span>
                    </div>
                    <hr class="border-neutral-4" />
                    <div class="space-y-3 text-sm">
                        <div class="text-neutral-9 flex items-center gap-3">
                            <i class="ri-calendar-check-line text-lg"></i>
                            <div class="flex-1">
                                <p class="text-xs uppercase tracking-wide">Booked On</p>
                                <p class="text-neutral-12 font-semibold">{{ formatDateTime(booking.created_at) }}</p>
                            </div>
                        </div>
                        <div v-if="booking.confirmed_at" class="flex items-center gap-3 text-emerald-600 dark:text-emerald-400">
                            <i class="ri-checkbox-circle-line text-lg"></i>
                            <div class="flex-1">
                                <p class="text-xs uppercase tracking-wide">Confirmed</p>
                                <p class="font-semibold">{{ formatDateTime(booking.confirmed_at) }}</p>
                            </div>
                        </div>
                        <div v-if="booking.cancelled_at" class="flex items-center gap-3 text-red-600 dark:text-red-400">
                            <i class="ri-close-circle-line text-lg"></i>
                            <div class="flex-1">
                                <p class="text-xs uppercase tracking-wide">Cancelled</p>
                                <p class="font-semibold">{{ formatDateTime(booking.cancelled_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
                        <i class="ri-calculator-line text-lg text-white"></i>
                    </div>
                    <h3 class="text-neutral-12 text-lg font-semibold">Price Breakdown</h3>
                </div>
                <div class="space-y-3 p-5">
                    <div class="text-neutral-11 flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold">৳{{ formatNumber(booking.subtotal) }}</span>
                    </div>
                    <div v-if="booking.extras_total > 0" class="text-neutral-11 flex justify-between">
                        <span>Extras</span>
                        <span class="font-semibold">৳{{ formatNumber(booking.extras_total) }}</span>
                    </div>
                    <div v-if="booking.discount_amount > 0" class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>Discount</span>
                        <span class="font-semibold">-৳{{ formatNumber(booking.discount_amount) }}</span>
                    </div>
                    <div v-if="booking.discount_code" class="text-neutral-9 flex justify-between text-xs">
                        <span>Code:</span>
                        <span class="font-mono">{{ booking.discount_code }}</span>
                    </div>
                    <div v-if="booking.tax_amount > 0" class="text-neutral-11 flex justify-between">
                        <span>Tax</span>
                        <span class="font-semibold">৳{{ formatNumber(booking.tax_amount) }}</span>
                    </div>
                    <hr class="border-neutral-4" />
                    <div class="flex justify-between text-lg font-bold">
                        <span class="text-neutral-12">Total</span>
                        <span class="text-primary-11">৳{{ formatNumber(booking.total_amount) }}</span>
                    </div>
                    <hr class="border-neutral-4" />
                    <div class="flex justify-between font-semibold text-emerald-600 dark:text-emerald-400">
                        <span>Paid</span>
                        <span>৳{{ formatNumber(booking.amount_paid) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-red-600 dark:text-red-400">
                        <span>Due</span>
                        <span>৳{{ formatNumber(booking.amount_due) }}</span>
                    </div>
                </div>
            </div>

            <!-- Internal Notes -->
            <div v-if="booking.internal_notes" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-yellow-600 shadow-md">
                        <i class="ri-sticky-note-line text-lg text-white"></i>
                    </div>
                    <h3 class="text-neutral-12 text-lg font-semibold">Internal Notes</h3>
                </div>
                <div class="p-5">
                    <div class="rounded-xl border-2 border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                        <p class="text-neutral-11 whitespace-pre-wrap text-sm">{{ booking.internal_notes }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Reason Modal -->
    <AppModal v-model="showCancelModal" title="Cancel Booking" max-width="md">
        <div class="space-y-4">
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-sm text-red-700 dark:text-red-300">
                    <i class="ri-error-warning-line mr-1"></i>
                    Are you sure you want to cancel this booking? This action cannot be undone.
                </p>
            </div>
            
            <div>
                <AppLabel for="cancel_reason" required>Cancellation Reason</AppLabel>
                <AppTextArea
                    v-model="cancelReason"
                    rows="4"
                    placeholder="Please provide a reason for cancelling this booking..."
                    :error="cancelReasonError"
                />
            </div>
        </div>
        
        <template #footer>
            <div class="flex justify-end gap-3">
                <AppButton
                    class="btn btn-secondary"
                    @click="showCancelModal = false"
                >
                    Close
                </AppButton>
                <AppButton
                    class="btn btn-destructive"
                    @click="confirmCancel"
                    :disabled="!cancelReason.trim()"
                >
                    <i class="ri-close-circle-line mr-1"></i> Confirm Cancellation
                </AppButton>
            </div>
        </template>
    </AppModal>

    <!-- Status Change Modal -->
    <AppModal v-model="showStatusModal" title="Change Booking Status" max-width="md">
        <div class="space-y-4">
            <div>
                <AppLabel for="new_status" required>Select New Status</AppLabel>
                <AppSelect
                    v-model="newStatus"
                    :options="statusChangeOptions"
                    placeholder="Choose status..."
                />
            </div>
            
            <div v-if="newStatus">
                <AppLabel for="status_notes">Notes (Optional)</AppLabel>
                <AppTextArea
                    v-model="statusNotes"
                    rows="3"
                    placeholder="Add any notes about this status change..."
                />
            </div>

            <div v-if="newStatus" class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <i class="ri-information-line mr-1"></i>
                    The booking status will be changed to <strong class="capitalize">{{ newStatus }}</strong>.
                </p>
            </div>
        </div>
        
        <template #footer>
            <div class="flex justify-end gap-3">
                <AppButton
                    class="btn btn-secondary"
                    @click="showStatusModal = false"
                >
                    Close
                </AppButton>
                <AppButton
                    class="btn btn-primary"
                    @click="confirmStatusChange"
                    :disabled="!newStatus"
                >
                    <i class="ri-check-line mr-1"></i> Update Status
                </AppButton>
            </div>
        </template>
    </AppModal>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Booking Details')

const props = defineProps({
    booking: {
        type: Object,
        required: true
    }
})

// Modal states
const showCancelModal = ref(false)
const showStatusModal = ref(false)
const cancelReason = ref('')
const cancelReasonError = ref('')
const newStatus = ref('')
const statusNotes = ref('')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', href: route('tourBooking.index') },
    { label: props.booking.booking_number, last: true }
]

const totalTravelers = computed(() => 
    (props.booking.adult_count || 0) + (props.booking.child_count || 0) + (props.booking.infant_count || 0)
)

const formatNumber = (num) => new Intl.NumberFormat('en-BD').format(num || 0)
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'
const formatDateTime = (date) => date ? new Date(date).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'
const formatStatus = (status) => status?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) || '-'

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
        processing: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        confirmed: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
        completed: 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300',
        cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
        refunded: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
    }
    return classes[status] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getPaymentStatusClass = (status) => {
    const classes = {
        unpaid: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
        pending: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
        partial: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        paid: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
        refunded: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
    }
    return classes[status] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getTravelerTypeBadge = (type) => {
    const badges = {
        adult: 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800',
        child: 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800',
        infant: 'bg-green-50 text-green-700 ring-green-200 dark:bg-green-900/30 dark:text-green-300 dark:ring-green-800',
    }
    return badges[type] || 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700'
}

const statusChangeOptions = computed(() => {
    const allStatuses = [
        { value: 'pending', label: 'Pending' },
        { value: 'processing', label: 'Processing' },
        { value: 'confirmed', label: 'Confirmed' },
        { value: 'completed', label: 'Completed' },
    ]
    return allStatuses.filter(s => s.value !== props.booking.status)
})

const handlePrint = () => {
    window.print()
}

const updateStatus = (status) => {
    router.patch(route('tourBooking.updateStatus', [props.booking.id, status]))
}

const confirmCancel = () => {
    if (!cancelReason.value.trim()) {
        cancelReasonError.value = 'Please provide a cancellation reason'
        return
    }
    
    router.patch(
        route('tourBooking.updateStatus', [props.booking.id, 'cancelled']),
        { reason: cancelReason.value },
        {
            onSuccess: () => {
                showCancelModal.value = false
                cancelReason.value = ''
                cancelReasonError.value = ''
            }
        }
    )
}

const confirmStatusChange = () => {
    if (!newStatus.value) return
    
    router.patch(
        route('tourBooking.updateStatus', [props.booking.id, newStatus.value]),
        { notes: statusNotes.value },
        {
            onSuccess: () => {
                showStatusModal.value = false
                newStatus.value = ''
                statusNotes.value = ''
            }
        }
    )
}
</script>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
