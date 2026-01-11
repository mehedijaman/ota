<template>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Featured Tour Packages</h2>
                <p class="text-slate-500 mt-2">Curated experiences for unforgettable memories</p>
            </div>
        </div>

        <div v-if="tours.length" class="relative">
            <Splide :options="splideOptions" aria-label="Featured Tour Packages">
                <SplideSlide v-for="tour in tours" :key="tour.id">
                    <a :href="tour.url" class="block bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden border border-gray-100 flex flex-col h-full mx-2">
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
                            <div v-if="tour.is_bestseller" class="absolute top-3 left-3 bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                                Bestseller
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
                </SplideSlide>
            </Splide>
        </div>

        <div v-else class="text-center py-12 bg-slate-50 rounded-2xl">
            <i class="ri-compass-3-line text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No Featured Tours</h3>
            <p class="text-slate-500">Check back soon for exciting tour packages!</p>
        </div>
    </section>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'

const props = defineProps({
    tours: {
        type: Array,
        required: true,
        default: () => []
    }
})

const splideOptions = {
    type: 'loop',
    perPage: 4,
    perMove: 1,
    gap: '1rem',
    pagination: true,
    arrows: true,
    autoplay: true,
    interval: 4000,
    pauseOnHover: true,
    pauseOnFocus: true,
    breakpoints: {
        1280: {
            perPage: 3
        },
        1024: {
            perPage: 2
        },
        640: {
            perPage: 1,
            arrows: false
        }
    }
}

const formatRating = (rating) => {
    return (rating ?? 4.5).toFixed(1)
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-BD').format(price)
}
</script>

<style scoped>
:deep(.splide__arrow) {
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    width: 2.5rem;
    height: 2.5rem;
    opacity: 1;
    transition: all 2s;
}

:deep(.splide__arrow:hover) {
    background-color: rgb(255, 255, 255);
    transform: scale(1.1);
}

:deep(.splide__arrow svg) {
    fill: rgb(234, 88, 12);
    width: 1rem;
    height: 1rem;
}

:deep(.splide__arrow--prev) {
    left: -1rem;
}

:deep(.splide__arrow--next) {
    right: -1rem;
}

:deep(.splide__pagination) {
    bottom: -2rem;
}

:deep(.splide__pagination__page) {
    background: rgb(203, 213, 225);
    width: 0.5rem;
    height: 0.5rem;
    margin: 0 0.25rem;
    transition: all 0.2s;
}

:deep(.splide__pagination__page.is-active) {
    background: rgb(234, 88, 12);
    transform: scale(1.2);
}
</style>
