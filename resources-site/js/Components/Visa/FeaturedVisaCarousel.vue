<template>        
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-slate-50 rounded-3xl mb-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Featured Visa Services</h2>
            <p class="text-slate-500 mt-2">Our most popular visa packages with the best success rates</p>
        </div>

        <div v-if="visas.length" class="relative">
            <Splide :options="splideOptions" aria-label="Featured Visas">
                <SplideSlide v-for="visa in visas" :key="visa.id">
                    <VisaCard :visa="visa" />
                </SplideSlide>
            </Splide>
        </div>

         <div v-else class="text-center py-12">
            <i class="ri-passport-line text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No Featured Visas</h3>
            <p class="text-slate-500">Check back later for our recommended visa services!</p>
        </div>
    </section>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'
import VisaCard from './VisaCard.vue'

const props = defineProps({
    visas: {
        type: Array,
        required: true,
        default: () => []
    }
})

const splideOptions = {
    type: 'loop',
    perPage: 3,
    perMove: 1,
    gap: '1.5rem',
    pagination: true,
    arrows: true,
    autoplay: true,
    interval: 3500,
    pauseOnHover: true,
    pauseOnFocus: true,
    breakpoints: {
        1280: {
            perPage: 3,
            gap: '1rem'
        },
        1024: {
            perPage: 2,
            gap: '1rem'
        },
        768: {
             perPage: 2,
             gap: '1rem'
        },
        640: {
            perPage: 1,
            padding: '1rem',
            gap: '1rem',
            arrows: false
        }
    }
}
</script>

<style scoped>
:deep(.splide__arrow) {
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    width: 3rem;
    height: 3rem;
    opacity: 1;
    transition: all 0.2s;
}

:deep(.splide__arrow:hover) {
    background-color: rgb(255, 255, 255);
    transform: scale(1.1);
    color: rgb(147, 51, 234); /* Purple-600 */
}

:deep(.splide__arrow svg) {
    fill: rgb(147, 51, 234); /* Purple-600 */
    width: 1.2rem;
    height: 1.2rem;
}

:deep(.splide__arrow--prev) {
    left: -1.5rem;
}

:deep(.splide__arrow--next) {
    right: -1.5rem;
}

:deep(.splide__pagination) {
    bottom: -2.5rem;
}

:deep(.splide__pagination__page) {
    background: rgb(203, 213, 225);
    width: 0.5rem;
    height: 0.5rem;
    margin: 0 0.25rem;
    transition: all 0.2s;
}

:deep(.splide__pagination__page.is-active) {
    background: rgb(147, 51, 234); /* Purple-600 */
    transform: scale(1.2);
}
</style>
