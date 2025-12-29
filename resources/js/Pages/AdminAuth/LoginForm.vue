<template>
    <Head title="Login"></Head>
    <AppAuthShell>
        <!-- Logo -->
        <div class="mb-8 flex justify-center lg:justify-start">
            <AppAuthLogo />
        </div>

        <!-- Welcome text -->
        <div class="mb-8">
            <h1
                class="text-2xl font-bold tracking-tight text-neutral-12 md:text-3xl"
            >
                {{ __('Welcome back') }}
            </h1>
            <p class="mt-2 text-neutral-11">
                {{ __('Sign in to access your admin dashboard') }}
            </p>
        </div>

        <!-- Login form -->
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
                    name="email"
                    type="email"
                    class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                    autocomplete="email"
                    placeholder="name@company.com"
                    :class="{
                        'input-error': errorsFields.includes('email')
                    }"
                />
            </div>

            <!-- Password field -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <AppLabel
                        for="password"
                        class="text-sm font-medium text-neutral-12"
                    >
                        {{ __('Password') }}
                    </AppLabel>
                    <AppLink
                        :href="route('adminAuth.forgotPassword')"
                        class="text-sm font-medium text-primary-9 transition-colors hover:text-primary-11"
                    >
                        {{ __('Forgot password?') }}
                    </AppLink>
                </div>
                <AppInputPassword
                    id="password"
                    v-model="form.password"
                    name="password"
                    class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                    autocomplete="current-password"
                    :class="{
                        'input-error': errorsFields.includes('password')
                    }"
                />
            </div>

            <!-- Remember me -->
            <div class="flex items-center gap-3">
                <AppCheckbox
                    id="remember"
                    v-model="form.remember"
                    name="remember"
                    :value="true"
                    class="h-5 w-5 rounded border-neutral-6 text-primary-9 focus:ring-primary-9/20"
                />
                <AppLabel
                    for="remember"
                    class="cursor-pointer select-none text-sm text-neutral-11"
                >
                    {{ __('Keep me signed in') }}
                </AppLabel>
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
                    {{ __('Signing in...') }}
                </span>
                <span v-else>{{ __('Sign in') }}</span>
            </AppButton>
        </form>

        <!-- Security badge -->
        <div
            class="mt-8 flex items-center justify-center gap-2 text-xs text-neutral-9"
        >
            <svg
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"
                />
            </svg>
            <span>{{ __('Secured with SSL encryption') }}</span>
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
    email: '',
    password: '',
    remember: false
})

function submitForm() {
    form.post(route('adminAuth.login'))
}

const { errorsFields } = useFormErrors()
</script>
