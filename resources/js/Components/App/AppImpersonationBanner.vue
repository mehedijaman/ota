<template>
    <Transition name="slide-down">
        <div
            v-if="impersonation.isImpersonating"
            class="fixed top-0 right-0 z-40 flex items-center justify-center gap-4 bg-linear-to-r from-amber-500 via-orange-500 to-amber-500 px-4 py-2 text-white shadow-lg transition-all duration-300"
            :class="sidebarOpen ? 'left-64' : 'left-0'"
        >
            <div class="flex items-center gap-2">
                <i class="ri-user-shared-line text-lg"></i>
                <span class="text-sm font-medium">
                    You are impersonating as <strong>{{ auth.user?.name }}</strong>
                </span>
                <span v-if="impersonation.impersonatorName" class="text-sm opacity-90">
                    (Original: {{ impersonation.impersonatorName }})
                </span>
            </div>
            <button
                @click="leaveImpersonation"
                :disabled="leaving"
                class="flex items-center gap-1 rounded-lg bg-white/20 px-3 py-1 text-sm font-medium transition-all hover:bg-white/30 disabled:opacity-50"
            >
                <i v-if="leaving" class="ri-loader-4-line animate-spin"></i>
                <i v-else class="ri-logout-box-r-line"></i>
                <span>Exit Impersonation</span>
            </button>
        </div>
    </Transition>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

defineProps({
    sidebarOpen: {
        type: Boolean,
        default: true
    }
})

const page = usePage()
const auth = page.props.auth
const impersonation = page.props.impersonation || { isImpersonating: false }

const leaving = ref(false)

const leaveImpersonation = () => {
    leaving.value = true
    // Use window.location for full page reload to properly refresh auth state
    window.location.href = route('impersonate.leave')
}
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
    transform: translateY(-100%);
    opacity: 0;
}
</style>
