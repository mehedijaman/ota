<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="badgeClasses"
    >
        <slot></slot>
    </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: {
        type: String,
        default: 'secondary'
    },
    size: {
        type: String,
        default: 'md' // sm, md, lg
    }
})

const badgeClasses = computed(() => {
    const baseClasses = 'inline-flex items-center rounded-full font-medium'

    const sizeClasses = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-0.5 text-xs',
        lg: 'px-3 py-1 text-sm'
    }

    const typeClasses = {
        primary: 'bg-blue-100 text-blue-800',
        secondary: 'bg-gray-100 text-gray-800',
        success: 'bg-green-100 text-green-800',
        danger: 'bg-red-100 text-red-800',
        warning: 'bg-yellow-100 text-yellow-800',
        info: 'bg-blue-100 text-blue-800',
        light: 'bg-gray-100 text-gray-800',
        dark: 'bg-gray-800 text-gray-100'
    }

    // Override specific badge types
    const specificTypeClasses = {
        asset: 'bg-green-100 text-green-800',
        liability: 'bg-yellow-100 text-yellow-800',
        equity: 'bg-blue-100 text-blue-800',
        revenue: 'bg-green-100 text-green-800',
        expense: 'bg-red-100 text-red-800',
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        draft: 'bg-gray-100 text-gray-800',
        posted: 'bg-green-100 text-green-800',
        void: 'bg-red-100 text-red-800',
        paid: 'bg-green-100 text-green-800',
        unpaid: 'bg-yellow-100 text-yellow-800',
        overdue: 'bg-red-100 text-red-800',
        sent: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
        reconciled: 'bg-green-100 text-green-800',
        unreconciled: 'bg-yellow-100 text-yellow-800'
    }

    return [
        baseClasses,
        sizeClasses[props.size] || sizeClasses.md,
        specificTypeClasses[props.type] ||
            typeClasses[props.type] ||
            typeClasses.secondary
    ].join(' ')
})
</script>

<style scoped>
/* Additional styling if needed */
</style>
