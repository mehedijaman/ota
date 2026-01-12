<template>
    <Head title="Forgot Password"></Head>
    <AppAuthShell>
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                {{ __('Forgot your password?') }}
            </h1>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __("No worries, we'll send you reset instructions.") }}
            </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-4">
            <AppFormErrors class="mb-4" />

            <!-- Email field -->
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
                    class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                    placeholder="you@example.com"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('email')
                    }"
                />
            </div>

            <!-- Submit button -->
            <button
                class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-all duration-200 hover:bg-blue-700 hover:shadow-blue-700/25 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:shadow-blue-600/10 dark:focus:ring-offset-neutral-900"    
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
                    {{ __('Sending...') }}
                </span>
                <span v-else>
                    {{ __('Send reset link') }}
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

const form = useForm({
    email: ''
})

function submitForm() {
    form.post(route('auth.sendResetLinkEmail'))
}

const { errorsFields } = useFormErrors()
</script>
