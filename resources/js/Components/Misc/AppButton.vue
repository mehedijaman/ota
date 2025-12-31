<template>
    <button :type="type" @click="$emit('click')">
        <slot></slot>
    </button>
</template>

<script setup>
defineEmits(['click'])

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    }
})
</script>

<style scoped>
@reference "../../../css/app.css";

.btn {
    @apply z-0 flex items-center rounded-md px-4 py-2 text-sm font-medium transition-colors hover:cursor-pointer focus-visible:outline focus-visible:outline-offset-2;
}

.btn-primary {
    @apply bg-primary-9 text-primary-1 hover:bg-primary-10 focus-visible:outline-primary-9;
}

.btn-secondary {
    @apply bg-primary-3 text-primary-11 hover:bg-primary-4 focus-visible:outline-primary-11;
}

.btn-neutral {
    @apply bg-neutral-3 text-neutral-12 hover:bg-neutral-4 focus-visible:outline-neutral-11;
}

/* Destructive/Danger variant */
.btn-destructive,
.btn-danger {
    @apply bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600;
}

.btn-primary-outline {
    @apply border-primary-7 text-primary-11 hover:bg-primary-4 focus-visible:outline-primary-11 border;
}

.btn-neutral-outline {
    @apply border-neutral-7 text-neutral-12 hover:bg-neutral-4 focus-visible:outline-neutral-11 border;
}

.btn-icon {
    @apply h-8 rounded-md px-3;
}

/* Success variant */
.btn-success {
    @apply bg-green-600 text-white hover:bg-green-700 focus-visible:outline-green-600;
}

/* Warning variant */
.btn-warning {
    @apply bg-yellow-500 text-white hover:bg-yellow-600 focus-visible:outline-yellow-500;
}

/* Info variant */
.btn-info {
    @apply bg-blue-600 text-white hover:bg-blue-700 focus-visible:outline-blue-600;
}

/* Outline variants */
.btn-primary-outline {
    @apply border-primary-7 text-primary-11 hover:bg-primary-4 focus-visible:outline-primary-11 border;
}

.btn-neutral-outline {
    @apply border-neutral-7 text-neutral-12 hover:bg-neutral-4 focus-visible:outline-neutral-11 border;
}

.btn-success-outline {
    @apply border border-green-600 text-green-600 hover:bg-green-50 focus-visible:outline-green-600;
}

.btn-warning-outline {
    @apply border border-yellow-500 text-yellow-600 hover:bg-yellow-50 focus-visible:outline-yellow-500;
}

.btn-danger-outline {
    @apply border border-red-600 text-red-600 hover:bg-red-50 focus-visible:outline-red-600;
}

.btn-info-outline {
    @apply border border-blue-600 text-blue-600 hover:bg-blue-50 focus-visible:outline-blue-600;
}

/* Disabled state */
.btn-disabled {
    @apply cursor-not-allowed opacity-50 hover:cursor-not-allowed;
}
</style>
