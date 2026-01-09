<template>
    <Head title="Forgot Password"></Head>
    <AppAuthShell>
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1
                class="text-2xl font-bold tracking-tight text-neutral-12 md:text-3xl"
            >
                {{ __('Reset your password') }}
            </h1>
            <p class="mt-2 text-neutral-11">
                {{
                    __(
                        "Enter your email address and we'll send you a secure link to reset your password."
                    )
                }}
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
            <AppFormErrors class="mb-4" />

            <!-- Email field -->
            <div class="space-y-2">
                <AppLabel
                    for="email"
                    class="text-sm font-medium text-neutral-12"
                >
                    {{ __('Email Address') }}
                </AppLabel>
                <AppInputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                    placeholder="name@company.com"
                    :class="{
                        'input-error': errorsFields.includes('email')
                    }"
                />
            </div>

            <!-- Submit button -->
            <AppButton
                class="flex h-12 w-full items-center justify-center rounded-lg bg-primary-9 text-base font-semibold text-white shadow-lg shadow-primary-9/25 transition-all duration-200 hover:bg-primary-10 hover:shadow-xl hover:shadow-primary-9/30 focus:outline-none focus:ring-2 focus:ring-primary-9 focus:ring-offset-2 disabled:opacity-50"
                type="submit"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="flex items-center gap-2">
                    <svg
                        class="h-5 w-5 animate-spin"
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
                    {{ __('Sending...') }}
                </span>
                <span v-else>{{ __('Send reset link') }}</span>
            </AppButton>

            <!-- Back to login -->
            <div class="text-center">
                <AppLink
                    :href="route('auth.loginForm')"
                    class="inline-flex items-center gap-2 text-sm font-medium text-neutral-11 transition-colors hover:text-primary-9"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    {{ __('Back to sign in') }}
                </AppLink>
            </div>
        </form>

        <!-- Help text -->
        <div
            class="mt-8 rounded-lg border border-neutral-6 bg-neutral-2 p-4 dark:border-neutral-7 dark:bg-neutral-3"
        >
            <div class="flex gap-3">
                <svg
                    class="h-5 w-5 shrink-0 text-primary-9"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"
                    />
                </svg>
                <div class="text-sm">
                    <p class="font-medium text-neutral-12">
                        {{ __('Need help?') }}
                    </p>
                    <p class="mt-1 text-neutral-11">
                        {{
                            __(
                                'Check your spam folder if you don\'t receive the email within a few minutes.'
                            )
                        }}
                    </p>
                </div>
            </div>
        </div>
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

const form = useForm({
    email: ''
})

function submitForm() {
    form.post(route('auth.sendResetLinkEmail'))
}

const { errorsFields } = useFormErrors()
</script>
