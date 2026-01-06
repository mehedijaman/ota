<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full xl:w-2/3">
        <template #title>Send Quick SMS</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Phone Number -->
                    <div>
                        <AppLabel for="phone"
                            >Phone Number
                            <span class="text-red-500">*</span></AppLabel
                        >
                        <AppInputText
                            id="phone"
                            v-model="form.phone"
                            placeholder="e.g., 01712345678"
                            :class="{
                                'input-error': errorsFields.includes('phone')
                            }"
                        />
                    </div>

                    <!-- Gateway Selection -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="gateway_id">SMS Gateway</AppLabel>
                            <AppSelect
                                id="gateway_id"
                                v-model="form.gateway_id"
                                :options="gatewaySelectOptions"
                            />
                            <p class="mt-1 text-xs text-neutral-500">
                                Leave empty to use default gateway
                            </p>
                        </div>

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
                            <span
                                v-if="characterCount > 160"
                                class="text-amber-600"
                            >
                                Message will be split into multiple parts
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                class="btn btn-primary"
                @click="submitForm"
                :disabled="form.processing"
            >
                <i v-if="!form.processing" class="ri-send-plane-fill mr-1"></i>
                {{ form.processing ? 'Sending...' : 'Send SMS' }}
            </AppButton>
        </template>
    </AppCard>

    <!-- Recent Messages -->
    <AppCard
        v-if="recentMessages && recentMessages.length"
        class="mt-4 w-full xl:w-2/3"
    >
        <template #title>Recent Messages</template>
        <template #content>
            <div class="space-y-3">
                <div
                    v-for="message in recentMessages"
                    :key="message.id"
                    class="flex items-start justify-between rounded-lg border p-3 dark:border-neutral-700"
                >
                    <div>
                        <div class="font-medium">{{ message.phone }}</div>
                        <div
                            class="mt-1 max-w-md truncate text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ message.message }}
                        </div>
                        <div class="mt-1 text-xs text-neutral-500">
                            {{ message.created_at }}
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                        :class="
                            message.status === 'sent'
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                        "
                    >
                        {{ message.status }}
                    </span>
                </div>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'
import AppSelect from '@/Components/Form/AppSelect.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'

const { title } = useTitle('Send SMS')

const props = defineProps({
    templates: {
        type: Array,
        default: () => []
    },
    gateways: {
        type: Array,
        default: () => []
    },
    recentMessages: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS', last: false },
    { label: 'Send', last: true }
]

const templateSelectOptions = computed(() => [
    { value: '', label: 'No Template' },
    ...props.templates.map((t) => ({
        value: t.id,
        label: t.name
    }))
])

const gatewaySelectOptions = computed(() => [
    { value: '', label: 'Default Gateway' },
    ...props.gateways.map((g) => ({
        value: g.id,
        label: `${g.name}${g.is_default ? ' (Default)' : ''}`
    }))
])

const form = useForm({
    phone: '',
    message: '',
    gateway_id: '',
    template_id: ''
})

const { errorsFields } = useFormErrors()

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

const submitForm = () => {
    form.post(route('sms.send.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('phone', 'message')
        }
    })
}
</script>
