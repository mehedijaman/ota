<template>
    <a
        :href="route('site.index')"
        class="group flex items-center gap-3 transition-all duration-200"
    >
        <!-- Logo Image or Icon -->
        <div
            v-if="siteSettings?.site_logo && variant !== 'light'"
            class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl shadow-lg transition-transform duration-200 group-hover:scale-105"
        >
            <img
                :src="siteSettings.site_logo"
                :alt="siteSettings?.site_name || 'Logo'"
                class="h-full w-full object-contain"
            />
        </div>
        <div
            v-else
            :class="[
                'flex h-10 w-10 items-center justify-center rounded-xl transition-transform duration-200 group-hover:scale-105',
                variant === 'light' 
                    ? 'bg-white/20 shadow-lg shadow-white/10' 
                    : 'bg-primary-9 shadow-lg shadow-primary-9/25'
            ]"
        >
            <svg
                :class="[
                    'h-5 w-5',
                    variant === 'light' ? 'text-white' : 'text-white'
                ]"
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
        
        <!-- Site Name -->
        <span 
            :class="[
                'text-xl font-bold tracking-tight',
                variant === 'light' ? 'text-white' : 'text-neutral-12'
            ]"
        >
            {{ siteSettings?.site_name || 'App' }}
        </span>
    </a>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineProps({
    variant: {
        type: String,
        default: 'default'
    }
})

const page = usePage()
const siteSettings = computed(() => page.props.siteSettings)
</script>
