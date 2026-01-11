<template>
    <section v-if="posts.length" class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-12 flex flex-col items-center justify-between gap-6 text-center md:flex-row md:text-left">
                <div>
                    <span class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        From Our Blog
                    </span>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-800 sm:text-4xl">
                        Latest Travel Stories
                    </h2>
                    <p class="mt-3 max-w-2xl text-lg text-slate-500">
                        Tips, guides, and inspiration for your next adventure
                    </p>
                </div>
                <a :href="seeAllUrl" class="group inline-flex items-center gap-2 rounded-full bg-blue-600 px-6 py-3 font-medium text-white shadow-lg shadow-blue-600/25 transition-all hover:bg-blue-700 hover:shadow-blue-600/40">
                    View All Posts
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Posts Carousel -->
            <Splide :options="splideOptions" aria-label="Latest Blog Posts">
                <SplideSlide v-for="post in posts" :key="post.id">
                    <article class="group mx-2 flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <!-- Image -->
                        <a :href="post.url" class="relative block aspect-[16/10] overflow-hidden">
                            <img v-if="post.image_url" :src="post.image_url" :alt="post.title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
                                <svg class="h-16 w-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <!-- Date Badge -->
                            <div class="absolute left-4 top-4 rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm backdrop-blur-sm">
                                {{ post.published_at }}
                            </div>
                        </a>

                        <!-- Content -->
                        <div class="flex flex-1 flex-col p-6">
                            <!-- Tags -->
                            <div v-if="post.tags && post.tags.length > 0" class="mb-3 flex flex-wrap gap-2">
                                <span v-for="tag in post.tags.slice(0, 2)" :key="tag" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600">
                                    {{ tag }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="mb-3 text-xl font-bold text-slate-800">
                                <a :href="post.url" class="transition-colors hover:text-blue-600">
                                    {{ truncateText(post.title, 60) }}
                                </a>
                            </h3>

                            <!-- Summary -->
                            <p class="mb-4 flex-1 text-sm leading-relaxed text-slate-500">
                                {{ truncateText(post.summary, 120) }}
                            </p>

                            <!-- Footer -->
                            <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
                                <!-- Author -->
                                <div v-if="post.author_name" class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-xs font-bold text-white">
                                        {{ getInitial(post.author_name) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-600">
                                        {{ post.author_name }}
                                    </span>
                                </div>
                                <div v-else class="text-sm text-slate-400">
                                    TriptonicBD
                                </div>

                                <!-- Read More -->
                                <a :href="post.url" class="group/link inline-flex items-center gap-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700">
                                    Read More
                                    <svg class="h-4 w-4 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                </SplideSlide>
            </Splide>
        </div>
    </section>
</template>

<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/vue-splide/css'

const props = defineProps({
    posts: {
        type: Array,
        required: true,
        default: () => []
    },
    seeAllUrl: {
        type: String,
        required: true
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
    interval: 5000,
    pauseOnHover: true,
    pauseOnFocus: true,
    breakpoints: {
        1024: {
            perPage: 2
        },
        640: {
            perPage: 1,
            arrows: false
        }
    }
}

const truncateText = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const getInitial = (name) => {
    return name ? name.charAt(0).toUpperCase() : ''
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
    background: rgb(37, 99, 235);
    transform: scale(1.2);
}
</style>
