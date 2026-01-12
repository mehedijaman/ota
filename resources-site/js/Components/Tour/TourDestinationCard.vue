<template>
    <a :href="destination.url" class="group relative block rounded-2xl overflow-hidden aspect-4/5 shadow-lg hover:shadow-xl transition-all mx-2">
        <img 
            :src="destination.thumbnail_url || 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400'"
            :alt="destination.name"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
        />
        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/20 to-transparent"></div>
        <div class="absolute bottom-4 left-4 right-4 text-white">
            <h3 class="font-bold text-lg">{{ destination.name }}</h3>
            <p class="text-sm text-white/80">{{ destination.tours_count }} Tours</p>
        </div>
    </a>
</template>

<script setup>
defineProps({
    destination: {
        type: Object,
        required: true
    }
})
</script>
