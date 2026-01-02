<template>
    <textarea
        ref="textarea"
        placeholder="Enter your message here"
        class="peer border-neutral-8 block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-neutral-900 placeholder-transparent shadow-sm transition-all duration-200 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
        :class="{ 'overflow-y-hidden': autoResize }"
        :value="modelValue"
        @input="handleInput"
    ></textarea>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, null],
        required: true
    },
    autoResize: {
        type: Boolean,
        default: true
    }
})

onMounted(() => {
    if (props.autoResize) {
        resizeTextarea()
    }
})

const emit = defineEmits(['update:modelValue'])
const textarea = ref(null)

function handleInput() {
    const value = textarea.value.value
    emit('update:modelValue', value)
    if (props.autoResize) {
        resizeTextarea()
    }
}

function resizeTextarea() {
    if (textarea.value) {
        textarea.value.style.height = `${textarea.value.scrollHeight}px`
    }
}
</script>

<style scoped>
@reference "../../../css/app.css";
* {
    scrollbar-width: thin;
    scrollbar-color: theme('colors.neutral.7') theme('colors.neutral.1');
}

::-webkit-scrollbar {
    background-color: theme('colors.neutral.1');
}

::-webkit-scrollbar-corner {
    background-color: theme('colors.neutral.1');
}

::-webkit-scrollbar-corner,
::-webkit-scrollbar-track {
    background-color: theme('colors.neutral.7');
}

::-webkit-scrollbar-thumb {
    background-color: theme('colors.neutral.1');
    border-radius: 20px;
    border: 3px solid theme('colors.neutral.7');
}
</style>
