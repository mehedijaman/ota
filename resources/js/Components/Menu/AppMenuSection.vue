<template>
    <!-- Section with collapsible children -->
    <template v-if="item.children && item.children.length > 0">
        <!-- Section Header -->
        <div
            v-if="item.label && !item.link"
            class="mb-2 mt-6 first:mt-0"
        >
            <button
                type="button"
                class="group flex w-full items-center justify-between px-3 py-1"
                @click="toggleSection"
            >
                <span
                    class="text-[11px] font-semibold uppercase tracking-widest text-neutral-9/80 transition-colors group-hover:text-neutral-10 dark:text-neutral-7 dark:group-hover:text-neutral-6"
                >
                    {{ __(item.label) }}
                </span>
                <div
                    class="flex h-5 w-5 items-center justify-center rounded transition-colors"
                    :class="isExpanded ? 'text-neutral-9 dark:text-neutral-6' : 'text-neutral-7 dark:text-neutral-5'"
                >
                    <svg
                        class="h-3 w-3 transition-transform duration-200"
                        :class="{ '-rotate-90': !isExpanded }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </div>
            </button>
        </div>

        <!-- Collapsible children -->
        <transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="isExpanded" class="space-y-0.5">
                <template
                    v-for="(level1Item, level1Index) in item.children"
                    :key="level1Index"
                >
                    <AppMenuItemCollapsible
                        :menu-item="level1Item"
                        :level="1"
                    />
                </template>
            </div>
        </transition>
    </template>

    <!-- Single item without children -->
    <app-menu-item
        v-else-if="!item.children"
        :menu-item="item"
    />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppMenuItemCollapsible from './AppMenuItemCollapsible.vue'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const page = usePage()

const storageKey = computed(() => {
    const label = props.item.label || 'section'
    return `sidebar_section_${label.toLowerCase().replace(/\s+/g, '_')}`
})

const hasActiveChild = computed(() => {
    const currentUrl = page.url
    const checkActive = (items) => {
        if (!items) return false
        return items.some((item) => {
            if (item.link && (currentUrl === item.link || currentUrl.startsWith(item.link + '/'))) {
                return true
            }
            if (item.children) {
                return checkActive(item.children)
            }
            return false
        })
    }
    return checkActive(props.item.children)
})

const getInitialState = () => {
    if (typeof window === 'undefined') return false
    const saved = localStorage.getItem(storageKey.value)
    if (saved !== null) {
        return saved === 'true'
    }
    return hasActiveChild.value
}

const isExpanded = ref(false)

onMounted(() => {
    isExpanded.value = getInitialState()
    if (hasActiveChild.value) {
        isExpanded.value = true
    }
})

watch(isExpanded, (newValue) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem(storageKey.value, String(newValue))
    }
})

const toggleSection = () => {
    isExpanded.value = !isExpanded.value
}
</script>
