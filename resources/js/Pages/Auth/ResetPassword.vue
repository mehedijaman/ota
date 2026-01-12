<template>
    <Head title="Reset Password"></Head>
    <AppAuthShell>
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                {{ __('Set new password') }}
            </h1>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('Must be at least 8 characters.') }}
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
            <AppFormErrors class="mb-4" />

            <!-- Email field (readonly) -->
            <div class="space-y-1.5">
                <AppLabel
                    for="email"
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ __('Email') }}
                </AppLabel>
                <AppInputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="h-10 w-full cursor-not-allowed rounded-lg border-neutral-300 bg-neutral-100 text-sm text-neutral-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-400"
                    readonly
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('email')
                    }"
                />
            </div>

            <!-- Password fields -->
            <div class="space-y-1.5">
                <AppLabel
                    for="password"
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ __('New password') }}
                </AppLabel>
                <AppInputPassword
                    id="password"
                    v-model="form.password"
                    name="password"
                    class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                    autocomplete="new-password"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('password')
                    }"
                />
            </div>
            <div class="space-y-1.5">
                <AppLabel
                    for="password_confirmation"
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ __('Confirm password') }}
                </AppLabel>
                <AppInputPassword
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    name="password_confirmation"
                    class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                    autocomplete="new-password"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('password_confirmation')
                    }"
                />
            </div>

            <!-- Submit button -->
            <button
                class="flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-primary-600 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-primary-600 dark:hover:bg-primary-500 dark:focus:ring-offset-neutral-900"
                type="submit"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="flex items-center gap-2">
                    <svg
                        class="h-4 w-4 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        />
                    </svg>
                    {{ __('Resetting...') }}
                </span>
                <span v-else>
                    {{ __('Reset password') }}
                </span>
            </button>
        </form>

        <!-- Back to login -->
        <p class="mt-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
            <AppLink
                :href="route('auth.loginForm')"
                class="font-medium text-primary-600 transition-colors hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
            >
                ← {{ __('Back to sign in') }}
            </AppLink>
        </p>
    </AppAuthShell>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'

export default {
    layout: GuestLayout
}
</script>

<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    email: {
        type: String,
        required: true
    },
    token: {
        type: String,
        required: true
    }
})

const form = useForm({
    email: props.email,
    token: props.token,
    password: '',
    password_confirmation: ''
})

function submitForm() {
    form.post(route('auth.resetPassword'))
}

const { errorsFields } = useFormErrors()
</script>
