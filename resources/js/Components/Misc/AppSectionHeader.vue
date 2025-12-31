<template>
    <header class="my-4 pb-4 border-b border-slate-200 dark:border-slate-700 print:hidden">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Left: Title & Breadcrumb -->
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-3">
                    <!-- Optional Icon Slot -->
                    <div v-if="$slots.icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-4 text-primary-11">
                        <slot name="icon" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-neutral-12 truncate sm:text-2xl">
                            {{ title }}
                        </h1>
                        <AppBreadCrumb v-if="breadCrumb.length" :items="breadCrumb" class="mt-0.5" />
                    </div>
                </div>
            </div>
            
            <!-- Right: Actions -->
            <div class="flex flex-wrap items-center gap-2 sm:flex-nowrap sm:gap-3">
                <slot name="right" />
            </div>
        </div>
    </header>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: '-'
    },
    breadCrumb: {
        type: Array,
        default: () => []
    }
})
</script>
