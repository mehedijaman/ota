<template>
    <AppCard>
        <template #title>
            <div class="flex items-center gap-2">
                <i class="ri-palette-line text-lg"></i>
                Theme Customization
            </div>
        </template>
        <template #description>
            Customize the colors for different sections of your site.
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Navbar Colors -->
                    <div class="rounded-lg border p-4 dark:border-gray-700">
                        <h4
                            class="mb-4 flex items-center gap-2 font-medium text-gray-900 dark:text-white"
                        >
                            <i class="ri-menu-line"></i>
                            Navbar
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <AppLabel for="navbar_background">
                                    Background Color
                                </AppLabel>
                                <div class="flex gap-2">
                                    <input
                                        type="color"
                                        id="navbar_background"
                                        v-model="form.navbar_background"
                                        class="h-10 w-14 cursor-pointer rounded border p-1"
                                    />
                                    <AppInputText
                                        v-model="form.navbar_background"
                                        placeholder="#ffffff"
                                        class="flex-1"
                                    />
                                </div>
                            </div>
                            <div>
                                <AppLabel for="navbar_text_color">
                                    Text Color
                                </AppLabel>
                                <div class="flex gap-2">
                                    <input
                                        type="color"
                                        id="navbar_text_color"
                                        v-model="form.navbar_text_color"
                                        class="h-10 w-14 cursor-pointer rounded border p-1"
                                    />
                                    <AppInputText
                                        v-model="form.navbar_text_color"
                                        placeholder="#1e293b"
                                        class="flex-1"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- Preview -->
                        <div
                            class="mt-4 rounded p-3 text-center text-sm"
                            :style="{
                                backgroundColor: form.navbar_background,
                                color: form.navbar_text_color
                            }"
                        >
                            Navbar Preview
                        </div>
                    </div>

                    <!-- Body Colors -->
                    <div class="rounded-lg border p-4 dark:border-gray-700">
                        <h4
                            class="mb-4 flex items-center gap-2 font-medium text-gray-900 dark:text-white"
                        >
                            <i class="ri-layout-line"></i>
                            Body
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <AppLabel for="body_background">
                                    Background Color
                                </AppLabel>
                                <div class="flex gap-2">
                                    <input
                                        type="color"
                                        id="body_background"
                                        v-model="form.body_background"
                                        class="h-10 w-14 cursor-pointer rounded border p-1"
                                    />
                                    <AppInputText
                                        v-model="form.body_background"
                                        placeholder="#f8fafc"
                                        class="flex-1"
                                    />
                                </div>
                            </div>
                            <div>
                                <AppLabel for="body_text_color">
                                    Text Color
                                </AppLabel>
                                <div class="flex gap-2">
                                    <input
                                        type="color"
                                        id="body_text_color"
                                        v-model="form.body_text_color"
                                        class="h-10 w-14 cursor-pointer rounded border p-1"
                                    />
                                    <AppInputText
                                        v-model="form.body_text_color"
                                        placeholder="#334155"
                                        class="flex-1"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- Preview -->
                        <div
                            class="mt-4 rounded p-3 text-center text-sm"
                            :style="{
                                backgroundColor: form.body_background,
                                color: form.body_text_color
                            }"
                        >
                            Body Preview
                        </div>
                    </div>

                    <!-- Footer Colors -->
                    <div class="rounded-lg border p-4 dark:border-gray-700">
                        <h4
                            class="mb-4 flex items-center gap-2 font-medium text-gray-900 dark:text-white"
                        >
                            <i class="ri-layout-bottom-line"></i>
                            Footer
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <AppLabel for="footer_background">
                                    Background Color
                                </AppLabel>
                                <div class="flex gap-2">
                                    <input
                                        type="color"
                                        id="footer_background"
                                        v-model="form.footer_background"
                                        class="h-10 w-14 cursor-pointer rounded border p-1"
                                    />
                                    <AppInputText
                                        v-model="form.footer_background"
                                        placeholder="#1e293b"
                                        class="flex-1"
                                    />
                                </div>
                            </div>
                            <div>
                                <AppLabel for="footer_text_color">
                                    Text Color
                                </AppLabel>
                                <div class="flex gap-2">
                                    <input
                                        type="color"
                                        id="footer_text_color"
                                        v-model="form.footer_text_color"
                                        class="h-10 w-14 cursor-pointer rounded border p-1"
                                    />
                                    <AppInputText
                                        v-model="form.footer_text_color"
                                        placeholder="#f1f5f9"
                                        class="flex-1"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- Preview -->
                        <div
                            class="mt-4 rounded p-3 text-center text-sm"
                            :style="{
                                backgroundColor: form.footer_background,
                                color: form.footer_text_color
                            }"
                        >
                            Footer Preview
                        </div>
                    </div>
                </div>

                <!-- Reset to Defaults -->
                <div class="mt-6 flex justify-start">
                    <button
                        type="button"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400"
                        @click="resetToDefaults"
                    >
                        <i class="ri-refresh-line mr-1"></i>
                        Reset to Defaults
                    </button>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <AppButton
                    class="btn btn-primary"
                    :loading="form.processing"
                    @click="submitForm"
                >
                    <i class="ri-save-line mr-1"></i>
                    Save Changes
                </AppButton>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const theme = props.settings.theme ?? {}

const defaultColors = {
    navbar_background: '#ffffff',
    navbar_text_color: '#1e293b',
    body_background: '#f8fafc',
    body_text_color: '#334155',
    footer_background: '#1e293b',
    footer_text_color: '#f1f5f9'
}

const form = useForm({
    _method: 'PUT',
    navbar_background:
        theme.navbar_background ?? defaultColors.navbar_background,
    navbar_text_color:
        theme.navbar_text_color ?? defaultColors.navbar_text_color,
    body_background: theme.body_background ?? defaultColors.body_background,
    body_text_color: theme.body_text_color ?? defaultColors.body_text_color,
    footer_background:
        theme.footer_background ?? defaultColors.footer_background,
    footer_text_color:
        theme.footer_text_color ?? defaultColors.footer_text_color
})

const resetToDefaults = () => {
    Object.assign(form, defaultColors)
}

const submitForm = () => {
    form.post(route('setting.update'), {
        preserveScroll: true
    })
}
</script>
