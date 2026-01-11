<template>
    <Head title="Modular"></Head>

    <!-- Impersonation Banner -->
    <AppImpersonationBanner :sidebar-open="isSideBarOpen" />

    <AppSideBar
        ref="sidebarRef"
        :backdrop="isMobile"
        :body-scrolling="!isMobile"
        @sidebar:toggle="sidebarToggle"
    >
        <!-- <Link :href="route('dashboard.index')" class="mb-6 flex pl-2">
            <img
                src="@resources/images/logo.svg"
                class="w-40"
                alt="Modular Logo"
            />
        </Link> -->

        <AppMenu :items="items" />
    </AppSideBar>

    <main
        class="flex flex-1 flex-col pb-9 transition-[padding-top] duration-300"
        :class="[
            { 'md:pl-70': isSideBarOpen },
            isImpersonating ? 'pt-12' : 'pt-0'
        ]"
    >
        <AppFlashMessage />
        <AppTopBar
            :class="{ 'ml-70': isSideBarOpen }"
            class="md:ml-0"
            @sidebar:toggle="sidebarToggle"
        />
        <div class="mx-8 2xl:mx-16">
            <transition name="fade" mode="out-in">
                <!-- eslint-disable-next-line vue/require-toggle-inside-transition -->
                <div :key="page.props.ziggy.location">
                    <slot></slot>
                </div>
            </transition>
        </div>
    </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import useIsMobile from '@/Composables/useIsMobile'
import menu from '@/Configs/menu'
import AppImpersonationBanner from '@/Components/App/AppImpersonationBanner.vue'

const page = usePage()

const isSideBarOpen = ref(true)
const sidebarRef = ref()

const { isMobile } = useIsMobile()

const isImpersonating = computed(() => {
    return page.props.impersonation?.isImpersonating ?? false
})

onMounted(() => {
    if (isMobile.value) {
        sidebarToggle()
    }
})

const sidebarToggle = () => {
    isSideBarOpen.value = !isSideBarOpen.value
    sidebarRef.value.toggle()
}

const items = menu.items
</script>

<style scoped>
@reference "tailwindcss/theme";

.fade-enter-active,
.fade-leave-active {
    @apply transition-opacity duration-300 ease-out;
}
.fade-enter-from,
.fade-leave-to {
    @apply opacity-0;
}
</style>
