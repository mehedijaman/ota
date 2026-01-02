<template>
    <!-- For Boolean -->
    <input
        v-if="isBooleanType"
        type="checkbox"
        class="text-primary h-4 w-4 rounded-sm focus:ring-2 hover:cursor-pointer"
        v-bind="$attrs"
        :checked="modelValue"
        @change="updateBooleanValue"
    />

    <!-- For Array -->
    <input
        v-else
        type="checkbox"
        class="text-primary h-4 w-4 rounded-sm focus:ring-2 hover:cursor-pointer"
        v-bind="$attrs"
        :value="value"
        :checked="isChecked"
        @change="updateArrayValue"
    />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Boolean, Array],
        default: () => []
    },
    value: {
        type: [String, Number, Boolean, Object],
        required: false,
        default: ''
    }
})

// boolean type
const emit = defineEmits(['update:modelValue'])

const isBooleanType = computed(() => typeof props.modelValue === 'boolean')

function updateBooleanValue(event) {
    emit('update:modelValue', event.target.checked)
}

// array type - make isChecked reactive to modelValue changes
const isChecked = computed(() => {
    if (Array.isArray(props.modelValue) && props.value) {
        return props.modelValue.some((item) => item.id === props.value.id)
    }
    return false
})

const updateArrayValue = (event) => {
    if (Array.isArray(props.modelValue)) {
        const updatedValue = [...props.modelValue]
        if (event.target.checked) {
            updatedValue.push(props.value)
        } else {
            const index = updatedValue.findIndex((item) => item.id === props.value.id)
            if (index > -1) {
                updatedValue.splice(index, 1)
            }
        }
        emit('update:modelValue', updatedValue)
    }
}
</script>
