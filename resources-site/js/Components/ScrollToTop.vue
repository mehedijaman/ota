<template>
    <a
        v-show="showScroll"
        href="javascript:void(0);"
        @click="scrollToTop"
        class="fixed bottom-4 right-4 z-[9999] flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition-all hover:bg-blue-700"
    >
        <i class="ri-arrow-up-line text-xl"></i>
    </a>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showScroll = ref(false)

// Function to check scroll position
const handleScroll = () => {
    showScroll.value = window.scrollY > 300 // Show after 300px scroll
}

// Function to scroll to the top
const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Add event listener when component is mounted
onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

// Remove event listener when component is unmounted
onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>
