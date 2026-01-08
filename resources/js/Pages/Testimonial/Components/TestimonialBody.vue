<template>
    <div>
        <AppLabel for="name">Name</AppLabel>
        <AppInputText
            id="name"
            v-model="testimonialStore.testimonial.name"
            type="text"
            :class="{
                'input-error': errorsFields.includes('name')
            }"
            autocomplete="off"
        />
    </div>

    <div>
        <AppLabel for="designation">Designation</AppLabel>
        <AppInputText
            id="designation"
            v-model="testimonialStore.testimonial.designation"
            type="text"
            :class="{
                'input-error': errorsFields.includes('designation')
            }"
            autocomplete="off"
        />
    </div>

    <div>
        <AppLabel for="company">Company</AppLabel>
        <AppInputText
            id="company"
            v-model="testimonialStore.testimonial.company"
            type="text"
            :class="{
                'input-error': errorsFields.includes('company')
            }"
            autocomplete="off"
        />
    </div>

    <div>
        <AppLabel for="title">Title</AppLabel>
        <AppInputText
            id="title"
            v-model="testimonialStore.testimonial.title"
            type="text"
            :class="{
                'input-error': errorsFields.includes('title')
            }"
            autocomplete="off"
        />
    </div>

    <div>
        <AppLabel for="description">Description</AppLabel>

        <AppTextArea
            id="description"
            v-model="testimonialStore.testimonial.description"
            type="text"
            :class="{
                'input-error': errorsFields.includes('description')
            }"
            placeholder="Description"
        />
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { useTestimonialStore } from '../TestimonialStore'
const testimonialStore = useTestimonialStore()
const { errorsFields } = useFormErrors()
</script>
