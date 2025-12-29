<template>
    <a
        :href="route('adminAuth.loginForm')"
        class="group flex items-center gap-3 transition-all duration-200"
    >
        <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-9 shadow-lg shadow-primary-9/25 transition-transform duration-200 group-hover:scale-105"
        >
            <svg
                class="h-5 w-5 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M13 10V3L4 14h7v7l9-11h-7z"
                />
            </svg>
        </div>
        <span class="text-xl font-bold tracking-tight text-neutral-12">
            Admin
        </span>
    </a>
</template>
