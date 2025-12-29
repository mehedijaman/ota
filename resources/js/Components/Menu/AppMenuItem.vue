<template>
    <li class="block">
        <!-- Navigation Link -->
        <Link
            v-if="menuItem.label && menuItem.link && can(menuItem.permission)"
            :href="menuItem.link"
            class="group flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] font-medium transition-all duration-150"
            :class="[
                isActive
                    ? 'bg-primary-9 text-white'
                    : 'text-neutral-11 hover:bg-neutral-3/70 dark:text-neutral-10 dark:hover:bg-neutral-4/50'
            ]"
        >
            <span
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-all duration-150"
                :class="[
                    isActive
                        ? 'bg-white/20 text-white'
                        : 'bg-neutral-4/50 text-neutral-9 group-hover:bg-neutral-4 group-hover:text-neutral-11 dark:bg-neutral-5/40 dark:text-neutral-8 dark:group-hover:bg-neutral-5'
                ]"
            >
                <i :class="menuItem.icon" class="text-sm"></i>
            </span>
            <span class="font-medium">{{ __(menuItem.label) }}</span>
        </Link>

        <!-- Children slot -->
        <slot />
    </li>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

const props = defineProps({
    menuItem: {
        type: Object,
        default: () => ({})
    }
})

const { can } = useAuthCan()

const page = usePage()
const isActive = computed(() => {
    if (!props.menuItem.link) return false
    const currentUrl = page.url
    return (
        currentUrl === props.menuItem.link ||
        currentUrl.startsWith(props.menuItem.link + '/')
    )
})
</script>
