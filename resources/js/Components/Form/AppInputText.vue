<template>
    <input
        ref="inputRef"
        :value="modelValue"
        :placeholder="placeholder"
        class="peer border-neutral-200 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 shadow-sm transition-all duration-200 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:ring-blue-900"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

<script setup>
import { ref } from 'vue'

// ✅ Props
defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    }
})

// ✅ Emits
defineEmits(['update:modelValue'])

// ✅ Ref & methods
const inputRef = ref(null)

const focusInput = () => {
    inputRef.value?.focus()
}

defineExpose({
    focusInput
})
</script>
