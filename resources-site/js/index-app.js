import { createVueApp } from './create-vue-app.js'
import TourPhotoGallery from './Components/Tour/TourPhotoGallery.vue'
import ScrollToTop from './Components/ScrollToTop.vue'
import FeaturedToursCarousel from './Components/Tour/FeaturedToursCarousel.vue'
import TourCard from './Components/Tour/TourCard.vue'
import FeaturedTourDestinationsCarousel from './Components/Tour/FeaturedTourDestinationsCarousel.vue'
import FeaturedVisaDestinationsCarousel from './Components/Visa/FeaturedVisaDestinationsCarousel.vue'
import FeaturedVisaCarousel from './Components/Visa/FeaturedVisaCarousel.vue'
import VisaCard from './Components/Visa/VisaCard.vue'
import FeaturedTourCategoriesCarousel from './Components/Tour/FeaturedTourCategoriesCarousel.vue'
import TestimonialsCarousel from './Components/Testimonial/TestimonialsCarousel.vue'
import LatestPostsCarousel from './Components/Blog/LatestPostsCarousel.vue'

createVueApp({
    ScrollToTop,
    TourPhotoGallery,
    FeaturedToursCarousel,
    TourCard,
    FeaturedTourDestinationsCarousel,
    FeaturedVisaDestinationsCarousel,
    FeaturedVisaCarousel,
    VisaCard,
    FeaturedTourCategoriesCarousel,
    TestimonialsCarousel,
    LatestPostsCarousel,
}).mount('#app')
