<template>
    <Head :title="title"></Head>
    
    <!-- Header with Actions -->
    <AppSectionHeader :title="'Booking #' + booking.booking_number" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-wrap gap-2">
                <AppButton
                    v-if="booking.status === 'pending'"
                    class="btn btn-success"
                    @click="updateStatus('confirmed')"
                >
                    <i class="ri-check-line mr-1"></i> Confirm
                </AppButton>
                <AppButton
                    v-if="['pending', 'confirmed'].includes(booking.status)"
                    class="btn btn-destructive"
                    @click="showCancelModal = true"
                >
                    <i class="ri-close-line mr-1"></i> Cancel
                </AppButton>
                <AppButton
                    v-if="booking.status !== 'cancelled'"
                    class="btn btn-secondary"
                    @click="showStatusModal = true"
                >
                    <i class="ri-refresh-line mr-1"></i> Change Status
                </AppButton>
                <AppButton
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Amount -->
        <div class="bg-linear-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 p-6 rounded-xl border-2 border-primary-200 dark:border-primary-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-600 dark:text-primary-400 font-medium">Total Amount</p>
                    <p class="text-2xl font-bold text-primary-900 dark:text-primary-100 mt-1">৳{{ formatNumber(booking.total_amount) }}</p>
                </div>
                <div class="bg-primary-200 dark:bg-primary-800 p-3 rounded-lg">
                    <i class="ri-money-dollar-circle-line text-2xl text-primary-600 dark:text-primary-400"></i>
                </div>
            </div>
        </div>

        <!-- Amount Paid -->
        <div class="bg-linear-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-6 rounded-xl border-2 border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Paid</p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">৳{{ formatNumber(booking.amount_paid) }}</p>
                </div>
                <div class="bg-green-200 dark:bg-green-800 p-3 rounded-lg">
                    <i class="ri-check-double-line text-2xl text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Amount Due -->
        <div class="bg-linear-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 p-6 rounded-xl border-2 border-red-200 dark:border-red-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">Due</p>
                    <p class="text-2xl font-bold text-red-900 dark:text-red-100 mt-1">৳{{ formatNumber(booking.amount_due) }}</p>
                </div>
                <div class="bg-red-200 dark:bg-red-800 p-3 rounded-lg">
                    <i class="ri-error-warning-line text-2xl text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Travelers -->
        <div class="bg-linear-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-6 rounded-xl border-2 border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Travelers</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ totalTravelers }}</p>
                </div>
                <div class="bg-blue-200 dark:bg-blue-800 p-3 rounded-lg">
                    <i class="ri-group-line text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tour Information -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-map-pin-line text-primary-600"></i>
                        Tour Information
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <!-- Tour Name with Image -->
                        <div class="flex items-start gap-4 p-4 bg-linear-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg">
                            <div class="bg-primary-100 dark:bg-primary-800 p-3 rounded-lg">
                                <i class="ri-map-2-line text-2xl text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">{{ booking.tour?.name }}</h3>
                                <p v-if="booking.tour?.code" class="text-sm text-slate-500 dark:text-slate-400 mt-1">Code: {{ booking.tour.code }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-calendar-line text-xl text-primary-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Travel Date</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ formatDate(booking.travel_date) }}</p>
                                </div>
                            </div>

                            <div v-if="booking.pricing" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-price-tag-3-line text-xl text-primary-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Pricing Plan</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.pricing.name }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-user-line text-xl text-blue-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Adults</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.adult_count }}</p>
                                </div>
                            </div>

                            <div v-if="booking.child_count > 0" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-user-smile-line text-xl text-amber-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Children</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.child_count }}</p>
                                </div>
                            </div>

                            <div v-if="booking.infant_count > 0" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-user-heart-line text-xl text-green-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Infants</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.infant_count }}</p>
                                </div>
                            </div>

                            <div v-if="booking.customer" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-user-star-line text-xl text-purple-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Customer</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.customer.name }}</p>
                                </div>
                            </div>

                            <div v-if="booking.agent" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                                <i class="ri-user-settings-line text-xl text-indigo-600"></i>
                                <div>
                                    <label class="text-xs text-slate-500 dark:text-slate-400">Agent</label>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.agent.name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Contact Information -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-contacts-line text-primary-600"></i>
                        Contact Information
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <i class="ri-user-line text-xl text-primary-600"></i>
                            <div class="flex-1">
                                <label class="text-xs text-slate-500 dark:text-slate-400">Name</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <i class="ri-mail-line text-xl text-primary-600"></i>
                            <div class="flex-1">
                                <label class="text-xs text-slate-500 dark:text-slate-400">Email</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ booking.contact_email }}</p>
                            </div>
                        </div>

                        <div v-if="booking.contact_phone" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <i class="ri-phone-line text-xl text-primary-600"></i>
                            <div class="flex-1">
                                <label class="text-xs text-slate-500 dark:text-slate-400">Phone</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_phone }}</p>
                            </div>
                        </div>

                        <div v-if="booking.whatsapp" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <i class="ri-whatsapp-line text-xl text-green-600"></i>
                            <div class="flex-1">
                                <label class="text-xs text-slate-500 dark:text-slate-400">WhatsApp</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.whatsapp }}</p>
                            </div>
                        </div>

                        <div v-if="booking.contact_address" class="md:col-span-2 flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <i class="ri-map-pin-line text-xl text-primary-600 mt-1"></i>
                            <div class="flex-1">
                                <label class="text-xs text-slate-500 dark:text-slate-400">Address</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.contact_address }}</p>
                            </div>
                        </div>

                        <div v-if="booking.emergency_contact" class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <i class="ri-alarm-warning-line text-xl text-red-600"></i>
                            <div class="flex-1">
                                <label class="text-xs text-red-600 dark:text-red-400">Emergency Contact</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.emergency_contact }}</p>
                            </div>
                        </div>

                        <div v-if="booking.emergency_phone" class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <i class="ri-phone-line text-xl text-red-600"></i>
                            <div class="flex-1">
                                <label class="text-xs text-red-600 dark:text-red-400">Emergency Phone</label>
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ booking.emergency_phone }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Travelers -->
            <AppCard v-if="booking.travelers?.length">
                <template #title>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="ri-group-line text-primary-600"></i>
                            Traveler Details
                        </div>
                        <span class="text-sm bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 px-3 py-1 rounded-full">
                            {{ booking.travelers.length }} {{ booking.travelers.length === 1 ? 'Traveler' : 'Travelers' }}
                        </span>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div
                            v-for="(traveler, index) in booking.travelers"
                            :key="traveler.id"
                            class="p-5 bg-linear-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 rounded-xl border-2 border-slate-200 dark:border-slate-600"
                        >
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-primary-100 dark:bg-primary-800 p-2 rounded-lg">
                                        <i class="ri-user-line text-xl text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-lg text-slate-800 dark:text-slate-200">
                                            {{ traveler.first_name }} {{ traveler.last_name }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span :class="getTravelerTypeBadge(traveler.type)" class="text-xs px-2 py-0.5 rounded capitalize font-medium">
                                                {{ traveler.type }}
                                            </span>
                                            <span v-if="traveler.is_lead" class="text-xs bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded font-medium">
                                                <i class="ri-star-fill"></i> Lead Traveler
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">
                                <div v-if="traveler.email" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Email</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ traveler.email }}</p>
                                </div>
                                <div v-if="traveler.phone" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Phone</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.phone }}</p>
                                </div>
                                <div v-if="traveler.date_of_birth" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Date of Birth</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ formatDate(traveler.date_of_birth) }}</p>
                                </div>
                                <div v-if="traveler.gender" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Gender</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200 capitalize">{{ traveler.gender }}</p>
                                </div>
                                <div v-if="traveler.nationality" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Nationality</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.nationality }}</p>
                                </div>
                                <div v-if="traveler.passport_number" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Passport</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.passport_number }}</p>
                                </div>
                                <div v-if="traveler.passport_expiry" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Passport Expiry</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ formatDate(traveler.passport_expiry) }}</p>
                                </div>
                                <div v-if="traveler.nid_number" class="flex flex-col gap-1">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">NID</span>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ traveler.nid_number }}</p>
                                </div>
                            </div>

                            <div v-if="traveler.dietary_requirements || traveler.medical_conditions || traveler.special_requests" class="mt-4 pt-4 border-t border-slate-300 dark:border-slate-600 space-y-2">
                                <div v-if="traveler.dietary_requirements" class="flex items-start gap-2">
                                    <i class="ri-restaurant-line text-green-600 mt-0.5"></i>
                                    <div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Dietary:</span>
                                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ traveler.dietary_requirements }}</p>
                                    </div>
                                </div>
                                <div v-if="traveler.medical_conditions" class="flex items-start gap-2">
                                    <i class="ri-heart-pulse-line text-red-600 mt-0.5"></i>
                                    <div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Medical:</span>
                                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ traveler.medical_conditions }}</p>
                                    </div>
                                </div>
                                <div v-if="traveler.special_requests" class="flex items-start gap-2">
                                    <i class="ri-message-3-line text-blue-600 mt-0.5"></i>
                                    <div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Requests:</span>
                                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ traveler.special_requests }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Extras -->
            <AppCard v-if="booking.extras?.length">
                <template #title>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="ri-add-circle-line text-primary-600"></i>
                            Tour Extras
                        </div>
                        <span class="text-sm bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 px-3 py-1 rounded-full">
                            {{ booking.extras.length }} {{ booking.extras.length === 1 ? 'Item' : 'Items' }}
                        </span>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div
                            v-for="extra in booking.extras"
                            :key="extra.id"
                            class="flex justify-between items-center p-4 bg-linear-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg border border-primary-200 dark:border-primary-800"
                        >
                            <div class="flex items-center gap-3">
                                <div class="bg-primary-100 dark:bg-primary-800 p-2 rounded-lg">
                                    <i class="ri-gift-line text-xl text-primary-600 dark:text-primary-400"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ extra.extra?.name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Quantity: {{ extra.quantity }} × ৳{{ formatNumber(extra.unit_price) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">৳{{ formatNumber(extra.total_price) }}</p>
                            </div>
                        </div>

    <!-- Cancel Reason Modal -->
    <AppModal v-model="showCancelModal" title="Cancel Booking" max-width="md">
        <div class="space-y-4">
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
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

            <div v-if="newStatus" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
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
                    </div>
                </template>
            </AppCard>

            <!-- Payment History -->
            <AppCard v-if="booking.payments?.length">
                <template #title>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="ri-money-dollar-circle-line text-primary-600"></i>
                            Payment History
                        </div>
                        <span class="text-sm bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-3 py-1 rounded-full">
                            {{ booking.payments.length }} {{ booking.payments.length === 1 ? 'Payment' : 'Payments' }}
                        </span>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div
                            v-for="payment in booking.payments"
                            :key="payment.id"
                            class="p-4 rounded-lg border-l-4"
                            :class="getPaymentCardClass(payment.status)"
                        >
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-2xl font-bold text-slate-800 dark:text-slate-200">৳{{ formatNumber(payment.amount) }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                        <i class="ri-calendar-line"></i> {{ formatDateTime(payment.paid_at || payment.created_at) }}
                                    </p>
                                </div>
                                <span :class="getPaymentStatusClass(payment.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                                    {{ formatStatus(payment.status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <i class="ri-bank-card-line text-slate-500"></i>
                                    <div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Method</span>
                                        <p class="font-medium capitalize text-slate-800 dark:text-slate-200">{{ payment.payment_method }}</p>
                                    </div>
                                </div>
                                <div v-if="payment.transaction_id" class="flex items-center gap-2">
                                    <i class="ri-hashtag text-slate-500"></i>
                                    <div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Transaction ID</span>
                                        <p class="font-mono text-xs text-slate-800 dark:text-slate-200">{{ payment.transaction_id }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="payment.notes" class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ payment.notes }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Special Requests & Requirements -->
            <AppCard v-if="booking.special_requests || booking.dietary_requirements || booking.medical_conditions">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-file-list-line text-primary-600"></i>
                        Special Requests & Requirements
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-if="booking.special_requests" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start gap-3">
                                <i class="ri-message-3-line text-xl text-blue-600 mt-0.5"></i>
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-blue-700 dark:text-blue-300">Special Requests</label>
                                    <p class="text-slate-700 dark:text-slate-300 mt-1">{{ booking.special_requests }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="booking.dietary_requirements" class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-start gap-3">
                                <i class="ri-restaurant-line text-xl text-green-600 mt-0.5"></i>
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-green-700 dark:text-green-300">Dietary Requirements</label>
                                    <p class="text-slate-700 dark:text-slate-300 mt-1">{{ booking.dietary_requirements }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="booking.medical_conditions" class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-start gap-3">
                                <i class="ri-heart-pulse-line text-xl text-red-600 mt-0.5"></i>
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-red-700 dark:text-red-300">Medical Conditions</label>
                                    <p class="text-slate-700 dark:text-slate-300 mt-1">{{ booking.medical_conditions }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-information-line text-primary-600"></i>
                        Booking Status
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Status</span>
                            <span :class="getStatusClass(booking.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatStatus(booking.status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Payment</span>
                            <span :class="getPaymentStatusClass(booking.payment_status)" class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatStatus(booking.payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Source</span>
                            <span class="text-slate-800 dark:text-slate-200 capitalize font-medium">{{ booking.booking_source }}</span>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700" />
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <i class="ri-calendar-check-line"></i>
                                <div class="flex-1">
                                    <p class="text-xs">Booked On</p>
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ formatDateTime(booking.created_at) }}</p>
                                </div>
                            </div>
                            <div v-if="booking.confirmed_at" class="flex items-center gap-2 text-green-600 dark:text-green-400">
                                <i class="ri-checkbox-circle-line"></i>
                                <div class="flex-1">
                                    <p class="text-xs">Confirmed</p>
                                    <p class="font-medium">{{ formatDateTime(booking.confirmed_at) }}</p>
                                </div>
                            </div>
                            <div v-if="booking.cancelled_at" class="flex items-center gap-2 text-red-600 dark:text-red-400">
                                <i class="ri-close-circle-line"></i>
                                <div class="flex-1">
                                    <p class="text-xs">Cancelled</p>
                                    <p class="font-medium">{{ formatDateTime(booking.cancelled_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Price Breakdown -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-calculator-line text-primary-600"></i>
                        Price Breakdown
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div class="flex justify-between text-slate-700 dark:text-slate-300">
                            <span>Subtotal</span>
                            <span class="font-medium">৳{{ formatNumber(booking.subtotal) }}</span>
                        </div>
                        <div v-if="booking.extras_total > 0" class="flex justify-between text-slate-700 dark:text-slate-300">
                            <span>Extras</span>
                            <span class="font-medium">৳{{ formatNumber(booking.extras_total) }}</span>
                        </div>
                        <div v-if="booking.discount_amount > 0" class="flex justify-between text-green-600 dark:text-green-400">
                            <span>Discount</span>
                            <span class="font-medium">-৳{{ formatNumber(booking.discount_amount) }}</span>
                        </div>
                        <div v-if="booking.discount_code" class="flex justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Code:</span>
                            <span class="font-mono">{{ booking.discount_code }}</span>
                        </div>
                        <div v-if="booking.tax_amount > 0" class="flex justify-between text-slate-700 dark:text-slate-300">
                            <span>Tax</span>
                            <span class="font-medium">৳{{ formatNumber(booking.tax_amount) }}</span>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700" />
                        <div class="flex justify-between font-bold text-lg text-slate-800 dark:text-slate-200">
                            <span>Total</span>
                            <span class="text-primary-600 dark:text-primary-400">৳{{ formatNumber(booking.total_amount) }}</span>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700" />
                        <div class="flex justify-between text-green-600 dark:text-green-400">
                            <span>Paid</span>
                            <span class="font-semibold">৳{{ formatNumber(booking.amount_paid) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600 dark:text-red-400 font-medium">
                            <span>Due</span>
                            <span class="font-bold">৳{{ formatNumber(booking.amount_due) }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Internal Notes -->
            <AppCard v-if="booking.internal_notes">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-sticky-note-line text-primary-600"></i>
                        Internal Notes
                    </div>
                </template>
                <template #content>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ booking.internal_notes }}</p>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import useTitle from '@/Composables/useTitle'

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
        pending: 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300',
        processing: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        confirmed: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
        completed: 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300',
        cancelled: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
        refunded: 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300',
    }
    return classes[status] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getPaymentStatusClass = (status) => {
    const classes = {
        unpaid: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
        pending: 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300',
        partial: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        paid: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
        refunded: 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300',
    }
    return classes[status] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getTravelerTypeBadge = (type) => {
    const badges = {
        adult: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        child: 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300',
        infant: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
    }
    return badges[type] || 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'
}

const getPaymentCardClass = (status) => {
    const classes = {
        pending: 'bg-amber-50 dark:bg-amber-900/10 border-amber-400',
        completed: 'bg-green-50 dark:bg-green-900/10 border-green-400',
        failed: 'bg-red-50 dark:bg-red-900/10 border-red-400',
        refunded: 'bg-purple-50 dark:bg-purple-900/10 border-purple-400',
    }
    return classes[status] || 'bg-slate-50 dark:bg-slate-800 border-slate-300'
}

const statusChangeOptions = computed(() => {
    const allStatuses = [
        { value: 'pending', label: 'Pending' },
        { value: 'processing', label: 'Processing' },
        { value: 'confirmed', label: 'Confirmed' },
        { value: 'completed', label: 'Completed' },
    ]
    // Filter out current status
    return allStatuses.filter(s => s.value !== props.booking.status)
})

const handlePrint = () => {
    window.print()
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
