<template>
    <Head :title="title"></Head>
    
    <!-- Header with Actions -->
    <AppSectionHeader :title="'Booking #' + booking.booking_number" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap gap-2">
                <AppButton
                    v-if="booking.payment_status !== 'paid' && booking.status !== 'cancelled' && can('tour-booking-edit')"
                    class="btn btn-primary"
                    @click="openPaymentModal"
                >
                    <i class="ri-bank-card-line mr-1"></i> Add Payment
                </AppButton>
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

    <!-- Booking Hero Card -->
    <div class="border-neutral-6 bg-neutral-1 relative mb-6 overflow-hidden rounded-2xl border shadow-lg print:shadow-none">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 via-transparent to-purple-500/5"></div>
        <div class="absolute right-0 top-0 h-64 w-64 translate-x-1/3 -translate-y-1/3 rounded-full bg-gradient-to-br from-primary-500/10 to-purple-500/10 blur-3xl"></div>
        
        <div class="relative p-6 lg:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <!-- Left: Tour Info -->
                <div class="flex items-start gap-5">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-xl shadow-primary-500/30 ring-4 ring-white dark:ring-neutral-800">
                        <i class="ri-plane-line text-4xl text-white"></i>
                    </div>
                    <div>
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span :class="[getStatusClass(booking.status), getStatusRing(booking.status)]" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold ring-1 ring-inset">
                                <span class="h-1.5 w-1.5 rounded-full" :class="getStatusDot(booking.status)"></span>
                                {{ formatStatus(booking.status) }}
                            </span>
                            <span :class="[getPaymentStatusClass(booking.payment_status), getPaymentStatusRing(booking.payment_status)]" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold ring-1 ring-inset">
                                <i class="ri-wallet-3-line text-xs"></i>
                                {{ formatStatus(booking.payment_status) }}
                            </span>
                        </div>
                        <h2 class="text-neutral-12 text-2xl font-bold lg:text-3xl">{{ booking.tour?.name }}</h2>
                        <div class="text-neutral-9 mt-2 flex flex-wrap items-center gap-4 text-sm">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-calendar-event-line"></i>
                                {{ formatDate(booking.travel_date) }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="ri-team-line"></i>
                                {{ totalTravelers }} {{ totalTravelers === 1 ? 'Traveler' : 'Travelers' }}
                            </span>
                            <span v-if="booking.tour?.code" class="flex items-center gap-1.5 font-mono">
                                <i class="ri-barcode-line"></i>
                                {{ booking.tour.code }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Number & QR Placeholder -->
                <div class="bg-neutral-2 flex flex-col items-center rounded-xl border border-dashed border-neutral-300 p-4 text-center dark:border-neutral-700">
                    <p class="text-neutral-9 mb-1 text-xs font-medium uppercase tracking-wider">Booking Reference</p>
                    <p class="font-mono text-2xl font-bold tracking-wide text-primary-600 dark:text-primary-400">{{ booking.booking_number }}</p>
                    <p class="text-neutral-9 mt-2 text-xs">{{ formatDateTime(booking.created_at) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- Total Amount -->
        <div class="group border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/10 to-primary-600/5 transition-opacity group-hover:opacity-75"></div>
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-primary-500/10 blur-2xl transition-all group-hover:scale-150"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/30 transition-transform group-hover:scale-110">
                    <i class="ri-money-dollar-circle-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Total Amount</p>
                    <p class="text-primary-11 mt-1 text-2xl font-bold">৳{{ formatNumber(booking.total_amount) }}</p>
                </div>
            </div>
        </div>

        <!-- Amount Paid -->
        <div class="group border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 transition-opacity group-hover:opacity-75"></div>
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl transition-all group-hover:scale-150"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30 transition-transform group-hover:scale-110">
                    <i class="ri-check-double-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Paid</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ formatNumber(booking.amount_paid) }}</p>
                </div>
            </div>
        </div>

        <!-- Amount Due -->
        <div class="group border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-red-600/5 transition-opacity group-hover:opacity-75"></div>
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-red-500/10 blur-2xl transition-all group-hover:scale-150"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/30 transition-transform group-hover:scale-110">
                    <i class="ri-error-warning-line text-2xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 text-sm font-medium">Due</p>
                    <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">৳{{ formatNumber(booking.amount_due) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Travelers -->
        <div class="group border-neutral-6 bg-neutral-1 relative overflow-hidden rounded-xl border shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/5 transition-opacity group-hover:opacity-75"></div>
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl transition-all group-hover:scale-150"></div>
            <div class="relative flex items-center gap-4 p-5">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/30 transition-transform group-hover:scale-110">
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
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/25">
                            <i class="ri-map-pin-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Tour Information</h3>
                            <p class="text-neutral-9 text-xs">Package details and traveler counts</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <div class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 transition-transform group-hover:scale-110 dark:bg-primary-900/30">
                                <i class="ri-calendar-line text-lg text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Travel Date</label>
                            <p class="text-neutral-12 mt-0.5 font-semibold">{{ formatDate(booking.travel_date) }}</p>
                        </div>

                        <div v-if="booking.pricing" class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 transition-transform group-hover:scale-110 dark:bg-purple-900/30">
                                <i class="ri-price-tag-3-line text-lg text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Pricing Plan</label>
                            <p class="text-neutral-12 mt-0.5 font-semibold">{{ booking.pricing.name }}</p>
                        </div>

                        <div class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 transition-transform group-hover:scale-110 dark:bg-blue-900/30">
                                <i class="ri-user-line text-lg text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Adults</label>
                            <p class="text-neutral-12 mt-0.5 text-xl font-bold">{{ booking.adult_count }}</p>
                        </div>

                        <div v-if="booking.child_count > 0" class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 transition-transform group-hover:scale-110 dark:bg-amber-900/30">
                                <i class="ri-user-smile-line text-lg text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Children</label>
                            <p class="text-neutral-12 mt-0.5 text-xl font-bold">{{ booking.child_count }}</p>
                        </div>

                        <div v-if="booking.infant_count > 0" class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 transition-transform group-hover:scale-110 dark:bg-green-900/30">
                                <i class="ri-user-heart-line text-lg text-green-600 dark:text-green-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Infants</label>
                            <p class="text-neutral-12 mt-0.5 text-xl font-bold">{{ booking.infant_count }}</p>
                        </div>

                        <div v-if="booking.customer" class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 transition-transform group-hover:scale-110 dark:bg-indigo-900/30">
                                <i class="ri-user-star-line text-lg text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Customer</label>
                            <p class="text-neutral-12 mt-0.5 truncate font-semibold">{{ booking.customer.name }}</p>
                        </div>

                        <div v-if="booking.agent" class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-pink-100 transition-transform group-hover:scale-110 dark:bg-pink-900/30">
                                <i class="ri-user-settings-line text-lg text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Agent</label>
                            <p class="text-neutral-12 mt-0.5 truncate font-semibold">{{ booking.agent.name }}</p>
                        </div>

                        <div class="bg-neutral-2 group rounded-xl p-4 transition-all hover:bg-neutral-3">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 transition-transform group-hover:scale-110 dark:bg-cyan-900/30">
                                <i class="ri-global-line text-lg text-cyan-600 dark:text-cyan-400"></i>
                            </div>
                            <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Source</label>
                            <p class="text-neutral-12 mt-0.5 font-semibold capitalize">{{ booking.booking_source }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 shadow-lg shadow-teal-500/25">
                            <i class="ri-contacts-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Contact Information</h3>
                            <p class="text-neutral-9 text-xs">Primary contact and emergency details</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <!-- Primary Contact -->
                    <div class="bg-neutral-2 mb-4 rounded-xl p-4">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-user-line text-primary-600 dark:text-primary-400"></i>
                            <span class="text-neutral-9 text-sm font-medium">Primary Contact</span>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-sm font-bold text-white shadow-md">
                                    {{ booking.contact_name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Name</label>
                                    <p class="text-neutral-12 truncate font-semibold">{{ booking.contact_name }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                    <i class="ri-mail-line text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Email</label>
                                    <p class="text-neutral-12 truncate font-semibold">{{ booking.contact_email }}</p>
                                </div>
                            </div>

                            <div v-if="booking.contact_phone" class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                    <i class="ri-phone-line text-green-600 dark:text-green-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Phone</label>
                                    <p class="text-neutral-12 font-semibold">{{ booking.contact_phone }}</p>
                                </div>
                            </div>

                            <div v-if="booking.whatsapp" class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                                    <i class="ri-whatsapp-line text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">WhatsApp</label>
                                    <p class="text-neutral-12 font-semibold">{{ booking.whatsapp }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="booking.contact_address" class="border-neutral-4 mt-4 flex items-start gap-3 border-t pt-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30">
                                <i class="ri-map-pin-line text-orange-600 dark:text-orange-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="text-neutral-9 text-xs font-medium uppercase tracking-wide">Address</label>
                                <p class="text-neutral-12 font-semibold">{{ booking.contact_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div v-if="booking.emergency_contact || booking.emergency_phone" class="overflow-hidden rounded-xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 dark:border-red-800 dark:from-red-900/20 dark:to-rose-900/20">
                        <div class="border-b border-red-200 bg-red-100/50 px-4 py-2 dark:border-red-800 dark:bg-red-900/30">
                            <div class="flex items-center gap-2">
                                <i class="ri-alarm-warning-line text-red-600 dark:text-red-400"></i>
                                <span class="text-sm font-semibold text-red-700 dark:text-red-300">Emergency Contact</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2">
                            <div v-if="booking.emergency_contact" class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                                    <i class="ri-user-heart-line text-red-600 dark:text-red-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label class="text-xs font-medium uppercase tracking-wide text-red-600 dark:text-red-400">Name</label>
                                    <p class="text-neutral-12 font-semibold">{{ booking.emergency_contact }}</p>
                                </div>
                            </div>

                            <div v-if="booking.emergency_phone" class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                                    <i class="ri-phone-line text-red-600 dark:text-red-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <label class="text-xs font-medium uppercase tracking-wide text-red-600 dark:text-red-400">Phone</label>
                                    <p class="text-neutral-12 font-semibold">{{ booking.emergency_phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Travelers -->
            <div v-if="booking.travelers?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25">
                            <i class="ri-group-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Traveler Details</h3>
                            <p class="text-neutral-9 text-xs">Personal and passport information</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-800">
                        <i class="ri-user-line text-xs"></i>
                        {{ booking.travelers.length }} {{ booking.travelers.length === 1 ? 'Traveler' : 'Travelers' }}
                    </span>
                </div>
                <div class="divide-neutral-4 divide-y p-5">
                    <div
                        v-for="(traveler, index) in booking.travelers"
                        :key="traveler.id"
                        class="py-5 first:pt-0 last:pb-0"
                    >
                        <!-- Traveler Header -->
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div :class="getTravelerAvatarGradient(traveler.type)" class="flex h-14 w-14 items-center justify-center rounded-full text-lg font-bold text-white shadow-lg ring-4 ring-white dark:ring-neutral-800">
                                        {{ traveler.first_name?.charAt(0) }}{{ traveler.last_name?.charAt(0) }}
                                    </div>
                                    <span v-if="traveler.is_lead" class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 shadow-md ring-2 ring-white dark:ring-neutral-800">
                                        <i class="ri-star-fill text-xs text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-neutral-12 text-lg font-bold">
                                        {{ traveler.first_name }} {{ traveler.last_name }}
                                    </h4>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span :class="getTravelerTypeBadge(traveler.type)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize ring-1 ring-inset">
                                            <i :class="getTravelerTypeIcon(traveler.type)" class="text-[10px]"></i>
                                            {{ traveler.type }}
                                        </span>
                                        <span v-if="traveler.is_lead" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800">
                                            Lead Traveler
                                        </span>
                                        <span v-if="traveler.gender" class="text-neutral-9 flex items-center gap-1 text-xs capitalize">
                                            <i class="ri-genderless-line"></i> {{ traveler.gender }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-neutral-9 hidden text-sm font-medium sm:block">#{{ index + 1 }}</span>
                        </div>

                        <!-- Traveler Info Grid -->
                        <div class="bg-neutral-2 grid grid-cols-2 gap-4 rounded-xl p-4 text-sm md:grid-cols-3 lg:grid-cols-4">
                            <div v-if="traveler.email" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-mail-line"></i> Email
                                </span>
                                <p class="text-neutral-12 truncate font-medium">{{ traveler.email }}</p>
                            </div>
                            <div v-if="traveler.phone" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-phone-line"></i> Phone
                                </span>
                                <p class="text-neutral-12 font-medium">{{ traveler.phone }}</p>
                            </div>
                            <div v-if="traveler.date_of_birth" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-cake-2-line"></i> Date of Birth
                                </span>
                                <p class="text-neutral-12 font-medium">{{ formatDate(traveler.date_of_birth) }}</p>
                            </div>
                            <div v-if="traveler.nationality" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-global-line"></i> Nationality
                                </span>
                                <p class="text-neutral-12 font-medium">{{ traveler.nationality }}</p>
                            </div>
                            <div v-if="traveler.passport_number" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-passport-line"></i> Passport
                                </span>
                                <p class="text-neutral-12 font-mono font-medium">{{ traveler.passport_number }}</p>
                            </div>
                            <div v-if="traveler.passport_expiry" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-calendar-check-line"></i> Passport Expiry
                                </span>
                                <p class="text-neutral-12 font-medium">{{ formatDate(traveler.passport_expiry) }}</p>
                            </div>
                            <div v-if="traveler.nid_number" class="space-y-1">
                                <span class="text-neutral-9 flex items-center gap-1.5 text-xs uppercase tracking-wide">
                                    <i class="ri-id-card-line"></i> NID
                                </span>
                                <p class="text-neutral-12 font-mono font-medium">{{ traveler.nid_number }}</p>
                            </div>
                        </div>

                        <!-- Traveler Notes -->
                        <div v-if="traveler.dietary_requirements || traveler.medical_conditions || traveler.special_requests" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div v-if="traveler.dietary_requirements" class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/20">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                                    <i class="ri-restaurant-line text-green-600 dark:text-green-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-semibold text-green-700 dark:text-green-300">Dietary</span>
                                    <p class="text-neutral-11 mt-0.5 text-sm">{{ traveler.dietary_requirements }}</p>
                                </div>
                            </div>
                            <div v-if="traveler.medical_conditions" class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                                    <i class="ri-heart-pulse-line text-red-600 dark:text-red-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-semibold text-red-700 dark:text-red-300">Medical</span>
                                    <p class="text-neutral-11 mt-0.5 text-sm">{{ traveler.medical_conditions }}</p>
                                </div>
                            </div>
                            <div v-if="traveler.special_requests" class="flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/20">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                    <i class="ri-message-3-line text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-300">Requests</span>
                                    <p class="text-neutral-11 mt-0.5 text-sm">{{ traveler.special_requests }}</p>
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
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 shadow-lg shadow-pink-500/25">
                            <i class="ri-gift-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Tour Extras</h3>
                            <p class="text-neutral-9 text-xs">Additional services and add-ons</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-pink-100 px-3 py-1 text-sm font-semibold text-pink-700 ring-1 ring-inset ring-pink-200 dark:bg-pink-900/30 dark:text-pink-300 dark:ring-pink-800">
                        <i class="ri-add-circle-line text-xs"></i>
                        {{ booking.extras.length }} {{ booking.extras.length === 1 ? 'Item' : 'Items' }}
                    </span>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div
                            v-for="extra in booking.extras"
                            :key="extra.id"
                            class="group flex items-center justify-between rounded-xl border border-pink-100 bg-gradient-to-r from-pink-50 to-rose-50 p-4 transition-all hover:border-pink-200 hover:shadow-md dark:border-pink-800/50 dark:from-pink-900/10 dark:to-rose-900/10 dark:hover:border-pink-700"
                        >
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 shadow-lg shadow-pink-500/25 transition-transform group-hover:scale-110">
                                    <i class="ri-gift-line text-xl text-white"></i>
                                </div>
                                <div>
                                    <p class="text-neutral-12 font-semibold">{{ extra.extra?.name }}</p>
                                    <p class="text-neutral-9 flex items-center gap-2 text-sm">
                                        <span>{{ extra.quantity }} × ৳{{ formatNumber(extra.unit_price) }}</span>
                                    </p>
                                </div>
                            </div>
                            <p class="text-xl font-bold text-pink-600 dark:text-pink-400">৳{{ formatNumber(extra.total_price) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div v-if="booking.payments?.length" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-bank-card-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-neutral-12 text-lg font-semibold">Payment History</h3>
                            <p class="text-neutral-9 text-xs">Transaction records and receipts</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800">
                        <i class="ri-secure-payment-line text-xs"></i>
                        {{ booking.payments.length }} {{ booking.payments.length === 1 ? 'Payment' : 'Payments' }}
                    </span>
                </div>
                <div class="p-5">
                    <div class="relative">
                        <!-- Timeline line -->
                        <div v-if="booking.payments.length > 1" class="absolute bottom-0 left-6 top-0 w-0.5 bg-gradient-to-b from-emerald-500 via-emerald-300 to-emerald-100 dark:from-emerald-600 dark:via-emerald-800 dark:to-emerald-900"></div>
                        
                        <div class="space-y-6">
                            <div
                                v-for="(payment, index) in booking.payments"
                                :key="payment.id"
                                class="relative flex gap-4"
                            >
                                <!-- Timeline dot -->
                                <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 ring-4 ring-white dark:ring-neutral-900">
                                    <i class="ri-bank-card-line text-lg"></i>
                                </div>
                                
                                <!-- Payment Card -->
                                <div class="flex-1 overflow-hidden rounded-xl border border-emerald-100 bg-gradient-to-r from-emerald-50 to-teal-50 dark:border-emerald-800/50 dark:from-emerald-900/10 dark:to-teal-900/10">
                                    <div class="p-4">
                                        <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ formatNumber(payment.amount) }}</p>
                                                <p class="text-neutral-9 mt-0.5 flex items-center gap-1.5 text-sm">
                                                    <i class="ri-time-line"></i>
                                                    {{ formatDateTime(payment.paid_at || payment.created_at) }}
                                                </p>
                                            </div>
                                            <span :class="[getPaymentStatusClass(payment.status), getPaymentStatusRing(payment.status)]" class="rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset">
                                                {{ formatStatus(payment.status) }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div class="flex items-center gap-2">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                                                    <i class="ri-bank-card-line text-emerald-600 dark:text-emerald-400"></i>
                                                </div>
                                                <div>
                                                    <span class="text-neutral-9 text-xs">Method</span>
                                                    <p class="text-neutral-12 font-medium capitalize">{{ payment.payment_method }}</p>
                                                </div>
                                            </div>
                                            <div v-if="payment.transaction_id" class="flex items-center gap-2">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                                                    <i class="ri-hashtag text-emerald-600 dark:text-emerald-400"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="text-neutral-9 text-xs">Transaction ID</span>
                                                    <p class="text-neutral-12 truncate font-mono text-xs font-medium">{{ payment.transaction_id }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="payment.notes" class="border-neutral-4 text-neutral-11 mt-3 border-t pt-3 text-sm">
                                            <i class="ri-sticky-note-line mr-1"></i> {{ payment.notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Requests & Requirements -->
            <div v-if="booking.special_requests || booking.dietary_requirements || booking.medical_conditions" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                        <i class="ri-file-list-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Special Requests & Requirements</h3>
                        <p class="text-neutral-9 text-xs">Customer preferences and notes</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-3">
                    <div v-if="booking.special_requests" class="group overflow-hidden rounded-xl border-2 border-blue-200 transition-all hover:border-blue-300 hover:shadow-lg dark:border-blue-800 dark:hover:border-blue-700">
                        <div class="bg-blue-100/50 px-4 py-2 dark:bg-blue-900/30">
                            <div class="flex items-center gap-2">
                                <i class="ri-message-3-line text-blue-600 dark:text-blue-400"></i>
                                <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">Special Requests</span>
                            </div>
                        </div>
                        <div class="bg-blue-50 p-4 dark:bg-blue-900/20">
                            <p class="text-neutral-11 text-sm">{{ booking.special_requests }}</p>
                        </div>
                    </div>
                    <div v-if="booking.dietary_requirements" class="group overflow-hidden rounded-xl border-2 border-green-200 transition-all hover:border-green-300 hover:shadow-lg dark:border-green-800 dark:hover:border-green-700">
                        <div class="bg-green-100/50 px-4 py-2 dark:bg-green-900/30">
                            <div class="flex items-center gap-2">
                                <i class="ri-restaurant-line text-green-600 dark:text-green-400"></i>
                                <span class="text-sm font-semibold text-green-700 dark:text-green-300">Dietary Requirements</span>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 dark:bg-green-900/20">
                            <p class="text-neutral-11 text-sm">{{ booking.dietary_requirements }}</p>
                        </div>
                    </div>
                    <div v-if="booking.medical_conditions" class="group overflow-hidden rounded-xl border-2 border-red-200 transition-all hover:border-red-300 hover:shadow-lg dark:border-red-800 dark:hover:border-red-700">
                        <div class="bg-red-100/50 px-4 py-2 dark:bg-red-900/30">
                            <div class="flex items-center gap-2">
                                <i class="ri-heart-pulse-line text-red-600 dark:text-red-400"></i>
                                <span class="text-sm font-semibold text-red-700 dark:text-red-300">Medical Conditions</span>
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 dark:bg-red-900/20">
                            <p class="text-neutral-11 text-sm">{{ booking.medical_conditions }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Timeline Card -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-600 to-slate-700 shadow-lg shadow-slate-500/25">
                        <i class="ri-time-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Status Timeline</h3>
                        <p class="text-neutral-9 text-xs">Booking progress and history</p>
                    </div>
                </div>
                <div class="p-5">
                    <!-- Current Status -->
                    <div class="mb-4 flex items-center justify-between rounded-xl bg-gradient-to-r from-primary-50 to-purple-50 p-4 dark:from-primary-900/20 dark:to-purple-900/20">
                        <div>
                            <p class="text-neutral-9 mb-1 text-xs font-medium uppercase tracking-wider">Current Status</p>
                            <span :class="[getStatusClass(booking.status), getStatusRing(booking.status)]" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-bold ring-1 ring-inset">
                                <span class="h-2 w-2 rounded-full" :class="getStatusDot(booking.status)"></span>
                                {{ formatStatus(booking.status) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-neutral-9 mb-1 text-xs font-medium uppercase tracking-wider">Payment</p>
                            <span :class="[getPaymentStatusClass(booking.payment_status), getPaymentStatusRing(booking.payment_status)]" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-bold ring-1 ring-inset">
                                {{ formatStatus(booking.payment_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="relative space-y-4">
                        <div class="absolute bottom-4 left-3 top-4 w-0.5 bg-gradient-to-b from-primary-500 via-emerald-500 to-neutral-300 dark:to-neutral-700"></div>
                        
                        <!-- Booked -->
                        <div class="relative flex items-start gap-4">
                            <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full bg-primary-500 text-white shadow-md ring-4 ring-white dark:ring-neutral-900">
                                <i class="ri-add-circle-line text-xs"></i>
                            </div>
                            <div class="flex-1 pb-2">
                                <p class="text-neutral-12 text-sm font-semibold">Booking Created</p>
                                <p class="text-neutral-9 text-xs">{{ formatDateTime(booking.created_at) }}</p>
                            </div>
                        </div>

                        <!-- Confirmed -->
                        <div v-if="booking.confirmed_at" class="relative flex items-start gap-4">
                            <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white shadow-md ring-4 ring-white dark:ring-neutral-900">
                                <i class="ri-check-line text-xs"></i>
                            </div>
                            <div class="flex-1 pb-2">
                                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Confirmed</p>
                                <p class="text-neutral-9 text-xs">{{ formatDateTime(booking.confirmed_at) }}</p>
                            </div>
                        </div>

                        <!-- Cancelled -->
                        <div v-if="booking.cancelled_at" class="relative flex items-start gap-4">
                            <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow-md ring-4 ring-white dark:ring-neutral-900">
                                <i class="ri-close-line text-xs"></i>
                            </div>
                            <div class="flex-1 pb-2">
                                <p class="text-sm font-semibold text-red-600 dark:text-red-400">Cancelled</p>
                                <p class="text-neutral-9 text-xs">{{ formatDateTime(booking.cancelled_at) }}</p>
                                <p v-if="booking.cancellation_reason" class="text-neutral-11 mt-1 text-xs italic">{{ booking.cancellation_reason }}</p>
                            </div>
                        </div>

                        <!-- Pending indicator -->
                        <div v-if="!booking.confirmed_at && !booking.cancelled_at && booking.status === 'pending'" class="relative flex items-start gap-4">
                            <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full border-2 border-dashed border-amber-400 bg-amber-100 text-amber-600 ring-4 ring-white dark:bg-amber-900/30 dark:text-amber-400 dark:ring-neutral-900">
                                <i class="ri-time-line animate-pulse text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">Awaiting Confirmation</p>
                                <p class="text-neutral-9 text-xs">Pending review</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                        <i class="ri-calculator-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Price Breakdown</h3>
                        <p class="text-neutral-9 text-xs">Detailed cost summary</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="text-neutral-11 flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="ri-ticket-line text-neutral-9"></i> Subtotal</span>
                            <span class="font-semibold">৳{{ formatNumber(booking.subtotal) }}</span>
                        </div>
                        <div v-if="booking.extras_total > 0" class="text-neutral-11 flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="ri-gift-line text-neutral-9"></i> Extras</span>
                            <span class="font-semibold">৳{{ formatNumber(booking.extras_total) }}</span>
                        </div>
                        <div v-if="booking.discount_amount > 0" class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                            <span class="flex items-center gap-2"><i class="ri-coupon-line"></i> Discount</span>
                            <span class="font-semibold">-৳{{ formatNumber(booking.discount_amount) }}</span>
                        </div>
                        <div v-if="booking.discount_code" class="text-neutral-9 flex items-center justify-between text-xs">
                            <span class="ml-6">Code Applied:</span>
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 font-mono text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">{{ booking.discount_code }}</span>
                        </div>
                        <div v-if="booking.tax_amount > 0" class="text-neutral-11 flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="ri-percent-line text-neutral-9"></i> Tax</span>
                            <span class="font-semibold">৳{{ formatNumber(booking.tax_amount) }}</span>
                        </div>
                    </div>
                    
                    <div class="border-neutral-4 my-4 border-t"></div>
                    
                    <div class="rounded-xl bg-gradient-to-r from-primary-50 to-purple-50 p-4 dark:from-primary-900/20 dark:to-purple-900/20">
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-12 text-lg font-bold">Total</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">৳{{ formatNumber(booking.total_amount) }}</span>
                        </div>
                    </div>
                    
                    <div class="border-neutral-4 my-4 border-t"></div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between rounded-lg bg-emerald-50 p-3 dark:bg-emerald-900/20">
                            <span class="flex items-center gap-2 font-medium text-emerald-700 dark:text-emerald-300">
                                <i class="ri-checkbox-circle-line"></i> Paid
                            </span>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">৳{{ formatNumber(booking.amount_paid) }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
                            <span class="flex items-center gap-2 font-medium text-red-700 dark:text-red-300">
                                <i class="ri-error-warning-line"></i> Due
                            </span>
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">৳{{ formatNumber(booking.amount_due) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Internal Notes -->
            <div v-if="booking.internal_notes" class="border-neutral-6 bg-neutral-1 overflow-hidden rounded-xl border shadow-sm">
                <div class="border-neutral-6 flex items-center gap-3 border-b px-5 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-yellow-600 shadow-lg shadow-amber-500/25">
                        <i class="ri-sticky-note-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-neutral-12 text-lg font-semibold">Internal Notes</h3>
                        <p class="text-neutral-9 text-xs">Staff-only information</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="rounded-xl border-2 border-dashed border-amber-300 bg-gradient-to-br from-amber-50 to-yellow-50 p-4 dark:border-amber-700 dark:from-amber-900/20 dark:to-yellow-900/20">
                        <div class="mb-2 flex items-center gap-2">
                            <i class="ri-lock-line text-amber-600 dark:text-amber-400"></i>
                            <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Private Note</span>
                        </div>
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

    <!-- Add Payment Modal -->
    <AppModal v-model="showPaymentModal" title="Add Manual Payment" max-width="md">
        <form @submit.prevent="submitPayment">
            <div class="space-y-4">
                <!-- Amount Due Info -->
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Amount Due</p>
                            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">৳{{ formatNumber(booking.amount_due) }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50">
                            <i class="ri-wallet-3-line text-xl text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Amount -->
                <div>
                    <AppLabel for="amount" required>Payment Amount (৳)</AppLabel>
                    <AppInputText
                        id="amount"
                        v-model="paymentForm.amount"
                        type="number"
                        step="0.01"
                        min="0.01"
                        :max="booking.amount_due"
                        placeholder="Enter payment amount"
                        :error="paymentForm.errors.amount"
                    />
                    <div class="mt-1 flex gap-2">
                        <button
                            type="button"
                            class="rounded bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-700 transition hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            @click="paymentForm.amount = booking.amount_due"
                        >
                            Full Amount
                        </button>
                        <button
                            v-if="booking.amount_due > 0"
                            type="button"
                            class="rounded bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-700 transition hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            @click="paymentForm.amount = (booking.amount_due / 2).toFixed(2)"
                        >
                            50%
                        </button>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <AppLabel for="payment_method" required>Payment Method</AppLabel>
                    <AppSelect
                        id="payment_method"
                        v-model="paymentForm.payment_method"
                        :options="paymentMethodOptions"
                        placeholder="Select payment method..."
                        :error="paymentForm.errors.payment_method"
                    />
                </div>

                <!-- Transaction ID -->
                <div>
                    <AppLabel for="transaction_id">Transaction ID / Reference</AppLabel>
                    <AppInputText
                        id="transaction_id"
                        v-model="paymentForm.transaction_id"
                        placeholder="Enter transaction ID (optional)"
                        :error="paymentForm.errors.transaction_id"
                    />
                </div>

                <!-- Notes -->
                <div>
                    <AppLabel for="notes">Notes</AppLabel>
                    <AppTextArea
                        id="notes"
                        v-model="paymentForm.notes"
                        rows="2"
                        placeholder="Add any notes about this payment..."
                        :error="paymentForm.errors.notes"
                    />
                </div>
            </div>
        </form>
        
        <template #footer>
            <div class="flex justify-end gap-3 border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <AppButton
                    class="btn btn-secondary"
                    @click="closePaymentModal"
                >
                    Cancel
                </AppButton>
                <AppButton
                    class="btn btn-primary"
                    @click="submitPayment"
                    :loading="paymentForm.processing"
                    :disabled="!paymentForm.amount || !paymentForm.payment_method"
                >
                    <i class="ri-check-line mr-1"></i> Record Payment
                </AppButton>
            </div>
        </template>
    </AppModal>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppModal from '@/Components/Overlay/AppModal.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'
import AppSelect from '@/Components/Form/AppSelect.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'

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
const showPaymentModal = ref(false)
const cancelReason = ref('')
const cancelReasonError = ref('')
const newStatus = ref('')
const statusNotes = ref('')

// Payment form
const paymentForm = useForm({
    amount: '',
    payment_method: '',
    transaction_id: '',
    notes: ''
})

const paymentMethodOptions = [
    { value: 'cash', label: 'Cash' },
    { value: 'bank_transfer', label: 'Bank Transfer' },
    { value: 'mobile_banking', label: 'Mobile Banking (bKash/Nagad)' },
    { value: 'card', label: 'Credit/Debit Card' },
    { value: 'cheque', label: 'Cheque' },
    { value: 'other', label: 'Other' }
]

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

const getTravelerAvatarGradient = (type) => {
    const gradients = {
        adult: 'bg-gradient-to-br from-blue-500 to-cyan-600 shadow-blue-500/30',
        child: 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/30',
        infant: 'bg-gradient-to-br from-green-500 to-emerald-600 shadow-green-500/30',
    }
    return gradients[type] || 'bg-gradient-to-br from-slate-500 to-slate-600 shadow-slate-500/30'
}

const getTravelerTypeIcon = (type) => {
    const icons = {
        adult: 'ri-user-line',
        child: 'ri-user-smile-line',
        infant: 'ri-user-heart-line',
    }
    return icons[type] || 'ri-user-line'
}

const getStatusRing = (status) => {
    const rings = {
        pending: 'ring-amber-200 dark:ring-amber-800',
        processing: 'ring-blue-200 dark:ring-blue-800',
        confirmed: 'ring-emerald-200 dark:ring-emerald-800',
        completed: 'ring-indigo-200 dark:ring-indigo-800',
        cancelled: 'ring-red-200 dark:ring-red-800',
        refunded: 'ring-purple-200 dark:ring-purple-800',
    }
    return rings[status] || 'ring-slate-200 dark:ring-slate-700'
}

const getStatusDot = (status) => {
    const dots = {
        pending: 'bg-amber-500',
        processing: 'bg-blue-500',
        confirmed: 'bg-emerald-500',
        completed: 'bg-indigo-500',
        cancelled: 'bg-red-500',
        refunded: 'bg-purple-500',
    }
    return dots[status] || 'bg-slate-500'
}

const getPaymentStatusRing = (status) => {
    const rings = {
        unpaid: 'ring-red-200 dark:ring-red-800',
        pending: 'ring-amber-200 dark:ring-amber-800',
        partial: 'ring-blue-200 dark:ring-blue-800',
        paid: 'ring-emerald-200 dark:ring-emerald-800',
        refunded: 'ring-purple-200 dark:ring-purple-800',
    }
    return rings[status] || 'ring-slate-200 dark:ring-slate-700'
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

const openPaymentModal = () => {
    paymentForm.reset()
    paymentForm.clearErrors()
    showPaymentModal.value = true
}

const closePaymentModal = () => {
    showPaymentModal.value = false
    paymentForm.reset()
    paymentForm.clearErrors()
}

const submitPayment = () => {
    paymentForm.post(route('tourBooking.addPayment', props.booking.id), {
        preserveScroll: true,
        onSuccess: () => {
            closePaymentModal()
        }
    })
}
</script>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    
    /* Reset shadows and transitions for print */
    * {
        box-shadow: none !important;
        transition: none !important;
    }
    
    /* Force light mode colors for printing */
    .dark\:bg-neutral-900\/30,
    .dark\:bg-primary-900\/20 {
        background: #f9fafb !important;
    }
    
    /* Ensure good contrast */
    .text-neutral-9 {
        color: #6b7280 !important;
    }
    
    .text-neutral-12 {
        color: #111827 !important;
    }
    
    /* Page breaks */
    .border-neutral-6 {
        page-break-inside: avoid;
    }
}

/* Animations */
@keyframes pulse-subtle {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse-subtle {
    animation: pulse-subtle 2s ease-in-out infinite;
}
</style>
