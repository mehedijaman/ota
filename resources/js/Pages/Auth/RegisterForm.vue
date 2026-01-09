<template>
    <Head title="Create Account"></Head>
    <AppAuthShell>
        <!-- Welcome text -->
        <div class="mb-8 text-center">
            <h1
                class="text-2xl font-bold tracking-tight text-neutral-12 md:text-3xl"
            >
                {{ __('Create your account') }}
            </h1>
            <p class="mt-2 text-neutral-11">
                {{ __('Register to book your dream tours') }}
            </p>
        </div>

        <!-- Registration form -->
        <form @submit.prevent="submitForm" class="space-y-5">
            <AppFormErrors class="mb-4" />

            <!-- Name fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <AppLabel
                        for="first_name"
                        class="text-sm font-medium text-neutral-12"
                    >
                        {{ __('First Name') }}
                    </AppLabel>
                    <AppInputText
                        id="first_name"
                        v-model="form.first_name"
                        name="first_name"
                        type="text"
                        class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                        placeholder="John"
                        :class="{
                            'input-error': errorsFields.includes('first_name')
                        }"
                    />
                </div>
                <div class="space-y-2">
                    <AppLabel
                        for="last_name"
                        class="text-sm font-medium text-neutral-12"
                    >
                        {{ __('Last Name') }}
                    </AppLabel>
                    <AppInputText
                        id="last_name"
                        v-model="form.last_name"
                        name="last_name"
                        type="text"
                        class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                        placeholder="Doe"
                        :class="{
                            'input-error': errorsFields.includes('last_name')
                        }"
                    />
                </div>
            </div>

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

            <!-- Phone field -->
            <div class="space-y-2">
                <AppLabel
                    for="phone"
                    class="text-sm font-medium text-neutral-12"
                >
                    {{ __('Phone Number') }}
                </AppLabel>
                <AppInputText
                    id="phone"
                    v-model="form.phone"
                    name="phone"
                    type="tel"
                    class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                    placeholder="+880 1XXX-XXXXXX"
                    :class="{
                        'input-error': errorsFields.includes('phone')
                    }"
                />
            </div>

            <!-- Password field -->
            <div class="space-y-2">
                <AppLabel
                    for="password"
                    class="text-sm font-medium text-neutral-12"
                >
                    {{ __('Password') }}
                </AppLabel>
                <AppInputPassword
                    id="password"
                    v-model="form.password"
                    name="password"
                    class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                    autocomplete="new-password"
                    :class="{
                        'input-error': errorsFields.includes('password')
                    }"
                />
            </div>

            <!-- Confirm Password field -->
            <div class="space-y-2">
                <AppLabel
                    for="password_confirmation"
                    class="text-sm font-medium text-neutral-12"
                >
                    {{ __('Confirm Password') }}
                </AppLabel>
                <AppInputPassword
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    name="password_confirmation"
                    class="h-12 w-full rounded-lg border-neutral-6 bg-white px-4 text-base shadow-sm transition-all duration-200 focus:border-primary-9 focus:ring-2 focus:ring-primary-9/20 dark:border-neutral-7 dark:bg-neutral-3"
                    autocomplete="new-password"
                    :class="{
                        'input-error': errorsFields.includes('password_confirmation')
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
                    {{ __('Creating account...') }}
                </span>
                <span v-else>{{ __('Create Account') }}</span>
            </AppButton>

            <!-- Login link -->
            <p class="text-center text-sm text-neutral-11">
                {{ __('Already have an account?') }}
                <AppLink
                    :href="route('auth.loginForm')"
                    class="font-medium text-primary-9 transition-colors hover:text-primary-11"
                >
                    {{ __('Sign in') }}
                </AppLink>
            </p>
        </form>
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
