<template>
    <a :href="category.url" class="group bg-white block rounded-2xl p-6 text-center border border-slate-100 hover:border-orange-200 hover:shadow-lg transition-all mx-2 h-full">
        <div class="w-16 h-16 mx-auto mb-4 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
            <i :class="[category.icon || 'ri-compass-3-line', 'text-3xl text-orange-600']"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">{{ category.name }}</h3>
        <p class="text-sm text-slate-500">{{ category.tours_count }} Tours</p>
    </a>
</template>

<script setup>
defineProps({
    category: {
        type: Object,
        required: true
    }
})
</script>
