<template>
    <div class="mt-5 flex items-center">
        <AppCheckbox
            id="term"
            v-model="sliderStore.slider.active"
            name="term"
            :value="true"
        />
        <AppLabel for="term" class="ml-3"> Active </AppLabel>
    </div>
</template>

<script setup>
import { useSliderStore } from '../SliderStore'
const sliderStore = useSliderStore()
</script>
