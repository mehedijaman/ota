<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb" />

    <div class="mx-auto max-w-6xl px-4 py-6">
        <!-- Tabs Navigation -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        activeTab === tab.id
                            ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                        'flex items-center gap-2 border-b-2 px-1 py-4 text-sm font-medium transition-colors'
                    ]"
                >
                    <i :class="tab.icon"></i>
                    {{ tab.label }}
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="mt-6">
            <SiteIdentitySetting
                v-show="activeTab === 'identity'"
                :settings="settings"
            />
            <ContactSocialSetting
                v-show="activeTab === 'contact'"
                :settings="settings"
            />
            <ThemeSetting v-show="activeTab === 'theme'" :settings="settings" />
            <AdvancedSetting
                v-show="activeTab === 'advanced'"
                :settings="settings"
            />
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import SiteIdentitySetting from './Components/SiteIdentitySetting.vue'
import ContactSocialSetting from './Components/ContactSocialSetting.vue'
import ThemeSetting from './Components/ThemeSetting.vue'
import AdvancedSetting from './Components/AdvancedSetting.vue'

const { title } = useTitle('Settings')

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Settings', last: true }
]

const tabs = [
    { id: 'identity', label: 'Site Identity', icon: 'ri-global-line' },
    { id: 'contact', label: 'Contact & Social', icon: 'ri-contacts-line' },
    { id: 'theme', label: 'Theme', icon: 'ri-palette-line' },
    { id: 'advanced', label: 'Advanced', icon: 'ri-code-s-slash-line' }
]

const activeTab = ref('identity')

const confirmDialogRef = ref(null)
</script>
