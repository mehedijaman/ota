<template>
    <AppCard class="w-full">
        <template #title>Site Settings</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form
                class="grid grid-cols-1 gap-4 md:grid-cols-2"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
            >
                <div>
                    <AppLabel for="site_name">{{ __('Site Name') }}</AppLabel>
                    <AppInputText
                        v-model="form.site_name"
                        id="site_name"
                        type="text"
                        placeholder="Site Name"
                    />
                </div>

                <div>
                    <AppLabel for="slogan">{{ __('Slogan') }}</AppLabel>
                    <AppInputText
                        v-model="form.slogan"
                        id="slogan"
                        type="text"
                        placeholder="Slogan"
                    />
                </div>

                <div>
                    <AppLabel for="logo">{{ __('Logo') }}</AppLabel>
                    <AppInputFile
                        v-model="form.logo"
                        :image-preview-url="getLogoPreviewURL()"
                        @remove-file="form.remove_previous_logo = true"
                    ></AppInputFile>
                </div>

                <div>
                    <AppLabel for="favicon">{{ __('Favicon') }}</AppLabel>
                    <AppInputFile
                        v-model="form.favicon"
                        :image-preview-url="getFaviconPreviewURL()"
                        @remove-file="form.remove_previous_favicon = true"
                    ></AppInputFile>
                </div>

                <div>
                    <AppLabel for="watermark">{{ __('Watermark') }}</AppLabel>
                    {{ getWatermarkPreviewURL() }}
                    <AppInputFile
                        v-model="form.watermark"
                        :image-preview-url="getWatermarkPreviewURL()"
                        @remove-file="form.remove_previous_watermark = true"
                    ></AppInputFile>
                </div>
                <div></div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_name"
                        v-model="form.show_name"
                        name="show_name"
                        :value="true"
                    />
                    <AppLabel for="show_name" class="ml-3">
                        Show Name
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_slogan"
                        v-model="form.show_slogan"
                        name="show_slogan"
                        :value="true"
                    />
                    <AppLabel for="show_slogan" class="ml-3">
                        Show Slogan
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_logo"
                        v-model="form.show_logo"
                        name="show_logo"
                        :value="true"
                    />
                    <AppLabel for="show_logo" class="ml-3">
                        Show Logo
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_favicon"
                        v-model="form.show_favicon"
                        name="show_favicon"
                        :value="true"
                    />
                    <AppLabel for="show_favicon" class="ml-3">
                        Show Favicon
                    </AppLabel>
                </div>

                <div>
                    <AppLabel for="currency_symbol">{{
                        __('Currency Symbol')
                    }}</AppLabel>
                    <AppInputText
                        v-model="form.currency_symbol"
                        id="currency_symbol"
                        type="text"
                        placeholder="Currency Symbol"
                    />
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

const getWatermarkPreviewURL = () => {
    if (props.settings.watermark_url) {
        return props.settings.watermark_url
    }

    return null
}

const form = useForm({
    site_name: props.settings?.site_name || '',
    slogan: props.settings?.slogan || '',
    logo: null,
    favicon: null,
    watermark: null,

    show_name: props.settings?.show_name || false,
    show_slogan: props.settings?.show_slogan || false,
    show_logo: props.settings?.show_logo || false,
    show_favicon: props.settings?.show_favicon || false,
    show_watermark: props.settings?.show_watermark || false,

    remove_previous_logo: false,
    remove_previous_favicon: false,
    remove_previous_watermark: false,

    currency_symbol: props.settings?.currency_symbol || ''
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
