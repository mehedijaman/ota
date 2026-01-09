<template>
    <AgentLayout>
        <div
            class="border-neutral-4 bg-neutral-2 mt-6 flex flex-col justify-between rounded-md border px-4 py-2 shadow-2xs md:flex-row"
        >
            <div>
                <i class="ri-megaphone-line"></i>
                Welcome to Agent Panel,
                <span class="font-bold">{{ $page.props.auth.user.name }}</span> !
            </div>
        </div>

        <div class="my-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Placeholder cards for agent dashboard -->
            <div class="rounded-lg border border-neutral-4 bg-white p-6 shadow-sm dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                        <i class="ri-flight-takeoff-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Bookings</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage your bookings</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-lg border border-neutral-4 bg-white p-6 shadow-sm dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                        <i class="ri-wallet-3-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Earnings</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Track your earnings</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-lg border border-neutral-4 bg-white p-6 shadow-sm dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                        <i class="ri-customer-service-2-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Customers</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">View your customers</p>
                    </div>
                </div>
            </div>
        </div>
    </AgentLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3'
import AgentLayout from '@/Layouts/AgentLayout.vue'
</script>
