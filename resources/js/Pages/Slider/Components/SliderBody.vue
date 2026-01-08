<template>
    <div>
        <AppLabel for="title">Title</AppLabel>
        <AppInputText
            id="title"
            v-model="sliderStore.slider.title"
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
            v-model="sliderStore.slider.description"
            type="text"
            :class="{
                'input-error': errorsFields.includes('description')
            }"
            placeholder="Description"
        />
    </div>

    <SliderVisibility />
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { useSliderStore } from '../SliderStore'
const sliderStore = useSliderStore()
const { errorsFields } = useFormErrors()
import SliderVisibility from './SliderVisibility.vue'
</script>
