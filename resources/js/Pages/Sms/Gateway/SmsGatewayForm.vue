<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full xl:w-3/4">
        <template #title>{{
            isEdit ? 'Edit SMS Gateway' : 'Add SMS Gateway'
        }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Name and Provider -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="name"
                                >Gateway Name
                                <span class="text-red-500">*</span></AppLabel
                            >
                            <AppInputText
                                id="name"
                                v-model="form.name"
                                placeholder="e.g., Primary Gateway"
                                :class="{
                                    'input-error': errorsFields.includes('name')
                                }"
                            />
                        </div>

                        <div>
                            <AppLabel for="provider_class"
                                >SMS Provider
                                <span class="text-red-500">*</span></AppLabel
                            >
                            <AppSelect
                                id="provider_class"
                                v-model="form.provider_class"
                                :options="providerSelectOptions"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('provider_class')
                                }"
                            />
                        </div>
                    </div>

                    <!-- Dynamic Credentials -->
                    <div
                        v-if="
                            form.provider_class &&
                            currentProviderFields.length > 0
                        "
                        class="mt-2 border-t pt-4"
                    >
                        <h3
                            class="mb-3 text-sm font-medium text-neutral-700 dark:text-neutral-300"
                        >
                            Provider Credentials
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                v-for="field in currentProviderFields"
                                :key="field"
                            >
                                <AppLabel :for="'cred_' + field">{{
                                    formatFieldLabel(field)
                                }}</AppLabel>
                                <AppInputText
                                    :id="'cred_' + field"
                                    v-model="form.credentials[field]"
                                    :placeholder="formatFieldLabel(field)"
                                    :type="
                                        isSecretField(field)
                                            ? 'password'
                                            : 'text'
                                    "
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Status Options -->
                    <div class="mt-2 border-t pt-4">
                        <div class="flex flex-wrap gap-6">
                            <AppCheckbox
                                id="is_active"
                                v-model="form.is_active"
                                label="Active"
                            />
                            <AppCheckbox
                                id="is_default"
                                v-model="form.is_default"
                                label="Set as Default Gateway"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                class="btn btn-secondary mr-2"
                @click="$inertia.visit(route('sms.gateways.index'))"
            >
                Cancel
            </AppButton>
            <AppButton
                class="btn btn-primary"
                @click="submitForm"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Saving...' : 'Save Gateway' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppSelect from '@/Components/Form/AppSelect.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppCheckbox from '@/Components/Form/AppCheckbox.vue'

const { title } = useTitle('SMS Gateway')

const props = defineProps({
    gateway: {
        type: Object,
        default: null
    },
    providerOptions: {
        type: Array,
        default: () => []
    },
    providerFields: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS Gateways', href: route('sms.gateways.index') },
    { label: props.gateway ? 'Edit' : 'Create', last: true }
]

const providerSelectOptions = computed(() => [
    { value: '', label: 'Select Provider' },
    ...props.providerOptions
])

const form = useForm({
    name: props.gateway?.name ?? '',
    provider_class: props.gateway?.provider_class ?? '',
    credentials: props.gateway?.credentials ?? {},
    is_default: props.gateway?.is_default ?? false,
    is_active: props.gateway?.is_active ?? true
})

const { isCreate, isEdit } = useFormContext()
const { errorsFields } = useFormErrors()

const currentProviderFields = computed(() => {
    if (!form.provider_class || !props.providerFields[form.provider_class]) {
        return []
    }
    return props.providerFields[form.provider_class]
})

// Initialize credentials when provider changes
watch(
    () => form.provider_class,
    (newProvider, oldProvider) => {
        if (newProvider !== oldProvider && newProvider) {
            const fields = props.providerFields[newProvider] || []
            const existingCreds = { ...form.credentials }
            const newCreds = {}

            fields.forEach((field) => {
                newCreds[field] = existingCreds[field] ?? ''
            })

            form.credentials = newCreds
        }
    },
    { immediate: !props.gateway }
)

const formatFieldLabel = (field) => {
    return field
        .replace(/_/g, ' ')
        .replace(/([A-Z])/g, ' $1')
        .replace(/^./, (str) => str.toUpperCase())
        .trim()
}

const isSecretField = (field) => {
    const secretFields = [
        'password',
        'secret',
        'api_key',
        'api_secret',
        'token',
        'apikey'
    ]
    return secretFields.some((s) => field.toLowerCase().includes(s))
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('sms.gateways.store'))
    } else {
        form.put(route('sms.gateways.update', props.gateway.id))
    }
}
</script>
