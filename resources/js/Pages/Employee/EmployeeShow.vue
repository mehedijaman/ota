<!-- resources/js/Pages/Employee/EmployeeShow.vue -->
<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('employee-view')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('employee.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    {{ __('Back') }}
                </AppButton>

                <AppButton
                    v-if="can('employee-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('employee.edit', employee.uuid))"
                >
                    <i class="ri-edit-line mr-1"></i>
                    {{ __('Edit') }}
                </AppButton>

                <AppButton
                    v-if="can('employee-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('employee.destroy', employee.uuid))"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    {{ __('Delete') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Hero Profile Card -->
    <div class="relative mb-6 overflow-hidden rounded-2xl shadow-xl">
        <!-- Decorative Background -->
        <div class="absolute inset-0 bg-linear-to-br from-blue-600 via-indigo-600 to-purple-700"></div>
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px), radial-gradient(circle at 75% 75%, white 1px, transparent 1px); background-size: 60px 60px;"></div>
        
        <div class="relative z-10 p-8">
            <div class="flex flex-col items-center gap-8 md:flex-row">
                <!-- Profile Avatar -->
                <div class="relative">
                    <div class="absolute -inset-2 rounded-full bg-white/20 blur-lg"></div>
                    <div v-if="employee.photo_url" class="relative h-32 w-32 overflow-hidden rounded-full border-4 border-white/40 shadow-2xl ring-4 ring-white/10">
                        <img :src="employee.photo_url" :alt="employee.name" class="h-full w-full object-cover" />
                    </div>
                    <div v-else class="relative flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/40 bg-white/20 text-5xl font-bold text-white shadow-2xl ring-4 ring-white/10 backdrop-blur-sm">
                        {{ getInitials(employee.name) }}
                    </div>
                    <!-- Status Indicator -->
                    <span
                        class="absolute bottom-2 right-2 h-6 w-6 rounded-full border-4 border-white shadow-lg"
                        :class="employee.is_active ? 'bg-emerald-400' : 'bg-gray-400'"
                    ></span>
                </div>

                <!-- Profile Info -->
                <div class="flex-1 text-center text-white md:text-left">
                    <h1 class="text-3xl font-bold tracking-tight md:text-4xl">{{ employee.name }}</h1>
                    <p class="mt-2 flex items-center justify-center gap-2 text-lg text-blue-100 md:justify-start">
                        <i class="ri-briefcase-line"></i>
                        {{ employee.designation?.name || 'No Designation' }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-4 md:justify-start">
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-id-card-line text-blue-200"></i>
                            <span class="font-mono font-medium">{{ employee.code }}</span>
                        </span>
                        <span v-if="employee.phone" class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-phone-line text-blue-200"></i>
                            {{ employee.phone }}
                        </span>
                        <span v-if="employee.email" class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm">
                            <i class="ri-mail-line text-blue-200"></i>
                            {{ employee.email }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm">
                        <p class="text-3xl font-bold text-white">{{ employee.service_length?.split(',')[0]?.trim() || '-' }}</p>
                        <p class="mt-1 text-xs font-medium uppercase tracking-wider text-blue-200">Service</p>
                    </div>
                    <div class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-semibold"
                            :class="employee.is_active ? 'bg-emerald-400/30 text-emerald-100' : 'bg-gray-400/30 text-gray-200'"
                        >
                            <span class="h-2 w-2 rounded-full" :class="employee.is_active ? 'bg-emerald-400' : 'bg-gray-400'"></span>
                            {{ employee.is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <p class="mt-2 text-xs font-medium uppercase tracking-wider text-blue-200">Status</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Professional Information -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-briefcase-line text-lg text-white"></i>
                        </div>
                        <span class="text-lg font-semibold text-neutral-12">Professional Info</span>
                    </div>
                </div>
                <div class="divide-y divide-neutral-5 p-5">
                    <DetailRow label="Employee Code" :value="employee.code" icon="ri-barcode-line" />
                    <DetailRow label="Designation" :value="employee.designation?.name" icon="ri-medal-line" />
                    <DetailRow label="Department" :value="employee.department?.name" icon="ri-building-2-line" />
                    <DetailRow label="Area" :value="employee.area?.name" icon="ri-map-pin-2-line" />
                    <DetailRow label="Employee Type" :value="employee.type" icon="ri-user-settings-line" />
                    <DetailRow label="Joining Date" :value="formatDate(employee.joining_date)" icon="ri-calendar-line" />
                    <DetailRow label="Service Length" :value="employee.service_length" icon="ri-time-line" />
                </div>
            </div>

            <!-- Contact Information -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/25">
                            <i class="ri-contacts-line text-lg text-white"></i>
                        </div>
                        <span class="text-lg font-semibold text-neutral-12">Contact Info</span>
                    </div>
                </div>
                <div class="divide-y divide-neutral-5 p-5">
                    <DetailRow label="Phone" :value="employee.phone" icon="ri-phone-line" />
                    <DetailRow label="Email" :value="employee.email" icon="ri-mail-line" />
                    <DetailRow label="Emergency Contact" :value="employee.emergency_contact" icon="ri-first-aid-kit-line" />
                </div>
                <div v-if="employee.present_address || employee.permanent_address" class="border-t border-neutral-5 p-5 space-y-4">
                    <div v-if="employee.present_address" class="rounded-lg bg-neutral-2 p-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-neutral-10">
                            <i class="ri-map-pin-line"></i>
                            Present Address
                        </div>
                        <p class="mt-2 text-sm text-neutral-12 whitespace-pre-line">{{ employee.present_address }}</p>
                    </div>
                    <div v-if="employee.permanent_address" class="rounded-lg bg-neutral-2 p-4">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-neutral-10">
                            <i class="ri-home-4-line"></i>
                            Permanent Address
                        </div>
                        <p class="mt-2 text-sm text-neutral-12 whitespace-pre-line">{{ employee.permanent_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Personal Information -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-purple-500 to-pink-600 shadow-lg shadow-purple-500/25">
                            <i class="ri-user-heart-line text-lg text-white"></i>
                        </div>
                        <span class="text-lg font-semibold text-neutral-12">Personal Information</span>
                    </div>
                </div>
                <div class="p-5">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-neutral-5">
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10 w-1/3">Full Name</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.name || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Father's Name</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.father_name || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Mother's Name</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.mother_name || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Date of Birth</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ formatDate(employee.birthday) }} <span v-if="employee.birthday" class="text-neutral-9">({{ calculateAge(employee.birthday) }})</span></td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Gender</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.gender || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Marital Status</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.marital_status || '-' }}</td>
                            </tr>
                            <tr v-if="employee.spouse_name">
                                <td class="py-2.5 pr-4 text-neutral-10">Spouse Name</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.spouse_name }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Religion</td>
                                <td class="py-2.5 font-medium text-neutral-12">{{ employee.religion || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Blood Group</td>
                                <td class="py-2.5 font-medium text-neutral-12">
                                    <span v-if="employee.blood_group" class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                        <i class="ri-drop-fill"></i>{{ employee.blood_group }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Identity Documents -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-file-shield-2-line text-lg text-white"></i>
                        </div>
                        <span class="text-lg font-semibold text-neutral-12">Identity Documents</span>
                    </div>
                </div>
                <div class="p-5">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-neutral-5">
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10 w-1/3">National ID (NID)</td>
                                <td class="py-2.5 font-mono font-medium text-neutral-12">{{ employee.nid_no || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Passport No</td>
                                <td class="py-2.5 font-mono font-medium text-neutral-12">{{ employee.passport_no || '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 pr-4 text-neutral-10">Driving License</td>
                                <td class="py-2.5 font-mono font-medium text-neutral-12">{{ employee.dl_no || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bank Account Information -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                            <i class="ri-bank-line text-lg text-white"></i>
                        </div>
                        <span class="text-lg font-semibold text-neutral-12">Bank Account</span>
                    </div>
                </div>
                <div class="p-5">
                    <div v-if="employee.bank_account">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-neutral-5">
                                <tr>
                                    <td class="py-2.5 pr-4 text-neutral-10 w-1/3">Bank Name</td>
                                    <td class="py-2.5 font-medium text-neutral-12">{{ employee.bank_account.bank_name || '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 pr-4 text-neutral-10">Branch Name</td>
                                    <td class="py-2.5 font-medium text-neutral-12">{{ employee.bank_account.branch_name || '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 pr-4 text-neutral-10">Account Name</td>
                                    <td class="py-2.5 font-medium text-neutral-12">{{ employee.bank_account.account_name || '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 pr-4 text-neutral-10">Account Number</td>
                                    <td class="py-2.5 font-mono font-medium text-neutral-12">{{ employee.bank_account.account_no || '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 pr-4 text-neutral-10">Routing Number</td>
                                    <td class="py-2.5 font-mono font-medium text-neutral-12">{{ employee.bank_account.routing_no || '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 pr-4 text-neutral-10">Account Type</td>
                                    <td class="py-2.5 font-medium text-neutral-12">{{ employee.bank_account.account_type || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-8 text-neutral-9">
                        <i class="ri-bank-line text-3xl mb-2"></i>
                        <p class="text-sm">No bank account information</p>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-purple-500 to-violet-600 shadow-lg shadow-purple-500/25">
                            <i class="ri-file-text-line text-lg text-white"></i>
                        </div>
                        <span class="text-lg font-semibold text-neutral-12">Documents</span>
                    </div>
                </div>
                <div class="p-5">
                    <div v-if="employee.documents && employee.documents.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div 
                            v-for="doc in employee.documents" 
                            :key="doc.id"
                            class="flex items-center gap-4 rounded-xl border border-neutral-5 bg-neutral-2/50 p-4 transition-all hover:border-neutral-6 hover:shadow-sm"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl" :class="getDocTypeColorClass(doc.type)">
                                <i :class="getDocTypeIcon(doc.type)" class="text-2xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-neutral-12">{{ doc.type_label }}</p>
                                <p class="text-xs text-neutral-9 truncate">{{ doc.name }}</p>
                                <p class="text-xs text-neutral-8">{{ doc.size_formatted }} • {{ doc.created_at }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a :href="doc.url" target="_blank" class="btn btn-icon btn-secondary btn-sm" title="View">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a :href="doc.url" :download="doc.name" class="btn btn-icon btn-secondary btn-sm" title="Download">
                                    <i class="ri-download-2-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-12 text-neutral-9">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-3">
                            <i class="ri-file-text-line text-3xl"></i>
                        </div>
                        <p class="mt-4 text-sm font-medium">No documents uploaded</p>
                        <p class="mt-1 text-xs text-neutral-8">Add documents by editing this employee</p>
                    </div>
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
import { ref, getCurrentInstance, h } from 'vue'

const instance = getCurrentInstance()
const __ = instance?.appContext?.config?.globalProperties?.__ || window.__ || ((key) => key)

const { can } = useAuthCan()

const props = defineProps({
    employee: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: __('Dashboard'), href: route('dashboard.index') },
    { label: __('Employees'), href: route('employee.index') },
    { label: props.employee.name, last: true }
]

const { title } = useTitle(`${props.employee.name}`)

const getInitials = (name) => {
    if (!name) return '?'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.substring(0, 2).toUpperCase()
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
}

const calculateAge = (birthDateString) => {
    if (!birthDateString) return '-'
    const birthDate = new Date(birthDateString)
    const today = new Date()
    let age = today.getFullYear() - birthDate.getFullYear()
    const monthDiff = today.getMonth() - birthDate.getMonth()
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--
    }
    return `${age} years`
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

// Document display helpers
const getDocTypeIcon = (type) => {
    const icons = {
        nid_card: 'ri-id-card-line',
        passport: 'ri-passport-line',
        driving_licence: 'ri-steering-2-line',
        tin_certificate: 'ri-file-list-3-line'
    }
    return icons[type] || 'ri-file-line'
}

const getDocTypeColorClass = (type) => {
    const colors = {
        nid_card: 'bg-primary-100 text-primary-600',
        passport: 'bg-blue-100 text-blue-600',
        driving_licence: 'bg-amber-100 text-amber-600',
        tin_certificate: 'bg-emerald-100 text-emerald-600'
    }
    return colors[type] || 'bg-neutral-100 text-neutral-600'
}

// DetailRow Component
const DetailRow = {
    props: ['label', 'value', 'icon'],
    template: `
        <div class="flex items-center gap-4 py-3 first:pt-0 last:pb-0">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-neutral-3">
                <i :class="[icon, 'text-neutral-10']"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-neutral-9">{{ label }}</p>
                <p class="mt-0.5 text-sm font-semibold text-neutral-12 truncate">{{ value || '-' }}</p>
            </div>
        </div>
    `
}

// InfoCard Component
const colorClasses = {
    blue: 'bg-blue-50 border-blue-100 text-blue-600',
    indigo: 'bg-indigo-50 border-indigo-100 text-indigo-600',
    violet: 'bg-violet-50 border-violet-100 text-violet-600',
    purple: 'bg-purple-50 border-purple-100 text-purple-600',
    pink: 'bg-pink-50 border-pink-100 text-pink-600',
    rose: 'bg-rose-50 border-rose-100 text-rose-600',
    red: 'bg-red-50 border-red-100 text-red-600',
    amber: 'bg-amber-50 border-amber-100 text-amber-600',
    emerald: 'bg-emerald-50 border-emerald-100 text-emerald-600',
    teal: 'bg-teal-50 border-teal-100 text-teal-600',
    cyan: 'bg-cyan-50 border-cyan-100 text-cyan-600',
}

const InfoCard = {
    props: ['label', 'value', 'icon', 'color', 'isMono'],
    setup(props) {
        const colorClass = colorClasses[props.color] || colorClasses.blue
        return { colorClass }
    },
    template: `
        <div class="group rounded-xl border border-neutral-5 bg-neutral-1 p-4 transition-all hover:border-neutral-6 hover:shadow-sm">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg text-sm" :class="colorClass.split(' ').slice(0, 2).join(' ')">
                    <i :class="[icon, colorClass.split(' ').slice(2).join(' ')]"></i>
                </span>
                <span class="text-xs font-medium text-neutral-9">{{ label }}</span>
            </div>
            <p class="mt-3 text-base font-semibold text-neutral-12" :class="{ 'font-mono': isMono }">{{ value || '-' }}</p>
        </div>
    `
}
</script>
