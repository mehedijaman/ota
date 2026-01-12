<template>
    <a :href="category.url" class="group relative block rounded-2xl overflow-hidden aspect-4/5 mx-2 ring-1 ring-slate-900/5 shadow-md hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
        <!-- Background Image -->
        <img 
            :src="category.image_url || 'https://images.unsplash.com/photo-1530789253388-582c481c54b0?w=600'" 
            :alt="category.name"
            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 will-change-transform"
        />
        
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/40 to-transparent transition-opacity duration-300"></div>

        <!-- Content -->
        <div class="absolute inset-0 flex flex-col justify-end p-6 text-center">
             <!-- Icon (Floating) -->
            <div class="w-12 h-12 mx-auto mb-auto mt-4 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20 group-hover:scale-110 transition-transform duration-300">
                <i :class="[category.icon || 'ri-compass-3-line', 'text-2xl text-white']"></i>
            </div>

            <div class="transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                <h3 class="text-xl font-bold text-white mb-2 shadow-sm">{{ category.name }}</h3>
                
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm border border-white/10 text-xs font-medium text-white group-hover:bg-orange-500 group-hover:border-orange-500 transition-colors duration-300">
                    <span>{{ category.tours_count }}</span>
                    <span>{{ category.tours_count === 1 ? 'Tour' : 'Tours' }}</span>
                </span>
            </div>
        </div>
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
