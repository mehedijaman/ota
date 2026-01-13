<template>
    <header
        class="no-print sticky top-0 z-40 flex h-16 shrink-0 items-center justify-between border-b border-neutral-4 bg-neutral-1/95 px-4 backdrop-blur-sm"
    >
        <!-- Left Section -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Menu Toggle -->
            <AppButton
                type="button"
                @click="$emit('sidebar:toggle')"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-neutral-11 transition-colors hover:bg-neutral-3 hover:text-neutral-12"
            >
                <i class="ri-menu-line "></i>
            </AppButton>

            <!-- Divider -->
            <div class="hidden h-6 w-px bg-neutral-4 sm:block"></div>

            <!-- + New Button -->
            <div class="relative">
                <AppButton
                    type="button"
                    @click="toggleMenu"
                    class="flex h-9 items-center gap-1.5 rounded-lg border border-neutral-5 bg-neutral-2 px-3 text-sm font-medium text-neutral-11 transition-all hover:border-neutral-6 hover:bg-neutral-3 hover:text-neutral-12"
                >
                    <i class="ri-add-line"></i>
                    <span class="hidden sm:inline">{{ __('New') }}</span>
                </AppButton>
                <NewEntityMenu :open="showNewMenu" />
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-1 sm:gap-2">
            <!-- Desktop Actions -->
            <div class="hidden items-center gap-1.5 md:flex">

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
                        class=" transition-transform duration-300"
                        :style="{ transform: isFullscreen ? 'rotate(180deg)' : 'rotate(0deg)' }"
                    ></i>
                </AppButton>

                <!-- Calculator -->
                <AppButton
                    type="button"
                    @click="showCalculator = true"
                    class="btn btn-icon hover:bg-neutral-4"
                    :title="__('Calculator')"
                >
                    <i class="ri-calculator-line "></i>
                </AppButton>

                <!-- Theme Toggle -->
                <AppButton
                    type="button"
                    @click="toggleTheme"
                    class="btn btn-icon hover:bg-neutral-4"
                    :title="__('Toggle Theme')"
                >
                    <i :class="iconThemeClass" class=""></i>
                </AppButton>
            </div>

            <!-- Mobile More Menu -->
            <div class="md:hidden">
                <AppDropdown placement="bottom-end">
                    <template #trigger>
                        <AppButton
                            type="button"
                            class="btn btn-icon hover:bg-neutral-4"
                        >
                            <i class="ri-more-2-fill "></i>
                        </AppButton>
                    </template>
                    
                    <div class="py-1">
                        <AppDropdownItem @click="showCalculator = true">
                            <i class="ri-calculator-line mr-2 text-neutral-9"></i>
                            {{ __('Calculator') }}
                        </AppDropdownItem>
                        <AppDropdownItem @click="toggleTheme">
                            <i :class="iconThemeClass" class="mr-2 text-neutral-9"></i>
                            {{ __('Theme') }}
                        </AppDropdownItem>
                        <AppDropdownItem @click="toggleFullscreen">
                            <i :class="iconFullscreenClass" class="mr-2 text-neutral-9"></i>
                            {{ __('Fullscreen') }}
                        </AppDropdownItem>
                    </div>
                </AppDropdown>
            </div>

            <!-- Divider -->
            <div class="hidden h-6 w-px bg-neutral-4 sm:block"></div>

            <!-- User Menu -->
            <AppDropdown placement="bottom-end">
                <template #trigger>
                    <AppButton
                        type="button"
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-neutral-3"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-4 text-sm font-bold text-primary-11 ring-2 ring-neutral-3">
                            <img
                                v-if="$page.props.auth.user?.avatar_url"
                                :src="$page.props.auth.user.avatar_url"
                                :alt="$page.props.auth.user.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>{{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
                        </div>
                        <i class="ri-arrow-down-s-line text-neutral-9 hidden sm:block"></i>
                    </AppButton>
                </template>

                <!-- User Profile Header -->
                <div class="border-b border-neutral-5 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-primary-4  font-bold text-primary-11">
                            <img
                                v-if="$page.props.auth.user?.avatar_url"
                                :src="$page.props.auth.user.avatar_url"
                                :alt="$page.props.auth.user.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>{{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-neutral-12 truncate">{{ $page.props.auth.user?.name }}</p>
                            <p class="text-xs text-neutral-10 truncate">{{ $page.props.auth.user?.email }}</p>
                            <p class="text-xs text-emerald-700">{{ __('Online') }}</p>
                        </div>
                    </div>
                </div>

                <div class="py-1">
                    <AppDropdownItem @click="$inertia.visit(route('profile.edit'))">
                        <i class="ri-user-settings-line mr-2 text-neutral-9"></i>
                        {{ __('Edit Profile') }}
                    </AppDropdownItem>

                    <div class="border-t border-neutral-4 my-1"></div>

                    <AppDropdownItem 
                        class="text-danger hover:bg-danger-light" 
                        @click="$inertia.visit(route('adminAuth.logout'))"
                    >
                        <i class="ri-logout-box-r-line mr-2"></i>
                        {{ __('Logout') }}
                    </AppDropdownItem>
                </div>
            </AppDropdown>
        </div>

        <!-- Calculator Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showCalculator"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                    @click.self="showCalculator = false"
                >
                    <div class="relative animate-in zoom-in-95 duration-200">
                        <AppButton
                            type="button"
                            @click="showCalculator = false"
                            class="absolute -top-3 -right-3 flex h-8 w-8 items-center justify-center rounded-full bg-neutral-1 text-neutral-11 shadow-lg ring-1 ring-neutral-5 transition-colors hover:bg-neutral-2"
                        >
                            <i class="ri-close-line"></i>
                        </AppButton>
                        <AppCalculator />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'
import NewEntityMenu from '@/Components/App/NewEntityMenu.vue'
import AppCalculator from '@/Components/Calculator/AppCalculator.vue'
import LanguageSelector from '@/Components/Misc/LanguageSelector.vue'

const __ = window.__ || ((key) => key)

defineEmits(['sidebar:toggle'])

const showNewMenu = ref(false)
const showCalculator = ref(false)

const toggleMenu = () => {
    showNewMenu.value = !showNewMenu.value
}

// Close menu when clicking outside
const onClickOutside = (e) => {
    if (!e.target.closest('.relative')) {
        showNewMenu.value = false
    }
}
onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))

// Theme toggle
const iconThemeClass = ref('ri-sun-line')
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

// Close calculator with Escape key
onMounted(() => {
    const handleEscape = (e) => {
        if (e.key === 'Escape' && showCalculator.value) {
            showCalculator.value = false
        }
    }
    document.addEventListener('keydown', handleEscape)
    return () => {
        document.removeEventListener('keydown', handleEscape)
    }
})

const openSite = () => {
    window.open(route('site.index'), '_blank')
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.2s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
