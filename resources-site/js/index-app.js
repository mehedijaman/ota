import { createVueApp } from './create-vue-app.js'
import TourCarousel from './Components/TourCarousel.vue'
import ScrollToTop from './Components/ScrollToTop.vue'

createVueApp({
    TourCarousel,
    ScrollToTop
}).mount('#app')
