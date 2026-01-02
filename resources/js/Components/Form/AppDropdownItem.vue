<template>
    <button
        type="button"
        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:cursor-pointer hover:bg-gray-100"
        @click="$emit('click')"
    >
        <slot />
    </button>
</template>

<script setup>
defineEmits(['click'])
</script>
