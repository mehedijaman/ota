<template>
        <div
            class="border-neutral-4 bg-neutral-2 mt-6 flex flex-col justify-between rounded-md border px-4 py-2 shadow-2xs md:flex-row"
        >
            <div>
                <i class="ri-megaphone-line"></i>
                Welcome to your Customer Portal,
                <span class="font-bold">{{ $page.props.auth.user.name }}</span> !
            </div>
        </div>

        <div class="my-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- My Bookings Card -->
            <div class="rounded-lg border border-neutral-4 bg-white p-6 shadow-sm dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                        <i class="ri-calendar-check-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">My Bookings</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">View your tour bookings</p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Card -->
            <div class="rounded-lg border border-neutral-4 bg-white p-6 shadow-sm dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                        <i class="ri-user-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">My Profile</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage your account</p>
                    </div>
                </div>
            </div>
            
            <!-- Payment History Card -->
            <div class="rounded-lg border border-neutral-4 bg-white p-6 shadow-sm dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                        <i class="ri-wallet-3-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Payments</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">View payment history</p>
                    </div>
                </div>
            </div>
        </div>
</template>
<script setup>
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
</script>
