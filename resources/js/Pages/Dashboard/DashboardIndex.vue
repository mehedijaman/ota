<template>
    <Head title="Dashboard" />

    <div class="space-y-8">
        <!-- Header & Welcome -->
        <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">
                    {{ greeting }}, {{ firstName }}
                </h1>
                <div class="mt-2 flex items-center gap-3 text-sm text-neutral-500 dark:text-neutral-400">
                    <span class="flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-0.5 font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                        <i class="ri-calendar-line"></i>
                        {{ currentDate }}
                    </span>
                    <span class="h-1 w-1 rounded-full bg-neutral-300 dark:bg-neutral-600"></span>
                    <span>System v2.4.0</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                 <div class="hidden items-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-2 text-sm font-medium shadow-sm dark:border-neutral-700 dark:bg-neutral-800 md:flex">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="text-neutral-700 dark:text-neutral-200">System Operational</span>
                </div>
            </div>
        </div>

        <!-- Primary Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Users Config -->
            <div v-if="can('Acl')" @click="$inertia.visit(route('user.index'))" class="group relative cursor-pointer overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-xs transition-all hover:-translate-y-1 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <div class="pointer-events-none absolute -right-6 -top-6 text-neutral-50 opacity-50 dark:text-neutral-700/30">
                     <i class="ri-user-3-fill text-[120px]"></i>
                </div>
                <div class="relative flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-800/30">
                            <i class="ri-user-3-line text-xl"></i>
                        </div>
                        <span class="flex items-center gap-1 rounded-full bg-neutral-50 px-2 py-1 text-xs font-medium text-neutral-500 group-hover:bg-blue-50 group-hover:text-blue-600 dark:bg-neutral-700/50 dark:text-neutral-400 dark:group-hover:bg-blue-900/20 dark:group-hover:text-blue-400">
                            View <i class="ri-arrow-right-line"></i>
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Users</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ formatNumber(props.count?.users) }}</p>
                    </div>
                </div>
            </div>

            <!-- Agents Config -->
            <div v-if="can('agent-list')" @click="$inertia.visit(route('agent.index'))" class="group relative cursor-pointer overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-xs transition-all hover:-translate-y-1 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <div class="pointer-events-none absolute -right-6 -top-6 text-neutral-50 opacity-50 dark:text-neutral-700/30">
                     <i class="ri-user-star-fill text-[120px]"></i>
                </div>
                 <div class="relative flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 ring-1 ring-violet-100 dark:bg-violet-900/20 dark:text-violet-400 dark:ring-violet-800/30">
                            <i class="ri-user-star-line text-xl"></i>
                        </div>
                         <span class="flex items-center gap-1 rounded-full bg-neutral-50 px-2 py-1 text-xs font-medium text-neutral-500 group-hover:bg-violet-50 group-hover:text-violet-600 dark:bg-neutral-700/50 dark:text-neutral-400 dark:group-hover:bg-violet-900/20 dark:group-hover:text-violet-400">
                            Manage <i class="ri-arrow-right-line"></i>
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Active Agents</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ formatNumber(props.count?.agents) }}</p>
                    </div>
                </div>
            </div>

            <!-- Hotels Config -->
            <div v-if="can('hotel-list')" @click="$inertia.visit(route('hotel.index'))" class="group relative cursor-pointer overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-xs transition-all hover:-translate-y-1 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <div class="pointer-events-none absolute -right-6 -top-6 text-neutral-50 opacity-50 dark:text-neutral-700/30">
                     <i class="ri-hotel-fill text-[120px]"></i>
                </div>
                 <div class="relative flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between">
                         <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-600 ring-1 ring-teal-100 dark:bg-teal-900/20 dark:text-teal-400 dark:ring-teal-800/30">
                            <i class="ri-hotel-line text-xl"></i>
                        </div>
                         <span class="flex items-center gap-1 rounded-full bg-neutral-50 px-2 py-1 text-xs font-medium text-neutral-500 group-hover:bg-teal-50 group-hover:text-teal-600 dark:bg-neutral-700/50 dark:text-neutral-400 dark:group-hover:bg-teal-900/20 dark:group-hover:text-teal-400">
                            Inventory <i class="ri-arrow-right-line"></i>
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Listed Properties</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ formatNumber(props.count?.hotels) }}</p>
                    </div>
                </div>
            </div>

            <!-- Tours Config -->
            <div v-if="can('tour-list')" @click="$inertia.visit(route('tour.index'))" class="group relative cursor-pointer overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-xs transition-all hover:-translate-y-1 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <div class="pointer-events-none absolute -right-6 -top-6 text-neutral-50 opacity-50 dark:text-neutral-700/30">
                     <i class="ri-compass-3-fill text-[120px]"></i>
                </div>
                <div class="relative flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between">
                         <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-800/30">
                            <i class="ri-compass-3-line text-xl"></i>
                        </div>
                        <span class="flex items-center gap-1 rounded-full bg-neutral-50 px-2 py-1 text-xs font-medium text-neutral-500 group-hover:bg-amber-50 group-hover:text-amber-600 dark:bg-neutral-700/50 dark:text-neutral-400 dark:group-hover:bg-amber-900/20 dark:group-hover:text-amber-400">
                            Packages <i class="ri-arrow-right-line"></i>
                        </span>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Tour Packages</p>
                        <p class="mt-1 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ formatNumber(props.count?.tours) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
             <!-- Left: Quick Actions -->
             <div class="lg:col-span-2 space-y-6">
                  <!-- Actions Panel -->
                 <div class="flex flex-col gap-4">
                     <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Quick Actions</h3>
                     </div>
                     
                     <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <button v-if="can('agent-create')" @click="$inertia.visit(route('agent.create'))" class="group flex flex-col items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-xs transition-all hover:border-violet-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-violet-600">
                             <div class="rounded-lg bg-neutral-100 p-2.5 text-neutral-600 transition-colors group-hover:bg-violet-100 group-hover:text-violet-600 dark:bg-neutral-700 dark:text-neutral-300 dark:group-hover:bg-violet-900/30 dark:group-hover:text-violet-400">
                                <i class="ri-user-add-line text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-neutral-900 dark:text-white">New Agent</h4>
                                <p class="text-xs text-neutral-500 group-hover:text-neutral-700 dark:text-neutral-400 dark:group-hover:text-neutral-300">Registration</p>
                            </div>
                        </button>

                         <button v-if="can('hotel-create')" @click="$inertia.visit(route('hotel.create'))" class="group flex flex-col items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-xs transition-all hover:border-teal-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-teal-600">
                             <div class="rounded-lg bg-neutral-100 p-2.5 text-neutral-600 transition-colors group-hover:bg-teal-100 group-hover:text-teal-600 dark:bg-neutral-700 dark:text-neutral-300 dark:group-hover:bg-teal-900/30 dark:group-hover:text-teal-400">
                                <i class="ri-hotel-line text-xl"></i>
                            </div>
                             <div>
                                <h4 class="font-medium text-neutral-900 dark:text-white">Add Hotel</h4>
                                <p class="text-xs text-neutral-500 group-hover:text-neutral-700 dark:text-neutral-400 dark:group-hover:text-neutral-300">Property listing</p>
                            </div>
                        </button>

                        <button v-if="can('tour-create')" @click="$inertia.visit(route('tour.create'))" class="group flex flex-col items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-xs transition-all hover:border-amber-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-amber-600">
                             <div class="rounded-lg bg-neutral-100 p-2.5 text-neutral-600 transition-colors group-hover:bg-amber-100 group-hover:text-amber-600 dark:bg-neutral-700 dark:text-neutral-300 dark:group-hover:bg-amber-900/30 dark:group-hover:text-amber-400">
                                <i class="ri-compass-3-line text-xl"></i>
                            </div>
                             <div>
                                <h4 class="font-medium text-neutral-900 dark:text-white">Create Tour</h4>
                                <p class="text-xs text-neutral-500 group-hover:text-neutral-700 dark:text-neutral-400 dark:group-hover:text-neutral-300">New package</p>
                            </div>
                        </button>

                        <button v-if="can('visa-create')" @click="$inertia.visit(route('visa.create'))" class="group flex flex-col items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-xs transition-all hover:border-rose-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-rose-600">
                             <div class="rounded-lg bg-neutral-100 p-2.5 text-neutral-600 transition-colors group-hover:bg-rose-100 group-hover:text-rose-600 dark:bg-neutral-700 dark:text-neutral-300 dark:group-hover:bg-rose-900/30 dark:group-hover:text-rose-400">
                                <i class="ri-passport-line text-xl"></i>
                            </div>
                             <div>
                                <h4 class="font-medium text-neutral-900 dark:text-white">New Visa</h4>
                                <p class="text-xs text-neutral-500 group-hover:text-neutral-700 dark:text-neutral-400 dark:group-hover:text-neutral-300">Application</p>
                            </div>
                        </button>

                        <button v-if="can('user-create')" @click="$inertia.visit(route('user.create'))" class="group flex flex-col items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-xs transition-all hover:border-blue-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-600">
                             <div class="rounded-lg bg-neutral-100 p-2.5 text-neutral-600 transition-colors group-hover:bg-blue-100 group-hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-300 dark:group-hover:bg-blue-900/30 dark:group-hover:text-blue-400">
                                <i class="ri-user-follow-line text-xl"></i>
                            </div>
                             <div>
                                <h4 class="font-medium text-neutral-900 dark:text-white">Add User</h4>
                                <p class="text-xs text-neutral-500 group-hover:text-neutral-700 dark:text-neutral-400 dark:group-hover:text-neutral-300">System user</p>
                            </div>
                        </button>

                         <button v-if="can('Acl')" @click="$inertia.visit(route('aclRole.index'))" class="group flex flex-col items-start gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-xs transition-all hover:border-orange-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-orange-600">
                             <div class="rounded-lg bg-neutral-100 p-2.5 text-neutral-600 transition-colors group-hover:bg-orange-100 group-hover:text-orange-600 dark:bg-neutral-700 dark:text-neutral-300 dark:group-hover:bg-orange-900/30 dark:group-hover:text-orange-400">
                                <i class="ri-shield-keyhole-line text-xl"></i>
                            </div>
                             <div>
                                <h4 class="font-medium text-neutral-900 dark:text-white">Roles</h4>
                                <p class="text-xs text-neutral-500 group-hover:text-neutral-700 dark:text-neutral-400 dark:group-hover:text-neutral-300">Permissions</p>
                            </div>
                        </button>
                     </div>
                 </div>

                 <!-- Secondary Stats Grid -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-xs dark:border-neutral-700 dark:bg-neutral-800">
                        <div class="mb-2 flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
                            <i class="ri-plane-fill text-sky-500"></i>
                            <span class="text-xs font-semibold uppercase tracking-wider">Flights</span>
                        </div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ formatNumber(props.count?.flights) }}</p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-xs dark:border-neutral-700 dark:bg-neutral-800">
                        <div class="mb-2 flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
                            <i class="ri-passport-fill text-rose-500"></i>
                            <span class="text-xs font-semibold uppercase tracking-wider">Visas</span>
                        </div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ formatNumber(props.count?.visas) }}</p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-xs dark:border-neutral-700 dark:bg-neutral-800">
                        <div class="mb-2 flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
                            <i class="ri-group-fill text-indigo-500"></i>
                            <span class="text-xs font-semibold uppercase tracking-wider">Customers</span>
                        </div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ formatNumber(props.count?.customers) }}</p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-xs dark:border-neutral-700 dark:bg-neutral-800">
                        <div class="mb-2 flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
                            <i class="ri-file-list-3-fill text-emerald-500"></i>
                            <span class="text-xs font-semibold uppercase tracking-wider">Bookings</span>
                        </div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ formatNumber(props.count?.bookings) }}</p>
                    </div>
                </div>
             </div>

             <!-- Right: System Overview Sidebar -->
             <div class="space-y-6">
                 <!-- Status Card -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-xs dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="mb-4 text-base font-semibold text-neutral-900 dark:text-white">Platform Health</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <i class="ri-database-2-line"></i>
                                </div>
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Database</span>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400">Online</span>
                        </div>
                         <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <i class="ri-server-line"></i>
                                </div>
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">API Gateway</span>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400">Connected</span>
                        </div>
                         <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <i class="ri-cpu-line"></i>
                                </div>
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Queue Worker</span>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                        </div>
                    </div>
                </div>

                <!-- Access Summary -->
                 <div v-if="can('Acl')" class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-xs dark:border-neutral-700 dark:bg-neutral-800">
                     <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Security</h3>
                        <button @click="$inertia.visit(route('aclRole.index'))" class="text-xs font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">Configure</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-neutral-50 p-3 text-center dark:bg-neutral-700/30">
                            <span class="block text-xl font-bold text-neutral-900 dark:text-white">{{ props.count?.roles || 0 }}</span>
                            <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Roles</span>
                        </div>
                         <div class="rounded-xl bg-neutral-50 p-3 text-center dark:bg-neutral-700/30">
                            <span class="block text-xl font-bold text-neutral-900 dark:text-white">{{ props.count?.permissions || 0 }}</span>
                            <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Permissions</span>
                        </div>
                    </div>
                 </div>
             </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()

const props = defineProps({
    count: {
        type: Object,
        default: () => ({})
    }
})

const firstName = computed(() => {
    const name = window.$page?.props?.auth?.user?.name ?? ''
    return name.split(' ')[0] || 'there'
})

const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Good morning'
    if (hour < 17) return 'Good afternoon'
    return 'Good evening'
})

const currentDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric'
    })
})

const formatNumber = (num) => {
    if (!num && num !== 0) return '0'
    return new Intl.NumberFormat().format(num)
}
</script>
