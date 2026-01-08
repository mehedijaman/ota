<template>
    <AppCard>
        <template #title>
            <div class="flex items-center gap-2">
                <i class="ri-shield-check-line text-lg"></i>
                reCAPTCHA Settings
            </div>
        </template>
        <template #description>
            Configure Google reCAPTCHA to protect your forms from spam and
            abuse.
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="space-y-6">
                    <!-- Enable Toggle -->
                    <div
                        class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                    >
                        <div>
                            <h4
                                class="font-medium text-gray-900 dark:text-white"
                            >
                                Enable reCAPTCHA
                            </h4>
                            <p class="text-sm text-gray-500">
                                When enabled, reCAPTCHA will be added to forms
                                to prevent spam.
                            </p>
                        </div>
                        <label class="relative inline-flex cursor-pointer">
                            <input
                                v-model="form.recaptcha_enabled"
                                type="checkbox"
                                class="peer sr-only"
                            />
                            <div
                                class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-blue-800"
                            ></div>
                        </label>
                    </div>

                    <!-- reCAPTCHA Version -->
                    <div>
                        <AppLabel for="recaptcha_version">
                            reCAPTCHA Version
                        </AppLabel>
                        <select
                            id="recaptcha_version"
                            v-model="form.recaptcha_version"
                            class="focus:border-primary-500 focus:ring-primary-500 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="v2">
                                reCAPTCHA v2 ("I'm not a robot" Checkbox)
                            </option>
                            <option value="v3">
                                reCAPTCHA v3 (Invisible, Score-based)
                            </option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">
                            <span v-if="form.recaptcha_version === 'v2'">
                                Users will see a checkbox to verify they're
                                human.
                            </span>
                            <span v-else>
                                Invisible verification. Scores user interactions
                                without interruption.
                            </span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Site Key -->
                        <div>
                            <AppLabel for="recaptcha_site_key" required>
                                Site Key
                            </AppLabel>
                            <AppInputText
                                id="recaptcha_site_key"
                                v-model="form.recaptcha_site_key"
                                placeholder="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                This key is used in the HTML code your site
                                serves to users.
                            </p>
                        </div>

                        <!-- Secret Key -->
                        <div>
                            <AppLabel for="recaptcha_secret_key" required>
                                Secret Key
                            </AppLabel>
                            <AppInputText
                                id="recaptcha_secret_key"
                                v-model="form.recaptcha_secret_key"
                                type="password"
                                placeholder="••••••••"
                                autocomplete="new-password"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Keep this key secret. Leave empty to keep
                                existing.
                            </p>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div
                        class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950"
                    >
                        <h4
                            class="mb-2 flex items-center gap-2 font-medium text-blue-800 dark:text-blue-200"
                        >
                            <i class="ri-information-line"></i>
                            How to get reCAPTCHA keys
                        </h4>
                        <ol
                            class="list-inside list-decimal space-y-1 text-sm text-blue-700 dark:text-blue-300"
                        >
                            <li>
                                Go to the
                                <a
                                    href="https://www.google.com/recaptcha/admin"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="underline hover:no-underline"
                                    >Google reCAPTCHA Admin Console</a
                                >
                            </li>
                            <li>Click the "+" button to register a new site</li>
                            <li>
                                Choose reCAPTCHA
                                {{ form.recaptcha_version }} and add your
                                domain(s)
                            </li>
                            <li>
                                Copy the Site Key and Secret Key to the fields
                                above
                            </li>
                        </ol>
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

const recaptcha = props.settings.recaptcha ?? {}

const form = useForm({
    _method: 'PUT',
    recaptcha_enabled: recaptcha.recaptcha_enabled ?? false,
    recaptcha_site_key: recaptcha.recaptcha_site_key ?? '',
    recaptcha_secret_key: '',
    recaptcha_version: recaptcha.recaptcha_version ?? 'v2'
})

const submitForm = () => {
    form.post(route('setting.update'), {
        preserveScroll: true
    })
}
</script>
