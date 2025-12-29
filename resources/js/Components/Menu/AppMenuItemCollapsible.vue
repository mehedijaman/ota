<template>
    <div>
        <!-- Item with children (collapsible submenu) -->
        <template v-if="menuItem.children && menuItem.children.length > 0">
            <button
                v-if="can(menuItem.permission)"
                type="button"
                class="group flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-[13px] font-medium transition-all duration-150"
                :class="[
                    hasActiveChild
                        ? 'bg-primary-3 text-primary-11 dark:bg-primary-4/40 dark:text-primary-11'
                        : 'text-neutral-11 hover:bg-neutral-3/70 dark:text-neutral-10 dark:hover:bg-neutral-4/50'
                ]"
                @click="toggleSubmenu"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-all duration-150"
                        :class="[
                            hasActiveChild
                                ? 'bg-primary-9 text-white'
                                : 'bg-neutral-4/50 text-neutral-9 group-hover:bg-neutral-4 group-hover:text-neutral-11 dark:bg-neutral-5/40 dark:text-neutral-8 dark:group-hover:bg-neutral-5'
                        ]"
                    >
                        <i :class="menuItem.icon" class="text-sm"></i>
                    </span>
                    <span class="font-medium">{{ __(menuItem.label) }}</span>
                </div>
                <svg
                    class="h-4 w-4 shrink-0 transition-transform duration-200"
                    :class="[
                        isSubmenuExpanded ? 'rotate-0' : '-rotate-90',
                        hasActiveChild ? 'text-primary-9' : 'text-neutral-7 dark:text-neutral-6'
                    ]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </button>

            <!-- Submenu items -->
            <transition
                enter-active-class="transition-all duration-150 ease-out"
                leave-active-class="transition-all duration-100 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="isSubmenuExpanded"
                    class="relative ml-5 mt-1 space-y-0.5 border-l border-neutral-5/50 pl-3 dark:border-neutral-6/30"
                >
                    <template
                        v-for="(childItem, childIndex) in menuItem.children"
                        :key="childIndex"
                    >
                        <AppMenuItemCollapsible
                            :menu-item="childItem"
                            :level="level + 1"
                        />
                    </template>
                </div>
            </transition>
        </template>

        <!-- Link item (no children) -->
        <Link
            v-else-if="menuItem.label && menuItem.link && can(menuItem.permission)"
            :href="menuItem.link"
            class="group flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] font-medium transition-all duration-150"
            :class="[
                isActive
                    ? 'bg-primary-9 text-white'
                    : 'text-neutral-11 hover:bg-neutral-3/70 dark:text-neutral-10 dark:hover:bg-neutral-4/50'
            ]"
        >
            <span
                v-if="level === 1"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-all duration-150"
                :class="[
                    isActive
                        ? 'bg-white/20 text-white'
                        : 'bg-neutral-4/50 text-neutral-9 group-hover:bg-neutral-4 group-hover:text-neutral-11 dark:bg-neutral-5/40 dark:text-neutral-8 dark:group-hover:bg-neutral-5'
                ]"
            >
                <i :class="menuItem.icon" class="text-sm"></i>
            </span>
            <span
                v-else
                class="flex h-5 w-5 shrink-0 items-center justify-center"
            >
                <span
                    class="h-1.5 w-1.5 rounded-full transition-all duration-150"
                    :class="[
                        isActive
                            ? 'bg-white'
                            : 'bg-neutral-6 group-hover:bg-neutral-8 dark:bg-neutral-5 dark:group-hover:bg-neutral-7'
                    ]"
                ></span>
            </span>
            <span class="font-medium">{{ __(menuItem.label) }}</span>
        </Link>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    menuItem: {
        type: Object,
        default: () => ({})
    },
    level: {
        type: Number,
        default: 1
    }
})

const { can } = useAuthCan()
const page = usePage()

const storageKey = computed(() => {
    const label = props.menuItem.label || 'item'
    return `sidebar_submenu_${label.toLowerCase().replace(/\s+/g, '_')}`
})

const isActive = computed(() => {
    if (!props.menuItem.link) return false
    const currentUrl = page.url
    return (
        currentUrl === props.menuItem.link ||
        currentUrl.startsWith(props.menuItem.link + '/')
    )
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
    return checkActive(props.menuItem.children)
})

const getInitialState = () => {
    if (typeof window === 'undefined') return false
    const saved = localStorage.getItem(storageKey.value)
    if (saved !== null) {
        return saved === 'true'
    }
    return hasActiveChild.value
}

const isSubmenuExpanded = ref(false)

onMounted(() => {
    isSubmenuExpanded.value = getInitialState()
    if (hasActiveChild.value) {
        isSubmenuExpanded.value = true
    }
})

watch(isSubmenuExpanded, (newValue) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem(storageKey.value, String(newValue))
    }
})

const toggleSubmenu = () => {
    isSubmenuExpanded.value = !isSubmenuExpanded.value
}
</script>
