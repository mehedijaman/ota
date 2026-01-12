<template>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-left mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Browse by Category</h2>
            <p class="text-slate-500 mt-2">Find your perfect adventure</p>
        </div>

        <div v-if="categories.length" class="relative">
            <Splide :options="splideOptions" aria-label="Tour Categories">
                <SplideSlide v-for="category in categories" :key="category.id">
                    <a :href="category.url" class="group bg-white block rounded-2xl p-6 text-center border border-slate-100 hover:border-orange-200 hover:shadow-lg transition-all mx-2 h-full">
                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-50 rounded-2xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                            <i :class="[category.icon || 'ri-compass-3-line', 'text-3xl text-orange-600']"></i>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-1">{{ category.name }}</h3>
                        <p class="text-sm text-slate-500">{{ category.tours_count }} Tours</p>
                    </a>
                </SplideSlide>
            </Splide>
        </div>

        <div v-else class="text-center py-8">
            <p class="text-slate-500">No categories found.</p>
        </div>
    </section>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'

const props = defineProps({
    categories: {
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
    arrows: true,
    autoplay: true,
    interval: 4000,
    pauseOnHover: true,
    breakpoints: {
        1280: {
            perPage: 4
        },
        1024: {
            perPage: 3
        },
        768: {
            perPage: 2
        },
        640: {
            perPage: 2,
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
