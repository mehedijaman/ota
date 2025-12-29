<template>
    <!-- Search Input -->
    <div v-if="showInputSearch" class="mb-4">
        <div class="relative">
            <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
            >
                <svg
                    class="h-[14px] w-[14px] text-neutral-7 dark:text-neutral-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
            </div>
            <input
                id="sidebar-search"
                v-model="searchTerm"
                type="text"
                :placeholder="__('Search...')"
                class="w-full rounded-lg bg-white/60 py-2 pl-8 pr-8 text-[13px] text-neutral-12 placeholder-neutral-7 shadow-sm ring-1 ring-neutral-6/30 transition-all duration-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-9/40 dark:bg-neutral-3/40 dark:placeholder-neutral-6 dark:ring-neutral-6/20 dark:focus:bg-neutral-3/60"
            />
            <button
                v-if="searchTerm"
                type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-neutral-7 transition-colors hover:text-neutral-10 dark:text-neutral-6 dark:hover:text-neutral-9"
                @click="searchTerm = ''"
            >
                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>
    </div>

    <!-- Menu Sections -->
    <nav class="space-y-0.5">
        <ul v-for="(item, index) in filteredItems" :key="index">
            <AppMenuSection :item="item" />
        </ul>
    </nav>

    <!-- Empty state -->
    <div
        v-if="searchTerm && filteredItems.length === 0"
        class="flex flex-col items-center py-8"
    >
        <div
            class="mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-neutral-3/80 dark:bg-neutral-4/60"
        >
            <svg
                class="h-5 w-5 text-neutral-6 dark:text-neutral-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
            </svg>
        </div>
        <p class="text-xs text-neutral-8 dark:text-neutral-7">
            {{ __('No results') }}
        </p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
})

const showInputSearch = true

const searchTerm = ref('')

const filteredItems = computed(() => {
    if (!showInputSearch || !searchTerm.value) {
        return props.items
    }

    const term = searchTerm.value.toLowerCase()

    const filterItems = (items) => {
        return items.reduce((acc, item) => {
            const isParentMatch = item.label.toLowerCase().includes(term)
            let filteredChildren = []

            if (item.children) {
                if (isParentMatch) {
                    filteredChildren = item.children
                } else {
                    filteredChildren = filterItems(item.children)
                }
            }

            if (
                isParentMatch ||
                (filteredChildren && filteredChildren.length)
            ) {
                acc.push({
                    ...item,
                    children: isParentMatch ? item.children : filteredChildren
                })
            }

            return acc
        }, [])
    }

    return filterItems(props.items)
})
</script>
