<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('customer-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('customer.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    {{ __('Back') }}
                </AppButton>

                <AppButton
                    v-if="
                        can('customer-edit') &&
                        !customer.has_user &&
                        customer.email
                    "
                    class="btn btn-secondary"
                    @click="openCreateUserModal"
                >
                    <i class="ri-user-add-line mr-1"></i>
                    {{ __('Create User') }}
                </AppButton>

                <AppButton
                    v-if="can('customer-edit')"
                    class="btn btn-primary"
                    @click="
                        $inertia.visit(route('customer.edit', customer.uuid))
                    "
                >
                    <i class="ri-edit-line mr-1"></i>
                    {{ __('Edit') }}
                </AppButton>

                <AppButton
                    v-if="can('customer-delete')"
                    class="btn btn-destructive"
                    @click="
                        confirmDelete(route('customer.destroy', customer.uuid))
                    "
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
            class="bg-linear-to-br absolute inset-0 from-teal-600 via-cyan-600 to-blue-700"
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
                        v-if="customer.avatar_url"
                        :src="customer.avatar_url"
                        :alt="customer.full_name"
                        class="relative h-32 w-32 rounded-full border-4 border-white/40 object-cover shadow-2xl ring-4 ring-white/10"
                    />
                    <div
                        v-else
                        class="relative flex h-32 w-32 items-center justify-center rounded-full border-4 border-white/40 bg-white/20 text-5xl font-bold text-white shadow-2xl ring-4 ring-white/10 backdrop-blur-sm"
                    >
                        {{ getInitials(customer.full_name) }}
                    </div>
                    <!-- Status Indicator -->
                    <span
                        class="absolute bottom-2 right-2 h-6 w-6 rounded-full border-4 border-white shadow-lg"
                        :class="getStatusBgClass(customer.status)"
                    ></span>
                </div>

                <!-- Profile Info -->
                <div class="flex-1 text-center text-white md:text-left">
                    <h1 class="text-3xl font-bold tracking-tight md:text-4xl">
                        {{ customer.full_name }}
                    </h1>
                    <div
                        class="mt-4 flex flex-wrap items-center justify-center gap-4 md:justify-start"
                    >
                        <span
                            v-if="customer.phone"
                            class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm"
                        >
                            <i class="ri-phone-line text-teal-200"></i>
                            {{ customer.phone }}
                        </span>
                        <span
                            v-if="customer.email"
                            class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm backdrop-blur-sm"
                        >
                            <i class="ri-mail-line text-teal-200"></i>
                            {{ customer.email }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="flex gap-4">
                    <div
                        class="flex flex-col items-center rounded-xl bg-white/10 px-6 py-4 backdrop-blur-sm"
                    >
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-semibold"
                            :class="getStatusPillClass(customer.status)"
                        >
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="getStatusBgClass(customer.status)"
                            ></span>
                            {{ formatStatus(customer.status) }}
                        </span>
                        <p
                            class="mt-2 text-xs font-medium uppercase tracking-wider text-teal-200"
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
        <!-- Personal Information -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-primary-2 flex h-8 w-8 items-center justify-center rounded-lg"
                    >
                        <i class="ri-user-line text-primary-11 text-lg"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Personal Information</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            First Name
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.first_name || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Last Name
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.last_name || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Gender
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm capitalize">
                            {{ customer.gender || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Date of Birth
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.date_of_birth || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Email
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.email || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Phone
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.phone || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Status
                        </dt>
                        <dd class="mt-1">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="getStatusClasses(customer.status)"
                            >
                                {{ formatStatus(customer.status) }}
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
                            {{ customer.address || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            City
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.city || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            State
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.state || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Postal Code
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.postal_code || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Country
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.country || '-' }}
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>

        <!-- Identification Documents -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100"
                    >
                        <i class="ri-id-card-line text-lg text-amber-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Identification</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            NID Number
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.nid_no || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Passport Number
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.passport_no || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Passport Expiry
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.passport_expiry || '-' }}
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>

        <!-- Timestamps -->
        <AppCard>
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100"
                    >
                        <i class="ri-time-line text-lg text-purple-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Record Info</span
                    >
                </div>
            </template>
            <template #content>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Created At
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.created_at || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-neutral-10 text-sm font-medium">
                            Updated At
                        </dt>
                        <dd class="text-neutral-12 mt-1 text-sm">
                            {{ customer.updated_at || '-' }}
                        </dd>
                    </div>
                </dl>
            </template>
        </AppCard>
    </div>

    <!-- Documents Section -->
    <AppCard
        v-if="customer.documents && customer.documents.length"
        class="mt-6"
    >
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
                    v-for="doc in customer.documents"
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
    <CustomerCreateUserModal
        :isOpen="showCreateUserModal"
        :customer="customer"
        @close="showCreateUserModal = false"
    />

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import CustomerCreateUserModal from './Partials/CustomerCreateUserModal.vue'

const { can } = useAuthCan()
const { title } = useTitle('Customer Details')

const props = defineProps({
    customer: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Customers', href: route('customer.index') },
    { label: props.customer.full_name, last: true }
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

const getInitials = (name) => {
    if (!name) return '?'
    return name
        .split(' ')
        .map((word) => word[0])
        .join('')
        .substring(0, 2)
        .toUpperCase()
}

const formatStatus = (status) => {
    return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'
}

const getStatusClasses = (status) => {
    const classes = {
        active: 'bg-emerald-100 text-emerald-700',
        inactive: 'bg-neutral-100 text-neutral-600',
        suspended: 'bg-amber-100 text-amber-700',
        blocked: 'bg-red-100 text-red-700'
    }
    return classes[status] || 'bg-neutral-100 text-neutral-600'
}

const getStatusBgClass = (status) => {
    const classes = {
        active: 'bg-emerald-400',
        inactive: 'bg-gray-400',
        suspended: 'bg-amber-400',
        blocked: 'bg-red-400'
    }
    return classes[status] || 'bg-gray-400'
}

const getStatusPillClass = (status) => {
    const classes = {
        active: 'bg-emerald-400/30 text-emerald-100',
        inactive: 'bg-gray-400/30 text-gray-200',
        suspended: 'bg-amber-400/30 text-amber-100',
        blocked: 'bg-red-400/30 text-red-100'
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
