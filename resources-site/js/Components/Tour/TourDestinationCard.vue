<template>
    <a :href="destination.url" class="group relative block rounded-2xl overflow-hidden aspect-3/4 shadow-md hover:shadow-2xl transition-all duration-500 mx-2 ring-1 ring-slate-900/5">
        <!-- Background Image with Zoom Effect -->
        <img 
            :src="destination.thumbnail_url || 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800'"
            :alt="destination.name"
            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 will-change-transform"
        />

        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-300"></div>

        <!-- Top Badge (Tour Count) -->
        <div class="absolute top-4 right-4 translate-y-[-10px] opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 delay-100">
            <span class="bg-white/95 backdrop-blur-md text-slate-800 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                {{ destination.tours_count }} {{ destination.tours_count === 1 ? 'Tour' : 'Tours' }}
            </span>
        </div>

        <!-- content -->
        <div class="absolute bottom-0 inset-x-0 p-6 flex flex-col justify-end">
            <h3 class="text-2xl font-bold text-white mb-2 leading-tight drop-shadow-md group-hover:text-orange-50 transition-colors">
                {{ destination.name }}
            </h3>
            
            <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-300 ease-out opacity-0 group-hover:opacity-100">
                <p class="text-sm text-slate-200 font-medium flex items-center gap-2 mb-1">
                    <span>Discover {{ destination.tours_count }} experiences</span>
                    <i class="ri-arrow-right-line text-orange-400"></i>
                </p>
            </div>
            
            <!-- Default state subtitle (fades out on hover) -->
            <p class="text-sm text-white/70 font-medium group-hover:hidden transition-opacity duration-300">
                {{ destination.tours_count }} {{ destination.tours_count === 1 ? 'Tour' : 'Tours' }} Available
            </p>
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
