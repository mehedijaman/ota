<template>
    <a :href="hotel.url" class="group block h-full bg-white rounded-xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <!-- Image Container -->
        <div class="relative aspect-4/3 overflow-hidden">
            <img
                :src="hotel.thumbnail_url || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80'"
                :alt="hotel.name"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
            />
            
            <!-- Badges -->
            <div class="absolute top-3 inset-x-3 flex justify-between items-start">
                <div class="flex flex-col gap-2">
                    <span v-if="hotel.is_featured" class="bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-sm">
                        Featured
                    </span>
                </div>
                <div class="bg-white/95 backdrop-blur-sm px-2 py-1 rounded-md flex items-center gap-1 shadow-sm">
                    <i class="ri-star-fill text-amber-400 text-xs"></i>
                    <span class="text-xs font-bold text-slate-800">{{ formatRating(hotel.avg_rating) }}</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5 flex flex-col h-[calc(100%-aspect-[4/3])]">
            <!-- Star Rating -->
            <div class="flex items-center gap-1 mb-2">
                <i v-for="star in hotel.star_rating" :key="star" class="ri-star-fill text-amber-400 text-xs"></i>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-slate-800 leading-tight mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                {{ hotel.name }}
            </h3>

            <!-- Location -->
            <div v-if="hotel.city_name" class="flex items-center gap-1 text-xs text-slate-500 mb-4">
                <i class="ri-map-pin-line text-blue-500"></i>
                <span class="truncate">{{ hotel.city_name }}<span v-if="hotel.country_name">, {{ hotel.country_name }}</span></span>
            </div>

            <div class="mt-auto pt-4 border-t border-slate-50 flex items-end justify-between">
                <div v-if="hotel.min_price">
                    <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Starting From</span>
                    <div class="flex items-center gap-1">
                        <span class="text-xl font-extrabold text-blue-600">৳{{ formatPrice(hotel.min_price) }}</span>
                        <span class="text-xs text-slate-400">/night</span>
                    </div>
                </div>
                
                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="ri-arrow-right-line"></i>
                </div>
            </div>
        </div>
    </a>
</template>

<script setup>
const props = defineProps({
    hotel: {
        type: Object,
        required: true
    }
})

const formatRating = (rating) => parseFloat(rating || 4.5).toFixed(1)
const formatPrice = (price) => new Intl.NumberFormat('en-BD').format(price || 0)
</script>
