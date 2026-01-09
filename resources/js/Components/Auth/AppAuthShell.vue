<template>
    <section
        class="relative flex min-h-screen w-full flex-col bg-gradient-to-br from-neutral-1 via-primary-1/30 to-neutral-2 dark:from-neutral-1 dark:via-neutral-2 dark:to-neutral-1"
    >
        <!-- Professional Top Bar -->
        <header
            class="sticky top-0 z-50 w-full border-b border-neutral-6/50 bg-white/80 backdrop-blur-lg dark:border-neutral-7/50 dark:bg-neutral-2/80"
        >
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <!-- Logo -->
                <div class="flex items-center">
                    <AppAuthLogo />
                </div>
                
                <!-- Right side actions -->
                <div class="flex items-center gap-4">
                    <AppThemeToggle />
                </div>
            </div>
        </header>

        <!-- Main Content - Centered -->
        <div class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Card Container -->
                <div
                    class="rounded-2xl border border-neutral-6 bg-white p-8 shadow-xl shadow-neutral-4/10 dark:border-neutral-7 dark:bg-neutral-2 dark:shadow-neutral-1/20 sm:p-10"
                >
                    <slot></slot>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-neutral-6/50 bg-white/50 py-6 backdrop-blur-sm dark:border-neutral-7/50 dark:bg-neutral-2/50">
            <p class="text-center text-sm text-neutral-10 dark:text-neutral-11">
                © {{ new Date().getFullYear() }} All rights reserved.
            </p>
        </footer>
    </section>
</template>

<script setup>
import AppAuthLogo from '@/Components/Auth/AppAuthLogo.vue'
import AppThemeToggle from '@/Components/Misc/AppThemeToggle.vue'
</script>
