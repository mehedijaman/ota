<template>
    <Head title="Login"></Head>
    <AppAuthShell>
        <!-- Secure Login Badge -->
        <div class="mb-6 flex justify-start">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                {{ __('Secure Access') }}
            </span>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">
                {{ __('Welcome back, Traveler') }}
            </h1>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('Sign in to manage your bookings and explore new destinations.') }}
            </p>
        </div>

        <!-- Social Login -->
        <button
            type="button"
            class="flex h-12 w-full items-center justify-center gap-3 rounded-xl border border-neutral-200 bg-white text-sm font-medium text-neutral-700 transition-all duration-200 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            {{ __('Continue with Google') }}
        </button>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-neutral-200 dark:border-neutral-700"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase tracking-wide">
                <span class="bg-neutral-100 px-3 text-neutral-400 dark:bg-neutral-950 dark:text-neutral-500">
                    {{ __('OR') }}
                </span>
            </div>
        </div>

        <!-- Login form -->
        <form @submit.prevent="submitForm" class="space-y-5">
            <AppFormErrors class="mb-4" />

            <!-- Email field -->
            <div class="space-y-2">
                <AppLabel
                    for="email"
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ __('Email address') }}
                </AppLabel>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-5 w-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        name="email"
                        type="email"
                        class="h-12 w-full rounded-xl border-neutral-200 bg-neutral-50 pl-12 pr-4 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-blue-500 dark:focus:bg-neutral-800"
                        autocomplete="email"
                        placeholder="traveler@example.com"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500/20': errorsFields.includes('email')
                        }"
                    />
                </div>
            </div>

            <!-- Password field -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <AppLabel
                        for="password"
                        class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        {{ __('Password') }}
                    </AppLabel>
                    <AppLink
                        :href="route('auth.forgotPassword')"
                        class="text-sm font-medium text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        {{ __('Forgot password?') }}
                    </AppLink>
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="h-5 w-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <AppInputPassword
                        id="password"
                        v-model="form.password"
                        name="password"
                        class="h-12 w-full rounded-xl border-neutral-200 bg-neutral-50 pl-12 pr-12 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-blue-500 dark:focus:bg-neutral-800"
                        autocomplete="current-password"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500/20': errorsFields.includes('password')
                        }"
                    />
                </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center gap-3">
                <AppCheckbox
                    id="remember"
                    v-model="form.remember"
                    name="remember"
                    :value="true"
                    class="h-4 w-4 rounded border-neutral-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-600"
                />
                <AppLabel
                    for="remember"
                    class="cursor-pointer select-none text-sm text-neutral-600 dark:text-neutral-400"
                >
                    {{ __('Remember me') }}
                </AppLabel>
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
                    {{ __('Signing in...') }}
                </span>
                <span v-else class="flex items-center gap-2">
                    {{ __('Sign in') }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </button>
        </form>

        <!-- Register link -->
        <p class="mt-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
            {{ __("Don't have an account?") }}
            <AppLink
                :href="route('auth.registerForm')"
                class="font-semibold text-blue-600 underline decoration-blue-600/30 underline-offset-4 transition-colors hover:text-blue-700 hover:decoration-blue-700 dark:text-blue-400 dark:decoration-blue-400/30 dark:hover:text-blue-300"
            >
                {{ __('Create one') }}
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
    email: '',
    password: '',
    remember: false
})

function submitForm() {
    form.post(route('auth.login'))
}

const { errorsFields } = useFormErrors()
</script>
