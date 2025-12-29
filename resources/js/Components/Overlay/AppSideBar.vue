<template>
    <div :class="baseClasses" :aria-hidden="!isVisible.toString()">
        <!-- Sidebar Container -->
        <div
            class="fixed z-40 flex h-screen w-[260px] flex-col overflow-hidden border-r border-neutral-6/20 bg-gradient-to-b from-slate-50 via-white to-slate-50/80 dark:border-neutral-7/20 dark:from-neutral-2 dark:via-neutral-2 dark:to-neutral-1"
        >
            <!-- Decorative gradient orb -->
            <div
                class="pointer-events-none absolute -right-20 -top-20 h-40 w-40 rounded-full bg-primary-9/10 blur-3xl dark:bg-primary-9/5"
            ></div>
            <div
                class="pointer-events-none absolute -left-10 top-1/3 h-32 w-32 rounded-full bg-primary-7/10 blur-3xl dark:bg-primary-7/5"
            ></div>

            <!-- Logo / Brand Header -->
            <div
                class="relative flex h-16 shrink-0 items-center gap-3 px-5"
            >
                <div class="relative">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-9 via-primary-10 to-primary-11 shadow-lg shadow-primary-9/25 ring-1 ring-white/20"
                    >
                        <svg
                            class="h-[18px] w-[18px] text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-base font-bold tracking-tight text-neutral-12">
                        Admin Panel
                    </h1>
                    <p class="text-[10px] font-medium text-neutral-9 dark:text-neutral-8">
                        Management System
                    </p>
                </div>
            </div>

            <!-- Divider -->
            <div class="mx-4 h-px bg-gradient-to-r from-transparent via-neutral-6/40 to-transparent dark:via-neutral-6/20"></div>

            <!-- Scrollable Menu Area -->
            <aside
                aria-label="Sidebar"
                class="sidebar-scroll relative flex-1 overflow-y-auto overflow-x-hidden"
            >
                <div class="px-3 py-4">
                    <slot></slot>
                </div>
            </aside>
            
        </div>

        <!-- Backdrop overlay for mobile -->
        <transition name="fade">
            <div
                v-if="backdrop && isVisible"
                class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm"
                @click="$emit('sidebar:toggle')"
            ></div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import useIsMobile from '@/Composables/useIsMobile'

const props = defineProps({
    placement: {
        type: String,
        default: 'left'
    },
    bodyScrolling: {
        type: Boolean,
        default: false
    },
    backdrop: {
        type: Boolean,
        default: false
    },
    startsVisible: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['sidebar:toggle'])

const isVisible = ref(true)

// User info from page props
const page = usePage()
const userName = computed(
    () => page.props.authUser?.name || page.props.auth?.user?.name || 'User'
)
const userRole = computed(() => {
    const roles = page.props.authUser?.roles || page.props.auth?.user?.roles
    if (roles && roles.length > 0) {
        return roles[0].name || 'Member'
    }
    return 'Administrator'
})
const userInitials = computed(() => {
    const name = userName.value
    if (!name) return 'U'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.substring(0, 2).toUpperCase()
})

const logout = () => {
    router.post(route('adminAuth.logout'))
}

onMounted(() => {
    document.addEventListener('inertia:start', hideMenuOnNavigation)

    if (!props.startsVisible) {
        isVisible.value = false
    }
})

onUnmounted(() => {
    document.removeEventListener('inertia:start', hideMenuOnNavigation)
})

const { isMobile } = useIsMobile()
const hideMenuOnNavigation = () => {
    if (isMobile.value && isVisible.value) {
        window.setTimeout(() => {
            emit('sidebar:toggle')
        }, 200)
    }
}

const baseClasses = computed(() => {
    const base = [
        'fixed',
        'transition-transform',
        'h-screen',
        'w-[260px]',
        'overflow-y-auto',
        'transition-all',
        'ease-out',
        'duration-300',
        'z-30'
    ]

    switch (props.placement) {
        case 'right':
            return [
                ...base,
                'top-0',
                'right-0',
                isVisible.value ? 'transform-none' : 'translate-x-full'
            ]
        case 'left':
        default:
            return [
                ...base,
                'top-0',
                'left-0',
                isVisible.value ? 'transform-none' : '-translate-x-full'
            ]
    }
})

const show = () => {
    isVisible.value = true
    if (!props.bodyScrolling) {
        document.body.style.overflow = 'hidden'
    }
}

const hide = () => {
    isVisible.value = false
    if (!props.bodyScrolling) {
        document.body.style.overflow = ''
    }
}

const toggle = () => {
    isVisible.value ? hide() : show()
}

defineExpose({
    toggle
})
</script>

<style scoped>
@reference "../../../css/app.css";

.fade-enter-active,
.fade-leave-active {
    transition-property: opacity;
    transition-duration: 200ms;
    transition-timing-function: ease-out;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Custom scrollbar styles */
.sidebar-scroll::-webkit-scrollbar {
    width: 3px;
}
.sidebar-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.sidebar-scroll::-webkit-scrollbar-thumb {
    background-color: var(--color-neutral-5);
    border-radius: 10px;
}
.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background-color: var(--color-neutral-7);
}
</style>
