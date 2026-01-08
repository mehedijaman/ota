<template>
    <AppCard>
        <template #title>
            <div class="flex items-center gap-2">
                <i class="ri-mail-settings-line text-lg"></i>
                SMTP / Email Settings
            </div>
        </template>
        <template #description>
            Configure your email server settings for sending emails.
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- SMTP Host -->
                    <div>
                        <AppLabel for="smtp_host" required>
                            SMTP Host
                        </AppLabel>
                        <AppInputText
                            id="smtp_host"
                            v-model="form.smtp_settings.host"
                            placeholder="smtp.example.com"
                        />
                    </div>

                    <!-- SMTP Port -->
                    <div>
                        <AppLabel for="smtp_port" required>
                            SMTP Port
                        </AppLabel>
                        <AppInputText
                            id="smtp_port"
                            v-model="form.smtp_settings.port"
                            type="number"
                            placeholder="587"
                        />
                    </div>

                    <!-- SMTP Username -->
                    <div>
                        <AppLabel for="smtp_username"> Username </AppLabel>
                        <AppInputText
                            id="smtp_username"
                            v-model="form.smtp_settings.username"
                            placeholder="your-email@example.com"
                            autocomplete="off"
                        />
                    </div>

                    <!-- SMTP Password -->
                    <div>
                        <AppLabel for="smtp_password"> Password </AppLabel>
                        <AppInputText
                            id="smtp_password"
                            v-model="form.smtp_settings.password"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Leave empty to keep existing password
                        </p>
                    </div>

                    <!-- Encryption -->
                    <div>
                        <AppLabel for="smtp_encryption"> Encryption </AppLabel>
                        <select
                            id="smtp_encryption"
                            v-model="form.smtp_settings.encryption"
                            class="focus:border-primary-500 focus:ring-primary-500 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="">None</option>
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                        </select>
                    </div>

                    <!-- From Name -->
                    <div>
                        <AppLabel for="smtp_from_name"> From Name </AppLabel>
                        <AppInputText
                            id="smtp_from_name"
                            v-model="form.smtp_settings.from_name"
                            placeholder="Your Company Name"
                        />
                    </div>

                    <!-- From Email -->
                    <div class="lg:col-span-2">
                        <AppLabel for="smtp_from_address">
                            From Email Address
                        </AppLabel>
                        <AppInputText
                            id="smtp_from_address"
                            v-model="form.smtp_settings.from_address"
                            type="email"
                            placeholder="noreply@example.com"
                        />
                    </div>
                </div>

                <!-- Test Email Section -->
                <div
                    class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <h4
                        class="mb-3 flex items-center gap-2 font-medium text-gray-900 dark:text-white"
                    >
                        <i class="ri-send-plane-line"></i>
                        Test Email Configuration
                    </h4>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <AppInputText
                            v-model="testEmail"
                            type="email"
                            placeholder="test@example.com"
                            class="flex-1"
                        />
                        <AppButton
                            type="button"
                            class="btn btn-secondary"
                            :loading="sendingTest"
                            @click="sendTestEmail"
                        >
                            <i class="ri-mail-send-line mr-1"></i>
                            Send Test Email
                        </AppButton>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Save your settings first, then send a test email to
                        verify the configuration.
                    </p>
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
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const defaultSmtpSettings = {
    host: '',
    port: 587,
    username: '',
    password: '',
    encryption: 'tls',
    from_address: '',
    from_name: ''
}

const form = useForm({
    _method: 'PUT',
    smtp_settings: {
        ...defaultSmtpSettings,
        ...(props.settings.smtp_settings ?? {}),
        password: '' // Always start with empty password field (masked)
    }
})

const testEmail = ref('')
const sendingTest = ref(false)

const submitForm = () => {
    // Ensure port is a number
    form.smtp_settings.port = form.smtp_settings.port
        ? Number(form.smtp_settings.port)
        : 587

    form.post(route('setting.update'), {
        preserveScroll: true
    })
}

const sendTestEmail = () => {
    if (!testEmail.value) {
        alert('Please enter a test email address')
        return
    }

    sendingTest.value = true

    router.post(
        route('setting.testEmail'),
        {
            email: testEmail.value
        },
        {
            preserveScroll: true,
            onFinish: () => {
                sendingTest.value = false
            }
        }
    )
}
</script>
