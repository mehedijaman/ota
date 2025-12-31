<template>
    <div :class="baseClasses" :aria-hidden="!isVisible.toString()">
        <!-- Sidebar Content -->
        <div class="sidebar-container fixed z-40 h-screen w-64 overflow-hidden">
            <!-- Dark navy background -->
            <div class="absolute inset-0 sidebar-bg"></div>
            
            <aside aria-label="Sidebar" class="relative flex flex-col h-full">
                <!-- Logo Section -->
                <div class="px-4 py-5">
                    <Link :href="route('dashboard.index')" class="flex items-center gap-3 group">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/25 transition-transform duration-300 group-hover:scale-105">
                            <i class="ri-hotel-line text-white text-lg"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-base font-bold text-white tracking-tight">OTA</span>
                            <p class="text-[10px] text-slate-400 font-medium">Online Travel Agency</p>
                        </div>
                    </Link>
                </div>

                <!-- Divider -->
                <div class="mx-4 h-px bg-slate-700/50 mb-3"></div>

                <!-- Scrollable Menu -->
                <div class="flex-1 overflow-y-auto px-3 py-2 custom-scrollbar">
                    <slot></slot>
                </div>
            </aside>
        </div>

        <transition name="fade">
            <div
                v-if="backdrop && isVisible"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                @click="$emit('sidebar:toggle')"
            ></div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
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
const page = usePage()

const isVisible = ref(true)

// User info computed properties
const userName = computed(() => {
    const user = page.props.auth?.user
    return user?.profile?.name || user?.name || user?.email?.split('@')[0] || 'User'
})

const userRole = computed(() => {
    if (page.props.auth?.isSuperAdmin) return 'Super Admin'
    if (page.props.auth?.isTenant) return 'Business Owner'
    return 'Staff'
})

const userInitial = computed(() => userName.value.charAt(0).toUpperCase())

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
        'w-64',
        'overflow-y-auto',
        'transition-all',
        'ease-out',
        'duration-300',
        'z-30',
        'print:hidden'
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
    toggle,
    show,
    hide
})
</script>

<style scoped>
/* @reference "tailwindcss/theme"; */
@reference "../../../css/app.css";

/* Dark navy sidebar background */
.sidebar-bg {
    background: linear-gradient(180deg, #0d1526 0%, #0a1120 100%);
}

.fade-enter-active,
.fade-leave-active {
    transition-property: opacity;
    transition-duration: 300ms;
    transition-timing-function: ease-in;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Dark scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.2);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.3);
}
</style>
