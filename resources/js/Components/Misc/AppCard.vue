<template>
    <div 
        class="bg-neutral-1 rounded-xl border border-neutral-5 shadow-sm transition-shadow hover:shadow-md"
        :class="[compact ? 'mt-2' : 'mt-4']"
    >
        <!-- Header -->
        <div 
            v-if="$slots.title || $slots.description || $slots.actions"
            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-neutral-5 bg-neutral-2 px-4 py-3 sm:px-5"
        >
            <div class="min-w-0 flex-1">
                <h3 
                    v-if="$slots.title" 
                    class="flex items-center gap-2 text-base font-semibold text-neutral-12 sm:text-lg"
                >
                    <slot name="icon" />
                    <slot name="title" />
                </h3>
                <p v-if="$slots.description" class="mt-0.5 text-sm text-neutral-10">
                    <slot name="description" />
                </p>
            </div>
            <div v-if="$slots.actions" class="flex items-center gap-2 shrink-0">
                <slot name="actions" />
            </div>
        </div>

        <!-- Content -->
        <div 
            class="text-neutral-12"
            :class="[noPadding ? '' : 'p-4 sm:p-5']"
        >
            <slot name="content" />
        </div>

        <!-- Footer -->
        <div 
            v-if="$slots.footer" 
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end border-t border-neutral-5 bg-neutral-2/50 px-4 py-3 sm:px-5"
        >
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    compact: {
        type: Boolean,
        default: false
    },
    noPadding: {
        type: Boolean,
        default: false
    }
})
</script>
