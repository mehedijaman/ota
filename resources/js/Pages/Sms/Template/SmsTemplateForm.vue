<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full xl:w-3/4">
        <template #title>{{
            isEdit ? 'Edit SMS Template' : 'Add SMS Template'
        }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Name and Category -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="name"
                                >Template Name
                                <span class="text-red-500">*</span></AppLabel
                            >
                            <AppInputText
                                id="name"
                                v-model="form.name"
                                placeholder="e.g., Welcome Message"
                                :class="{
                                    'input-error': errorsFields.includes('name')
                                }"
                            />
                        </div>

                        <div>
                            <AppLabel for="category">Category</AppLabel>
                            <AppSelect
                                id="category"
                                v-model="form.category"
                                :options="categoryOptions"
                            />
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <AppLabel for="content"
                            >Message Content
                            <span class="text-red-500">*</span></AppLabel
                        >
                        <AppTextArea
                            id="content"
                            v-model="form.content"
                            placeholder="Enter your SMS message. Use {variable} syntax for dynamic content."
                            :rows="5"
                            :class="{
                                'input-error': errorsFields.includes('content')
                            }"
                        />
                        <p class="mt-1 text-xs text-neutral-500">
                            Use
                            <code
                                class="rounded bg-neutral-100 px-1 py-0.5 dark:bg-neutral-700"
                                >{variable_name}</code
                            >
                            syntax for placeholders. Example: Hello {name}, your
                            OTP is {otp_code}
                        </p>
                    </div>

                    <!-- Detected Variables -->
                    <div v-if="detectedVariables.length > 0">
                        <AppLabel>Detected Variables</AppLabel>
                        <div class="mt-1 flex flex-wrap gap-2">
                            <span
                                v-for="variable in detectedVariables"
                                :key="variable"
                                class="inline-flex items-center rounded bg-blue-100 px-2 py-1 font-mono text-sm text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                            >
                                {{ variable }}
                            </span>
                        </div>
                    </div>

                    <!-- Character Count -->
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-neutral-600 dark:text-neutral-400">
                            Characters: <strong>{{ characterCount }}</strong>
                        </span>
                        <span class="text-neutral-600 dark:text-neutral-400">
                            SMS Parts: <strong>{{ smsParts }}</strong>
                        </span>
                        <span
                            v-if="characterCount > 160"
                            class="text-amber-600 dark:text-amber-400"
                        >
                            <i class="ri-information-line mr-1"></i>
                            Message will be split into multiple parts
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="border-t pt-4">
                        <AppCheckbox
                            id="is_active"
                            v-model="form.is_active"
                            label="Active"
                        />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                class="btn btn-secondary mr-2"
                @click="$inertia.visit(route('sms.templates.index'))"
            >
                Cancel
            </AppButton>
            <AppButton
                class="btn btn-primary"
                @click="submitForm"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Saving...' : 'Save Template' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppSelect from '@/Components/Form/AppSelect.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'
import AppCheckbox from '@/Components/Form/AppCheckbox.vue'

const { title } = useTitle('SMS Template')

const props = defineProps({
    template: {
        type: Object,
        default: null
    },
    categories: {
        type: Array,
        default: () => [
            'otp',
            'marketing',
            'transactional',
            'notification',
            'reminder'
        ]
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS Templates', href: route('sms.templates.index') },
    { label: props.template ? 'Edit' : 'Create', last: true }
]

const categoryOptions = computed(() => [
    { value: '', label: 'Select Category' },
    ...props.categories.map((cat) => ({
        value: cat,
        label: cat.charAt(0).toUpperCase() + cat.slice(1)
    }))
])

const form = useForm({
    name: props.template?.name ?? '',
    content: props.template?.content ?? '',
    category: props.template?.category ?? '',
    is_active: props.template?.is_active ?? true
})

const { isCreate, isEdit } = useFormContext()
const { errorsFields } = useFormErrors()

const characterCount = computed(() => {
    return form.content?.length || 0
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

const detectedVariables = computed(() => {
    if (!form.content) {
        return []
    }
    const matches = form.content.match(/\{([^}]+)\}/g)
    if (!matches) {
        return []
    }
    return [...new Set(matches)]
})

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('sms.templates.store'))
    } else {
        form.put(route('sms.templates.update', props.template.id))
    }
}
</script>
