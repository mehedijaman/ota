import { createVueApp } from './create-vue-app.js'
import ScrollToTop from './Components/ScrollToTop.vue'
import TestimonialsCarousel from './Components/Testimonial/TestimonialsCarousel.vue'
import LatestPostsCarousel from './Components/Blog/LatestPostsCarousel.vue'

// Tour Components
import TourCard from './Components/Tour/TourCard.vue'
import TourPhotoGallery from './Components/Tour/TourPhotoGallery.vue'
import FeaturedToursCarousel from './Components/Tour/FeaturedToursCarousel.vue'
import FeaturedTourDestinationsCarousel from './Components/Tour/FeaturedTourDestinationsCarousel.vue'
import FeaturedTourCategoriesCarousel from './Components/Tour/FeaturedTourCategoriesCarousel.vue'

// Visa Components
import VisaCard from './Components/Visa/VisaCard.vue'
import FeaturedVisaDestinationsCarousel from './Components/Visa/FeaturedVisaDestinationsCarousel.vue'
import FeaturedVisaCarousel from './Components/Visa/FeaturedVisaCarousel.vue'

// Hotel Components
import HotelCard from './Components/Hotel/HotelCard.vue'
import FeaturedHotelsCarousel from './Components/Hotel/FeaturedHotelsCarousel.vue'
import FeaturedHotelDestinationsCarousel from './Components/Hotel/FeaturedHotelDestinationsCarousel.vue'

createVueApp({
    ScrollToTop,
    TestimonialsCarousel,
    LatestPostsCarousel,

    // Visa components
    VisaCard,
    FeaturedVisaCarousel,
    FeaturedVisaDestinationsCarousel,

    // Tour components
    TourCard,
    TourPhotoGallery,
    FeaturedToursCarousel,
    FeaturedTourDestinationsCarousel,
    FeaturedTourCategoriesCarousel,

    // Hotel Components
    HotelCard,
    FeaturedHotelsCarousel,
    FeaturedHotelDestinationsCarousel,
}).mount('#app')
