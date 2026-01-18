<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('agent-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('agent.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    {{ __('Back') }}
                </AppButton>

                <AppButton
                    v-if="can('agent-edit') && !agent.has_user && agent.email"
                    class="btn btn-secondary"
                    @click="openCreateUserModal"
                >
                    <i class="ri-user-add-line mr-1"></i>
                    {{ __('Create User') }}
                </AppButton>

                <AppButton
                    v-if="can('agent-edit')"
                    class="btn btn-secondary"
                    @click="openDepositModal"
                >
                    <i class="ri-wallet-3-line mr-1"></i>
                    {{ __('Deposit') }}
                </AppButton>

                <AppButton
                    v-if="can('agent-edit')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('agent.edit', agent.uuid))"
                >
                    <i class="ri-edit-line mr-1"></i>
                    {{ __('Edit') }}
                </AppButton>

                <AppButton
                    v-if="can('agent-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('agent.destroy', agent.uuid))"
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
        <div
            class="bg-linear-to-br absolute inset-0 from-blue-600 via-indigo-600 to-purple-700"
        ></div>
        <div
            class="absolute inset-0 opacity-10"
            style="
                background-image:
                    radial-gradient(
                        circle at 25% 25%,
                        white 2px,
                        transparent 2px
                    ),
                    radial-gradient(
                        circle at 75% 75%,
                        white 1px,
                        transparent 1px
                    );
                background-size: 60px 60px;
            "
        ></div>

        <div class="relative z-10 p-8">
            <div class="flex flex-col items-center gap-8 md:flex-row">
                <!-- Profile Avatar -->
                <div class="relative">
                    <div
                        class="absolute -inset-2 rounded-full bg-white/20 blur-lg"
                    ></div>
                    <img
                        v-if="agent.logo_url"
                        :src="agent.logo_url"
                        :alt="agent.company_name"
                        class="relative h-32 w-32 rounded-full border-4 border-white/40 object-cover shadow-2xl ring-4 ring-white/10"
                    />
                    <div
                        v-else
                        class="relative flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/40 bg-white/20 text-5xl font-bold text-white shadow-2xl ring-4 ring-white/10 backdrop-blur-sm"
                    >
                        {{ getInitials(agent.company_name) }}
                    </div>
                    <!-- Status Indicator -->
                    <span
                        class="absolute bottom-2 right-2 h-6 w-6 rounded-full border-4 border-white shadow-lg"
                        :class="getStatusBgClass(agent.status)"
                    ></span>
                </div>

                <!-- Profile Info -->
                <div class="flex-1 text-center text-white md:text-left">
                    <h1 class="text-3xl font-bold tracking-tight md:text-4xl">
                        {{ agent.company_name }}
                    </h1>
                    <p
                        class="mt-2 flex items-center justify-center gap-2 text-lg text-blue-100 md:justify-start"
                    >
                        <i class="ri-user-line"></i>
                        {{ agent.contact_person }}
                    </p>
                    <div
                        class="mt-4 flex flex-wrap items-center justify-center gap-4 md:justify-start"
                    >
                        <span
                            v-if="agent.phone"
                            class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm"
                        >
                            <i class="ri-phone-line text-blue-200"></i>
                            {{ agent.phone }}
                        </span>
                        <span
                            v-if="agent.email"
                            class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm"
                        >
                            <i class="ri-mail-line text-blue-200"></i>
                            {{ agent.email }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="flex gap-4">
                    <div
                        class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm"
                    >
                        <p class="text-2xl font-bold text-white">
                            {{ formatCurrency(agent.balance) }}
                        </p>
                        <p
                            class="mt-1 text-xs font-medium uppercase tracking-wider text-blue-200"
                        >
                            Balance
                        </p>
                    </div>
                    <div
                        class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm"
                    >
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-semibold"
                            :class="getStatusPillClass(agent.status)"
                        >
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="getStatusBgClass(agent.status)"
                            ></span>
                            {{ formatStatus(agent.status) }}
                        </span>
                        <p
                            class="mt-2 text-xs font-medium uppercase tracking-wider text-blue-200"
                        >
                            Status
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Sections -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Company Information -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-primary-2 flex h-8 w-8 items-center justify-center rounded-lg"
                    >
                        <i class="ri-building-line text-primary-11 text-lg"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Company Information</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Company Name
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.company_name || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Contact Person
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.contact_person || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Email
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.email || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Phone
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.phone || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Established Date
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.established_at || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Status
                        </dt>
                        <dd class="mt-1">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="getStatusClasses(agent.status)"
                            >
                                {{ formatStatus(agent.status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>

        <!-- Address Information -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                    >
                        <i class="ri-map-pin-line text-lg text-blue-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Address Information</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <dt class="text-neutral-10 text-sm font-medium">
                            Address
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.address || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            City
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.city || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            State
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.state || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Postal Code
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.postal_code || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Country
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.country || '-' }}
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>

        <!-- Legal & Compliance -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100"
                    >
                        <i class="ri-file-text-line text-lg text-amber-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Legal & Compliance</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Trade License No
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.trade_license_no || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            MOCAT No
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.mocat_no || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            NID No
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.nid_no || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            TIN No
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.tin_no || '-' }}
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>

        <!-- Financial Information -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100"
                    >
                        <i
                            class="ri-money-dollar-circle-line text-lg text-emerald-600"
                        ></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Financial Information</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Balance
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-lg font-semibold">
                            {{ formatCurrency(agent.balance) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Credit Limit
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-lg font-semibold">
                            {{ formatCurrency(agent.credit_limit) }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-neutral-10 text-sm font-medium">
                            Remarks
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ agent.remarks || '-' }}
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>
    </div>

    <!-- Wallet Transactions Section -->
    <AppCard v-if="agent.transactions && agent.transactions.length" class="mt-6">
        <template #title>
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100">
                    <i class="ri-exchange-dollar-line text-lg text-teal-600"></i>
                </div>
                <span class="text-neutral-12 font-semibold">Wallet Transactions</span>
            </div>
        </template>
        <template #content>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">Date</th>
                            <th scope="col" class="px-6 py-3">Type</th>
                            <th scope="col" class="px-6 py-3">Amount</th>
                            <th scope="col" class="px-6 py-3">Balance</th>
                            <th scope="col" class="px-6 py-3">Ref</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="txn in agent.transactions"
                            :key="txn.id"
                            class="border-b bg-white hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ new Date(txn.created_at).toLocaleDateString() }}
                                <span class="text-xs text-gray-400 block">{{ new Date(txn.created_at).toLocaleTimeString() }}</span>
                            </td>
                            <td class="px-6 py-4 capitalize">
                                <span :class="{
                                    'text-emerald-600 font-medium': txn.type === 'deposit',
                                    'text-red-500': txn.type === 'withdraw' || txn.type === 'payment'
                                }">
                                    {{ txn.type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium" :class="txn.amount > 0 ? (txn.type === 'deposit' ? 'text-emerald-600' : 'text-gray-900') : 'text-gray-900'">
                                {{ formatCurrency(txn.amount) }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ formatCurrency(txn.balance_after) }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 truncate max-w-[150px]" :title="txn.reference">
                                {{ txn.reference || '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="{
                                        'bg-green-100 text-green-800': txn.status === 'completed',
                                        'bg-yellow-100 text-yellow-800': txn.status === 'pending',
                                        'bg-red-100 text-red-800': txn.status === 'failed' || txn.status === 'rejected'
                                    }"
                                >
                                    {{ txn.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2" v-if="txn.type === 'deposit'">
                                    <button
                                        v-if="can('agent-edit')"
                                        type="button"
                                        class="text-blue-600 hover:text-blue-800"
                                        title="Edit Transaction"
                                        @click="editTransaction(txn)"
                                    >
                                        <i class="ri-pencil-line text-lg"></i>
                                    </button>
                                    <button
                                        v-if="can('agent-edit')"
                                        type="button"
                                        class="text-red-600 hover:text-red-800"
                                        title="Delete Transaction"
                                        @click="confirmDeleteTransaction(txn)"
                                    >
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </AppCard>

    <!-- Documents Section -->
    <AppCard v-if="agent.documents && agent.documents.length" class="mt-6">
        <template #title>
            <div class="flex items-center gap-2">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100"
                >
                    <i class="ri-folder-line text-lg text-purple-600"></i>
                </div>
                <span class="text-neutral-12 font-semibold">Documents</span>
            </div>
        </template>
        <template #content>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="doc in agent.documents"
                    :key="doc.id"
                    class="border-neutral-5 bg-neutral-2/50 flex items-center gap-4 rounded-xl border p-4"
                >
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg"
                        :class="getDocumentBgClass(doc.mime_type)"
                    >
                        <i
                            :class="getDocumentIcon(doc.mime_type)"
                            class="text-2xl"
                        ></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-neutral-12 truncate text-sm font-medium">
                            {{ doc.type_label }}
                        </p>
                        <p class="text-neutral-10 text-xs">
                            {{ doc.size_formatted }} • {{ doc.created_at }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a
                            :href="doc.url"
                            target="_blank"
                            class="btn btn-icon btn-secondary btn-sm"
                            title="View"
                        >
                            <i class="ri-eye-line"></i>
                        </a>
                        <a
                            :href="doc.url"
                            :download="doc.name"
                            class="btn btn-icon btn-secondary btn-sm"
                            title="Download"
                        >
                            <i class="ri-download-line"></i>
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </AppCard>

    <!-- Create User Modal -->
    <AgentCreateUserModal
        :isOpen="showCreateUserModal"
        :agent="agent"
        @close="showCreateUserModal = false"
    />

    <!-- Deposit Modal -->
    <AgentDepositModal
        :isOpen="showDepositModal"
        :agent="agent"
        :transaction="selectedTransaction"
        @close="showDepositModal = false"
    />

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AgentCreateUserModal from './Partials/AgentCreateUserModal.vue'
import AgentDepositModal from './Partials/AgentDepositModal.vue'

const { can } = useAuthCan()
const { title } = useTitle('Agent Details')

const props = defineProps({
    agent: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Agents', href: route('agent.index') },
    { label: props.agent.company_name, last: true }
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

// Create User Modal
const showCreateUserModal = ref(false)

const openCreateUserModal = () => {
    showCreateUserModal.value = true
}

// Deposit Modal
const showDepositModal = ref(false)
const selectedTransaction = ref(null)

const openDepositModal = () => {
    selectedTransaction.value = null
    showDepositModal.value = true
}

const editTransaction = (txn) => {
    selectedTransaction.value = txn
    showDepositModal.value = true
}

const confirmDeleteTransaction = (txn) => {
    confirmDialogRef.value.openModal(
        route('agent.wallet.transaction.destroy', [props.agent.uuid, txn.id]),
        'Delete Transaction',
        'Are you sure you want to delete this transaction? This will reverse the balance adjustment. This action cannot be undone.'
    )
}

const getInitials = (name) => {
    if (!name) return '?'
    return name
        .split(' ')
        .map((word) => word[0])
        .join('')
        .substring(0, 2)
        .toUpperCase()
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'BDT',
        minimumFractionDigits: 2
    }).format(value || 0)
}

const formatStatus = (status) => {
    return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'
}

const getStatusClasses = (status) => {
    const classes = {
        approved: 'bg-emerald-100 text-emerald-700',
        pending: 'bg-amber-100 text-amber-700',
        suspended: 'bg-red-100 text-red-700',
        inactive: 'bg-neutral-100 text-neutral-600'
    }
    return classes[status] || 'bg-neutral-100 text-neutral-600'
}

const getStatusBgClass = (status) => {
    const classes = {
        approved: 'bg-emerald-400',
        pending: 'bg-amber-400',
        suspended: 'bg-red-400',
        inactive: 'bg-gray-400'
    }
    return classes[status] || 'bg-gray-400'
}

const getStatusPillClass = (status) => {
    const classes = {
        approved: 'bg-emerald-400/30 text-emerald-100',
        pending: 'bg-amber-400/30 text-amber-100',
        suspended: 'bg-red-400/30 text-red-100',
        inactive: 'bg-gray-400/30 text-gray-200'
    }
    return classes[status] || 'bg-gray-400/30 text-gray-200'
}

const getDocumentIcon = (mimeType) => {
    if (!mimeType) return 'ri-file-line text-neutral-500'
    if (mimeType.includes('pdf')) return 'ri-file-pdf-line text-red-500'
    if (mimeType.includes('image')) return 'ri-image-line text-blue-500'
    return 'ri-file-line text-neutral-500'
}

const getDocumentBgClass = (mimeType) => {
    if (!mimeType) return 'bg-neutral-100'
    if (mimeType.includes('pdf')) return 'bg-red-100'
    if (mimeType.includes('image')) return 'bg-blue-100'
    return 'bg-neutral-100'
}
</script>
