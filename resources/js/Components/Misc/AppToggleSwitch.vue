<template>
    <div class="flex items-center">
        <button
            type="button"
            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
            :class="toggleClasses"
            :aria-pressed="isActive"
            :disabled="disabled"
            @click="toggle"
        >
            <span class="sr-only">{{ ariaLabel }}</span>
            <span
                aria-hidden="true"
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="knobClasses"
            />
        </button>
        <span
            v-if="trueLabel || falseLabel"
            class="ml-3 text-sm font-medium text-gray-700"
        >
            {{ isActive ? trueLabel : falseLabel }}
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Boolean, String, Number],
        default: false
    },
    trueValue: {
        type: [Boolean, String, Number],
        default: true
    },
    falseValue: {
        type: [Boolean, String, Number],
        default: false
    },
    trueLabel: {
        type: String,
        default: 'On'
    },
    falseLabel: {
        type: String,
        default: 'Off'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    ariaLabel: {
        type: String,
        default: 'Toggle switch'
    }
})

const emit = defineEmits(['update:modelValue'])

const isActive = computed(() => {
    return props.modelValue === props.trueValue
})

const toggle = () => {
    if (props.disabled) return

    const newValue = isActive.value ? props.falseValue : props.trueValue
    emit('update:modelValue', newValue)
}

const toggleClasses = computed(() => {
    const baseClasses = 'focus:ring-2 focus:ring-offset-2'
    const stateClasses = isActive.value ? 'bg-blue-600' : 'bg-gray-200'
    const disabledClasses = props.disabled
        ? 'opacity-50 cursor-not-allowed'
        : 'cursor-pointer'

    return [baseClasses, stateClasses, disabledClasses].join(' ')
})

const knobClasses = computed(() => {
    const translateClass = isActive.value ? 'translate-x-5' : 'translate-x-0'
    return translateClass
})
</script>

<style scoped>
/* Ensure smooth transitions */
button:focus:not(:focus-visible) {
    outline: none;
}
</style>
