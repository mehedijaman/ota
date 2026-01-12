<template>
    <section class="relative flex min-h-screen w-full">
        <!-- Left Panel - Branding/Visual Side -->
        <div class="relative hidden w-1/2 overflow-hidden lg:flex">
            <!-- Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 via-transparent to-transparent"></div>

            <!-- Content -->
            <div class="relative z-10 flex h-full w-full flex-col justify-between p-10 xl:p-14">
                <!-- Logo -->
                <div>
                    <AppAuthLogo variant="light" />
                </div>

                <!-- Main Visual Content -->
                <div class="flex flex-1 flex-col items-start justify-center py-8">
                    <h2 class="max-w-lg text-4xl font-bold leading-[1.1] tracking-tight text-white xl:text-5xl">
                        {{ __('Discover Your Next') }}
                        <span class="relative inline-block text-cyan-300">
                            {{ __('Adventure') }}
                            <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 200 8" fill="none">
                                <path d="M2 6C50 2 150 2 198 6" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="block font-normal text-white/80">{{ __('with confidence') }}</span>
                    </h2>

                    <!-- Description Card -->
                    <div class="mt-10 rounded-2xl bg-white/10 p-6 backdrop-blur-sm">
                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            <p class="text-base leading-relaxed text-white/90">
                                {{ __('Book flights, hotels, tours & visas seamlessly with our') }} 
                                <strong class="font-semibold text-white">{{ __('all-in-one') }}</strong> 
                                {{ __('travel management platform.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Feature Pills -->
                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2.5 text-sm font-medium text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            {{ __('Instant Booking') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2.5 text-sm font-medium text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            {{ __('Secure Payments') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2.5 text-sm font-medium text-white/90 backdrop-blur-sm">
                            <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                            {{ __('Best Price Guarantee') }}
                        </span>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="flex flex-col gap-4">
                    <div class="flex gap-6 text-sm text-white/60">
                        <a href="#" class="hover:text-white">{{ __('Privacy Policy') }}</a>
                        <a href="#" class="hover:text-white">{{ __('Terms of Service') }}</a>
                        <a href="#" class="hover:text-white">{{ __('Help Center') }}</a>
                    </div>
                    <div class="text-sm text-white/40">
                        © {{ new Date().getFullYear() }} {{ __('All rights reserved.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Side -->
        <div class="relative flex w-full flex-col bg-neutral-100 dark:bg-neutral-950 lg:w-1/2">
            <!-- Mobile Header -->
            <header class="relative z-10 flex items-center justify-between border-b border-neutral-200/80 bg-white/80 px-6 py-4 backdrop-blur-xl dark:border-neutral-800/80 dark:bg-neutral-900/80 lg:hidden">
                <AppAuthLogo />
                <AppThemeToggle />
            </header>

            <!-- Desktop Header with Theme Toggle -->
            <div class="absolute right-8 top-8 z-50 hidden items-center gap-3 lg:flex">
                <AppThemeToggle />
            </div>

            <!-- Main Content -->
            <div class="relative z-10 flex flex-1 flex-col items-center justify-center px-6 py-12 sm:px-12 lg:px-16 xl:px-24">
                <div class="w-full max-w-[420px]">
                    <slot></slot>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import AppAuthLogo from '@/Components/Auth/AppAuthLogo.vue'
import AppThemeToggle from '@/Components/Misc/AppThemeToggle.vue'
</script>
