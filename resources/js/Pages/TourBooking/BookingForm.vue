<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="isEditing ? 'Edit Booking' : 'Create Booking'" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary group" @click="$inertia.visit(route('tourBooking.index'))">
                <i class="ri-arrow-left-line mr-1.5 transition-transform group-hover:-translate-x-0.5"></i> Back to List
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submit" class="space-y-8">
        <!-- Tour Selection & Basic Info -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/25">
                        <i class="ri-map-pin-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Tour & Travel Details</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Select tour, pricing plan, and travel information</p>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Tour Selection -->
                    <div>
                        <AppLabel for="tour_id" required>Select Tour</AppLabel>
                        <AppCombobox
                            class="w-full"
                            v-model="form.tour_id"
                            :options="tourOptions"
                            :error="form.errors.tour_id"
                            :emit-value="true"
                            combo-label="Choose a tour..."
                            @update:modelValue="onTourChange"
                        />
                    </div>

                    <!-- Pricing Plan Selection -->
                    <div>
                        <AppLabel for="pricing_id" required>Select Pricing Plan</AppLabel>
                        <AppSelect
                            v-model="form.pricing_id"
                            :options="pricingSelectOptions"
                            :error="form.errors.pricing_id"
                            placeholder="Choose a pricing plan..."
                        />
                    </div>

                    <!-- Pricing Details Display -->
                    <div v-if="selectedPricing" class="md:col-span-2">
                        <div class="relative overflow-hidden rounded-2xl border border-primary-200 bg-gradient-to-br from-primary-50 via-primary-50/50 to-white p-6 shadow-lg shadow-primary-500/10 dark:border-primary-800 dark:from-primary-900/30 dark:via-primary-900/20 dark:to-slate-900">
                            <div class="absolute right-0 top-0 -mr-16 -mt-16 h-40 w-40 rounded-full bg-primary-100 opacity-50 blur-3xl dark:bg-primary-800"></div>
                            <div class="relative">
                                <div class="mb-5 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 shadow-lg shadow-primary-500/30">
                                        <i class="ri-price-tag-3-line text-lg text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-primary-900 dark:text-primary-100">{{ selectedPricing.name }}</h4>
                                        <p class="text-sm text-primary-600 dark:text-primary-400">Pricing Details</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div class="group rounded-xl bg-white/80 p-4 shadow-sm ring-1 ring-primary-100 transition-all hover:shadow-md hover:ring-primary-200 dark:bg-slate-800/80 dark:ring-primary-800 dark:hover:ring-primary-700">
                                        <div class="mb-2 flex items-center gap-2">
                                            <i class="ri-user-line text-primary-500"></i>
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Adult Price</span>
                                        </div>
                                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">৳{{ formatNumber(selectedPricing.adult_price) }}</div>
                                    </div>
                                    <div v-if="selectedPricing.child_price > 0" class="group rounded-xl bg-white/80 p-4 shadow-sm ring-1 ring-amber-100 transition-all hover:shadow-md hover:ring-amber-200 dark:bg-slate-800/80 dark:ring-amber-800 dark:hover:ring-amber-700">
                                        <div class="mb-2 flex items-center gap-2">
                                            <i class="ri-user-smile-line text-amber-500"></i>
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Child Price</span>
                                        </div>
                                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">৳{{ formatNumber(selectedPricing.child_price) }}</div>
                                    </div>
                                    <div v-if="selectedPricing.infant_price > 0" class="group rounded-xl bg-white/80 p-4 shadow-sm ring-1 ring-emerald-100 transition-all hover:shadow-md hover:ring-emerald-200 dark:bg-slate-800/80 dark:ring-emerald-800 dark:hover:ring-emerald-700">
                                        <div class="mb-2 flex items-center gap-2">
                                            <i class="ri-heart-line text-emerald-500"></i>
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Infant Price</span>
                                        </div>
                                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ formatNumber(selectedPricing.infant_price) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Date -->
                    <div>
                        <AppLabel for="travel_date" required>Travel Date</AppLabel>
                        <AppInputText
                            v-model="form.travel_date"
                            type="date"
                            :error="form.errors.travel_date"
                        />
                    </div>

                    <!-- Booking Source -->
                    <div>
                        <AppLabel for="booking_source">Booking Source</AppLabel>
                        <AppSelect
                            v-model="form.booking_source"
                            :options="bookingSourceOptions"
                            :error="form.errors.booking_source"
                        />
                    </div>

                    <!-- Traveler Counts -->
                    <div>
                        <AppLabel for="adult_count" required>Adults (12+)</AppLabel>
                        <AppInputText
                            v-model.number="form.adult_count"
                            type="number"
                            min="1"
                            :error="form.errors.adult_count"
                            @input="updateTravelerForms"
                        />
                    </div>

                    <div>
                        <AppLabel for="child_count">Children (2-11)</AppLabel>
                        <AppInputText
                            v-model.number="form.child_count"
                            type="number"
                            min="0"
                            :error="form.errors.child_count"
                            @input="updateTravelerForms"
                        />
                    </div>

                    <div>
                        <AppLabel for="infant_count">Infants (0-2)</AppLabel>
                        <AppInputText
                            v-model.number="form.infant_count"
                            type="number"
                            min="0"
                            :error="form.errors.infant_count"
                            @input="updateTravelerForms"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Customer & Agent Selection -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/25">
                        <i class="ri-user-star-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Customer & Agent</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Link booking to customer or agent account</p>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <AppLabel for="customer_id">Customer</AppLabel>
                        <AppSelect
                            v-model="form.customer_id"
                            :options="customerOptions"
                            :error="form.errors.customer_id"
                            placeholder="Select customer (optional)"
                            searchable
                            @update:modelValue="onCustomerChange"
                        />
                    </div>

                    <div>
                        <AppLabel for="agent_id">Agent</AppLabel>
                        <AppSelect
                            v-model="form.agent_id"
                            :options="agentOptions"
                            :error="form.errors.agent_id"
                            placeholder="Select agent (optional)"
                            searchable
                            @update:modelValue="onAgentChange"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Contact Information -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                        <i class="ri-contacts-book-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Contact Information</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Primary contact and emergency details</p>
                    </div>
                </div>
            </template>
            <template #content>
                <!-- Primary Contact Section -->
                <div class="mb-8">
                    <div class="mb-4 flex items-center gap-2">
                        <i class="ri-user-line text-primary-500"></i>
                        <h4 class="font-medium text-slate-700 dark:text-slate-300">Primary Contact</h4>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <AppLabel for="contact_name" required>Contact Name</AppLabel>
                        <AppInputText
                            v-model="form.contact_name"
                            :error="form.errors.contact_name"
                            placeholder="Full name"
                        />
                    </div>

                    <div>
                        <AppLabel for="contact_email" required>Contact Email</AppLabel>
                        <AppInputText
                            v-model="form.contact_email"
                            type="email"
                            :error="form.errors.contact_email"
                            placeholder="email@example.com"
                        />
                    </div>

                    <div>
                        <AppLabel for="contact_phone">Contact Phone</AppLabel>
                        <AppInputText
                            v-model="form.contact_phone"
                            :error="form.errors.contact_phone"
                            placeholder="+880 1XXX-XXXXXX"
                        />
                    </div>

                    <div>
                        <AppLabel for="whatsapp">WhatsApp Number</AppLabel>
                        <AppInputText
                            v-model="form.whatsapp"
                            :error="form.errors.whatsapp"
                            placeholder="+880 1XXX-XXXXXX"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <AppLabel for="contact_address">Address</AppLabel>
                        <AppTextArea
                            v-model="form.contact_address"
                            :error="form.errors.contact_address"
                            rows="2"
                            placeholder="Street address, city, country"
                        />
                    </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="rounded-xl border-2 border-dashed border-rose-200 bg-rose-50/50 p-5 dark:border-rose-800 dark:bg-rose-900/20">
                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900/50">
                            <i class="ri-first-aid-kit-line text-rose-600 dark:text-rose-400"></i>
                        </div>
                        <h4 class="font-medium text-rose-700 dark:text-rose-300">Emergency Contact</h4>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <AppLabel for="emergency_contact">Emergency Contact Name</AppLabel>
                            <AppInputText
                                v-model="form.emergency_contact"
                                :error="form.errors.emergency_contact"
                                placeholder="Emergency contact full name"
                            />
                        </div>

                        <div>
                            <AppLabel for="emergency_phone">Emergency Contact Phone</AppLabel>
                            <AppInputText
                                v-model="form.emergency_phone"
                                :error="form.errors.emergency_phone"
                                placeholder="+880 1XXX-XXXXXX"
                            />
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Travelers -->
        <AppCard>
            <template #title>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-group-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Traveler Details</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ totalTravelers }} traveler{{ totalTravelers > 1 ? 's' : '' }} • {{ form.adult_count }} adult{{ form.adult_count > 1 ? 's' : '' }}<span v-if="form.child_count > 0">, {{ form.child_count }} child{{ form.child_count > 1 ? 'ren' : '' }}</span><span v-if="form.infant_count > 0">, {{ form.infant_count }} infant{{ form.infant_count > 1 ? 's' : '' }}</span></p>
                        </div>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="space-y-6">
                    <div
                        v-for="(traveler, index) in form.travelers"
                        :key="index"
                        class="group relative overflow-hidden rounded-xl border-2 transition-all duration-200"
                        :class="[
                            traveler.is_lead 
                                ? 'border-primary-300 bg-gradient-to-br from-primary-50 to-purple-50 shadow-md dark:border-primary-700 dark:from-primary-900/20 dark:to-purple-900/20' 
                                : 'border-slate-200 bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-slate-600'
                        ]"
                    >
                        <!-- Lead badge for lead traveler -->
                        <div v-if="traveler.is_lead" class="absolute -right-8 top-3 rotate-45 bg-gradient-to-r from-primary-500 to-purple-600 px-10 py-1 text-xs font-bold text-white shadow">
                            LEAD
                        </div>
                        
                        <div class="p-6">
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div 
                                        class="flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold text-white shadow-lg"
                                        :class="getTravelerAvatarClass(traveler.type)"
                                    >
                                        {{ index + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-800 dark:text-slate-200">
                                            Traveler {{ index + 1 }}
                                        </h4>
                                        <span :class="getTravelerTypeBadge(traveler.type)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium">
                                            <i :class="getTravelerTypeIcon(traveler.type)"></i>
                                            {{ traveler.type.charAt(0).toUpperCase() + traveler.type.slice(1) }}
                                        </span>
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer rounded-lg bg-white px-3 py-2 shadow-sm ring-1 ring-slate-200 transition-all hover:ring-primary-300 dark:bg-slate-700 dark:ring-slate-600 dark:hover:ring-primary-600">
                                    <input
                                        type="checkbox"
                                        v-model="traveler.is_lead"
                                        @change="updateLeadTraveler(index)"
                                        class="rounded text-primary-600 focus:ring-primary-500"
                                    />
                                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Lead Traveler</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <AppLabel :for="`traveler_${index}_first_name`" required>First Name</AppLabel>
                                    <AppInputText
                                        v-model="traveler.first_name"
                                        :error="form.errors[`travelers.${index}.first_name`]"
                                        placeholder="First name"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_last_name`" required>Last Name</AppLabel>
                                    <AppInputText
                                        v-model="traveler.last_name"
                                        :error="form.errors[`travelers.${index}.last_name`]"
                                        placeholder="Last name"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_email`">Email</AppLabel>
                                    <AppInputText
                                        v-model="traveler.email"
                                        type="email"
                                        :error="form.errors[`travelers.${index}.email`]"
                                        placeholder="email@example.com"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_phone`">Phone</AppLabel>
                                    <AppInputText
                                        v-model="traveler.phone"
                                        :error="form.errors[`travelers.${index}.phone`]"
                                        placeholder="+880 1XXX-XXXXXX"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_date_of_birth`">Date of Birth</AppLabel>
                                    <AppInputText
                                        v-model="traveler.date_of_birth"
                                        type="date"
                                        :error="form.errors[`travelers.${index}.date_of_birth`]"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_gender`">Gender</AppLabel>
                                    <AppSelect
                                        v-model="traveler.gender"
                                        :options="genderOptions"
                                        :error="form.errors[`travelers.${index}.gender`]"
                                        placeholder="Select gender"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_nationality`">Nationality</AppLabel>
                                    <AppInputText
                                        v-model="traveler.nationality"
                                        :error="form.errors[`travelers.${index}.nationality`]"
                                        placeholder="e.g., Bangladeshi"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_passport_number`">Passport Number</AppLabel>
                                    <AppInputText
                                        v-model="traveler.passport_number"
                                        :error="form.errors[`travelers.${index}.passport_number`]"
                                        placeholder="Passport number"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_passport_expiry`">Passport Expiry</AppLabel>
                                    <AppInputText
                                        v-model="traveler.passport_expiry"
                                        type="date"
                                        :error="form.errors[`travelers.${index}.passport_expiry`]"
                                    />
                                </div>

                                <div>
                                    <AppLabel :for="`traveler_${index}_nid_number`">NID Number</AppLabel>
                                    <AppInputText
                                        v-model="traveler.nid_number"
                                        :error="form.errors[`travelers.${index}.nid_number`]"
                                        placeholder="National ID number"
                                    />
                                </div>

                                <div class="md:col-span-3">
                                    <AppLabel :for="`traveler_${index}_special_requests`">Special Requests</AppLabel>
                                    <AppTextArea
                                        v-model="traveler.special_requests"
                                        :error="form.errors[`travelers.${index}.special_requests`]"
                                        rows="2"
                                        placeholder="Any special requests for this traveler..."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Special Requirements -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 shadow-lg shadow-pink-500/25">
                        <i class="ri-heart-pulse-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Special Requirements</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Dietary needs, medical conditions, and special requests</p>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-chat-quote-line text-primary-500"></i>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Special Requests</span>
                        </div>
                        <AppTextArea
                            v-model="form.special_requests"
                            :error="form.errors.special_requests"
                            rows="4"
                            placeholder="Any special requests or requirements..."
                        />
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-restaurant-line text-amber-500"></i>
                            <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Dietary Requirements</span>
                        </div>
                        <AppTextArea
                            v-model="form.dietary_requirements"
                            :error="form.errors.dietary_requirements"
                            rows="4"
                            placeholder="Vegetarian, vegan, allergies, etc..."
                        />
                    </div>

                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-800 dark:bg-rose-900/20">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-first-aid-kit-line text-rose-500"></i>
                            <span class="text-sm font-medium text-rose-700 dark:text-rose-300">Medical Conditions</span>
                        </div>
                        <AppTextArea
                            v-model="form.medical_conditions"
                            :error="form.errors.medical_conditions"
                            rows="4"
                            placeholder="Any medical conditions we should be aware of..."
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Optional Extras -->
        <AppCard v-if="tourExtras.length > 0">
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/25">
                        <i class="ri-add-circle-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Optional Extras</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Add-ons and additional services for the tour</p>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div
                        v-for="extra in tourExtras"
                        :key="extra.id"
                        class="group relative overflow-hidden rounded-xl border-2 p-5 transition-all duration-200"
                        :class="isExtraSelected(extra.id) 
                            ? 'border-primary-400 bg-gradient-to-br from-primary-50 to-purple-50 shadow-lg shadow-primary-500/10 dark:border-primary-600 dark:from-primary-900/30 dark:to-purple-900/20' 
                            : 'border-slate-200 bg-slate-50 hover:border-slate-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800 dark:hover:border-slate-600'"
                    >
                        <!-- Selected badge -->
                        <div v-if="isExtraSelected(extra.id)" class="absolute right-3 top-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500 text-white shadow-lg">
                                <i class="ri-check-line text-sm"></i>
                            </span>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="flex items-center pt-1">
                                <input
                                    type="checkbox"
                                    :checked="isExtraSelected(extra.id)"
                                    @change="toggleExtra(extra)"
                                    class="h-5 w-5 cursor-pointer rounded text-primary-600 focus:ring-primary-500"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="flex items-center gap-2 font-semibold text-slate-800 dark:text-slate-200">
                                            {{ extra.name }}
                                            <span v-if="extra.is_required" class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-900/30 dark:text-red-400">Required</span>
                                        </h4>
                                        <p v-if="extra.description" class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                                            {{ extra.description }}
                                        </p>
                                        <p class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                            <i class="ri-price-tag-3-line"></i>
                                            {{ getPriceTypeLabel(extra.price_type) }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-primary-600 dark:text-primary-400">
                                            ৳{{ formatNumber(extra.price) }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="isExtraSelected(extra.id)" class="mt-4 flex items-center gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Quantity:</span>
                                    <div class="flex items-center overflow-hidden rounded-lg border border-slate-300 dark:border-slate-600">
                                        <button
                                            type="button"
                                            @click="updateExtraQuantity(extra.id, -1)"
                                            class="bg-slate-100 px-3 py-1.5 transition-colors hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600"
                                        >
                                            <i class="ri-subtract-line"></i>
                                        </button>
                                        <span class="min-w-[3rem] border-x border-slate-300 bg-white px-4 py-1.5 text-center font-semibold dark:border-slate-600 dark:bg-slate-800">
                                            {{ getExtraQuantity(extra.id) }}
                                        </span>
                                        <button
                                            type="button"
                                            @click="updateExtraQuantity(extra.id, 1)"
                                            class="bg-slate-100 px-3 py-1.5 transition-colors hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600"
                                            :disabled="extra.max_quantity && getExtraQuantity(extra.id) >= extra.max_quantity"
                                        >
                                            <i class="ri-add-line"></i>
                                        </button>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        = ৳{{ formatNumber(calculateExtraTotal(extra)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Pricing & Payment -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg shadow-emerald-500/25">
                        <i class="ri-money-dollar-circle-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Pricing & Payment</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Booking costs, discounts, and payment details</p>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Left Column: Adjustments -->
                    <div class="space-y-6">
                        <h4 class="flex items-center gap-2 font-medium text-slate-700 dark:text-slate-300">
                            <i class="ri-settings-3-line text-primary-500"></i>
                            Price Adjustments
                        </h4>
                        
                        <!-- Custom Subtotal Override -->
                        <div class="group rounded-xl border-2 p-5 transition-all" :class="useCustomSubtotal ? 'border-violet-300 bg-gradient-to-br from-violet-50 to-violet-100/50 shadow-md dark:border-violet-700 dark:from-violet-900/30 dark:to-violet-900/10' : 'border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl shadow" :class="useCustomSubtotal ? 'bg-violet-500 text-white' : 'bg-slate-300 text-slate-600 dark:bg-slate-600 dark:text-slate-300'">
                                        <i class="ri-edit-2-line text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold" :class="useCustomSubtotal ? 'text-violet-700 dark:text-violet-300' : 'text-slate-700 dark:text-slate-300'">Custom Subtotal</p>
                                        <p class="text-xs" :class="useCustomSubtotal ? 'text-violet-500 dark:text-violet-400' : 'text-slate-500 dark:text-slate-400'">Override calculated traveler price</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" v-model="useCustomSubtotal" class="peer sr-only">
                                    <div class="peer h-6 w-11 rounded-full bg-slate-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-violet-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-violet-300 dark:border-slate-600 dark:bg-slate-700 dark:peer-focus:ring-violet-800"></div>
                                </label>
                            </div>
                            <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-32 opacity-100" leave-active-class="transition-all duration-200 ease-in" leave-from-class="max-h-32 opacity-100" leave-to-class="max-h-0 opacity-0">
                                <div v-if="useCustomSubtotal" class="mt-4 overflow-hidden">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg font-bold text-violet-600 dark:text-violet-400">৳</span>
                                        <AppInputText
                                            v-model.number="form.subtotal"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="Enter custom subtotal"
                                            class="flex-1 text-xl font-bold"
                                        />
                                    </div>
                                    <p class="mt-2 flex items-center gap-1 text-xs text-violet-500 dark:text-violet-400">
                                        <i class="ri-information-line"></i>
                                        Package price: ৳{{ formatNumber(defaultSubtotal) }}
                                    </p>
                                </div>
                            </transition>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Discount -->
                            <div class="group rounded-xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-5 transition-all hover:border-emerald-300 hover:shadow-md dark:border-emerald-800 dark:from-emerald-900/30 dark:to-emerald-900/10 dark:hover:border-emerald-700">
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-white shadow">
                                            <i class="ri-discount-percent-line"></i>
                                        </div>
                                        <AppLabel for="discount_amount" class="mb-0 font-medium">Discount</AppLabel>
                                    </div>
                                </div>
                                <AppInputText
                                    v-model.number="form.discount_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :error="form.errors.discount_amount"
                                    placeholder="0.00"
                                    class="text-lg font-semibold"
                                />
                                <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400">
                                    <i class="ri-subtract-line mr-1"></i>
                                    Amount to subtract from total
                                </p>
                            </div>

                            <!-- Tax -->
                            <div class="group rounded-xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 to-amber-100/50 p-5 transition-all hover:border-amber-300 hover:shadow-md dark:border-amber-800 dark:from-amber-900/30 dark:to-amber-900/10 dark:hover:border-amber-700">
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-white shadow">
                                            <i class="ri-percent-line"></i>
                                        </div>
                                        <AppLabel for="tax_amount" class="mb-0 font-medium">Tax</AppLabel>
                                    </div>
                                </div>
                                <AppInputText
                                    v-model.number="form.tax_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :error="form.errors.tax_amount"
                                    placeholder="0.00"
                                    class="text-lg font-semibold"
                                />
                                <p class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                                    <i class="ri-add-line mr-1"></i>
                                    Amount to add to total
                                </p>
                            </div>
                        </div>

                        <!-- Price Breakdown Details -->
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                            <h5 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <i class="ri-list-check-2 text-primary-500"></i>
                                Price Breakdown
                            </h5>
                            
                            <!-- Traveler Breakdown -->
                            <div v-if="selectedPricing" class="mb-4 space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <i class="ri-user-line text-blue-500"></i>
                                        {{ form.adult_count }} Adult{{ form.adult_count > 1 ? 's' : '' }} × ৳{{ formatNumber(selectedPricing.adult_price) }}
                                    </span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">৳{{ formatNumber(form.adult_count * selectedPricing.adult_price) }}</span>
                                </div>
                                <div v-if="form.child_count > 0 && selectedPricing.child_price > 0" class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <i class="ri-user-smile-line text-amber-500"></i>
                                        {{ form.child_count }} Child{{ form.child_count > 1 ? 'ren' : '' }} × ৳{{ formatNumber(selectedPricing.child_price) }}
                                    </span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">৳{{ formatNumber(form.child_count * selectedPricing.child_price) }}</span>
                                </div>
                                <div v-if="form.infant_count > 0 && selectedPricing.infant_price > 0" class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <i class="ri-heart-line text-emerald-500"></i>
                                        {{ form.infant_count }} Infant{{ form.infant_count > 1 ? 's' : '' }} × ৳{{ formatNumber(selectedPricing.infant_price) }}
                                    </span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">৳{{ formatNumber(form.infant_count * selectedPricing.infant_price) }}</span>
                                </div>
                            </div>

                            <!-- Extras Breakdown -->
                            <div v-if="form.extras.length > 0" class="border-t border-slate-200 pt-3 dark:border-slate-700">
                                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-500">Extras</p>
                                <div v-for="extra in form.extras" :key="extra.extra_id" class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                        <i class="ri-add-circle-line text-cyan-500"></i>
                                        {{ getExtraName(extra.extra_id) }} × {{ extra.quantity }}
                                    </span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">৳{{ formatNumber(getExtraTotal(extra.extra_id)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Summary -->
                    <div>
                        <div class="sticky top-4 overflow-hidden rounded-2xl border-2 border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 text-white dark:from-slate-900 dark:to-black">
                                <h4 class="flex items-center gap-2 text-lg font-bold">
                                    <i class="ri-calculator-line"></i>
                                    Order Summary
                                </h4>
                                <p class="mt-1 text-sm text-slate-300">Final pricing calculation</p>
                            </div>
                            
                            <!-- Body -->
                            <div class="p-6">
                                <!-- Line Items -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                            <i class="ri-group-line text-blue-500"></i>
                                            Travelers Subtotal
                                        </span>
                                        <span class="text-lg font-semibold text-slate-800 dark:text-slate-200">৳{{ formatNumber(calculatedSubtotal) }}</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                            <i class="ri-add-circle-line text-cyan-500"></i>
                                            Extras <span v-if="form.extras.length > 0" class="rounded-full bg-cyan-100 px-2 py-0.5 text-xs font-medium text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-300">{{ form.extras.length }}</span>
                                        </span>
                                        <span class="text-lg font-semibold text-slate-800 dark:text-slate-200">৳{{ formatNumber(calculatedExtrasTotal) }}</span>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200 pt-4 dark:border-slate-700">
                                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                            <span>Gross Total</span>
                                            <span class="font-medium">৳{{ formatNumber(calculatedSubtotal + calculatedExtrasTotal) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div v-if="form.discount_amount > 0" class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                                        <span class="flex items-center gap-2">
                                            <i class="ri-discount-percent-line"></i>
                                            Discount
                                        </span>
                                        <span class="font-semibold">- ৳{{ formatNumber(form.discount_amount) }}</span>
                                    </div>
                                    
                                    <div v-if="form.tax_amount > 0" class="flex items-center justify-between text-amber-600 dark:text-amber-400">
                                        <span class="flex items-center gap-2">
                                            <i class="ri-percent-line"></i>
                                            Tax
                                        </span>
                                        <span class="font-semibold">+ ৳{{ formatNumber(form.tax_amount) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Total -->
                                <div class="my-5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600 p-5 text-white shadow-lg shadow-emerald-500/25">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-emerald-100">Grand Total</p>
                                            <p class="text-3xl font-bold">৳{{ formatNumber(calculatedTotal) }}</p>
                                        </div>
                                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/20 backdrop-blur">
                                            <i class="ri-money-dollar-circle-line text-3xl"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Payment Status -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 p-4 dark:from-blue-900/30 dark:to-blue-900/10">
                                        <div class="mb-2 flex items-center gap-2">
                                            <i class="ri-checkbox-circle-fill text-blue-500"></i>
                                            <span class="text-xs font-medium uppercase tracking-wide text-blue-600 dark:text-blue-400">Paid</span>
                                        </div>
                                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">৳{{ formatNumber(form.amount_paid) }}</p>
                                    </div>
                                    <div class="rounded-xl bg-gradient-to-br from-rose-50 to-rose-100 p-4 dark:from-rose-900/30 dark:to-rose-900/10">
                                        <div class="mb-2 flex items-center gap-2">
                                            <i class="ri-time-line text-rose-500"></i>
                                            <span class="text-xs font-medium uppercase tracking-wide text-rose-600 dark:text-rose-400">Due</span>
                                        </div>
                                        <p class="text-2xl font-bold text-rose-700 dark:text-rose-300">৳{{ formatNumber(calculatedAmountDue) }}</p>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div v-if="calculatedTotal > 0" class="mt-5">
                                    <div class="mb-2 flex items-center justify-between text-xs">
                                        <span class="text-slate-500 dark:text-slate-400">Payment Progress</span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ paymentPercentage }}%</span>
                                    </div>
                                    <div class="h-3 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                                        <div 
                                            class="h-full rounded-full transition-all duration-500"
                                            :class="paymentPercentage >= 100 ? 'bg-gradient-to-r from-emerald-500 to-green-500' : paymentPercentage > 0 ? 'bg-gradient-to-r from-blue-500 to-cyan-500' : 'bg-slate-300'"
                                            :style="{ width: Math.min(paymentPercentage, 100) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Status & Administrative -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 shadow-lg shadow-slate-500/25">
                        <i class="ri-settings-4-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Status & Administrative</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Booking status, payment status, and internal notes</p>
                    </div>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-checkbox-circle-line text-primary-500"></i>
                            <AppLabel for="status" class="mb-0">Booking Status</AppLabel>
                        </div>
                        <AppSelect
                            v-model="form.status"
                            :options="statusOptions"
                            :error="form.errors.status"
                        />
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-secure-payment-line text-emerald-500"></i>
                            <AppLabel for="payment_status" class="mb-0">Payment Status</AppLabel>
                        </div>
                        <AppSelect
                            v-model="form.payment_status"
                            :options="paymentStatusOptions"
                            :error="form.errors.payment_status"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 dark:border-slate-600 dark:bg-slate-800/30">
                            <div class="mb-3 flex items-center gap-2">
                                <i class="ri-lock-line text-slate-500"></i>
                                <AppLabel for="internal_notes" class="mb-0">Internal Notes</AppLabel>
                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-400">Private</span>
                            </div>
                            <AppTextArea
                                v-model="form.internal_notes"
                                :error="form.errors.internal_notes"
                                rows="4"
                                placeholder="Internal notes (not visible to customer)..."
                            />
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Form Actions -->
        <div class="sticky bottom-0 -mx-4 border-t border-slate-200 bg-white/95 px-4 py-4 backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/95 md:-mx-6 md:px-6">
            <div class="flex items-center justify-between gap-4">
                <p class="hidden text-sm text-slate-500 dark:text-slate-400 sm:block">
                    <i class="ri-information-line mr-1"></i>
                    All fields marked with <span class="text-red-500">*</span> are required
                </p>
                <div class="flex gap-3">
                    <AppButton
                        type="button"
                        class="btn btn-secondary group"
                        @click="$inertia.visit(route('tourBooking.index'))"
                    >
                        <i class="ri-close-line mr-1.5 transition-transform group-hover:rotate-90"></i>
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        class="btn btn-primary group"
                        :disabled="form.processing"
                    >
                        <i v-if="form.processing" class="ri-loader-4-line mr-1.5 animate-spin"></i>
                        <i v-else class="ri-save-line mr-1.5 transition-transform group-hover:scale-110"></i>
                        {{ isEditing ? 'Update Booking' : 'Create Booking' }}
                    </AppButton>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Booking Form')

const props = defineProps({
    booking: Object,
    tours: Array,
    customers: Array,
    agents: Array,
})

const isEditing = computed(() => !!props.booking)

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Bookings', href: route('tourBooking.index') },
    { label: isEditing.value ? 'Edit Booking' : 'Create Booking', last: true }
]

// Custom subtotal toggle - enabled if subtotal differs from calculated
const useCustomSubtotal = ref(false)

// Form initialization
const form = useForm({
    tour_id: props.booking?.tour_id || null,
    pricing_id: props.booking?.pricing_id || null,
    customer_id: props.booking?.customer_id || null,
    agent_id: props.booking?.agent_id || null,
    travel_date: props.booking?.travel_date || '',
    status: props.booking?.status || 'pending',
    payment_status: props.booking?.payment_status || 'unpaid',
    adult_count: props.booking?.adult_count || 1,
    child_count: props.booking?.child_count || 0,
    infant_count: props.booking?.infant_count || 0,
    contact_name: props.booking?.contact_name || '',
    contact_email: props.booking?.contact_email || '',
    contact_phone: props.booking?.contact_phone || '',
    whatsapp: props.booking?.whatsapp || '',
    contact_address: props.booking?.contact_address || '',
    emergency_contact: props.booking?.emergency_contact || '',
    emergency_phone: props.booking?.emergency_phone || '',
    special_requests: props.booking?.special_requests || '',
    dietary_requirements: props.booking?.dietary_requirements || '',
    medical_conditions: props.booking?.medical_conditions || '',
    subtotal: props.booking?.subtotal || 0,
    extras_total: props.booking?.extras_total || 0,
    discount_amount: props.booking?.discount_amount || 0,
    discount_code: props.booking?.discount_code || '',
    tax_amount: props.booking?.tax_amount || 0,
    total_amount: props.booking?.total_amount || 0,
    amount_paid: props.booking?.amount_paid || 0,
    amount_due: props.booking?.amount_due || 0,
    currency: props.booking?.currency || 'BDT',
    booking_source: props.booking?.booking_source || 'admin',
    internal_notes: props.booking?.internal_notes || '',
    travelers: props.booking?.travelers || [],
    extras: initializeExtras(),
})

// Initialize extras from booking data
function initializeExtras() {
    if (!props.booking?.extras) return []
    return props.booking.extras.map(e => ({
        extra_id: e.extra_id || e.extra?.id,
        quantity: e.quantity || 1,
    }))
}

// Options
const tourOptions = computed(() => 
    props.tours?.map(tour => ({ 
        value: tour.id, 
        label: tour.name 
    })) || []
)

const customerOptions = computed(() => 
    props.customers?.map(customer => ({ 
        value: customer.id, 
        label: `${customer.name}${customer.email ? ' - ' + customer.email : ''}${customer.phone ? ' - ' + customer.phone : ''}` 
    })) || []
)

const agentOptions = computed(() => 
    props.agents?.map(agent => ({ 
        value: agent.id, 
        label: `${agent.name}${agent.email ? ' - ' + agent.email : ''}${agent.phone ? ' - ' + agent.phone : ''}` 
    })) || []
)

const selectedTour = computed(() => {
    if (!form.tour_id) return null
    return props.tours?.find(tour => tour.id === form.tour_id)
})

const pricingOptions = computed(() => {
    if (!selectedTour.value) return []
    const pricings = selectedTour.value.pricings || []
    return pricings.map(p => ({
        id: p.id,
        name: p.name,
        adult_price: p.adult_price || 0,
        child_price: p.child_price || 0,
        infant_price: p.infant_price || 0,
    }))
})

const pricingSelectOptions = computed(() => 
    pricingOptions.value.map(p => ({
        value: p.id,
        label: p.name
    }))
)

// Tour extras
const tourExtras = computed(() => {
    if (!selectedTour.value) return []
    return selectedTour.value.extras || []
})

const bookingSourceOptions = [
    { value: 'website', label: 'Website' },
    { value: 'admin', label: 'Admin Panel' },
    { value: 'agent', label: 'Agent' },
    { value: 'api', label: 'API' },
]

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'refunded', label: 'Refunded' },
]

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid' },
    { value: 'partial', label: 'Partial' },
    { value: 'paid', label: 'Paid' },
    { value: 'refunded', label: 'Refunded' },
]

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
]

const currencyOptions = [
    { value: 'BDT', label: 'BDT (৳)' },
    { value: 'USD', label: 'USD ($)' },
    { value: 'EUR', label: 'EUR (€)' },
]

// Computed
const totalTravelers = computed(() => 
    form.adult_count + form.child_count + form.infant_count
)

const selectedPricing = computed(() => 
    pricingOptions.value.find(p => p.id === form.pricing_id)
)

const defaultSubtotal = computed(() => {
    if (!selectedPricing.value) return 0
    
    const adultTotal = form.adult_count * (selectedPricing.value.adult_price || 0)
    const childTotal = form.child_count * (selectedPricing.value.child_price || 0)
    const infantTotal = form.infant_count * (selectedPricing.value.infant_price || 0)
    
    return adultTotal + childTotal + infantTotal
})

const calculatedSubtotal = computed(() => {
    if (useCustomSubtotal.value && form.subtotal > 0) {
        return parseFloat(form.subtotal)
    }
    return defaultSubtotal.value
})

const calculatedExtrasTotal = computed(() => {
    if (!form.extras || form.extras.length === 0) return 0
    
    let total = 0
    form.extras.forEach(extra => {
        const tourExtra = tourExtras.value.find(e => e.id === extra.extra_id)
        if (tourExtra) {
            const extraPrice = tourExtra.price || 0
            const quantity = extra.quantity || 1
            
            // Handle different price types
            if (tourExtra.price_type === 'per_person') {
                total += extraPrice * quantity * (form.adult_count + form.child_count)
            } else {
                // per_booking or per_day
                total += extraPrice * quantity
            }
        }
    })
    
    return total
})

const calculatedTotal = computed(() => {
    return calculatedSubtotal.value + 
           calculatedExtrasTotal.value - 
           parseFloat(form.discount_amount || 0) + 
           parseFloat(form.tax_amount || 0)
})

const calculatedAmountDue = computed(() => {
    return Math.max(0, calculatedTotal.value - form.amount_paid)
})

const paymentPercentage = computed(() => {
    if (calculatedTotal.value <= 0) return 0
    return Math.round((form.amount_paid / calculatedTotal.value) * 100)
})

// Helper methods for extras breakdown
const getExtraName = (extraId) => {
    const extra = tourExtras.value.find(e => e.id === extraId)
    return extra?.name || 'Unknown Extra'
}

const getExtraTotal = (extraId) => {
    const selectedExtra = form.extras.find(e => e.extra_id === extraId)
    if (!selectedExtra) return 0
    
    const tourExtra = tourExtras.value.find(e => e.id === extraId)
    if (!tourExtra) return 0
    
    const quantity = selectedExtra.quantity || 1
    const price = tourExtra.price || 0
    
    if (tourExtra.price_type === 'per_person') {
        return price * quantity * (form.adult_count + form.child_count)
    }
    return price * quantity
}

// Methods
const formatNumber = (num) => new Intl.NumberFormat('en-BD').format(num || 0)

const getTravelerTypeBadge = (type) => {
    const badges = {
        adult: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        child: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        infant: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    }
    return badges[type] || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400'
}

const getTravelerAvatarClass = (type) => {
    const classes = {
        adult: 'bg-gradient-to-br from-blue-500 to-indigo-600 shadow-blue-500/30',
        child: 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/30',
        infant: 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/30',
    }
    return classes[type] || 'bg-gradient-to-br from-slate-500 to-slate-600 shadow-slate-500/30'
}

const getTravelerTypeIcon = (type) => {
    const icons = {
        adult: 'ri-user-line',
        child: 'ri-user-smile-line',
        infant: 'ri-heart-line',
    }
    return icons[type] || 'ri-user-line'
}

// Extras methods
const isExtraSelected = (extraId) => {
    return form.extras.some(e => e.extra_id === extraId)
}

const getExtraQuantity = (extraId) => {
    const extra = form.extras.find(e => e.extra_id === extraId)
    return extra ? extra.quantity : 0
}

const toggleExtra = (extra) => {
    const index = form.extras.findIndex(e => e.extra_id === extra.id)
    if (index > -1) {
        form.extras.splice(index, 1)
    } else {
        form.extras.push({
            extra_id: extra.id,
            quantity: 1
        })
    }
}

const updateExtraQuantity = (extraId, delta) => {
    const extra = form.extras.find(e => e.extra_id === extraId)
    if (extra) {
        const tourExtra = tourExtras.value.find(e => e.id === extraId)
        const maxQty = tourExtra?.max_quantity || 99
        extra.quantity = Math.max(1, Math.min(maxQty, extra.quantity + delta))
    }
}

const calculateExtraTotal = (extra) => {
    const selectedExtra = form.extras.find(e => e.extra_id === extra.id)
    if (!selectedExtra) return 0
    
    const quantity = selectedExtra.quantity || 1
    const price = extra.price || 0
    
    if (extra.price_type === 'per_person') {
        return price * quantity * (form.adult_count + form.child_count)
    }
    return price * quantity
}

const getPriceTypeLabel = (priceType) => {
    const labels = {
        'per_person': 'Per Person',
        'per_booking': 'Per Booking',
        'per_day': 'Per Day'
    }
    return labels[priceType] || priceType
}

const onTourChange = () => {
    console.log('Tour changed, tour_id:', form.tour_id)
    console.log('Selected tour:', selectedTour.value)
    console.log('Pricing options:', pricingOptions.value)
    
    // Reset pricing when tour changes
    form.pricing_id = null
    
    // Auto-select first pricing if available
    if (pricingOptions.value.length > 0) {
        form.pricing_id = pricingOptions.value[0].id
        console.log('Auto-selected pricing:', form.pricing_id)
    }
    
    // Reset extras when tour changes
    form.extras = []
    
    // Reset calculated values
    form.subtotal = 0
    form.total_amount = 0
}

const onCustomerChange = (customerId) => {
    if (!customerId) return
    
    const customer = props.customers?.find(c => c.id === customerId)
    if (!customer) return
    
    // Auto-fill contact details from customer
    if (!isEditing.value || !form.contact_name) {
        form.contact_name = customer.name || ''
    }
    if (!isEditing.value || !form.contact_email) {
        form.contact_email = customer.email || ''
    }
    if (!isEditing.value || !form.contact_phone) {
        form.contact_phone = customer.phone || ''
    }
    if (!isEditing.value || !form.whatsapp) {
        form.whatsapp = customer.whatsapp || customer.phone || ''
    }
    if (!isEditing.value || !form.contact_address) {
        form.contact_address = customer.address || ''
    }
}

const onAgentChange = (agentId) => {
    if (!agentId) return
    
    const agent = props.agents?.find(a => a.id === agentId)
    if (!agent) return
    
    // Auto-fill contact details from agent
    if (!isEditing.value || !form.contact_name) {
        form.contact_name = agent.name || ''
    }
    if (!isEditing.value || !form.contact_email) {
        form.contact_email = agent.email || ''
    }
    if (!isEditing.value || !form.contact_phone) {
        form.contact_phone = agent.phone || ''
    }
    if (!isEditing.value || !form.whatsapp) {
        form.whatsapp = agent.whatsapp || agent.phone || ''
    }
    if (!isEditing.value || !form.contact_address) {
        form.contact_address = agent.address || ''
    }
}

const updateTravelerForms = () => {
    const newTravelers = []
    
    // Add adults
    for (let i = 0; i < form.adult_count; i++) {
        newTravelers.push(form.travelers[i] || createTraveler('adult', i === 0))
    }
    
    // Add children
    for (let i = 0; i < form.child_count; i++) {
        const index = form.adult_count + i
        newTravelers.push(form.travelers[index] || createTraveler('child', false))
    }
    
    // Add infants
    for (let i = 0; i < form.infant_count; i++) {
        const index = form.adult_count + form.child_count + i
        newTravelers.push(form.travelers[index] || createTraveler('infant', false))
    }
    
    form.travelers = newTravelers
}

const createTraveler = (type, isLead = false) => ({
    type,
    is_lead: isLead,
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    date_of_birth: '',
    gender: '',
    nationality: '',
    passport_number: '',
    passport_expiry: '',
    passport_country: '',
    nid_number: '',
    dietary_requirements: '',
    medical_conditions: '',
    room_preference: '',
    special_requests: '',
})

const updateLeadTraveler = (index) => {
    if (form.travelers[index].is_lead) {
        form.travelers.forEach((traveler, i) => {
            if (i !== index) {
                traveler.is_lead = false
            }
        })
    }
}

const submit = () => {
    // Update calculated values
    form.subtotal = calculatedSubtotal.value
    form.extras_total = calculatedExtrasTotal.value
    form.total_amount = calculatedTotal.value
    form.amount_due = calculatedTotal.value - form.amount_paid
    
    if (isEditing.value) {
        form.put(route('tourBooking.update', props.booking.id))
    } else {
        form.post(route('tourBooking.store'))
    }
}

// Initialize travelers on mount
if (!isEditing.value || form.travelers.length === 0) {
    updateTravelerForms()
}

// Watch for pricing changes to update subtotal
watch(() => form.pricing_id, () => {
    form.subtotal = calculatedSubtotal.value
})

// Clear custom subtotal when toggle is turned off
watch(useCustomSubtotal, (enabled) => {
    if (!enabled) {
        form.subtotal = defaultSubtotal.value
    }
})

// Initialize custom subtotal toggle based on existing data
if (props.booking && props.booking.subtotal) {
    // We'll check after pricing is loaded if subtotal differs from calculated
    watch(() => selectedPricing.value, (pricing) => {
        if (pricing && props.booking) {
            const calculated = (props.booking.adult_count * (pricing.adult_price || 0)) +
                              (props.booking.child_count * (pricing.child_price || 0)) +
                              (props.booking.infant_count * (pricing.infant_price || 0))
            if (Math.abs(props.booking.subtotal - calculated) > 0.01) {
                useCustomSubtotal.value = true
            }
        }
    }, { immediate: true })
}
</script>
