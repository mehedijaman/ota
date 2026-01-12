<template>
    <a :href="tour.url" class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden border border-gray-100 flex flex-col h-full">
        <div class="relative h-48 overflow-hidden">
            <img
                :src="tour.thumbnail_url || 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800'"
                :alt="tour.name"
                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
            />
            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-slate-800 flex items-center gap-1">
                <i class="ri-star-fill text-amber-400"></i>
                <span>{{ formatRating(tour.average_rating) }}</span>
            </div>
            <div v-if="tour.is_bestseller || tour.is_featured" class="absolute top-3 left-3 bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                {{ tour.is_bestseller ? 'Bestseller' : 'Featured' }}
            </div>
        </div>
        <div class="p-5 flex flex-col flex-1">
            <div v-if="tour.category_name" class="text-xs font-bold text-orange-600 mb-2 uppercase tracking-wide">
                {{ tour.category_name }}
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-orange-600 transition-colors">
                {{ tour.name }}
            </h3>
            <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                <i class="ri-time-line"></i>
                <span>{{ tour.duration }}</span>
                <template v-if="tour.destination_name">
                    <span class="mx-1">•</span>
                    <i class="ri-map-pin-line"></i>
                    <span>{{ tour.destination_name }}</span>
                </template>
            </div>
            <div class="mt-auto flex items-end justify-between border-t border-gray-100 pt-4">
                <div>
                    <span class="text-xs text-slate-400 block">From</span>
                    <div v-if="tour.discount_price && tour.discount_price < tour.base_price" class="flex items-center gap-2">
                        <span class="text-lg font-bold text-orange-600">৳{{ formatPrice(tour.discount_price) }}</span>
                        <span class="text-sm text-slate-400 line-through">৳{{ formatPrice(tour.base_price) }}</span>
                    </div>
                    <span v-else class="text-lg font-bold text-slate-800">৳{{ formatPrice(tour.base_price) }}</span>
                </div>
                <span class="text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    View Details
                </span>
            </div>
        </div>
    </a>
</template>

<script setup>
const props = defineProps({
    tour: {
        type: Object,
        required: true
    }
})

const formatRating = (rating) => {
    return (rating ?? 4.5).toFixed(1)
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD').format(price)
}
</script>
