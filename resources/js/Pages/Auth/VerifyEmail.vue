<template>
    <Head title="Verify Email"></Head>
    <AppAuthShell>
        <!-- Header -->
        <div class="mb-8 text-center">
            <!-- Icon -->
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                <svg
                    class="h-6 w-6 text-primary-600 dark:text-primary-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                    />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                {{ __('Check your email') }}
            </h1>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('We sent a verification link to your email address.') }}
            </p>
        </div>

        <!-- Success message -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
        >
            <div
                v-if="verificationLinkSent"
                class="mb-6 rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/30"
            >
                <p class="text-center text-sm font-medium text-green-700 dark:text-green-300">
                    {{ __('A new verification link has been sent!') }}
                </p>
            </div>
        </Transition>

        <!-- Resend button -->
        <form @submit.prevent="resendVerification" class="space-y-3">
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
                    {{ __('Sending...') }}
                </span>
                <span v-else>
                    {{ __('Resend verification email') }}
                </span>
            </button>

            <!-- Logout link -->
            <AppLink
                :href="route('auth.logout')"
                class="flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white text-sm font-medium text-neutral-700 transition-all duration-200 hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
            >
                {{ __('Sign out') }}
            </AppLink>
        </form>

        <!-- Help text -->
        <p class="mt-6 text-center text-xs text-neutral-400 dark:text-neutral-500">
            {{ __('Didn\'t receive the email? Check your spam folder.') }}
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
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const verificationLinkSent = computed(
    () => page.props.status === 'verification-link-sent'
)

const form = useForm({})

function resendVerification() {
    form.post(route('verification.send'))
}
</script>
