<template>
    <button
        type="button"
        class="group inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-600 shadow-sm transition-all hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
        @click="toggleTheme"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <span class="sr-only">Toggle theme</span>
        <svg v-if="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
        </svg>
        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
        </svg>
    </button>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const isDark = ref(false)

onMounted(() => {
    checkTheme()
})

const checkTheme = () => {
    if (
        localStorage.getItem('modular-theme') === 'dark-theme' ||
        (!('modular-theme' in localStorage) &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        isDark.value = true
        document.documentElement.classList.add('dark-theme')
    } else {
        isDark.value = false
        document.documentElement.classList.remove('dark-theme')
    }
}

const toggleTheme = () => {
    if (isDark.value) {
        document.documentElement.classList.remove('dark-theme')
        localStorage.removeItem('modular-theme') // Or set to 'light'
        isDark.value = false
    } else {
        document.documentElement.classList.add('dark-theme')
        localStorage.setItem('modular-theme', 'dark-theme')
        isDark.value = true
    }
}
</script>
