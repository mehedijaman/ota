<template>
    <div class="mt-5">
        <AppInputFile
            class="w-full"
            v-model="sliderStore.slider.image"
            :image-preview-url="getImagePreviewURL()"
            @remove-file="sliderStore.slider.remove_previous_image = true"
        ></AppInputFile>
    </div>
</template>

<script setup>
import useFormContext from '@/Composables/useFormContext'
import { useSliderStore } from '../SliderStore'

const sliderStore = useSliderStore()

const { isCreate } = useFormContext()

const getImagePreviewURL = () => {
    if (!isCreate.value && sliderStore.slider.image_url) {
        return sliderStore.slider.image_url
    }

    return null
}
</script>
