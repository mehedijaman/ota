<template>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="pt-4">
                <SliderBody />

                <SliderImage />
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
import SliderBody from './Components/SliderBody.vue'
import SliderImage from './Components/SliderImage.vue'
// import SliderVisibility from './Components/SliderVisibility.vue'
import { useSliderStore } from './SliderStore'

const sliderStore = useSliderStore()

const props = defineProps({
    slider: {
        type: Object,
        default: null
    }
})

if (props.slider) {
    sliderStore.setSlider(props.slider)
}

onUnmounted(() => {
    sliderStore.$reset()
})

const { title } = useTitle('Slider')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Sliders', href: route('slider.index') },
    { label: title, last: true }
]

const { isCreate } = useFormContext()

const submitForm = () => {
    const form = useForm(sliderStore.slider)

    if (isCreate.value) {
        form.post(route('slider.store'))
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('slider.update', props.slider.id))
    }
}
</script>
