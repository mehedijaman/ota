<template>
    <AppSectionHeader title="Categories" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="pt-4">
                <TestimonialBody />

                <TestimonialImage />

                <TestimonialVisibility />
                <TestimonialFeatured />
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                Save
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { onUnmounted } from 'vue'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import TestimonialBody from './Components/TestimonialBody.vue'
import TestimonialImage from './Components/TestimonialImage.vue'
import TestimonialVisibility from './Components/TestimonialVisibility.vue'
import { useTestimonialStore } from './TestimonialStore'
import TestimonialFeatured from './Components/TestimonialFeatured.vue'

const testimonialStore = useTestimonialStore()

const props = defineProps({
    testimonial: {
        type: Object,
        default: null
    }
})

if (props.testimonial) {
    testimonialStore.setTestimonial(props.testimonial)
}

onUnmounted(() => {
    testimonialStore.$reset()
})

const { title } = useTitle('Testimonial')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Testimonials', href: route('testimonial.index') },
    { label: title, last: true }
]

const { isCreate } = useFormContext()

const submitForm = () => {
    const form = useForm(testimonialStore.testimonial)

    if (isCreate.value) {
        form.post(route('testimonial.store'))
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('testimonial.update', props.testimonial.id))
    }
}
</script>
