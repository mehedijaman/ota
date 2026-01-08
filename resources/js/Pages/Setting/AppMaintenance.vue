<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <div class="grid grid-cols-6 gap-4">
        <a href="/log-viewer/" target="_blank">
            <div
                class="flex flex-col items-center gap-1 rounded-md border border-skin-neutral-8 bg-skin-neutral-4 px-4 py-2 transition duration-300 ease-in-out hover:bg-skin-neutral-6 hover:text-skin-neutral-12"
            >
                <i class="ri-file-list-line text-xl"></i>
                Log Viewer
            </div>
        </a>

        <AppButton
            class="flex flex-col items-center gap-1 rounded-md border border-skin-neutral-8 bg-skin-neutral-4 px-4 py-2 transition duration-300 ease-in-out hover:bg-skin-neutral-6 hover:text-skin-neutral-12"
            @click="$inertia.visit(route('backup.index'))"
        >
            <i class="ri-download-2-line text-xl"></i>
            Backup
        </AppButton>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('App Maintendance')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: title, last: true }
]
</script>
