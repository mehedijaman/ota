<template>
    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="term"
            v-model="testimonialStore.testimonial.active"
            name="term"
            :value="true"
        />
        <AppLabel for="term" class="ml-3"> Active </AppLabel>
    </div>
</template>

<script setup>
import { useTestimonialStore } from '../TestimonialStore'
const testimonialStore = useTestimonialStore()
</script>
