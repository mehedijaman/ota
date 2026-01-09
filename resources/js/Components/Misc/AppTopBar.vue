<template>
    <div
        class="bg-neutral-2 text-neutral-11 sticky top-0 z-40 flex h-16 shrink-0 justify-between py-3 pr-9 pl-3 shadow-xs"
    >
        <div class="flex items-center">
            <AppButton
                class="btn btn-icon hover:bg-neutral-4"
                @click="$emit('sidebar:toggle')"
            >
                <i class="ri-menu-line"></i>
            </AppButton>

            <h1 class="flex items-center">{{ title }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <AppButton
                @click="openSite"
                class="btn btn-icon hover:bg-neutral-4"
            >
                <i class="ri-global-line"></i>
        </AppButton>

            <!-- Fullscreen -->
            <AppButton
                type="button"
                @click="toggleFullscreen"
                class="btn btn-icon hover:bg-neutral-4"
                :title="isFullscreen ? __('Exit Fullscreen') : __('Fullscreen')"
            >
                <i
                    :class="iconFullscreenClass"
                    class="transition-transform duration-300"
                    :style="{ transform: isFullscreen ? 'rotate(180deg)' : 'rotate(0deg)' }"
                ></i>
            </AppButton>

            <AppButton
                href="#"
                class="btn btn-icon hover:bg-neutral-4"
                @click="toggleTheme"
            >
                <i :class="iconThemeClass"></i>
            </AppButton>

            <!-- User Profile Dropdown -->
            <div class="relative" v-click-outside="closeDropdown">
                <button
                    @click="toggleDropdown"
                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-neutral-4 transition-colors"
                >
                    <!-- User Avatar -->
                    <div class="h-8 w-8 rounded-full overflow-hidden bg-primary-9 flex items-center justify-center text-white font-semibold">
                        <img
                            v-if="$page.props.auth.user?.avatar"
                            :src="$page.props.auth.user.avatar"
                            :alt="$page.props.auth.user?.name"
                            class="h-full w-full object-cover"
                        />
                        <span v-else class="text-sm">
                            {{ getInitials($page.props.auth.user?.name) }}
                        </span>
                    </div>
                    <i class="ri-arrow-down-s-line text-sm"></i>
                </button>

                <!-- Dropdown Menu -->
                <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <div
                        v-show="isDropdownOpen"
                        class="absolute right-0 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-neutral-3 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                        <div class="py-1">
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-neutral-6 dark:border-neutral-7">
                                <p class="text-sm font-medium text-neutral-12">{{ $page.props.auth.user?.name }}</p>
                                <p class="text-xs text-neutral-10 truncate">{{ $page.props.auth.user?.email }}</p>
                            </div>

                            <!-- Edit Profile -->
                            <AppLink
                                :href="route('profile.edit')"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-neutral-11 hover:bg-neutral-4 dark:hover:bg-neutral-5 transition-colors"
                                @click="closeDropdown"
                            >
                                <i class="ri-user-settings-line text-base"></i>
                                <span>Edit Profile</span>
                            </AppLink>

                            <!-- Logout -->
                            <button
                                @click="logout"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                                <i class="ri-logout-circle-r-line text-base"></i>
                                <span>Logout</span>
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({
    title: {
        type: String,
        default: ''
    }
})

defineEmits(['sidebar:toggle'])

const iconThemeClass = ref('ri-sun-line')
const isDropdownOpen = ref(false)

onMounted(() => {
    if (localStorage.getItem('modular-theme') === 'dark-theme') {
        document.documentElement.classList.add('dark-theme')
        iconThemeClass.value = 'ri-sun-line'
    } else {
        document.documentElement.classList.remove('dark-theme')
        iconThemeClass.value = 'ri-moon-line'
    }
})

const toggleTheme = () => {
    if (document.documentElement.classList.contains('dark-theme')) {
        document.documentElement.classList.remove('dark-theme')
        iconThemeClass.value = 'ri-moon-line'
        localStorage.removeItem('modular-theme')
    } else {
        localStorage.setItem('modular-theme', 'dark-theme')
        document.documentElement.classList.add('dark-theme')
        iconThemeClass.value = 'ri-sun-line'
    }
}

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = () => {
    isDropdownOpen.value = false
}

const logout = () => {
    router.visit(route('auth.logout'))
}

const getInitials = (name) => {
    if (!name) return 'U'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.substring(0, 2).toUpperCase()
}

// Click outside directive
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value()
            }
        }
        document.addEventListener('click', el.clickOutsideEvent)
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent)
    }
}

/* ========== FULLSCREEN ========== */
const isFullscreen = ref(false)
const iconFullscreenClass = ref('ri-fullscreen-line')

const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        document.documentElement
            .requestFullscreen()
            .then(() => {
                isFullscreen.value = true
            })
            .catch((err) => {
                console.error('Error trying to enable fullscreen:', err)
            })
    } else {
        document
            .exitFullscreen()
            .then(() => {
                isFullscreen.value = false
            })
            .catch((err) => {
                console.error('Error trying to exit fullscreen:', err)
            })
    }
}

const updateFullscreenState = () => {
    isFullscreen.value = !!document.fullscreenElement
    iconFullscreenClass.value = isFullscreen.value
        ? 'ri-fullscreen-exit-line'
        : 'ri-fullscreen-line'
    localStorage.setItem('fullscreen', isFullscreen.value ? '1' : '0')
}

onMounted(() => {
    document.addEventListener('fullscreenchange', updateFullscreenState)
    if (localStorage.getItem('fullscreen') === '1') {
        document.documentElement.requestFullscreen().catch(() => {})
    }
})

onUnmounted(() => {
    document.removeEventListener('fullscreenchange', updateFullscreenState)
})

const openSite = () => {
    window.open(route('site.index'), '_blank')
}
</script>
