<template>
    <section
        class="relative flex min-h-screen w-full items-center justify-center overflow-hidden"
    >
        <!-- Sophisticated split-screen background -->
        <div class="absolute inset-0 flex">
            <!-- Left decorative panel (hidden on mobile) -->
            <div
                class="hidden w-1/2 bg-gradient-to-br from-slate-900 via-primary-11 to-slate-800 lg:block"
            >
                <!-- Decorative pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div
                        class="absolute inset-0"
                        style="
                            background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);
                        "
                    ></div>
                </div>

                <!-- Floating elements -->
                <div
                    class="absolute left-1/4 top-1/4 h-64 w-64 rounded-full bg-primary-9/20 blur-3xl"
                ></div>
                <div
                    class="absolute bottom-1/4 right-1/4 h-48 w-48 rounded-full bg-primary-7/20 blur-3xl"
                ></div>

                <!-- Brand message -->
                <div
                    class="relative flex h-full flex-col items-center justify-center px-12 text-center"
                >
                    <div class="mb-6">
                        <div
                            class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm"
                        >
                            <svg
                                class="h-8 w-8 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"
                                />
                            </svg>
                        </div>
                    </div>
                    <h2
                        class="mb-4 text-3xl font-bold tracking-tight text-white"
                    >
                        Admin Portal
                    </h2>
                    <p class="max-w-sm text-lg text-white/70">
                        Secure access to your management dashboard
                    </p>
                </div>
            </div>

            <!-- Right content panel -->
            <div
                class="w-full bg-neutral-1 lg:w-1/2 dark:bg-neutral-1"
            >
                <!-- Subtle gradient overlay -->
                <div
                    class="absolute inset-0 bg-gradient-to-br from-primary-3/30 via-transparent to-primary-4/20 lg:left-1/2 dark:from-primary-3/10 dark:to-primary-4/10"
                ></div>
            </div>
        </div>

        <!-- Content container -->
        <div
            class="relative z-10 flex w-full flex-col items-center justify-center px-4 py-12 lg:ml-auto lg:w-1/2"
        >
            <div class="w-full max-w-md">
                <slot></slot>
            </div>

            <!-- Footer text -->
            <p
                class="mt-8 text-center text-xs text-neutral-9 dark:text-neutral-11"
            >
                © {{ new Date().getFullYear() }} All rights reserved.
            </p>
        </div>
    </section>
</template>

<script setup>
</script>
