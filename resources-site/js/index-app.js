import { createVueApp } from './create-vue-app.js'
import TourPhotoGallery from './Components/TourPhotoGallery.vue'
import ScrollToTop from './Components/ScrollToTop.vue'
import FeaturedToursCarousel from './Components/FeaturedToursCarousel.vue'
import TestimonialsCarousel from './Components/TestimonialsCarousel.vue'
import LatestPostsCarousel from './Components/LatestPostsCarousel.vue'

createVueApp({
    TourPhotoGallery,
    ScrollToTop,
    FeaturedToursCarousel,
    TestimonialsCarousel,
    LatestPostsCarousel
}).mount('#app')
