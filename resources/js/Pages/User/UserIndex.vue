<template>
    <AppSectionHeader :title="__('Users')" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('acl-user-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('user.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                {{ __('Create User') }}
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppCard class="w-full">
        <template #title>
            <i class="ri-filter-line"></i>
            Filter Users
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />

            <form class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Role Filter -->
                <div>
                    <AppLabel for="role">{{ __('Role') }}</AppLabel>
                    <AppCombobox
                        class="w-full"
                        v-model="filterForm.role"
                        :options="roles"
                        combo-label="Select Role"
                        emit-value
                    />
                </div>

                <!-- Status Filter -->
                <!-- <div>
                    <AppLabel for="is_active">{{ __('Status') }}</AppLabel>
                    <AppCombobox
                        class="w-full"
                        v-model="filterForm.is_active"
                        :options="statusOptions"
                        combo-label="Select Status"
                        emit-value
                    />
                </div> -->
            </form>
        </template>

        <template #footer>
            <div class="flex justify-end gap-2">
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('user.index'))"
                >
                    <i class="ri-refresh-line mr-1"></i>
                    Reset Filter
                </AppButton>
                <AppButton class="btn btn-primary" @click="applyFilter">
                    <i class="ri-filter-line mr-1"></i>
                    Filter List
                </AppButton>
            </div>
        </template>
    </AppCard>

    <!-- Search -->
    <AppDataSearch
        class="mt-4"
        v-if="users.data.length || route().params.searchTerm"
        :url="route('user.index')"
        fields-to-search="name,email"
    ></AppDataSearch>

    <AppDataTable v-if="users.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in users.data"
                    :key="item.uuid"
                >
                    <AppDataTableData>
                        {{
                            index +
                            1 +
                            (users.current_page - 1) * users.per_page
                        }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div
                                v-if="item.avatar_url"
                                class="h-8 w-8 shrink-0 overflow-hidden rounded-full"
                            >
                                <img
                                    :src="item.avatar_url"
                                    :alt="item.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600 dark:bg-primary-900 dark:text-primary-300"
                            >
                                {{ item.name?.charAt(0)?.toUpperCase() }}
                            </div>
                            <span>{{ item.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            v-if="item.roles && item.roles.length"
                            class="text-sm"
                        >
                            {{ item.roles.join(', ') }}
                        </span>
                        <span v-else class="text-sm text-gray-400"
                            >No roles</span
                        >
                    </AppDataTableData>

                    <AppDataTableData>
                        <span v-if="item.is_active" class="badge badge-success"
                            >Active</span
                        >
                        <span v-else class="badge badge-warning">Inactive</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit user roles -->
                        <AppTooltip :text="__('User Roles')" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('aclUserRole.edit', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-account-box-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- edit user permissions -->
                        <AppTooltip :text="__('User Permissions')" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'aclUserPermission.edit',
                                            item.uuid
                                        )
                                    )
                                "
                            >
                                <i class="ri-shield-keyhole-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- impersonate user (only for tenant users) -->
                        <AppTooltip
                            :text="__('Login as this user')"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-warning"
                                @click="showImpersonateConfirm(item)"
                                :disabled="impersonating"
                            >
                                <i class="ri-user-shared-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- edit user -->
                        <AppTooltip :text="__('Edit User')" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('user.edit', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete user -->
                        <AppTooltip :text="__('Delete User')">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('user.destroy', item.uuid)
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
        v-if="users.data.length"
        :links="users.links"
        :from="users.from || 0"
        :to="users.to || 0"
        :total="users.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!users.data.length"
        class="mt-4"
        :title="__('No users found')"
        :description="__('Create a new user or adjust your filters.')"
    >
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    users: {
        type: Object,
        default: () => {}
    },
    roles: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Users', last: true }
]

const headers = ['ID', 'Name', 'Email', 'Roles', 'Status', 'Actions']

const statusOptions = [
    { value: null, label: 'All Status' },
    { value: 1, label: 'Active' },
    { value: 0, label: 'Inactive' }
]

// --- Filter Form ---
const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    role: currentParams.get('role') ?? null,
    is_active: currentParams.get('is_active')
        ? parseInt(currentParams.get('is_active'))
        : null
})

// Apply filter
const applyFilter = () => {
    filterForm.get(route('user.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        params: {
            role: filterForm.role,
            is_active: filterForm.is_active
        }
    })
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

// --- Impersonation ---
const impersonating = ref(false)
const selectedUser = ref(null)

const showImpersonateConfirm = (user) => {
    selectedUser.value = user
    confirmDialogRef.value.openCustomModal({
        title: 'Impersonate User',
        message: `Are you sure you want to login as "${user.name}"? You will be logged in as this user and can browse the application from their perspective.`,
        buttonClass: 'btn btn-warning',
        buttonText: 'Login as User',
        modalType: 'primary',
        helpText: 'You can leave impersonation at any time using the banner at the top of the page.',
        onConfirm: () => impersonateUser()
    })
}

const impersonateUser = () => {
    if (!selectedUser.value) return

    impersonating.value = true
    router.visit(route('impersonate.start', selectedUser.value.id), {
        onFinish: () => {
            impersonating.value = false
        }
    })
}
</script>
