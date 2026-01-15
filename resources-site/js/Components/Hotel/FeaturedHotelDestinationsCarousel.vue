<template>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-slate-50">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Popular Destinations</h2>
                <p class="text-slate-500 mt-2">Explore hotels in trending locations</p>
            </div>
        </div>

        <div v-if="destinations.length" class="relative">
            <Splide :options="splideOptions" aria-label="Popular Destinations">
                <SplideSlide v-for="destination in destinations" :key="destination.id">
                    <a :href="destination.url" 
                       class="group block rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative aspect-square">
                            <div v-if="destination.image_url" class="absolute inset-0">
                                <img :src="destination.image_url" :alt="destination.name" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"/>
                            </div>
                            <div v-else class="absolute inset-0 bg-linear-to-br from-blue-500 to-indigo-600"></div>
                            
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/80 via-slate-900/30 to-transparent"></div>
                            
                            <!-- Content -->
                            <div class="absolute inset-x-0 bottom-0 p-4 text-white">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-building-line text-blue-300"></i>
                                    <span class="text-sm text-blue-200">{{ destination.hotels_count }} hotels</span>
                                </div>
                                <h3 class="text-xl font-bold group-hover:text-blue-300 transition-colors">
                                    {{ destination.name }}
                                </h3>
                            </div>
                        </div>
                    </a>
                </SplideSlide>
            </Splide>
        </div>

        <div v-else class="text-center py-12 bg-white rounded-2xl">
            <i class="ri-map-pin-line text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No Destinations</h3>
            <p class="text-slate-500">Check back soon for popular destinations!</p>
        </div>
    </section>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'

const props = defineProps({
    destinations: {
        type: Array,
        required: true,
        default: () => []
    }
})

const splideOptions = {
    type: 'loop',
    perPage: 5,
    perMove: 1,
    gap: '1rem',
    pagination: true,
    arrow: true,
    autoplay: true,
    interval: 5000,
    pauseOnHover: true,
    breakpoints: {
        1280: { perPage: 4 },
        1024: { perPage: 3 },
        768: { perPage: 2 },
        640: { perPage: 1, arrows: false }
    }
}
</script>

<style scoped>
:deep(.splide__arrow) {
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
