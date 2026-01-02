<template>
    <div class="flex items-center gap-2">
        <label class="relative inline-flex cursor-pointer items-center">
            <input
                type="checkbox"
                :checked="modelValue"
                @change="$emit('update:modelValue', $event.target.checked)"
                class="peer sr-only"
            />
            <div
                class="peer h-6 w-11 rounded-full bg-gray-200 transition-all peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300"
            ></div>
            <div
                class="absolute top-0.5 left-0.5 h-5 w-5 transform rounded-full bg-white shadow transition-transform peer-checked:translate-x-full"
            ></div>
        </label>
        <span v-if="label" class="text-sm select-none">{{ label }}</span>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        default: ''
    }
})
</script>

<style scoped>
/* Optional: smooth transition */
</style>
