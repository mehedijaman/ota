<template>
    <button
        type="button"
        class="group inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-6 bg-white text-neutral-9 transition-all hover:border-primary-9 hover:text-primary-9 focus:outline-none focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-2 dark:text-neutral-11 dark:hover:border-primary-9 dark:hover:text-primary-9"
        @click="toggleTheme"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <span class="sr-only">Toggle theme</span>
        <i
            class="transition-transform duration-200"
            :class="[
                isDark ? 'ri-sun-line rotate-0' : 'ri-moon-line rotate-0',
                'text-lg'
            ]"
        ></i>
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
