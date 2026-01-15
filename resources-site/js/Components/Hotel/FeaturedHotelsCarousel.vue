<template>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Featured Hotels</h2>
                <p class="text-slate-500 mt-2">Hand-picked hotels with exceptional ratings</p>
            </div>
        </div>

        <div v-if="hotels.length" class="relative">
            <Splide :options="splideOptions" aria-label="Featured Hotels">
                <SplideSlide v-for="hotel in hotels" :key="hotel.id">
                    <HotelCard :hotel="hotel" />
                </SplideSlide>
            </Splide>
        </div>

        <div v-else class="text-center py-12 bg-slate-50 rounded-2xl">
            <i class="ri-hotel-line text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No Featured Hotels</h3>
            <p class="text-slate-500">Check back soon for amazing hotel deals!</p>
        </div>
    </section>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'
import HotelCard from './HotelCard.vue'

const props = defineProps({
    hotels: {
        type: Array,
        required: true,
        default: () => []
    }
})

const splideOptions = {
    type: 'loop',
    perPage: 3,
    perMove: 1,
    gap: '1rem',
    pagination: true,
    arrow: true,
    autoplay: true,
    interval: 4000,
    pauseOnHover: true,
    pauseOnFocus: true,
    breakpoints: {
        1280: { perPage: 3 },
        1024: { perPage: 2 },
        640: { perPage: 1, arrows: false }
    }
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
    transition: all 0.2s;
}

:deep(.splide__arrow:hover) {
    background-color: rgb(255, 255, 255);
    transform: scale(1.1);
}

:deep(.splide__arrow svg) {
    fill: rgb(37, 99, 235);
    width: 1rem;
    height: 1rem;
}

:deep(.splide__arrow--prev) { left: -1rem; }
:deep(.splide__arrow--next) { right: -1rem; }
:deep(.splide__pagination) { bottom: -2rem; }

:deep(.splide__pagination__page) {
    background: rgb(203, 213, 225);
    width: 0.5rem;
    height: 0.5rem;
    margin: 0 0.25rem;
    transition: all 0.2s;
}

:deep(.splide__pagination__page.is-active) {
    background: rgb(37, 99, 235);
    transform: scale(1.2);
}
</style>
