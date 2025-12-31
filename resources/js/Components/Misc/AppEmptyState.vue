<template>
    <div class="rounded-2xl border border-neutral-6 bg-neutral-1 shadow-sm overflow-hidden">
        <div class="flex flex-col items-center justify-center py-16 px-8">
            <!-- Illustration -->
            <div class="relative mb-8">
                <!-- Background rings -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-36 w-36 rounded-full border border-dashed border-neutral-6"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-28 w-28 rounded-full bg-primary-3/50"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-20 w-20 rounded-full bg-primary-4/60"></div>
                </div>
                
                <!-- Icon container -->
                <div class="relative flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-primary-8 to-primary-10 shadow-lg shadow-primary-9/25 ring-4 ring-neutral-1">
                    <i :class="`${icon} text-2xl text-white`"></i>
                </div>
            </div>
            
            <!-- Content -->
            <div class="text-center max-w-sm">
                <h3 class="text-lg font-semibold text-neutral-12">
                    {{ title }}
                </h3>
                <p class="mt-2 text-sm text-neutral-10 leading-relaxed">
                    {{ description }}
                </p>
            </div>

            <!-- Actions -->
            <div v-if="$slots.actions" class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <slot name="actions" />
            </div>
            
            <!-- Help text -->
            <p v-if="helpText" class="mt-6 text-xs text-neutral-9 flex items-center gap-1.5">
                <i class="ri-information-line text-primary-9"></i>
                {{ helpText }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    icon: {
        type: String,
        default: 'ri-inbox-2-line'
    },
    title: {
        type: String,
        default: () => 'No Data Available'
    },
    description: {
        type: String,
        default: () => 'There are no items to display. Create your first entry to get started.'
    },
    helpText: {
        type: String,
        default: ''
    }
})
</script>
