<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('visa-application-view')"
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
                    <i class="ri-edit-line mr-1"></i>
                    Edit Application
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Application Overview -->
            <AppCard>
                <template #title>
                    <div class="flex items-center justify-between">
                        <span>Application #{{ application.application_number }}</span>
                        <span :class="getStatusColor(application.status)" class="px-3 py-1 rounded-full text-xs font-bold uppercase ring-1">
                            {{ application.status }}
                        </span>
                    </div>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-neutral-9 text-xs uppercase font-medium mb-1">Visa Package</p>
                            <p class="text-neutral-12 font-medium text-lg">{{ application.visa?.name || '-' }}</p>
                            <p v-if="application.visa?.country" class="text-neutral-11 text-sm mt-1">
                                <i class="ri-map-pin-line mr-1"></i>{{ application.visa.country.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-neutral-9 text-xs uppercase font-medium mb-1">Dates</p>
                             <div class="flex flex-col gap-1">
                                <span class="text-neutral-12 text-sm">
                                    <span class="text-neutral-9 w-24 inline-block">Applied:</span>
                                    {{ formatDate(application.application_date) }}
                                </span>
                                <span v-if="application.intended_travel_date" class="text-neutral-12 text-sm">
                                    <span class="text-neutral-9 w-24 inline-block">Travel:</span>
                                    {{ formatDate(application.intended_travel_date) }}
                                </span>
                                <span v-if="application.intended_return_date" class="text-neutral-12 text-sm">
                                    <span class="text-neutral-9 w-24 inline-block">Return:</span>
                                    {{ formatDate(application.intended_return_date) }}
                                </span>
                             </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-neutral-4">
                        <p class="text-neutral-9 text-xs uppercase font-medium mb-3">Applicant / Contact</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-neutral-12 font-medium">{{ application.contact_name }}</p>
                                <p class="text-neutral-11 text-sm">{{ application.contact_email }}</p>
                                <p class="text-neutral-11 text-sm">{{ application.contact_phone }}</p>
                            </div>
                            <div v-if="application.customer">
                                <p class="text-neutral-9 text-xs mb-1">Registered Customer:</p>
                                <div class="flex items-center gap-2">
                                     <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs ring-1 ring-primary-200">
                                        {{ getInitials(application.customer.name) }}
                                    </div>
                                    <span class="text-neutral-12 text-sm font-medium">{{ application.customer.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </AppCard>
            
            <!-- Travelers -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="ri-group-line text-neutral-9"></i>
                        <span>Travelers</span>
                        <span class="bg-neutral-2 text-neutral-11 px-2 py-0.5 rounded-full text-xs">{{ application.travelers?.length || 0 }}</span>
                    </div>
                </template>
                <template #content>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-neutral-9 border-b border-neutral-3">
                                    <th class="pb-2 font-medium">Name</th>
                                    <th class="pb-2 font-medium">Type</th>
                                    <th class="pb-2 font-medium">Passport Info</th>
                                    <th class="pb-2 font-medium">Nationality</th>
                                    <th class="pb-2 font-medium">DOB</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-3">
                                <tr v-for="traveler in application.travelers" :key="traveler.id" class="text-neutral-11">
                                    <td class="py-3 font-medium text-neutral-12">
                                        {{ traveler.first_name }} {{ traveler.last_name }}
                                        <i v-if="traveler.gender === 'male'" class="ri-men-line text-blue-500 ml-1"></i>
                                        <i v-if="traveler.gender === 'female'" class="ri-women-line text-pink-500 ml-1"></i>
                                    </td>
                                    <td class="py-3 capitalize">{{ traveler.type }}</td>
                                    <td class="py-3">
                                        <div class="flex flex-col">
                                            <span class="font-mono text-neutral-12">{{ traveler.passport_number }}</span>
                                            <span class="text-xs text-neutral-9">Exp: {{ formatDate(traveler.passport_expiry_date) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">{{ traveler.nationality }}</td>
                                    <td class="py-3">{{ formatDate(traveler.date_of_birth) }}</td>
                                </tr>
                                <tr v-if="!application.travelers?.length">
                                    <td colspan="5" class="py-4 text-center text-neutral-9">No travelers listed.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </AppCard>
            
            <!-- Notes -->
             <AppCard v-if="application.internal_notes || application.status_note">
                <template #title> Notes </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-if="application.status_note">
                             <p class="text-neutral-9 text-xs uppercase font-medium mb-1">Latest Status Note</p>
                             <div class="bg-neutral-2 p-3 rounded-lg text-sm text-neutral-11 italic border-l-4 border-amber-400">
                                 {{ application.status_note }}
                             </div>
                        </div>
                        <div v-if="application.internal_notes">
                             <p class="text-neutral-9 text-xs uppercase font-medium mb-1">Internal Notes</p>
                             <div class="bg-neutral-2 p-3 rounded-lg text-sm text-neutral-12 whitespace-pre-wrap">
                                 {{ application.internal_notes }}
                             </div>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Payment -->
            <AppCard>
                <template #title> Payment Details </template>
                <template #content>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-neutral-2 p-3 rounded-lg">
                            <span class="text-neutral-11 text-sm">Status</span>
                            <span :class="getPaymentStatusColor(application.payment_status)" class="px-2 py-0.5 rounded text-xs font-bold uppercase ring-1">
                                {{ application.payment_status }}
                            </span>
                        </div>
                        
                        <div class="pt-2 space-y-2 text-sm">
                            <div class="flex justify-between text-neutral-11">
                                <span>Visa Fee (x{{ application.adult_count + application.child_count }})</span>
                                <span>৳{{ formatPrice(application.total_amount - application.express_fees - application.additional_fees + (application.discount_amount || 0)) }}</span>
                            </div>
                            <div v-if="application.express_fees > 0" class="flex justify-between text-neutral-11">
                                <span>Express Fee</span>
                                <span>৳{{ formatPrice(application.express_fees) }}</span>
                            </div>
                            <div v-if="application.additional_fees > 0" class="flex justify-between text-neutral-11">
                                <span>Additional Fees</span>
                                <span>৳{{ formatPrice(application.additional_fees) }}</span>
                            </div>
                            <div v-if="application.discount_amount > 0" class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-৳{{ formatPrice(application.discount_amount) }}</span>
                            </div>
                            <div class="border-t border-neutral-4 pt-2 flex justify-between font-bold text-neutral-12 text-lg">
                                <span>Total</span>
                                <span>৳{{ formatPrice(application.total_amount) }}</span>
                            </div>
                             <div class="flex justify-between text-emerald-600 font-medium">
                                <span>Paid</span>
                                <span>৳{{ formatPrice(application.paid_amount) }}</span>
                            </div>
                             <div class="flex justify-between text-red-500 font-medium">
                                <span>Due</span>
                                <span>৳{{ formatPrice(application.due_amount) }}</span>
                            </div>
                        </div>
                        
                         <AppButton
                            v-if="application.due_amount > 0 && can('visa-application-edit')"
                            class="btn btn-outline-primary w-full mt-4"
                            @click="$inertia.visit(route('visaApplication.edit', application.id))"
                        >
                            <i class="ri-money-dollar-circle-line mr-1"></i> Record Payment
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <!-- Quick Actions -->
             <AppCard title="Quick Actions">
                <template #content>
                    <div class="flex flex-col gap-2">
                        <AppButton
                            v-if="application.status !== 'approved' && can('visa-application-edit')"
                            class="btn btn-sm btn-outline-success justify-start"
                            @click="updateStatus('approved')"
                        >
                            <i class="ri-check-double-line mr-2"></i> Mark as Approved
                        </AppButton>
                         <AppButton
                            v-if="application.status !== 'rejected' && can('visa-application-edit')"
                            class="btn btn-sm btn-outline-destructive justify-start"
                            @click="updateStatus('rejected')"
                        >
                            <i class="ri-close-circle-line mr-2"></i> Mark as Rejected
                        </AppButton>
                         <AppButton
                             v-if="can('visa-application-edit')"
                            class="btn btn-sm btn-outline-secondary justify-start"
                             @click="updateStatus('processing')"
                        >
                            <i class="ri-loader-4-line mr-2"></i> Mark as Processing
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <!-- Agent Info -->
            <AppCard v-if="application.agent">
                <template #title> Agent Info </template>
                <template #content>
                    <div class="flex items-center gap-3">
                         <div class="h-10 w-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-bold ring-1 ring-violet-200">
                            {{ getInitials(application.agent.name) }}
                        </div>
                        <div>
                            <p class="text-neutral-12 font-medium text-sm">{{ application.agent.name }}</p>
                            <p class="text-neutral-9 text-xs">{{ application.agent.email }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

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

const getInitials = (name) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
}

const getStatusColor = (status) => {
    switch (status) {
        case 'submitted': return 'bg-blue-100 text-blue-700 ring-blue-200'
        case 'processing': return 'bg-amber-100 text-amber-700 ring-amber-200'
        case 'approved': return 'bg-emerald-100 text-emerald-700 ring-emerald-200'
        case 'rejected': return 'bg-red-100 text-red-700 ring-red-200'
        case 'completed': return 'bg-purple-100 text-purple-700 ring-purple-200'
        case 'cancelled': return 'bg-neutral-100 text-neutral-700 ring-neutral-200'
        default: return 'bg-neutral-100 text-neutral-700 ring-neutral-200'
    }
}

const getPaymentStatusColor = (status) => {
     switch (status) {
        case 'paid': return 'bg-emerald-100 text-emerald-700 ring-emerald-200'
        case 'partial': return 'bg-amber-100 text-amber-700 ring-amber-200'
        case 'pending': return 'bg-red-100 text-red-700 ring-red-200'
        default: return 'bg-neutral-100 text-neutral-700 ring-neutral-200'
    }
}

const updateStatus = (status) => {
    if (confirm(`Are you sure you want to update status to ${status.toUpperCase()}?`)) {
        router.post(route('visaApplication.updateStatus', props.application.id), { status }, {
            preserveScroll: true
        })
    }
}
</script>
