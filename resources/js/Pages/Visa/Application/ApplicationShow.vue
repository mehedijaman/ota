<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                 <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visaApplication.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>

                <AppButton
                    v-if="can('visa-application-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visaApplication.edit', application.id))"
                >
                    <i class="ri-pencil-line mr-1"></i>
                    Edit Application
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Key Info Card -->
            <AppCard class="overflow-hidden border-t-4 border-t-primary-500">
                <template #content>
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-neutral-900">{{ application.application_number }}</h2>
                            <div class="mt-2 flex items-center gap-3">
                                <span 
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset uppercase tracking-wider"
                                    :class="getStatusColor(application.status)"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ application.status }}
                                </span>
                                <span 
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset uppercase tracking-wider"
                                    :class="getPaymentStatusColor(application.payment_status)"
                                >
                                    <i class="ri-money-dollar-circle-line"></i>
                                    {{ application.payment_status }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                             <p class="text-neutral-500 text-sm">Applied On</p>
                             <p class="text-neutral-900 font-medium">{{ formatDate(application.created_at) }}</p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-neutral-100 pt-6">
                        <!-- Visa Details -->
                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <i class="ri-passport-line text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Visa Details</p>
                                <p class="text-neutral-900 font-bold mt-0.5">{{ application.visa?.name || '-' }}</p>
                                <p v-if="application.visa?.country" class="text-neutral-600 text-sm mt-0.5 flex items-center gap-1">
                                    <img :src="application.visa.country.flag_url" class="h-3 w-4 object-cover rounded-[1px]" v-if="application.visa.country.flag_url" />
                                    {{ application.visa.country.name }}
                                </p>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                <i class="ri-user-line text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Contact Person</p>
                                <p class="text-neutral-900 font-bold mt-0.5">{{ application.contact_name }}</p>
                                <div class="text-neutral-600 text-sm mt-0.5 space-y-0.5">
                                    <p class="flex items-center gap-1"><i class="ri-phone-line text-xs"></i> {{ application.contact_phone }}</p>
                                    <p class="flex items-center gap-1"><i class="ri-mail-line text-xs"></i> {{ application.contact_email }}</p>
                                </div>
                            </div>
                        </div>

                         <!-- Travel Dates -->
                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                <i class="ri-calendar-event-line text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Travel Dates</p>
                                <div class="text-neutral-900 text-sm mt-1 space-y-1">
                                    <p><span class="text-neutral-500 w-16 inline-block">Travel:</span> <span class="font-medium">{{ formatDate(application.intended_travel_date) }}</span></p>
                                    <p v-if="application.intended_return_date"><span class="text-neutral-500 w-16 inline-block">Return:</span> <span class="font-medium">{{ formatDate(application.intended_return_date) }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Tabs Section -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-neutral-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.name"
                            @click="currentTab = tab.name"
                            :class="[
                                currentTab === tab.name
                                    ? 'border-primary-500 text-primary-600'
                                    : 'border-transparent text-neutral-500 hover:border-neutral-300 hover:text-neutral-700',
                                'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors'
                            ]"
                        >
                            <i :class="[tab.icon, 'mr-2']"></i>
                            {{ tab.label }}
                            <span v-if="tab.count" class="ml-2 rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 ring-1 ring-inset ring-neutral-500/10">{{ tab.count }}</span>
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Travelers Tab -->
                    <div v-if="currentTab === 'travelers'" class="animate-fade-in">
                        <div class="overflow-hidden rounded-lg border border-neutral-200">
                             <table class="min-w-full divide-y divide-neutral-200">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Passport</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Nationality</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-neutral-200">
                                    <tr v-for="traveler in application.travelers" :key="traveler.id" class="hover:bg-neutral-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 text-xs font-bold mr-3">
                                                    {{ getInitials(traveler.first_name + ' ' + traveler.last_name) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-neutral-900">
                                                        {{ traveler.first_name }} {{ traveler.last_name }}
                                                        <span v-if="traveler.is_lead" class="ml-2 inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Lead</span>
                                                    </div>
                                                    <div class="text-xs text-neutral-500 flex items-center mt-0.5">
                                                        <i v-if="traveler.gender === 'male'" class="ri-men-line text-blue-500 mr-1"></i>
                                                        <i v-if="traveler.gender === 'female'" class="ri-women-line text-pink-500 mr-1"></i>
                                                        {{ formatDate(traveler.date_of_birth) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 capitalize">{{ traveler.type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                             <div class="text-sm text-neutral-900 font-mono">{{ traveler.passport_number }}</div>
                                             <div class="text-xs text-neutral-500">Exp: {{ formatDate(traveler.passport_expiry_date) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">{{ traveler.nationality }}</td>
                                    </tr>
                                    <tr v-if="!application.travelers?.length">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-neutral-500">No travelers added.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="currentTab === 'documents'" class="animate-fade-in">
                        <div v-if="application.documents?.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="doc in application.documents" :key="doc.id" class="flex items-center p-3 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-white hover:shadow-sm transition-all group">
                                <div class="h-10 w-10 shrink-0 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-xl mr-3">
                                    <i class="ri-file-pdf-line"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-900 truncate">{{ doc.name || 'Document' }}</p>
                                    <p class="text-xs text-neutral-500">Uploaded on {{ formatDate(doc.created_at) }}</p>
                                </div>
                                <a :href="doc.url" target="_blank" class="p-2 text-neutral-400 hover:text-primary-600 transition-colors">
                                    <i class="ri-download-line text-lg"></i>
                                </a>
                            </div>
                        </div>
                         <AppEmptyState
                            v-else
                            icon="ri-file-copy-2-line"
                            title="No Documents"
                            description="No documents have been uploaded for this application."
                            class="py-8"
                        />
                    </div>

                    <!-- Payments Tab -->
                    <div v-if="currentTab === 'payments'" class="animate-fade-in">
                        <div class="overflow-hidden rounded-lg border border-neutral-200 mb-6">
                            <table class="min-w-full divide-y divide-neutral-200">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Method</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider pl-8">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-neutral-200">
                                    <tr v-for="payment in application.payments" :key="payment.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">{{ formatDate(payment.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 font-mono">{{ payment.transaction_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 capitalize">{{ payment.method }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 font-medium text-right">৳{{ formatPrice(payment.amount) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap pl-8">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize" 
                                                :class="payment.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                                {{ payment.status }}
                                            </span>
                                        </td>
                                    </tr>
                                     <tr v-if="!application.payments?.length">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-neutral-500">No payment records found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Timeline Tab -->
                    <div v-if="currentTab === 'timeline'" class="animate-fade-in">
                        <div class="relative pl-4 border-l-2 border-neutral-200 space-y-8 my-4">
                            <div v-for="(history, index) in application.statusHistories" :key="history.id" class="relative">
                                <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full border-2 border-white bg-primary-500 ring-4 ring-primary-50"></div>
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1">
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-900 capitalize">{{ getHistoryTitle(history) }}</p>
                                        <p v-if="history.notes" class="mt-1 text-sm text-neutral-600 bg-neutral-50 p-2 rounded border border-neutral-100">{{ history.notes }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs text-neutral-500">{{ formatDate(history.created_at) }}</p>
                                        <p class="text-xs text-neutral-400">{{ formatTime(history.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                             <div class="relative">
                                <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full border-2 border-white bg-neutral-300"></div>
                                <div>
                                    <p class="text-sm font-medium text-neutral-500">Application Created</p>
                                    <p class="text-xs text-neutral-400">{{ formatDate(application.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                     <!-- Notes Tab -->
                    <div v-if="currentTab === 'notes'" class="animate-fade-in">
                        <div v-if="application.status_note" class="mb-4">
                            <h4 class="text-sm font-medium text-neutral-900 mb-2">Status Note (Public)</h4>
                            <div class="bg-amber-50 border border-amber-100 p-3 rounded-lg text-sm text-amber-800">
                                {{ application.status_note }}
                            </div>
                        </div>
                        <div v-if="application.internal_notes">
                            <h4 class="text-sm font-medium text-neutral-900 mb-2">Internal Notes (Private)</h4>
                            <div class="bg-neutral-50 border border-neutral-200 p-4 rounded-lg text-sm text-neutral-700 whitespace-pre-wrap">
                                {{ application.internal_notes }}
                            </div>
                        </div>
                         <AppEmptyState
                            v-if="!application.status_note && !application.internal_notes"
                            icon="ri-sticky-note-line"
                            title="No Notes"
                            description="No internal or status notes available."
                            class="py-8"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Payment Summary -->
            <AppCard>
                <template #title> Payment Breakdown </template>
                <template #content>
                    <div class="space-y-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-neutral-600">
                                <span>Visa Fee (x{{ application.total_travelers }})</span>
                                <span>৳{{ formatPrice(application.subtotal + application.embassy_fees + application.service_fees) }}</span>
                            </div>
                             <div v-if="application.express_fees > 0" class="flex justify-between text-neutral-600">
                                <span>Express Fee</span>
                                <span>৳{{ formatPrice(application.express_fees) }}</span>
                            </div>
                             <div v-if="application.additional_fees > 0" class="flex justify-between text-neutral-600">
                                <span>Additional Fees</span>
                                <span>৳{{ formatPrice(application.additional_fees) }}</span>
                            </div>
                             <div v-if="application.tax_amount > 0" class="flex justify-between text-neutral-600">
                                <span>Tax</span>
                                <span>৳{{ formatPrice(application.tax_amount) }}</span>
                            </div>
                            <div v-if="application.discount_amount > 0" class="flex justify-between text-emerald-600">
                                <span>Discount</span>
                                <span>-৳{{ formatPrice(application.discount_amount) }}</span>
                            </div>
                            
                            <div class="border-t border-dashed border-neutral-300 my-2"></div>
                            
                            <div class="flex justify-between font-bold text-neutral-900 text-lg">
                                <span>Total</span>
                                <span>৳{{ formatPrice(application.total_amount) }}</span>
                            </div>
                            <div class="flex justify-between text-emerald-600 font-medium">
                                <span>Paid</span>
                                <span>৳{{ formatPrice(application.amount_paid) }}</span>
                            </div>
                             <div class="flex justify-between text-red-600 font-medium">
                                <span>Due</span>
                                <span>৳{{ formatPrice(application.amount_due) }}</span>
                            </div>
                        </div>

                         <AppButton
                            v-if="application.amount_due > 0 && can('visa-application-edit')"
                            class="btn btn-primary w-full shadow-sm"
                            @click="$inertia.visit(route('visaApplication.edit', application.id))"
                        >
                            Record Payment
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <!-- Quick Actions -->
             <AppCard title="Quick Actions">
                <template #content>
                    <div class="space-y-2">
                         <h4 class="text-xs font-semibold text-neutral-400 uppercase tracking-wider mb-2">Update Status</h4>
                         <div class="grid grid-cols-1 gap-2">
                             <button
                                v-if="application.status !== 'approved' && can('visa-application-edit')"
                                @click="updateStatus('approved')"
                                class="flex items-center w-full px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200"
                            >
                                <i class="ri-check-double-line mr-2"></i> Approve Application
                            </button>
                             <button
                                v-if="application.status !== 'rejected' && can('visa-application-edit')"
                                @click="updateStatus('rejected')"
                                class="flex items-center w-full px-3 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200"
                            >
                                <i class="ri-close-circle-line mr-2"></i> Reject Application
                            </button>
                             <button
                                v-if="application.status !== 'processing' && can('visa-application-edit')"
                                @click="updateStatus('processing')"
                                class="flex items-center w-full px-3 py-2 text-sm font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors border border-amber-200"
                            >
                                <i class="ri-loader-4-line mr-2"></i> Mark as Processing
                            </button>
                             <button
                                v-if="application.status !== 'completed' && application.status === 'approved' && can('visa-application-edit')"
                                @click="updateStatus('completed')"
                                class="flex items-center w-full px-3 py-2 text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors border border-purple-200"
                            >
                                <i class="ri-flag-line mr-2"></i> Mark as Completed
                            </button>
                         </div>
                    </div>
                </template>
            </AppCard>

            <!-- Related Info -->
            <AppCard>
                 <template #title> Related Info </template>
                 <template #content>
                    <div class="space-y-4">
                        <div v-if="application.customer">
                            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider mb-2">Registered Customer</p>
                            <div class="flex items-center gap-3">
                                 <div class="h-9 w-9 rounded-full bg-primary-50 flex items-center justify-center text-primary-600 font-bold ring-1 ring-primary-100">
                                    {{ getInitials(application.customer.name) }}
                                </div>
                                <div>
                                    <p class="text-neutral-900 font-medium text-sm">{{ application.customer.name }}</p>
                                    <p class="text-neutral-500 text-xs text-ellipsis overflow-hidden w-40">{{ application.customer.email }}</p>
                                </div>
                            </div>
                        </div>

                         <div v-if="application.agent" class="pt-4 border-t border-neutral-100">
                            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider mb-2">Agent</p>
                            <div class="flex items-center gap-3">
                                 <div class="h-9 w-9 rounded-full bg-violet-50 flex items-center justify-center text-violet-600 font-bold ring-1 ring-violet-100">
                                    {{ getInitials(application.agent.name) }}
                                </div>
                                <div>
                                    <p class="text-neutral-900 font-medium text-sm">{{ application.agent.name }}</p>
                                    <p class="text-neutral-500 text-xs">{{ application.agent.email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-neutral-100">
                            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider mb-2">Metadata</p>
                            <div class="text-xs text-neutral-600 space-y-1">
                                <p>Source: <span class="capitalize">{{ application.application_source }}</span></p>
                                <p>Created: {{ formatDate(application.created_at) }}</p>
                                <p v-if="application.submitted_at">Submitted: {{ formatDate(application.submitted_at) }}</p>
                            </div>
                        </div>
                    </div>
                 </template>
            </AppCard>
        </div>
    </div>
    
    <AppConfirmDialog ref="confirmDialog" />
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppConfirmDialog from '@/Components/Overlay/AppConfirmDialog.vue'

const { can } = useAuthCan()
const confirmDialog = ref(null)

const props = defineProps({
    application: {
        type: Object,
        required: true
    }
})

const { title } = useTitle(`Application #${props.application.application_number}`)

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Applications', href: route('visaApplication.index') },
    { label: props.application.application_number, last: true }
]

// Tabs
const currentTab = ref('travelers')
const tabs = computed(() => [
    { name: 'travelers', label: 'Travelers', icon: 'ri-group-line', count: props.application.travelers?.length },
    { name: 'documents', label: 'Documents', icon: 'ri-file-list-2-line', count: props.application.documents?.length },
    { name: 'payments', label: 'Payments', icon: 'ri-money-dollar-circle-line', count: props.application.payments?.length },
    { name: 'timeline', label: 'Timeline', icon: 'ri-history-line', count: 0 },
    { name: 'notes', label: 'Notes', icon: 'ri-sticky-note-line', count: 0 },
])

const formatPrice = (price) => {
    return Number(price || 0).toLocaleString('en-BD', { minimumFractionDigits: 2 })
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getInitials = (name) => {
    if (!name) return '?'
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
}

const getHistoryTitle = (history) => {
    if (history.from_status) {
        return `Status updated to ${history.to_status}`
    }
    return `Status set to ${history.to_status}`
}

const getStatusColor = (status) => {
    switch (status) {
        case 'submitted': return 'bg-blue-50 text-blue-700 ring-blue-600/20'
        case 'processing': return 'bg-amber-50 text-amber-700 ring-amber-600/20'
        case 'approved': return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
        case 'rejected': return 'bg-red-50 text-red-700 ring-red-600/20'
        case 'completed': return 'bg-purple-50 text-purple-700 ring-purple-600/20'
        case 'cancelled': return 'bg-neutral-50 text-neutral-700 ring-neutral-500/10'
        default: return 'bg-neutral-50 text-neutral-700 ring-neutral-500/10'
    }
}

const getPaymentStatusColor = (status) => {
     switch (status) {
        case 'paid': return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
        case 'partial': return 'bg-amber-50 text-amber-700 ring-amber-600/20'
        case 'pending': return 'bg-red-50 text-red-700 ring-red-600/20'
        default: return 'bg-neutral-50 text-neutral-700 ring-neutral-500/10'
    }
}

const updateStatus = (status) => {
    const isDestructive = ['rejected', 'cancelled'].includes(status)
    const statusLabel = status.charAt(0).toUpperCase() + status.slice(1)
    
    confirmDialog.value.openCustomModal({
        title: `Update Status to ${statusLabel}`,
        message: `Are you sure you want to change the application status to ${status}? This will update the timeline and notify relevant parties.`,
        buttonText: `Mark as ${statusLabel}`,
        buttonClass: isDestructive ? 'btn btn-destructive' : 'btn btn-primary',
        modalType: isDestructive ? 'danger' : 'primary',
        onConfirm: () => {
             router.patch(route('visaApplication.updateStatus', { 
                id: props.application.id, 
                status: status 
            }), {}, {
                preserveScroll: true
            })
        }
    })
}
</script>
