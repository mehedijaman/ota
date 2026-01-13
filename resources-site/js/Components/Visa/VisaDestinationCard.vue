<template>
    <a :href="destination.url" class="group block h-full overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all hover:shadow-xl">
        <div class="relative mb-3 aspect-video overflow-hidden rounded-xl">
             <img 
                v-if="destination.image" 
                :src="destination.image" 
                :alt="destination.name" 
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div v-else class="flex h-full w-full items-center justify-center bg-linear-to-br from-purple-400 to-fuchsia-500">
                <span class="text-4xl font-bold text-white">{{ destination.name.substring(0, 2) }}</span>
            </div>
            
            <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-60 transition-opacity group-hover:opacity-40"></div>
            
            <div class="absolute bottom-3 left-3">
                 <span class="rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                    {{ destination.continent }}
                </span>
            </div>
        </div>
        <div class="px-4 pb-4 text-center">
            <h3 class="line-clamp-1 text-lg font-bold text-slate-800 transition-colors group-hover:text-purple-600">{{ destination.name }}</h3>
            <p class="mt-1 text-sm font-semibold text-green-600">
                {{ destination.visas_count }} Visa{{ destination.visas_count !== 1 ? 's' : '' }} Available
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
