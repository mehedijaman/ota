<template>
    <AppDataSearch
        v-if="employees.data.length || route().params.searchTerm"
        :url="route('employee.index')"
        fields-to-search="name,code,email,phone"
    ></AppDataSearch>

    <AppDataTable v-if="employees.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-y divide-neutral-4">
                <AppDataTableRow
                    v-for="(item, index) in employees.data"
                    :key="item.id"
                    class="group transition-colors hover:bg-neutral-2"
                >
                    <AppDataTableData class="text-neutral-10 text-center w-12">
                        {{ (employees.current_page - 1) * employees.per_page + (index + 1) }}
                    </AppDataTableData>

                    <!-- Employee Info (Name, Code, Designation) -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div v-if="item.photo_url" class="h-10 w-10 shrink-0 overflow-hidden rounded-full shadow-sm">
                                <img :src="item.photo_url" :alt="item.name" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-indigo-600 text-white font-semibold text-sm shadow-sm">
                                {{ getInitials(item.name) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-neutral-12 truncate">{{ item.name }}</div>
                                <div class="flex items-center gap-2 text-xs text-neutral-10">
                                    <span class="inline-flex items-center gap-1 font-mono text-primary-10">
                                        <i class="ri-id-card-line text-xs"></i>
                                        {{ item.code }}
                                    </span>
                                    <span v-if="item.designation" class="text-neutral-8">•</span>
                                    <span v-if="item.designation">{{ item.designation?.name }}</span>
                                </div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Joining Date & Service -->
                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="flex items-center gap-1.5 text-sm text-neutral-12">
                                <i class="ri-calendar-line text-neutral-9"></i>
                                {{ item.joining_date }}
                            </span>
                            <span v-if="item.service_length" class="text-xs text-neutral-10 mt-0.5">
                                {{ item.service_length }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Contact -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span v-if="item.phone" class="flex items-center gap-1.5 text-sm text-neutral-11">
                                <i class="ri-phone-line text-neutral-9"></i>
                                {{ item.phone }}
                            </span>
                            <span v-if="item.email" class="flex items-center gap-1.5 text-xs text-neutral-10">
                                <i class="ri-mail-line text-neutral-9"></i>
                                {{ item.email }}
                            </span>
                            <span v-if="!item.phone && !item.email" class="text-sm text-neutral-9">-</span>
                        </div>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="item.is_active 
                                ? 'bg-emerald-100 text-emerald-700' 
                                : 'bg-neutral-100 text-neutral-600'"
                        >
                            <span 
                                class="h-1.5 w-1.5 rounded-full"
                                :class="item.is_active ? 'bg-emerald-500' : 'bg-neutral-400'"
                            ></span>
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <!-- Actions Dropdown -->
                    <AppDataTableData class="overflow-visible text-right" @click.stop>
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
                                    v-if="can('employee-view')" 
                                    @click="$inertia.visit(route('employee.show', item.uuid))"
                                >
                                    <i class="ri-eye-line mr-2 text-neutral-9"></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem 
                                    v-if="can('employee-edit')" 
                                    @click="$inertia.visit(route('employee.edit', item.uuid))"
                                >
                                    <i class="ri-edit-line mr-2 text-neutral-9"></i>
                                    Edit Employee
                                </AppDropdownItem>

                                <div class="border-t border-neutral-4 my-1"></div>

                                <AppDropdownItem 
                                    v-if="can('employee-delete')" 
                                    class="text-danger hover:bg-danger-light"
                                    @click="confirmDelete(route('employee.destroy', item.uuid))"
                                >
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete Employee
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="employees.data.length"
        :links="employees.links"
        :from="employees.from ?? 0"
        :to="employees.to ?? 0"
        :total="employees.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!employees.data.length" class="mt-4">
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'

const { can } = useAuthCan()

const props = defineProps({
    employees: {
        type: Object,
        default: () => ({})
    }
})

const getInitials = (name) => {
    if (!name) return '?'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.substring(0, 2).toUpperCase()
}

const headers = [
    'SL',
    'Employee',
    'Joining',
    'Contact',
    'Status',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
