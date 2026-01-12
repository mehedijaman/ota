<template>
    <a :href="tour.url" class="group block h-full bg-white rounded-xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <!-- Image Container -->
        <div class="relative aspect-4/3 overflow-hidden">
            <img
                :src="tour.thumbnail_url || 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800'"
                :alt="tour.name"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
            />
            
            <!-- Badges -->
            <div class="absolute top-3 inset-x-3 flex justify-between items-start">
                <div class="flex flex-col gap-2">
                    <span v-if="tour.is_bestseller" class="bg-orange-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-sm">
                        Bestseller
                    </span>
                    <span v-else-if="tour.is_featured" class="bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider shadow-sm">
                        Featured
                    </span>
                </div>
                <div class="bg-white/95 backdrop-blur-sm px-2 py-1 rounded-md flex items-center gap-1 shadow-sm">
                    <i class="ri-star-fill text-amber-400 text-xs"></i>
                    <span class="text-xs font-bold text-slate-800">{{ formatRating(tour.average_rating) }}</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5 flex flex-col h-[calc(100%-aspect-[4/3])]">
            <!-- Meta -->
            <div class="flex items-center gap-3 text-xs text-slate-500 font-medium mb-3">
                <div class="flex items-center gap-1">
                    <i class="ri-time-line text-orange-500"></i>
                    <span>{{ tour.duration }}</span>
                </div>
                <div v-if="tour.destination_name" class="flex items-center gap-1">
                    <i class="ri-map-pin-line text-orange-500"></i>
                    <span class="truncate max-w-[100px]">{{ tour.destination_name }}</span>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-slate-800 leading-tight mb-2 group-hover:text-orange-600 transition-colors line-clamp-2">
                {{ tour.name }}
            </h3>

            <!-- Category -->
            <div v-if="tour.category_name" class="text-xs text-slate-400 mb-4">
                in <span class="text-slate-600 font-medium">{{ tour.category_name }}</span>
            </div>

            <div class="mt-auto pt-4 border-t border-slate-50 flex items-end justify-between">
                <div>
                    <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">Starting From</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold text-orange-600">৳{{ formatPrice(priceToDisplay) }}</span>
                        <span v-if="hasDiscount" class="text-xs text-slate-400 line-through decoration-slate-300">৳{{ formatPrice(originalPrice) }}</span>
                    </div>
                </div>
                
                <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class="ri-arrow-right-line"></i>
                </div>
            </div>
        </div>
    </a>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    tour: {
        type: Object,
        required: true
    }
})

const formatRating = (rating) => (rating ?? 4.5).toFixed(1)
const formatPrice = (price) => new Intl.NumberFormat('en-BD').format(price || 0)

const minPrice = computed(() => props.tour.min_price || 0)
const basePrice = computed(() => props.tour.base_price || 0)
const discountPrice = computed(() => props.tour.discount_price || 0)

const hasDiscount = computed(() => {
    return discountPrice.value > 0 && discountPrice.value < basePrice.value
})

const priceToDisplay = computed(() => {
    // Priority: Discount Price > Min Price from pricings > Base Price
    if (discountPrice.value > 0) return discountPrice.value
    if (minPrice.value > 0) return minPrice.value
    return basePrice.value
})

const originalPrice = computed(() => {
    if (hasDiscount.value) return basePrice.value
    return 0
})
</script>
