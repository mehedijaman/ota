<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('customer-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('customer.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Customer
                </AppButton>

                <AppButton
                    v-if="can('customer-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('customer.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="customers.data.length || route().params.searchTerm"
        :url="route('customer.index')"
        fields-to-search="first_name,last_name,email,phone"
    ></AppDataSearch>

    <AppDataTable v-if="customers.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in customers.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{
                            (customers.current_page - 1) * customers.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <!-- Customer Info -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img
                                    v-if="item.avatar_url"
                                    :src="item.avatar_url"
                                    :alt="item.full_name"
                                    class="ring-neutral-3 h-10 w-10 rounded-full object-cover ring-1"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 text-sm font-bold text-white ring-1"
                                >
                                    {{
                                        item.first_name
                                            ?.charAt(0)
                                            ?.toUpperCase() || 'C'
                                    }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div
                                    class="text-neutral-12 truncate font-medium"
                                >
                                    {{ item.full_name }}
                                </div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Contact -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span
                                v-if="item.email"
                                class="text-neutral-11 flex items-center gap-1.5 text-sm"
                            >
                                <i class="ri-mail-line text-neutral-9"></i>
                                {{ item.email }}
                            </span>
                            <span
                                v-if="item.phone"
                                class="text-neutral-10 flex items-center gap-1.5 text-xs"
                            >
                                <i class="ri-phone-line text-neutral-9"></i>
                                {{ item.phone }}
                            </span>
                            <span
                                v-if="!item.phone && !item.email"
                                class="text-neutral-9 text-sm"
                                >-</span
                            >
                        </div>
                    </AppDataTableData>

                    <!-- Location -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span
                                v-if="item.city || item.country"
                                class="text-neutral-11 text-sm"
                            >
                                {{
                                    [item.city, item.country]
                                        .filter(Boolean)
                                        .join(', ')
                                }}
                            </span>
                            <span v-else class="text-neutral-9 text-sm">-</span>
                        </div>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="getStatusClasses(item.status)"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full"
                                :class="getStatusDotClasses(item.status)"
                            ></span>
                            {{ formatStatus(item.status) }}
                        </span>
                    </AppDataTableData>

                    <!-- Actions -->
                    <AppDataTableData
                        class="overflow-visible text-right"
                        @click.stop
                    >
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton
                                    class="btn btn-icon btn-secondary"
                                    size="sm"
                                    aria-label="More actions"
                                >
                                    <i class="ri-more-2-fill"></i>
                                </AppButton>
                            </template>

                            <div class="py-1">
                                <AppDropdownItem
                                    v-if="can('customer-view')"
                                    @click="
                                        $inertia.visit(
                                            route('customer.show', item.uuid)
                                        )
                                    "
                                >
                                    <i
                                        class="ri-eye-line text-neutral-9 mr-2"
                                    ></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="can('customer-edit')"
                                    @click="
                                        $inertia.visit(
                                            route('customer.edit', item.uuid)
                                        )
                                    "
                                >
                                    <i
                                        class="ri-edit-line text-neutral-9 mr-2"
                                    ></i>
                                    Edit Customer
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="
                                        can('customer-edit') &&
                                        !item.has_user &&
                                        item.email
                                    "
                                    @click="openCreateUserModal(item)"
                                >
                                    <i
                                        class="ri-user-add-line text-neutral-9 mr-2"
                                    ></i>
                                    Create User
                                </AppDropdownItem>

                                <div
                                    class="border-neutral-4 my-1 border-t"
                                ></div>

                                <AppDropdownItem
                                    v-if="can('customer-delete')"
                                    class="text-danger hover:bg-danger-light"
                                    @click="
                                        confirmDelete(
                                            route('customer.destroy', item.uuid)
                                        )
                                    "
                                >
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete Customer
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="customers.data.length"
        :links="customers.links"
        :from="customers.from ?? 0"
        :to="customers.to ?? 0"
        :total="customers.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!customers.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>

    <!-- Create User Modal -->
    <CustomerCreateUserModal
        :isOpen="showCreateUserModal"
        :customer="selectedCustomer"
        @close="showCreateUserModal = false"
    />
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import CustomerCreateUserModal from './Partials/CustomerCreateUserModal.vue'

const { can } = useAuthCan()
const { title } = useTitle('Customers')

const props = defineProps({
    customers: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Customers', last: true }
]

const headers = ['SL', 'Customer', 'Contact', 'Location', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

// Create User Modal
const showCreateUserModal = ref(false)
const selectedCustomer = ref(null)

const openCreateUserModal = (customer) => {
    selectedCustomer.value = customer
    showCreateUserModal.value = true
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

const getStatusDotClasses = (status) => {
    const classes = {
        active: 'bg-emerald-500',
        inactive: 'bg-neutral-400',
        suspended: 'bg-amber-500',
        blocked: 'bg-red-500'
    }
    return classes[status] || 'bg-neutral-400'
}
</script>
