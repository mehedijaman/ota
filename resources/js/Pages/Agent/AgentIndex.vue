<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('agent-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('agent.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Agent
                </AppButton>

                <AppButton
                    v-if="can('agent-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('agent.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="agents.data.length || route().params.searchTerm"
        :url="route('agent.index')"
        fields-to-search="company_name,contact_person,email,phone"
    ></AppDataSearch>

    <AppDataTable v-if="agents.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in agents.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{
                            (agents.current_page - 1) * agents.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <!-- Company Info -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img
                                    v-if="item.logo_url"
                                    :src="item.logo_url"
                                    :alt="item.company_name"
                                    class="ring-neutral-3 h-10 w-10 rounded-lg object-cover ring-1"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-sm font-bold text-white ring-1"
                                >
                                    {{
                                        item.company_name
                                            ?.charAt(0)
                                            ?.toUpperCase() || 'A'
                                    }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div
                                    class="text-neutral-12 truncate font-medium"
                                >
                                    {{ item.company_name }}
                                </div>
                                <div class="text-neutral-10 text-xs">
                                    {{ item.contact_person }}
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

                    <!-- Balance & Credit -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-neutral-12 text-sm">
                                Balance: {{ formatCurrency(item.balance) }}
                            </span>
                            <span class="text-neutral-10 text-xs">
                                Credit: {{ formatCurrency(item.credit_limit) }}
                            </span>
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
                                    v-if="can('agent-view')"
                                    @click="
                                        $inertia.visit(
                                            route('agent.show', item.uuid)
                                        )
                                    "
                                >
                                    <i
                                        class="ri-eye-line text-neutral-9 mr-2"
                                    ></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="can('agent-edit')"
                                    @click="
                                        $inertia.visit(
                                            route('agent.edit', item.uuid)
                                        )
                                    "
                                >
                                    <i
                                        class="ri-edit-line text-neutral-9 mr-2"
                                    ></i>
                                    Edit Agent
                                </AppDropdownItem>

                                <AppDropdownItem
                                    v-if="
                                        can('agent-edit') &&
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
                                    v-if="can('agent-delete')"
                                    class="text-danger hover:bg-danger-light"
                                    @click="
                                        confirmDelete(
                                            route('agent.destroy', item.uuid)
                                        )
                                    "
                                >
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete Agent
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="agents.data.length"
        :links="agents.links"
        :from="agents.from ?? 0"
        :to="agents.to ?? 0"
        :total="agents.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!agents.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>

    <!-- Create User Modal -->
    <AgentCreateUserModal
        :isOpen="showCreateUserModal"
        :agent="selectedAgent"
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
import AgentCreateUserModal from './Partials/AgentCreateUserModal.vue'

const { can } = useAuthCan()
const { title } = useTitle('Agents')

const props = defineProps({
    agents: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Agents', last: true }
]

const headers = [
    'SL',
    'Company',
    'Contact',
    'Location',
    'Balance / Credit',
    'Status',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

// Create User Modal
const showCreateUserModal = ref(false)
const selectedAgent = ref(null)

const openCreateUserModal = (agent) => {
    selectedAgent.value = agent
    showCreateUserModal.value = true
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

const getStatusDotClasses = (status) => {
    const classes = {
        approved: 'bg-emerald-500',
        pending: 'bg-amber-500',
        suspended: 'bg-red-500',
        inactive: 'bg-neutral-400'
    }
    return classes[status] || 'bg-neutral-400'
}
</script>
