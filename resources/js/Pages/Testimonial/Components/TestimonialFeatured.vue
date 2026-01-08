<template>
    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="featured"
            v-model="testimonialStore.testimonial.featured"
            name="featured"
            :value="true"
        />
        <AppLabel for="featured" class="ml-3"> Featured </AppLabel>
    </div>
</template>

<script setup>
import { useTestimonialStore } from '../TestimonialStore'
const testimonialStore = useTestimonialStore()
</script>
