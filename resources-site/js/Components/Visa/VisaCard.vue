<template>
    <a :href="visa.url" class="group block h-full overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all hover:shadow-xl">
        <div class="relative aspect-4/3 overflow-hidden">
            <img 
                v-if="visa.image" 
                :src="visa.image" 
                :alt="visa.name" 
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div v-else class="flex h-full w-full items-center justify-center bg-linear-to-br from-purple-500 to-fuchsia-600">
                <svg class="h-16 w-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
            </div>

            <div v-if="visa.is_featured" class="absolute left-3 top-3">
                <span class="rounded-full bg-amber-500 px-2.5 py-1 text-xs font-bold text-white shadow">Featured</span>
            </div>
             <div v-if="visa.is_popular" class="absolute right-3 top-3">
                <span class="rounded-full bg-green-500 px-2.5 py-1 text-xs font-bold text-white shadow">Popular</span>
            </div>
        </div>
        
        <div class="p-5">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                <span v-if="visa.country" class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-600">
                    {{ visa.country }}
                </span>
                <span v-if="visa.category" class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">
                    {{ visa.category }}
                </span>
                 <span v-if="visa.type" class="rounded-full bg-fuchsia-50 px-2.5 py-1 text-xs font-semibold text-fuchsia-500">
                    {{ visa.type }}
                </span>
            </div>
            
            <h3 class="mb-2 text-lg font-bold text-slate-800 transition-colors group-hover:text-purple-600">
                {{ visa.name }}
            </h3>
            
            <p class="mb-4 text-sm text-slate-500 line-clamp-2">
                {{ visa.description }}
            </p>
            
            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <div>
                    <span class="text-xs text-slate-400">Starting from</span>
                    <p class="text-xl font-bold text-purple-600">৳{{ visa.price }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400">Processing</span>
                    <p class="text-sm font-semibold text-slate-700">{{ visa.processing_time || 'Varies' }}</p>
                </div>
            </div>
        </div>
    </a>
</template>

<script setup>
defineProps({
    visa: {
        type: Object,
        required: true
    }
})
</script>
