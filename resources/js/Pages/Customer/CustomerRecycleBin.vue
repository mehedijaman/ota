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
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="customers.data.length && can('customer-delete')"
                    class="btn btn-primary"
                    @click="confirmRestore(route('customer.restoreRecycleBin'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="customers.data.length && can('customer-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('customer.emptyRecycleBin'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="customers.data.length || route().params.searchTerm"
        :url="route('customer.recycleBin')"
        fields-to-search="first_name,last_name,email"
    ></AppDataSearch>

    <AppDataTable v-if="customers.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in customers.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{
                            (customers.current_page - 1) * customers.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="min-w-0">
                            <div class="text-neutral-12 truncate font-medium">
                                {{ item.full_name }}
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.phone }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="getStatusClasses(item.status)"
                        >
                            {{ formatStatus(item.status) }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Restore -->
                        <AppTooltip
                            v-if="can('customer-delete')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    confirmRestore(
                                        route('customer.restore', item.id)
                                    )
                                "
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete Permanently -->
                        <AppTooltip
                            v-if="can('customer-delete')"
                            text="Permanently Delete"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('customer.destroyForce', item.id)
                                    )
                                "
                            >
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
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
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('Customer Recycle Bin')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Customers', href: route('customer.index') },
    { label: 'Recycle Bin', last: true }
]

const props = defineProps({
    customers: {
        type: Object,
        default: () => ({})
    }
})

const headers = ['SL', 'Customer', 'Email', 'Phone', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.confirmRestore(restoreRoute)
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
</script>
