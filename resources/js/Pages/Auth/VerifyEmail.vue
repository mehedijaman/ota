<template>
    <Head title="Verify Email"></Head>
    <AppAuthShell>
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-primary-9/10">
                <svg
                    class="h-8 w-8 text-primary-9"
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
            <h1
                class="text-2xl font-bold tracking-tight text-neutral-12 md:text-3xl"
            >
                {{ __('Verify your email') }}
            </h1>
            <p class="mt-2 text-neutral-11">
                {{
                    __(
                        'Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent to your email.'
                    )
                }}
            </p>
        </div>

        <!-- Success message -->
        <div
            v-if="verificationLinkSent"
            class="mb-6 rounded-lg border border-green-6 bg-green-2 p-4 dark:border-green-7 dark:bg-green-3"
        >
            <div class="flex items-center gap-3">
                <svg
                    class="h-5 w-5 shrink-0 text-green-9"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <p class="text-sm font-medium text-green-11">
                    {{ __('A new verification link has been sent!') }}
                </p>
            </div>
        </div>

        <!-- Resend button -->
        <form @submit.prevent="resendVerification">
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
                <span v-else>{{ __('Resend verification email') }}</span>
            </AppButton>
        </form>

        <!-- Logout link -->
        <p class="mt-6 text-center text-sm text-neutral-11">
            <AppLink
                :href="route('auth.logout')"
                class="font-medium text-primary-9 transition-colors hover:text-primary-11"
            >
                {{ __('Log out') }}
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
