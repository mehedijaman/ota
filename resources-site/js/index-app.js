import { createVueApp } from './create-vue-app.js'
import TourPhotoGallery from './Components/TourPhotoGallery.vue'
import ScrollToTop from './Components/ScrollToTop.vue'
import FeaturedToursCarousel from './Components/FeaturedToursCarousel.vue'
import FeaturedTourDestinationsCarousel from './Components/FeaturedTourDestinationsCarousel.vue'
import FeaturedTourCategoriesCarousel from './Components/FeaturedTourCategoriesCarousel.vue'
import TestimonialsCarousel from './Components/TestimonialsCarousel.vue'
import LatestPostsCarousel from './Components/LatestPostsCarousel.vue'

createVueApp({
    ScrollToTop,
    TourPhotoGallery,
    FeaturedToursCarousel,
    FeaturedTourDestinationsCarousel,
    FeaturedTourCategoriesCarousel,
    TestimonialsCarousel,
    LatestPostsCarousel,
}).mount('#app')
