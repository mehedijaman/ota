<template>
    <div class="relative mt-1">
        <input
            :value="modelValue"
            v-bind="$attrs"
            class="peer border-neutral-200 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-transparent shadow-sm transition-all duration-200 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 dark:focus:ring-blue-900"
            :type="inputType"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <div
            class="absolute inset-y-0 right-0 flex items-center pr-3 hover:cursor-pointer"
        >
            <i
                class="inline-block w-5"
                :class="eyeIcon"
                @click="toggleInput"
            ></i>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
    modelValue: {
        type: String,
        required: true
    }
})

defineOptions({
    inheritAttrs: false
})

const inputType = ref('password')
const eyeIcon = ref('ri-eye-line')

const toggleInput = () => {
    inputType.value = inputType.value === 'password' ? 'text' : 'password'
    eyeIcon.value =
        eyeIcon.value === 'ri-eye-line' ? 'ri-eye-off-line' : 'ri-eye-line'
}

defineEmits(['update:modelValue'])
</script>
