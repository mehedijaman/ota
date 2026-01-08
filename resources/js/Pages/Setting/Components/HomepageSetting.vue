<template>
    <AppCard class="w-full">
        <template #title>Homepage Settings</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form
                class="grid grid-cols-1 gap-4 md:grid-cols-2"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
            >
                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_topbar"
                        v-model="form.show_topbar"
                        name="show_topbar"
                        :value="true"
                    />
                    <AppLabel for="show_topbar" class="ml-3">
                        Show Topbar
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_navbar"
                        v-model="form.show_navbar"
                        name="show_navbar"
                        :value="true"
                    />
                    <AppLabel for="show_navbar" class="ml-3">
                        Show Navbar
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_slider"
                        v-model="form.show_slider"
                        name="show_slider"
                        :value="true"
                    />
                    <AppLabel for="show_slider" class="ml-3">
                        Show Slider
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_top_categories"
                        v-model="form.show_top_categories"
                        name="show_top_categories"
                        :value="true"
                    />
                    <AppLabel for="show_top_categories" class="ml-3">
                        Show Top Categories
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_latest_products"
                        v-model="form.show_latest_products"
                        name="show_latest_products"
                        :value="true"
                    />
                    <AppLabel for="show_latest_products" class="ml-3">
                        Show Latest Products
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_featured_products"
                        v-model="form.show_featured_products"
                        name="show_featured_products"
                        :value="true"
                    />
                    <AppLabel for="show_featured_products" class="ml-3">
                        Show Featured Products
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_brands"
                        v-model="form.show_brands"
                        name="show_brands"
                        :value="true"
                    />
                    <AppLabel for="show_brands" class="ml-3">
                        Show Brands
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_testimonials"
                        v-model="form.show_testimonials"
                        name="show_testimonials"
                        :value="true"
                    />
                    <AppLabel for="show_testimonials" class="ml-3">
                        Show Testimonials
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_latest_posts"
                        v-model="form.show_latest_posts"
                        name="show_latest_posts"
                        :value="true"
                    />
                    <AppLabel for="show_latest_posts" class="ml-3">
                        Show Latest Blogs
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_footer_menu"
                        v-model="form.show_footer_menu"
                        name="show_footer_menu"
                        :value="true"
                    />
                    <AppLabel for="show_footer_menu" class="ml-3">
                        Show Footer Menu
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_floating_chat"
                        v-model="form.show_floating_chat"
                        name="show_floating_chat"
                        :value="true"
                    />
                    <AppLabel for="show_floating_chat" class="ml-3">
                        Show Floating Chat Button
                    </AppLabel>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('Update Site Setting') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const getLogoPreviewURL = () => {
    if (props.settings.logo_url) {
        return props.settings.logo_url
    }

    return null
}

const getFaviconPreviewURL = () => {
    if (props.settings.favicon_url) {
        return props.settings.favicon_url
    }

    return null
}

const form = useForm({
    site_name: props.settings?.site_name || '',
    slogan: props.settings?.slogan || '',
    logo: null,
    favicon: null,

    show_topbar: props.settings?.show_topbar || false,
    show_navbar: props.settings?.show_navbar || false,
    show_slider: props.settings?.show_slider || false,
    show_top_categories: props.settings?.show_top_categories || false,
    show_latest_products: props.settings?.show_latest_products || false,
    show_featured_products: props.settings?.show_featured_products || false,
    show_brands: props.settings?.show_brands || false,
    show_testimonials: props.settings?.show_testimonials || false,
    show_latest_posts: props.settings?.show_latest_posts || false,
    show_footer_menu: props.settings?.show_footer_menu || false,
    show_floating_chat: props.settings?.show_floating_chat || false,

    remove_previous_logo: false,
    remove_previous_favicon: false
})

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT'
    })).post(route('setting.update'), {
        forceFormData: true // Ensures files are sent as FormData
    })
}

const { errorsFields } = useFormErrors()
</script>
