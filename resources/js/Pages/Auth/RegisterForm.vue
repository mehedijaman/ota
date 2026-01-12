<template>
    <Head title="Create Account"></Head>
    <AppAuthShell>
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                {{ __('Create your account') }}
            </h1>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                {{ __('Join thousands of travelers today.') }}
            </p>
        </div>

        <!-- Registration form -->
        <form @submit.prevent="submitForm" class="space-y-4">
            <AppFormErrors class="mb-4" />

            <!-- Name fields -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <AppLabel
                        for="first_name"
                        class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        {{ __('First name') }}
                    </AppLabel>
                    <AppInputText
                        id="first_name"
                        v-model="form.first_name"
                        name="first_name"
                        type="text"
                        class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                        placeholder="John"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('first_name')
                        }"
                    />
                </div>
                <div class="space-y-1.5">
                    <AppLabel
                        for="last_name"
                        class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        {{ __('Last name') }}
                    </AppLabel>
                    <AppInputText
                        id="last_name"
                        v-model="form.last_name"
                        name="last_name"
                        type="text"
                        class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                        placeholder="Doe"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('last_name')
                        }"
                    />
                </div>
            </div>

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
                    name="email"
                    type="email"
                    class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                    autocomplete="email"
                    placeholder="you@example.com"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('email')
                    }"
                />
            </div>

            <!-- Phone field -->
            <div class="space-y-1.5">
                <AppLabel
                    for="phone"
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ __('Phone number') }}
                </AppLabel>
                <AppInputText
                    id="phone"
                    v-model="form.phone"
                    name="phone"
                    type="tel"
                    class="h-10 w-full rounded-lg border-neutral-300 bg-white text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500 dark:focus:border-primary-500"
                    placeholder="+880 1XXX-XXXXXX"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500': errorsFields.includes('phone')
                    }"
                />
            </div>

            <!-- Password fields -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <AppLabel
                        for="password"
                        class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        {{ __('Password') }}
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
                        {{ __('Confirm') }}
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
            </div>

            <!-- Terms notice -->
            <p class="text-xs leading-relaxed text-neutral-500 dark:text-neutral-400">
                {{ __('By creating an account, you agree to our') }}
                <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-400">{{ __('Terms of Service') }}</a>
                {{ __('and') }}
                <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-400">{{ __('Privacy Policy') }}</a>.
            </p>

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
                    {{ __('Creating account...') }}
                </span>
                <span v-else>
                    {{ __('Create account') }}
                </span>
            </button>
        </form>

        <!-- Login link -->
        <p class="mt-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
            {{ __('Already have an account?') }}
            <AppLink
                :href="route('auth.loginForm')"
                class="font-medium text-primary-600 transition-colors hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
            >
                {{ __('Sign in') }}
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
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: ''
})

function submitForm() {
    form.post(route('auth.register'))
}

const { errorsFields } = useFormErrors()
</script>
