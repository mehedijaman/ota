<template>
    <AppSectionHeader :title="__('Users')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('acl-user-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('user.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    {{ __('Recycle Bin') }}
                </AppButton>
                <AppButton
                    v-if="can('acl-user-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('user.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    {{ __('Create User') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppCard class="w-full" no-padding>
        <template #title>
            <div 
                class="flex w-full items-center justify-between cursor-pointer select-none -my-1 -mx-2 py-1 px-2 rounded-lg hover:bg-neutral-3/50 transition-colors"
                @click="showFilters = !showFilters"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-2">
                        <i class="ri-filter-line text-primary-9"></i>
                    </div>
                    <span>{{ __('Filter Users') }}</span>
                    <span 
                        v-if="hasActiveFilters" 
                        class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-9 px-1.5 text-xs font-bold text-white"
                    >
                        {{ activeFilterCount }}
                    </span>
                </div>
                <div class="flex items-center gap-1 text-sm text-neutral-11">
                    <span class="hidden sm:inline">{{ showFilters ? 'Hide' : 'Show' }}</span>
                    <i 
                        class="text-xl transition-transform duration-200" 
                        :class="showFilters ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                    ></i>
                </div>
            </div>
        </template>
        <template #content>
            <div v-show="showFilters" class="p-4 sm:p-5">
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
                </form>

                <div class="mt-4 flex justify-end gap-2">
                    <AppButton
                        class="btn btn-secondary"
                        @click="$inertia.visit(route('user.index'))"
                    >
                        <i class="ri-refresh-line mr-1"></i>
                        {{ __('Reset') }}
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="applyFilter">
                        <i class="ri-filter-line mr-1"></i>
                        {{ __('Apply') }}
                    </AppButton>
                </div>
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
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.profile_type === 'Employee' 
                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                        >
                            {{ item.profile_type }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="role in item.roles"
                                :key="role"
                                class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300"
                            >
                                {{ role }}
                            </span>
                            <span v-if="!item.roles || item.roles.length === 0" class="text-sm text-gray-400">
                                No roles
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit user roles -->
                        <AppTooltip :text="__('User Roles')" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('aclUserRole.edit', { uuid: item.uuid })
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
                                            { uuid: item.uuid }
                                        )
                                    )
                                "
                            >
                                <i class="ri-shield-keyhole-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- impersonate user -->
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
                                        route('user.edit', { uuid: item.uuid })
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
                                        route('user.destroy', { uuid: item.uuid })
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
import { ref, computed, watch, onMounted } from 'vue'
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

const headers = ['ID', 'Name', 'Email', 'Type', 'Roles', 'Actions']

// --- Collapsible Filter ---
const STORAGE_KEY = 'user_filter_expanded'
const showFilters = ref(localStorage.getItem(STORAGE_KEY) === 'true')

// Persist state to localStorage
watch(showFilters, (value) => {
    localStorage.setItem(STORAGE_KEY, value.toString())
})

// --- Filter Form ---
const currentParams = new URLSearchParams(window.location.search)

const filterForm = useForm({
    role: currentParams.get('role') ?? null
})

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return !!filterForm.role
})

const activeFilterCount = computed(() => {
    return filterForm.role ? 1 : 0
})

onMounted(() => {
    // Show filters if any are active (override localStorage)
    if (hasActiveFilters.value) {
        showFilters.value = true
    }
})

// Apply filter
const applyFilter = () => {
    filterForm.get(route('user.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true
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
    // Use window.location for full page reload to properly refresh auth state
    window.location.href = route('impersonate.start', { uuid: selectedUser.value.uuid })
}
</script>
