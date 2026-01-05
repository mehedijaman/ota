<template>
    <Head :title="title" />

    <AppSectionHeader :title="__('Role Permissions')" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(indexRoute)"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    {{ __('Back') }}
                </AppButton>
                <AppButton
                    class="btn btn-primary"
                    :disabled="form.processing"
                    @click="submitForm"
                >
                    <i v-if="form.processing" class="ri-loader-4-line mr-1 animate-spin"></i>
                    <i v-else class="ri-save-line mr-1"></i>
                    {{ __('Save') }}
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppFormErrors class="mb-6" />

    <div class="space-y-6">
        <!-- Role Info Card -->
        <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
            <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
                            <i class="ri-shield-keyhole-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">{{ role.name }}</h3>
                            <p class="text-xs text-neutral-10">{{ __('Configure permissions for this role') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-neutral-11">
                            <span class="font-semibold text-primary-10">{{ form.rolePermissions.length }}</span>
                            <span class="text-neutral-9">/ {{ permissions.length }} {{ __('selected') }}</span>
                        </div>
                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-neutral-6 bg-neutral-1 px-3 py-2 transition-all hover:bg-neutral-3">
                            <AppCheckbox
                                id="select-all"
                                v-model="isAllSelected"
                                :indeterminate="isIndeterminate"
                            />
                            <span class="text-sm font-medium text-neutral-11">{{ __('Select All') }}</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Search -->
            <div class="border-b border-neutral-6 px-5 py-3">
                <div class="relative max-w-md">
                    <i class="ri-search-line pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-9"></i>
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="w-full rounded-lg border border-neutral-6 bg-neutral-2 py-2 pr-3 pl-10 text-sm text-neutral-12 placeholder:text-neutral-9 focus:border-primary-8 focus:bg-neutral-1 focus:ring-1 focus:ring-primary-8 focus:outline-none"
                        :placeholder="__('Search permissions...')"
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-9 hover:text-neutral-11"
                    >
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Permission Groups -->
        <div v-if="visibleGroups.length" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="group in visibleGroups"
                :key="group.name"
                class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm transition-shadow hover:shadow-md"
            >
                <!-- Group Header -->
                <div class="border-b border-neutral-6 bg-neutral-2 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div :class="getGroupIconClass(group.name)" class="flex h-9 w-9 items-center justify-center rounded-lg shadow-sm">
                                <i :class="getGroupIcon(group.name)" class="text-base text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-neutral-12">{{ formatGroupName(group.name) }}</h4>
                                <p class="text-xs text-neutral-9">
                                    {{ getGroupSelectedCount(group.name) }}/{{ group.permissions.length }} {{ __('enabled') }}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="toggleGroupSelection(group.name, !isGroupFullySelected(group.name))"
                            :class="[
                                'rounded-lg px-2.5 py-1.5 text-xs font-medium transition-all',
                                isGroupFullySelected(group.name)
                                    ? 'bg-primary-3 text-primary-11 hover:bg-primary-4'
                                    : 'bg-neutral-3 text-neutral-11 hover:bg-neutral-4'
                            ]"
                        >
                            {{ isGroupFullySelected(group.name) ? __('Deselect') : __('Select All') }}
                        </button>
                    </div>
                </div>

                <!-- Permissions List -->
                <div class="max-h-64 overflow-y-auto p-3">
                    <div class="space-y-1">
                        <label
                            v-for="permission in group.permissions"
                            :key="permission.id"
                            class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 transition-colors hover:bg-neutral-3"
                        >
                            <AppCheckbox
                                v-model="form.rolePermissions"
                                :value="permission"
                            />
                            <span class="text-sm text-neutral-11">{{ formatPermissionName(permission.name) }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="searchQuery" class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 py-12 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-neutral-3">
                <i class="ri-search-line text-3xl text-neutral-9"></i>
            </div>
            <h3 class="mt-4 font-semibold text-neutral-12">{{ __('No results found') }}</h3>
            <p class="mt-1 text-sm text-neutral-10">{{ __('No permissions match') }} "{{ searchQuery }}"</p>
            <button
                @click="searchQuery = ''"
                class="mt-4 text-sm font-medium text-primary-10 hover:text-primary-11 hover:underline"
            >
                {{ __('Clear search') }}
            </button>
        </div>

        <div v-else class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 py-12 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-neutral-3">
                <i class="ri-shield-line text-3xl text-neutral-9"></i>
            </div>
            <h3 class="mt-4 font-semibold text-neutral-12">{{ __('No permissions available') }}</h3>
            <p class="mt-1 text-sm text-neutral-10">{{ __('There are no permissions configured yet.') }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Role Permissions')

const props = defineProps({
    role: { type: Object, required: true },
    permissions: { type: Array, required: true },
    isSuperadmin: { type: Boolean, default: false }
})

const indexRoute = computed(() => props.isSuperadmin ? route('superadmin.aclRole.index') : route('aclRole.index'))

const breadCrumb = computed(() => [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Roles', href: indexRoute.value },
    { label: 'Role Permissions', last: true }
])

const form = useForm({
    rolePermissions: [...props.role.permissions]
})

const searchQuery = ref('')

// Grouping
const allGroups = computed(() => {
    const groups = {}
    props.permissions.forEach((perm) => {
        const key = perm.name.split('-')[0] || perm.name
        if (!groups[key]) groups[key] = []
        groups[key].push(perm)
    })
    return Object.entries(groups)
        .map(([name, permissions]) => ({ name, permissions }))
        .sort((a, b) => a.name.localeCompare(b.name))
})

const visibleGroups = computed(() => {
    if (!searchQuery.value) return allGroups.value
    const q = searchQuery.value.toLowerCase()
    return allGroups.value.filter((group) => {
        return group.name.toLowerCase().includes(q) ||
            group.permissions.some((p) => p.name.toLowerCase().includes(q))
    })
})

// Selection
const total = computed(() => props.permissions.length)

const isAllSelected = computed({
    get: () => form.rolePermissions.length === total.value && total.value > 0,
    set: (checked) => { form.rolePermissions = checked ? [...props.permissions] : [] }
})

const isIndeterminate = computed(() => {
    const selected = form.rolePermissions.length
    return selected > 0 && selected < total.value
})

const getSelectedIdSet = () => new Set(form.rolePermissions.map((p) => p.id))

const isGroupFullySelected = (groupName) => {
    const group = allGroups.value.find((g) => g.name === groupName)
    if (!group) return false
    const selectedIds = getSelectedIdSet()
    return group.permissions.every((p) => selectedIds.has(p.id))
}

const getGroupSelectedCount = (groupName) => {
    const group = allGroups.value.find((g) => g.name === groupName)
    if (!group) return 0
    const selectedIds = getSelectedIdSet()
    return group.permissions.filter((p) => selectedIds.has(p.id)).length
}

const toggleGroupSelection = (groupName, checked) => {
    const group = allGroups.value.find((g) => g.name === groupName)
    if (!group) return
    
    const selectedIds = new Set(form.rolePermissions.map((p) => p.id))
    const groupPermissionIds = new Set(group.permissions.map((p) => p.id))
    
    if (checked) {
        // Add permissions from props.permissions to ensure same object references
        const toAdd = props.permissions.filter((p) => groupPermissionIds.has(p.id) && !selectedIds.has(p.id))
        form.rolePermissions = [...form.rolePermissions, ...toAdd]
    } else {
        form.rolePermissions = form.rolePermissions.filter((p) => !groupPermissionIds.has(p.id))
    }
}

// Icons with gradients matching the codebase pattern
const groupConfig = {
    acl: { icon: 'ri-shield-user-line', class: 'bg-linear-to-br from-purple-500 to-indigo-600' },
    user: { icon: 'ri-user-line', class: 'bg-linear-to-br from-blue-500 to-cyan-600' },
    role: { icon: 'ri-admin-line', class: 'bg-linear-to-br from-indigo-500 to-purple-600' },
    product: { icon: 'ri-shopping-bag-line', class: 'bg-linear-to-br from-green-500 to-emerald-600' },
    sale: { icon: 'ri-shopping-cart-line', class: 'bg-linear-to-br from-emerald-500 to-teal-600' },
    purchase: { icon: 'ri-truck-line', class: 'bg-linear-to-br from-cyan-500 to-blue-600' },
    customer: { icon: 'ri-user-heart-line', class: 'bg-linear-to-br from-pink-500 to-rose-600' },
    supplier: { icon: 'ri-store-line', class: 'bg-linear-to-br from-orange-500 to-red-600' },
    employee: { icon: 'ri-team-line', class: 'bg-linear-to-br from-teal-500 to-cyan-600' },
    attendance: { icon: 'ri-time-line', class: 'bg-linear-to-br from-sky-500 to-blue-600' },
    leave: { icon: 'ri-calendar-check-line', class: 'bg-linear-to-br from-violet-500 to-purple-600' },
    payroll: { icon: 'ri-money-dollar-circle-line', class: 'bg-linear-to-br from-lime-500 to-green-600' },
    report: { icon: 'ri-bar-chart-line', class: 'bg-linear-to-br from-rose-500 to-pink-600' },
    setting: { icon: 'ri-settings-3-line', class: 'bg-linear-to-br from-slate-500 to-gray-600' },
    dashboard: { icon: 'ri-dashboard-line', class: 'bg-linear-to-br from-fuchsia-500 to-pink-600' },
    category: { icon: 'ri-folder-line', class: 'bg-linear-to-br from-yellow-500 to-amber-600' },
    brand: { icon: 'ri-price-tag-3-line', class: 'bg-linear-to-br from-red-500 to-rose-600' },
    expense: { icon: 'ri-wallet-line', class: 'bg-linear-to-br from-amber-500 to-orange-600' },
    quotation: { icon: 'ri-file-list-line', class: 'bg-linear-to-br from-blue-500 to-indigo-600' },
    tenant: { icon: 'ri-building-line', class: 'bg-linear-to-br from-indigo-500 to-blue-600' },
    subscription: { icon: 'ri-vip-crown-line', class: 'bg-linear-to-br from-yellow-500 to-amber-600' },
    stock: { icon: 'ri-archive-line', class: 'bg-linear-to-br from-cyan-500 to-teal-600' },
    inventory: { icon: 'ri-stack-line', class: 'bg-linear-to-br from-emerald-500 to-green-600' },
    backup: { icon: 'ri-database-2-line', class: 'bg-linear-to-br from-stone-500 to-neutral-600' },
}

const getGroupIcon = (name) => {
    const key = name.toLowerCase()
    for (const [prefix, config] of Object.entries(groupConfig)) {
        if (key.startsWith(prefix)) return config.icon
    }
    return 'ri-settings-4-line'
}

const getGroupIconClass = (name) => {
    const key = name.toLowerCase()
    for (const [prefix, config] of Object.entries(groupConfig)) {
        if (key.startsWith(prefix)) return config.class
    }
    return 'bg-linear-to-br from-neutral-500 to-slate-600'
}

const formatGroupName = (name) => name.split('-').map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')

const formatPermissionName = (name) => {
    const parts = name.split('-')
    if (parts.length > 1) parts.shift()
    return parts.map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
}

const submitForm = () => {
    const routeName = props.isSuperadmin ? 'superadmin.aclRolePermission.update' : 'aclRolePermission.update'
    form.put(route(routeName, props.role.uuid))
}
</script>
