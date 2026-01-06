<template>
    <AppModal :isModalOpen="isOpen" @modal:toggle="$emit('close')" size="sm">
        <template #header>
            <div
                class="rounded-t-lg bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm"
                        >
                            <i class="ri-user-add-line text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">
                                Create User Account
                            </h3>
                            <p class="text-xs text-teal-100">
                                Set up login credentials for this customer
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="text-white/70 transition-colors hover:text-white"
                        @click="$emit('close')"
                    >
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
            </div>
        </template>

        <template #body>
            <form @submit.prevent="submitCreateUser" class="space-y-5">
                <!-- Customer Info Card -->
                <div
                    class="border-neutral-4 bg-neutral-2 flex items-center gap-4 rounded-xl border p-4"
                >
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 text-sm font-semibold text-white shadow-lg"
                    >
                        {{
                            customer?.first_name?.charAt(0)?.toUpperCase() ||
                            'C'
                        }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-neutral-12 truncate font-semibold">
                            {{ customer?.first_name }} {{ customer?.last_name }}
                        </p>
                        <p class="text-neutral-10 truncate text-sm">
                            {{ customer?.email }}
                        </p>
                    </div>
                </div>

                <!-- Generate Password Section -->
                <div class="text-center">
                    <AppButton
                        type="button"
                        class="btn btn-secondary"
                        @click="generatePassword"
                    >
                        <i class="ri-magic-line mr-2"></i>
                        Generate Secure Password
                    </AppButton>
                    <p class="text-neutral-9 mt-2 text-xs">
                        Click to auto-generate a strong password
                    </p>
                </div>

                <!-- Generated Password Display -->
                <div
                    v-if="generatedPasswordValue"
                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 px-4 py-4 shadow-sm transition-all hover:from-emerald-100 hover:to-teal-100"
                    @click="copyPassword"
                    title="Click to copy"
                >
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 shadow-sm"
                    >
                        <i class="ri-key-line text-sm text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="mb-0.5 text-xs font-medium text-emerald-600">
                            Generated Password
                        </p>
                        <p
                            class="font-mono text-sm font-semibold tracking-wide text-emerald-900"
                        >
                            {{ generatedPasswordValue }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1 text-emerald-600">
                        <span
                            v-if="copiedMessage"
                            class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium"
                        >
                            <i class="ri-check-line mr-1"></i>Copied!
                        </span>
                        <i
                            v-else
                            class="ri-file-copy-line text-lg transition-transform hover:scale-110"
                        ></i>
                    </div>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="border-neutral-4 w-full border-t"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="text-neutral-9 bg-white px-3"
                            >or enter manually</span
                        >
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="space-y-4">
                    <div>
                        <AppLabel for="modal-password" required
                            >Password</AppLabel
                        >
                        <div class="relative mt-1">
                            <AppInputText
                                id="modal-password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter password (min 8 characters)"
                                :error="form.errors.password"
                                class="pr-10"
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                class="text-neutral-9 hover:text-neutral-11 absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <i
                                    :class="
                                        showPassword
                                            ? 'ri-eye-off-line'
                                            : 'ri-eye-line'
                                    "
                                ></i>
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <AppLabel for="modal-password-confirmation" required
                            >Confirm Password</AppLabel
                        >
                        <div class="relative mt-1">
                            <AppInputText
                                id="modal-password-confirmation"
                                v-model="form.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Re-enter password"
                                class="pr-10"
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                class="text-neutral-9 hover:text-neutral-11 absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <i
                                    :class="
                                        showPassword
                                            ? 'ri-eye-off-line'
                                            : 'ri-eye-line'
                                    "
                                ></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-2">
                    <AppButton
                        type="button"
                        class="btn btn-secondary flex-1"
                        @click="$emit('close')"
                    >
                        <i class="ri-close-line mr-1"></i>
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        class="btn btn-primary flex-1"
                        :disabled="form.processing"
                    >
                        <i
                            v-if="form.processing"
                            class="ri-loader-4-line mr-1 animate-spin"
                        ></i>
                        <i v-else class="ri-user-add-line mr-1"></i>
                        Create User
                    </AppButton>
                </div>
            </form>
        </template>
    </AppModal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    customer: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close'])

const form = useForm({
    password: '',
    password_confirmation: ''
})

const showPassword = ref(false)
const generatedPasswordValue = ref('')
const copiedMessage = ref(false)

// Reset form when modal opens/closes
watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            form.reset()
            form.clearErrors()
            generatedPasswordValue.value = ''
            showPassword.value = false
        }
    }
)

const generatePassword = () => {
    const length = 16
    const charset =
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*'
    let password = ''

    // Ensure at least one of each type
    password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)]
    password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]
    password += '0123456789'[Math.floor(Math.random() * 10)]
    password += '!@#$%^&*'[Math.floor(Math.random() * 8)]

    // Fill the rest
    for (let i = password.length; i < length; i++) {
        password += charset[Math.floor(Math.random() * charset.length)]
    }

    // Shuffle the password
    password = password
        .split('')
        .sort(() => Math.random() - 0.5)
        .join('')

    generatedPasswordValue.value = password
    form.password = password
    form.password_confirmation = password
    showPassword.value = true
}

const copyPassword = async () => {
    try {
        await navigator.clipboard.writeText(generatedPasswordValue.value)
        copiedMessage.value = true
        setTimeout(() => {
            copiedMessage.value = false
        }, 2000)
    } catch (err) {
        console.error('Failed to copy password:', err)
    }
}

const submitCreateUser = () => {
    if (!props.customer?.uuid) return

    form.post(route('customer.createUser', props.customer.uuid), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close')
            form.reset()
            generatedPasswordValue.value = ''
        }
    })
}
</script>
