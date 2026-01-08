<template>
    <AppCard>
        <template #title>
            <div class="flex items-center gap-2">
                <i class="ri-global-line text-lg"></i>
                Site Identity
            </div>
        </template>
        <template #description>
            Configure your site name, logo, and branding options.
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Site Name -->
                    <div>
                        <AppLabel for="site_name" required>
                            Site Name
                        </AppLabel>
                        <AppInputText
                            id="site_name"
                            v-model="form.site_name"
                            placeholder="Enter site name"
                        />
                    </div>

                    <!-- Slogan -->
                    <div>
                        <AppLabel for="slogan">Slogan / Tagline</AppLabel>
                        <AppInputText
                            id="slogan"
                            v-model="form.slogan"
                            placeholder="Your catchy tagline"
                        />
                    </div>

                    <!-- Logo -->
                    <div>
                        <AppLabel for="logo">Logo</AppLabel>
                        <AppInputFile
                            id="logo"
                            v-model="form.logo"
                            :image-preview-url="settings.logo_url"
                            accept="image/*"
                            @remove-file="form.remove_previous_logo = true"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Recommended: PNG or SVG, max 2MB
                        </p>
                    </div>

                    <!-- Favicon -->
                    <div>
                        <AppLabel for="favicon">Favicon</AppLabel>
                        <AppInputFile
                            id="favicon"
                            v-model="form.favicon"
                            :image-preview-url="settings.favicon_url"
                            accept="image/*"
                            @remove-file="form.remove_previous_favicon = true"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Recommended: 32x32px ICO or PNG
                        </p>
                    </div>

                    <!-- Banner -->
                    <div class="lg:col-span-2">
                        <AppLabel for="banner">Default Banner</AppLabel>
                        <AppInputFile
                            id="banner"
                            v-model="form.banner"
                            :image-preview-url="settings.banner_url"
                            accept="image/*"
                            @remove-file="form.remove_previous_banner = true"
                        />
                    </div>
                </div>

                <!-- Display Options -->
                <div class="mt-8 border-t pt-6 dark:border-gray-700">
                    <h4
                        class="mb-4 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Display Options
                    </h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <label class="flex cursor-pointer items-center gap-3">
                            <AppCheckbox
                                id="show_name"
                                v-model="form.show_name"
                                :checked="form.show_name"
                            />
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300"
                            >
                                Show Site Name
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3">
                            <AppCheckbox
                                id="show_slogan"
                                v-model="form.show_slogan"
                                :checked="form.show_slogan"
                            />
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300"
                            >
                                Show Slogan
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3">
                            <AppCheckbox
                                id="show_logo"
                                v-model="form.show_logo"
                                :checked="form.show_logo"
                            />
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300"
                            >
                                Show Logo
                            </span>
                        </label>
                    </div>
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

const form = useForm({
    _method: 'PUT',
    site_name: props.settings.site_name ?? '',
    slogan: props.settings.slogan ?? '',
    logo: null,
    favicon: null,
    banner: null,
    show_name: props.settings.show_name ?? true,
    show_slogan: props.settings.show_slogan ?? true,
    show_logo: props.settings.show_logo ?? true,
    remove_previous_logo: false,
    remove_previous_favicon: false,
    remove_previous_banner: false
})

const submitForm = () => {
    form.post(route('setting.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('logo', 'favicon', 'banner')
            form.remove_previous_logo = false
            form.remove_previous_favicon = false
            form.remove_previous_banner = false
        }
    })
}
</script>
