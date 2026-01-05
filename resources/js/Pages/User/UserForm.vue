<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('user.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                {{ __('Back') }}
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="max-w-3xl">
        <AppFormErrors class="mb-6" />

        <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Employee Selection -->
            <div v-if="employees && employees.length > 0 && isCreate" class="rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                            <i class="ri-links-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">{{ __('Link to Employee') }}</h3>
                            <p class="text-xs text-neutral-10">Auto-fill user info from employee record</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <AppSelect
                        id="employee_id"
                        v-model="form.employee_id"
                        :options="employeeOptions"
                        placeholder="Select an employee (optional)"
                        @update:modelValue="handleEmployeeSelect"
                    />
                    <p class="mt-2 text-xs text-neutral-9">
                        <i class="ri-information-line mr-1"></i>
                        Name and email will be auto-filled when you select an employee
                    </p>
                </div>
            </div>

            <!-- Profile & Account Info -->
            <div class="overflow-hidden rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25">
                            <i class="ri-user-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">{{ __('Account Information') }}</h3>
                            <p class="text-xs text-neutral-10">User profile and login details</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-5">
                    <!-- Avatar Upload -->
                    <div>
                        <AppLabel class="mb-2">{{ __('Profile Photo') }}</AppLabel>
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div
                                    v-if="avatarPreview || props.user?.avatar_url"
                                    class="h-20 w-20 overflow-hidden rounded-full ring-4 ring-neutral-3 shadow-lg"
                                >
                                    <img
                                        :src="avatarPreview || props.user?.avatar_url"
                                        alt="Avatar"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div
                                    v-else
                                    class="flex h-20 w-20 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-indigo-600 text-2xl font-bold text-white ring-4 ring-neutral-3 shadow-lg"
                                >
                                    {{ form.name?.charAt(0)?.toUpperCase() || 'U' }}
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <input
                                    ref="avatarInput"
                                    type="file"
                                    accept="image/jpg,image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleAvatarChange"
                                />
                                <AppButton
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    @click="$refs.avatarInput.click()"
                                >
                                    <i class="ri-upload-2-line mr-1"></i>
                                    {{ __('Choose Photo') }}
                                </AppButton>
                                <label
                                    v-if="props.user?.avatar_url && !avatarPreview"
                                    class="flex items-center gap-2 text-sm text-neutral-10 cursor-pointer select-none"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.remove_avatar"
                                        class="rounded border-neutral-5"
                                    />
                                    {{ __('Remove photo') }}
                                </label>
                                <p v-if="form.errors.avatar" class="text-xs text-red-600">
                                    {{ form.errors.avatar }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Name & Email -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <AppLabel for="name" required>{{ __('Full Name') }}</AppLabel>
                            <AppInputText
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter full name"
                                :class="{ 'input-error': errorsFields.includes('name') }"
                                autocomplete="off"
                            />
                        </div>
                        <div>
                            <AppLabel for="email" required>{{ __('Email Address') }}</AppLabel>
                            <AppInputText
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="user@example.com"
                                :class="{ 'input-error': errorsFields.includes('email') }"
                                autocomplete="off"
                            />
                        </div>
                    </div>

                    <!-- Password with Generator -->
                    <div>
                        <AppLabel for="password" :required="isCreate">{{ __('Password') }}</AppLabel>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <AppInputText
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    :class="{ 'input-error': errorsFields.includes('password'), 'pr-10': true }"
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-9 hover:text-neutral-11"
                                    @click="showPassword = !showPassword"
                                >
                                    <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                                </button>
                            </div>
                            <AppButton
                                type="button"
                                class="btn btn-secondary shrink-0"
                                @click="generatePassword"
                            >
                                <i class="ri-key-2-line mr-1"></i>
                                {{ __('Generate') }}
                            </AppButton>
                        </div>
                        <p v-if="!isCreate" class="mt-1.5 text-xs text-neutral-9">
                            <i class="ri-information-line mr-1"></i>
                            Leave blank to keep current password
                        </p>
                        <p v-if="generatedPassword" class="mt-2 flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                            <i class="ri-check-line"></i>
                            Password generated successfully
                            <button type="button" class="ml-auto text-emerald-600 hover:text-emerald-700" @click="copyPassword">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Roles -->
            <div v-if="roles && roles.length > 0" class="rounded-xl border border-neutral-6 bg-neutral-1 shadow-sm">
                <div class="border-b border-neutral-6 bg-neutral-2 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-purple-500 to-violet-600 shadow-lg shadow-purple-500/25">
                            <i class="ri-shield-user-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-12">{{ __('Roles & Permissions') }}</h3>
                            <p class="text-xs text-neutral-10">Assign roles to this user</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <AppMultiSelect
                        id="role_ids"
                        v-model="form.role_ids"
                        :options="roles"
                        option-label="name"
                        option-value="id"
                        :placeholder="__('Select Roles')"
                        :class="{ 'input-error': errorsFields.includes('role_ids') }"
                    />
                </div>
            </div>

            <!-- Linked Employee Info (Edit Mode) -->
            <div v-if="!isCreate && props.user?.linked_employee" class="rounded-xl border border-primary-200 bg-primary-50 shadow-sm">
                <div class="border-b border-primary-200 bg-primary-100/50 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-500 shadow-lg">
                            <i class="ri-links-line text-lg text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-primary-800">{{ __('Linked Employee') }}</h3>
                            <p class="text-xs text-primary-600">This user account is linked to an employee profile</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100">
                                <i class="ri-user-line text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary-500">{{ __('Name') }}</p>
                                <p class="font-semibold text-primary-800">{{ props.user.linked_employee.name }}</p>
                            </div>
                        </div>
                        <div v-if="props.user.linked_employee.code" class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100">
                                <i class="ri-hashtag text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary-500">{{ __('Employee Code') }}</p>
                                <p class="font-semibold text-primary-800">{{ props.user.linked_employee.code }}</p>
                            </div>
                        </div>
                        <div v-if="props.user.linked_employee.designation" class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100">
                                <i class="ri-briefcase-line text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary-500">{{ __('Designation') }}</p>
                                <p class="font-semibold text-primary-800">{{ props.user.linked_employee.designation }}</p>
                            </div>
                        </div>
                        <div v-if="props.user.linked_employee.department" class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100">
                                <i class="ri-building-line text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary-500">{{ __('Department') }}</p>
                                <p class="font-semibold text-primary-800">{{ props.user.linked_employee.department }}</p>
                            </div>
                        </div>
                        <div v-if="props.user.linked_employee.email" class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100">
                                <i class="ri-mail-line text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary-500">{{ __('Email') }}</p>
                                <p class="font-semibold text-primary-800">{{ props.user.linked_employee.email }}</p>
                            </div>
                        </div>
                        <div v-if="props.user.linked_employee.phone" class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100">
                                <i class="ri-phone-line text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-primary-500">{{ __('Phone') }}</p>
                                <p class="font-semibold text-primary-800">{{ props.user.linked_employee.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <AppButton
                    type="button"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('user.index'))"
                    :disabled="form.processing"
                >
                    <i class="ri-close-line mr-1"></i>
                    {{ __('Cancel') }}
                </AppButton>
                <AppButton
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <i class="ri-save-3-line mr-1"></i>
                    {{ form.processing ? __('Saving...') : __('Save User') }}
                </AppButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('User')

const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    roles: {
        type: Array,
        default: () => []
    },
    employees: {
        type: Array,
        default: () => []
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Users', href: route('user.index') },
    { label: title, last: true }
]

// Transform employees for select dropdown
const employeeOptions = computed(() => {
    return [
        { value: '', label: 'No employee link' },
        ...props.employees
    ]
})

const form = useForm({
    name: props.user ? props.user.name : '',
    email: props.user ? props.user.email : '',
    password: '',
    role_ids: props.user && props.user.role_ids ? props.user.role_ids : [],
    avatar: null,
    remove_avatar: false,
    employee_id: props.user?.employee_id || ''
})

const avatarPreview = ref(null)
const showPassword = ref(false)
const generatedPassword = ref(false)

const handleAvatarChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.avatar = file
        form.remove_avatar = false
        avatarPreview.value = URL.createObjectURL(file)
    }
}

// Auto-fill name and email when employee is selected
const handleEmployeeSelect = (employeeId) => {
    if (!employeeId) return
    
    const selectedEmployee = props.employees.find(e => e.value === employeeId)
    if (selectedEmployee) {
        form.name = selectedEmployee.label
        form.email = selectedEmployee.email || ''
    }
}

// Password generator
const generatePassword = () => {
    const length = 12
    const lowercase = 'abcdefghijklmnopqrstuvwxyz'
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    const numbers = '0123456789'
    const symbols = '!@#$%^&*'
    const allChars = lowercase + uppercase + numbers + symbols
    
    let password = ''
    // Ensure at least one of each type
    password += lowercase.charAt(Math.floor(Math.random() * lowercase.length))
    password += uppercase.charAt(Math.floor(Math.random() * uppercase.length))
    password += numbers.charAt(Math.floor(Math.random() * numbers.length))
    password += symbols.charAt(Math.floor(Math.random() * symbols.length))
    
    // Fill remaining characters
    for (let i = password.length; i < length; i++) {
        password += allChars.charAt(Math.floor(Math.random() * allChars.length))
    }
    
    // Shuffle the password
    password = password.split('').sort(() => Math.random() - 0.5).join('')
    
    form.password = password
    showPassword.value = true
    generatedPassword.value = true
    
    // Hide success message after 3 seconds
    setTimeout(() => {
        generatedPassword.value = false
    }, 3000)
}

const copyPassword = () => {
    navigator.clipboard.writeText(form.password)
}

const { isCreate } = useFormContext()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('user.store'), {
            forceFormData: true
        })
    } else {
        form.post(route('user.update', props.user.uuid), {
            forceFormData: true,
            headers: { 'X-HTTP-Method-Override': 'PUT' }
        })
    }
}

const { errorsFields } = useFormErrors()
</script>
