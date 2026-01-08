<template>
    <div class="mt-5">
        <AppInputFile
            v-model="testimonialStore.testimonial.image"
            :image-preview-url="getImagePreviewURL()"
            @remove-file="
                testimonialStore.testimonial.remove_previous_image = true
            "
        ></AppInputFile>
    </div>
</template>

<script setup>
import useFormContext from '@/Composables/useFormContext'
import { useTestimonialStore } from '../TestimonialStore'

const testimonialStore = useTestimonialStore()

const { isCreate } = useFormContext()

const getImagePreviewURL = () => {
    if (!isCreate.value && testimonialStore.testimonial.image_url) {
        return testimonialStore.testimonial.image_url
    }

    return null
}
</script>
