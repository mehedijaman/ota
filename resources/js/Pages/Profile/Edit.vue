<template>
    <Head title="Edit Profile" />
    
    <div class="space-y-6">
        <div class="bg-white dark:bg-neutral-2 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-neutral-12 mb-4">Profile Information</h2>
            
            <form @submit.prevent="updateProfile" class="space-y-6">
                <AppFormErrors />

                <!-- Avatar Upload -->
                <div>
                    <AppLabel>Profile Photo</AppLabel>
                    <div class="mt-2 flex items-center gap-4">
                        <div class="h-20 w-20 rounded-full overflow-hidden bg-primary-9 flex items-center justify-center text-white text-2xl font-semibold">
                            <img
                                v-if="avatarPreview || avatarUrl"
                                :src="avatarPreview || avatarUrl"
                                alt="Avatar"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>
                                {{ getInitials($page.props.auth.user?.name) }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <label class="btn btn-sm btn-primary cursor-pointer">
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleAvatarChange"
                                />
                                Change Photo
                            </label>
                            <AppButton
                                v-if="avatarUrl || avatarPreview"
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                @click="removeAvatar"
                            >
                                Remove
                            </AppButton>
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <AppLabel for="name">Name</AppLabel>
                    <AppInputText
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1"
                        required
                    />
                </div>

                <!-- Email -->
                <div>
                    <AppLabel for="email">Email</AppLabel>
                    <AppInputText
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1"
                        required
                    />
                    <p v-if="mustVerifyEmail && !$page.props.auth.user?.email_verified_at" class="text-sm text-amber-600 mt-2">
                        Your email address is unverified.
                        <AppLink :href="route('verification.notice')" class="underline">
                            Click here to re-send the verification email.
                        </AppLink>
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <AppButton
                        type="submit"
                        class="btn btn-primary"
                        :disabled="form.processing"
                    >
                        Save Changes
                    </AppButton>
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                        Saved successfully!
                    </p>
                </div>
            </form>
        </div>

        <!-- Delete Account Section -->
        <div class="bg-white dark:bg-neutral-2 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-neutral-12 mb-2">Delete Account</h2>
            <p class="text-sm text-neutral-11 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>
            
            <AppButton
                type="button"
                class="btn btn-danger"
                @click="confirmingUserDeletion = true"
            >
                Delete Account
            </AppButton>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <AppModal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-neutral-12 mb-4">
                Are you sure you want to delete your account?
            </h3>
            <p class="text-sm text-neutral-11 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <form @submit.prevent="deleteAccount">
                <AppLabel for="password">Password</AppLabel>
                <AppInputText
                    id="password"
                    v-model="deleteForm.password"
                    type="password"
                    class="mt-1 mb-4"
                    required
                />

                <div class="flex justify-end gap-3">
                    <AppButton
                        type="button"
                        class="btn btn-outline"
                        @click="confirmingUserDeletion = false"
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        class="btn btn-danger"
                        :disabled="deleteForm.processing"
                    >
                        Delete Account
                    </AppButton>
                </div>
            </form>
        </div>
    </AppModal>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({
    layout: AuthenticatedLayout
})

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    avatarUrl: String
})

const form = useForm({
    name: usePage().props.auth.user?.name || '',
    email: usePage().props.auth.user?.email || '',
    avatar: null,
    remove_avatar: false
})

const deleteForm = useForm({
    password: ''
})

const avatarPreview = ref(null)
const confirmingUserDeletion = ref(false)

const handleAvatarChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.avatar = file
        form.remove_avatar = false
        
        const reader = new FileReader()
        reader.onload = (e) => {
            avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const removeAvatar = () => {
    form.avatar = null
    form.remove_avatar = true
    avatarPreview.value = null
}

const updateProfile = () => {
    form.transform((data) => ({
        ...data,
        _method: 'patch'
    })).post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            avatarPreview.value = null
        }
    })
}

const deleteAccount = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingUserDeletion.value = false
        }
    })
}

const getInitials = (name) => {
    if (!name) return 'U'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.substring(0, 2).toUpperCase()
}
</script>
