<template>
    <div class="tour-carousel-wrapper">
        <!-- Main Carousel -->
        <Splide
            ref="mainCarousel"
            :options="mainOptions"
            aria-label="Tour Gallery"
            class="main-carousel"
        >
            <SplideSlide v-for="(image, index) in images" :key="index">
                <img
                    :src="image.url"
                    :alt="image.alt || image.name || `Tour image ${index + 1}`"
                    class="h-[350px] w-full rounded-xl object-cover sm:h-[450px]"
                />
            </SplideSlide>
        </Splide>

        <!-- Thumbnail Carousel -->
        <Splide
            v-if="images.length > 1"
            ref="thumbsCarousel"
            :options="thumbsOptions"
            aria-label="Tour Gallery Thumbnails"
            class="thumbnails-carousel mt-4"
        >
            <SplideSlide v-for="(image, index) in images" :key="index">
                <img
                    :src="image.url"
                    :alt="image.alt || image.name || `Thumbnail ${index + 1}`"
                    class="h-20 w-full cursor-pointer rounded-lg object-cover transition-all duration-200 hover:opacity-100 sm:h-24"
                />
            </SplideSlide>
        </Splide>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'

defineProps({
    images: {
        type: Array,
        required: true,
        validator: (value) => {
            return value.every((img) => img.url)
        }
    }
})

const mainCarousel = ref(null)
const thumbsCarousel = ref(null)

const mainOptions = {
    type: 'fade',
    rewind: true,
    pagination: false,
    arrows: true,
    autoplay: true,
    interval: 5000,
    pauseOnHover: true,
    pauseOnFocus: true,
    resetProgress: false,
    heightRatio: 0.6,
    cover: true
}

const thumbsOptions = {
    type: 'slide',
    rewind: true,
    gap: '0.75rem',
    pagination: false,
    fixedWidth: 110,
    fixedHeight: 80,
    cover: true,
    focus: 'center',
    isNavigation: true,
    arrows: false,
    drag: 'free',
    breakpoints: {
        640: {
            fixedWidth: 90,
            fixedHeight: 70,
            gap: '0.5rem'
        }
    }
}

onMounted(() => {
    if (mainCarousel.value && thumbsCarousel.value) {
        const main = mainCarousel.value.splide
        const thumbs = thumbsCarousel.value.splide

        if (main && thumbs) {
            main.sync(thumbs)
        }
    }
})
</script>

<style scoped>
.tour-carousel-wrapper {
    width: 100%;
}

.main-carousel :deep(.splide__arrow) {
    background-color: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(4px);
    box-shadow:
        0 10px 15px -3px rgba(0, 0, 0, 0.1),
        0 4px 6px -4px rgba(0, 0, 0, 0.1);
    transition: all 0.2s;
    width: 2.5rem;
    height: 2.5rem;
}

.main-carousel :deep(.splide__arrow:hover) {
    background-color: rgb(255, 255, 255);
}

@media (min-width: 640px) {
    .main-carousel :deep(.splide__arrow) {
        width: 3rem;
        height: 3rem;
    }
}

.main-carousel :deep(.splide__arrow svg) {
    fill: rgb(30, 41, 59);
}

.main-carousel :deep(.splide__arrow--prev) {
    left: 0.5rem;
}

@media (min-width: 640px) {
    .main-carousel :deep(.splide__arrow--prev) {
        left: 1rem;
    }
}

.main-carousel :deep(.splide__arrow--next) {
    right: 0.5rem;
}

@media (min-width: 640px) {
    .main-carousel :deep(.splide__arrow--next) {
        right: 1rem;
    }
}

.thumbnails-carousel :deep(.splide__slide) {
    opacity: 0.6;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.thumbnails-carousel :deep(.splide__slide.is-active) {
    opacity: 1;
    border-color: rgb(20, 184, 166);
    box-shadow:
        0 10px 15px -3px rgba(0, 0, 0, 0.1),
        0 4px 6px -4px rgba(0, 0, 0, 0.1);
}

.thumbnails-carousel :deep(.splide__slide:hover) {
    opacity: 0.9;
}
</style>
