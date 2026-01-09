<template>
    <Head title="Agent Panel"></Head>

    <AppSideBar
        ref="sidebarRef"
        :backdrop="isMobile"
        :body-scrolling="!isMobile"
        @sidebar:toggle="sidebarToggle"
    >
        <AppMenu :items="items" />
    </AppSideBar>

    <main
        class="flex flex-1 flex-col pb-9 transition-[padding-top] duration-300"
        :class="[
            { 'md:pl-64': isSideBarOpen }
        ]"
    >
        <AppFlashMessage />
        <AppTopBar
            :class="{ 'ml-64': isSideBarOpen }"
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
import { ref, onMounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import useIsMobile from '@/Composables/useIsMobile'
import agentMenu from '@/Configs/agentMenu'

const page = usePage()

const isSideBarOpen = ref(true)
const sidebarRef = ref()

const { isMobile } = useIsMobile()

onMounted(() => {
    if (isMobile.value) {
        sidebarToggle()
    }
})

const sidebarToggle = () => {
    isSideBarOpen.value = !isSideBarOpen.value
    sidebarRef.value.toggle()
}

const items = agentMenu.items
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
