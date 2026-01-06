<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full xl:w-3/4">
        <template #title>Create SMS Campaign</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Campaign Name -->
                    <div>
                        <AppLabel for="name"
                            >Campaign Name
                            <span class="text-red-500">*</span></AppLabel
                        >
                        <AppInputText
                            id="name"
                            v-model="form.name"
                            placeholder="e.g., Eid Promotion 2025"
                            :class="{
                                'input-error': errorsFields.includes('name')
                            }"
                        />
                    </div>

                    <!-- Template or Custom Message -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="template_id"
                                >Use Template (Optional)</AppLabel
                            >
                            <AppSelect
                                id="template_id"
                                v-model="form.template_id"
                                :options="templateSelectOptions"
                            />
                        </div>

                        <div>
                            <AppLabel for="gateway_id"
                                >SMS Gateway
                                <span class="text-red-500">*</span></AppLabel
                            >
                            <AppSelect
                                id="gateway_id"
                                v-model="form.gateway_id"
                                :options="gatewaySelectOptions"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('gateway_id')
                                }"
                            />
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <AppLabel for="message"
                            >Message
                            <span class="text-red-500">*</span></AppLabel
                        >
                        <AppTextArea
                            id="message"
                            v-model="form.message"
                            placeholder="Enter your SMS message..."
                            :rows="5"
                            :class="{
                                'input-error': errorsFields.includes('message')
                            }"
                        />
                        <div
                            class="mt-1 flex justify-between text-xs text-neutral-500"
                        >
                            <span
                                >Characters: {{ characterCount }} | SMS Parts:
                                {{ smsParts }}</span
                            >
                            <span v-if="selectedTemplate">
                                Template loaded: {{ selectedTemplate.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Recipients Section -->
                    <div class="border-t pt-4">
                        <h3
                            class="mb-3 text-sm font-medium text-neutral-700 dark:text-neutral-300"
                        >
                            Recipients
                        </h3>

                        <div class="mb-3 flex gap-4">
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    v-model="recipientMode"
                                    value="manual"
                                    class="mr-2"
                                />
                                Enter Manually
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    v-model="recipientMode"
                                    value="file"
                                    class="mr-2"
                                />
                                Upload File
                            </label>
                        </div>

                        <!-- Manual Entry -->
                        <div v-if="recipientMode === 'manual'">
                            <AppLabel for="phones"
                                >Phone Numbers
                                <span class="text-red-500">*</span></AppLabel
                            >
                            <AppTextArea
                                id="phones"
                                v-model="form.phones"
                                placeholder="Enter phone numbers, one per line or comma-separated&#10;01712345678&#10;01812345678"
                                :rows="6"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('phones')
                                }"
                            />
                            <p class="mt-1 text-xs text-neutral-500">
                                {{ phoneCount }} phone numbers detected
                            </p>
                        </div>

                        <!-- File Upload -->
                        <div v-else>
                            <AppLabel for="recipients_file"
                                >Upload File</AppLabel
                            >
                            <input
                                type="file"
                                id="recipients_file"
                                ref="fileInput"
                                @change="handleFileChange"
                                accept=".csv,.txt,.xlsx"
                                class="file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900 dark:file:text-primary-300 block w-full text-sm text-neutral-500 file:mr-4 file:rounded-lg file:border-0 file:px-4 file:py-2 file:text-sm file:font-semibold"
                            />
                            <p class="mt-1 text-xs text-neutral-500">
                                Supported formats: CSV, TXT, XLSX. File should
                                contain phone numbers in the first column.
                            </p>
                        </div>
                    </div>

                    <!-- Schedule Option -->
                    <div class="border-t pt-4">
                        <AppCheckbox
                            id="is_scheduled"
                            v-model="isScheduled"
                            label="Schedule for later"
                        />

                        <div v-if="isScheduled" class="mt-3">
                            <AppLabel for="scheduled_at"
                                >Schedule Date & Time</AppLabel
                            >
                            <AppInputText
                                id="scheduled_at"
                                type="datetime-local"
                                v-model="form.scheduled_at"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('scheduled_at')
                                }"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                class="btn btn-secondary mr-2"
                @click="$inertia.visit(route('sms.campaigns.index'))"
            >
                Cancel
            </AppButton>
            <AppButton
                class="btn btn-primary"
                @click="submitForm"
                :disabled="form.processing"
            >
                {{
                    form.processing
                        ? 'Creating...'
                        : isScheduled
                          ? 'Schedule Campaign'
                          : 'Send Campaign'
                }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'
import AppSelect from '@/Components/Form/AppSelect.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'
import AppCheckbox from '@/Components/Form/AppCheckbox.vue'

const { title } = useTitle('New Campaign')

const props = defineProps({
    templates: {
        type: Array,
        default: () => []
    },
    gateways: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS Campaigns', href: route('sms.campaigns.index') },
    { label: 'Create', last: true }
]

const templateSelectOptions = computed(() => [
    { value: '', label: 'No Template (Custom Message)' },
    ...props.templates.map((t) => ({
        value: t.id,
        label: t.name
    }))
])

const gatewaySelectOptions = computed(() => [
    { value: '', label: 'Select Gateway' },
    ...props.gateways.map((g) => ({
        value: g.id,
        label: `${g.name}${g.is_default ? ' (Default)' : ''}`
    }))
])

const form = useForm({
    name: '',
    template_id: '',
    gateway_id: '',
    message: '',
    phones: '',
    recipients_file: null,
    scheduled_at: ''
})

const { errorsFields } = useFormErrors()

const recipientMode = ref('manual')
const isScheduled = ref(false)
const fileInput = ref(null)

const selectedTemplate = computed(() => {
    if (!form.template_id) {
        return null
    }
    return props.templates.find((t) => t.id === parseInt(form.template_id))
})

const characterCount = computed(() => {
    return form.message?.length || 0
})

const smsParts = computed(() => {
    const len = characterCount.value
    if (len === 0) {
        return 0
    }
    if (len <= 160) {
        return 1
    }
    return Math.ceil(len / 153)
})

const phoneCount = computed(() => {
    if (!form.phones) {
        return 0
    }
    const phones = form.phones
        .split(/[\n,;]+/)
        .map((p) => p.trim())
        .filter((p) => p.length > 0)
    return phones.length
})

// Load template content when selected
watch(
    () => form.template_id,
    (newId) => {
        if (newId) {
            const template = props.templates.find(
                (t) => t.id === parseInt(newId)
            )
            if (template) {
                form.message = template.content
            }
        }
    }
)

// Set default gateway
watch(
    () => props.gateways,
    (gateways) => {
        if (!form.gateway_id && gateways.length > 0) {
            const defaultGateway = gateways.find((g) => g.is_default)
            form.gateway_id = defaultGateway?.id || gateways[0]?.id || ''
        }
    },
    { immediate: true }
)

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.recipients_file = file
    }
}

const submitForm = () => {
    if (!isScheduled.value) {
        form.scheduled_at = ''
    }

    form.post(route('sms.campaigns.store'), {
        forceFormData: true
    })
}
</script>
