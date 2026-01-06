<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <form>
        <!-- Personal Information -->
        <AppCard class="mb-4 w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-primary-2 flex h-8 w-8 items-center justify-center rounded-lg"
                    >
                        <i class="ri-user-line text-primary-11 text-lg"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Personal Information</span
                    >
                </div>
            </template>
            <template #content>
                <AppFormErrors class="mb-4" />

                <!-- Avatar Upload Section -->
                <div class="mb-6 flex items-start gap-6">
                    <div class="shrink-0">
                        <div
                            v-if="avatarPreview || props.customer?.avatar_url"
                            class="relative"
                        >
                            <img
                                :src="
                                    avatarPreview || props.customer?.avatar_url
                                "
                                alt="Avatar"
                                class="ring-neutral-3 h-24 w-24 rounded-full object-cover shadow-lg ring-2"
                            />
                        </div>
                        <div
                            v-else
                            class="ring-neutral-3 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 text-3xl font-bold text-white shadow-lg ring-2"
                        >
                            {{
                                form.first_name?.charAt(0)?.toUpperCase() || 'C'
                            }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <input
                            ref="avatarInput"
                            type="file"
                            accept="image/jpg,image/jpeg,image/png,image/webp,image/gif"
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
                            v-if="props.customer?.avatar_url && !avatarPreview"
                            class="text-neutral-10 flex cursor-pointer select-none items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                v-model="form.remove_avatar"
                                class="border-neutral-5 rounded"
                            />
                            {{ __('Remove photo') }}
                        </label>
                        <p
                            v-if="form.errors.avatar"
                            class="text-xs text-red-600"
                        >
                            {{ form.errors.avatar }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <AppLabel for="first_name">
                            {{ __('First Name') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('first_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="last_name">
                            {{ __('Last Name') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('last_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="gender">
                            {{ __('Gender') }}
                        </AppLabel>
                        <AppSelect
                            id="gender"
                            v-model="form.gender"
                            :options="genderOptions"
                            :class="{
                                'input-error': errorsFields.includes('gender')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="date_of_birth">
                            {{ __('Date of Birth') }}
                        </AppLabel>
                        <AppInputText
                            id="date_of_birth"
                            v-model="form.date_of_birth"
                            type="date"
                            :class="{
                                'input-error':
                                    errorsFields.includes('date_of_birth')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="email">
                            {{ __('Email') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="email"
                            v-model="form.email"
                            type="email"
                            :class="{
                                'input-error': errorsFields.includes('email')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="phone">
                            {{ __('Phone') }}
                        </AppLabel>
                        <AppInputText
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('phone')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="status">
                            {{ __('Status') }}
                        </AppLabel>
                        <AppSelect
                            id="status"
                            v-model="form.status"
                            :options="statusOptions"
                            :class="{
                                'input-error': errorsFields.includes('status')
                            }"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Address Information -->
        <AppCard class="mb-4 w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                    >
                        <i class="ri-map-pin-line text-lg text-blue-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Address Information</span
                    >
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="md:col-span-3">
                        <AppLabel for="address">
                            {{ __('Address') }}
                        </AppLabel>
                        <AppInputText
                            id="address"
                            v-model="form.address"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('address')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="city">
                            {{ __('City') }}
                        </AppLabel>
                        <AppInputText
                            id="city"
                            v-model="form.city"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('city')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="state">
                            {{ __('State') }}
                        </AppLabel>
                        <AppInputText
                            id="state"
                            v-model="form.state"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('state')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="postal_code">
                            {{ __('Postal Code') }}
                        </AppLabel>
                        <AppInputText
                            id="postal_code"
                            v-model="form.postal_code"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('postal_code')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="country">
                            {{ __('Country') }}
                        </AppLabel>
                        <AppInputText
                            id="country"
                            v-model="form.country"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('country')
                            }"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Identification Documents -->
        <AppCard class="mb-4 w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100"
                    >
                        <i class="ri-file-text-line text-lg text-amber-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Identification Documents</span
                    >
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- NID Section -->
                    <div
                        class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <i
                                class="ri-id-card-line text-lg text-amber-600"
                            ></i>
                            <span class="text-neutral-12 font-medium">{{
                                __('National ID')
                            }}</span>
                        </div>
                        <div class="mb-3">
                            <AppLabel for="nid_no">
                                {{ __('NID Number') }}
                            </AppLabel>
                            <AppInputText
                                id="nid_no"
                                v-model="form.nid_no"
                                type="text"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('nid_no')
                                }"
                            />
                        </div>

                        <!-- NID Front -->
                        <div class="mb-3">
                            <p class="text-neutral-10 mb-2 text-sm">
                                NID Front
                            </p>
                            <div
                                v-if="
                                    getExistingDocument('nid_front') &&
                                    !form.nid_front &&
                                    !form.remove_nid_front
                                "
                            >
                                <div
                                    class="bg-neutral-1 border-neutral-5 flex items-center gap-3 rounded-lg border p-3"
                                >
                                    <i
                                        :class="
                                            getDocumentIcon(
                                                getExistingDocument('nid_front')
                                                    .mime_type
                                            )
                                        "
                                        class="text-neutral-10 text-2xl"
                                    ></i>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-neutral-12 truncate text-sm font-medium"
                                        >
                                            {{
                                                getExistingDocument('nid_front')
                                                    .name
                                            }}
                                        </p>
                                        <p class="text-neutral-9 text-xs">
                                            {{
                                                getExistingDocument('nid_front')
                                                    .size_formatted
                                            }}
                                        </p>
                                    </div>
                                    <a
                                        :href="
                                            getExistingDocument('nid_front').url
                                        "
                                        target="_blank"
                                        class="btn btn-icon btn-secondary btn-sm"
                                    >
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                                <label
                                    class="text-neutral-10 mt-2 flex cursor-pointer items-center gap-2 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.remove_nid_front"
                                        class="border-neutral-5 rounded"
                                    />
                                    {{ __('Remove document') }}
                                </label>
                            </div>
                            <div v-else>
                                <input
                                    ref="nidFrontInput"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="hidden"
                                    @change="
                                        (e) =>
                                            handleDocumentChange(e, 'nid_front')
                                    "
                                />
                                <AppButton
                                    type="button"
                                    class="btn btn-secondary btn-sm w-full"
                                    @click="$refs.nidFrontInput.click()"
                                >
                                    <i class="ri-upload-2-line mr-1"></i>
                                    {{
                                        form.nid_front
                                            ? 'Change File'
                                            : 'Upload NID Front'
                                    }}
                                </AppButton>
                                <p
                                    v-if="form.nid_front"
                                    class="mt-2 text-xs text-emerald-600"
                                >
                                    <i class="ri-check-line mr-1"></i
                                    >{{ form.nid_front.name }}
                                </p>
                            </div>
                            <p
                                v-if="form.errors.nid_front"
                                class="mt-2 text-xs text-red-600"
                            >
                                {{ form.errors.nid_front }}
                            </p>
                        </div>

                        <!-- NID Back -->
                        <div>
                            <p class="text-neutral-10 mb-2 text-sm">NID Back</p>
                            <div
                                v-if="
                                    getExistingDocument('nid_back') &&
                                    !form.nid_back &&
                                    !form.remove_nid_back
                                "
                            >
                                <div
                                    class="bg-neutral-1 border-neutral-5 flex items-center gap-3 rounded-lg border p-3"
                                >
                                    <i
                                        :class="
                                            getDocumentIcon(
                                                getExistingDocument('nid_back')
                                                    .mime_type
                                            )
                                        "
                                        class="text-neutral-10 text-2xl"
                                    ></i>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-neutral-12 truncate text-sm font-medium"
                                        >
                                            {{
                                                getExistingDocument('nid_back')
                                                    .name
                                            }}
                                        </p>
                                        <p class="text-neutral-9 text-xs">
                                            {{
                                                getExistingDocument('nid_back')
                                                    .size_formatted
                                            }}
                                        </p>
                                    </div>
                                    <a
                                        :href="
                                            getExistingDocument('nid_back').url
                                        "
                                        target="_blank"
                                        class="btn btn-icon btn-secondary btn-sm"
                                    >
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                                <label
                                    class="text-neutral-10 mt-2 flex cursor-pointer items-center gap-2 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.remove_nid_back"
                                        class="border-neutral-5 rounded"
                                    />
                                    {{ __('Remove document') }}
                                </label>
                            </div>
                            <div v-else>
                                <input
                                    ref="nidBackInput"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="hidden"
                                    @change="
                                        (e) =>
                                            handleDocumentChange(e, 'nid_back')
                                    "
                                />
                                <AppButton
                                    type="button"
                                    class="btn btn-secondary btn-sm w-full"
                                    @click="$refs.nidBackInput.click()"
                                >
                                    <i class="ri-upload-2-line mr-1"></i>
                                    {{
                                        form.nid_back
                                            ? 'Change File'
                                            : 'Upload NID Back'
                                    }}
                                </AppButton>
                                <p
                                    v-if="form.nid_back"
                                    class="mt-2 text-xs text-emerald-600"
                                >
                                    <i class="ri-check-line mr-1"></i
                                    >{{ form.nid_back.name }}
                                </p>
                            </div>
                            <p
                                v-if="form.errors.nid_back"
                                class="mt-2 text-xs text-red-600"
                            >
                                {{ form.errors.nid_back }}
                            </p>
                        </div>
                    </div>

                    <!-- Passport Section -->
                    <div
                        class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <i
                                class="ri-passport-line text-lg text-blue-600"
                            ></i>
                            <span class="text-neutral-12 font-medium">{{
                                __('Passport')
                            }}</span>
                        </div>
                        <div class="mb-3">
                            <AppLabel for="passport_no">
                                {{ __('Passport Number') }}
                            </AppLabel>
                            <AppInputText
                                id="passport_no"
                                v-model="form.passport_no"
                                type="text"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('passport_no')
                                }"
                            />
                        </div>
                        <div class="mb-3">
                            <AppLabel for="passport_expiry">
                                {{ __('Passport Expiry') }}
                            </AppLabel>
                            <AppInputText
                                id="passport_expiry"
                                v-model="form.passport_expiry"
                                type="date"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('passport_expiry')
                                }"
                            />
                        </div>
                        <div
                            v-if="
                                getExistingDocument('passport') &&
                                !form.passport &&
                                !form.remove_passport
                            "
                        >
                            <div
                                class="bg-neutral-1 border-neutral-5 flex items-center gap-3 rounded-lg border p-3"
                            >
                                <i
                                    :class="
                                        getDocumentIcon(
                                            getExistingDocument('passport')
                                                .mime_type
                                        )
                                    "
                                    class="text-neutral-10 text-2xl"
                                ></i>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-neutral-12 truncate text-sm font-medium"
                                    >
                                        {{
                                            getExistingDocument('passport').name
                                        }}
                                    </p>
                                    <p class="text-neutral-9 text-xs">
                                        {{
                                            getExistingDocument('passport')
                                                .size_formatted
                                        }}
                                    </p>
                                </div>
                                <a
                                    :href="getExistingDocument('passport').url"
                                    target="_blank"
                                    class="btn btn-icon btn-secondary btn-sm"
                                >
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                            <label
                                class="text-neutral-10 mt-2 flex cursor-pointer items-center gap-2 text-sm"
                            >
                                <input
                                    type="checkbox"
                                    v-model="form.remove_passport"
                                    class="border-neutral-5 rounded"
                                />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input
                                ref="passportInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden"
                                @change="
                                    (e) => handleDocumentChange(e, 'passport')
                                "
                            />
                            <AppButton
                                type="button"
                                class="btn btn-secondary btn-sm w-full"
                                @click="$refs.passportInput.click()"
                            >
                                <i class="ri-upload-2-line mr-1"></i>
                                {{
                                    form.passport
                                        ? 'Change File'
                                        : 'Upload Passport'
                                }}
                            </AppButton>
                            <p
                                v-if="form.passport"
                                class="mt-2 text-xs text-emerald-600"
                            >
                                <i class="ri-check-line mr-1"></i
                                >{{ form.passport.name }}
                            </p>
                        </div>
                        <p
                            v-if="form.errors.passport"
                            class="mt-2 text-xs text-red-600"
                        >
                            {{ form.errors.passport }}
                        </p>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3">
            <AppButton
                type="button"
                class="btn btn-secondary"
                @click="$inertia.visit(route('customer.index'))"
            >
                {{ __('Cancel') }}
            </AppButton>
            <AppButton
                type="button"
                class="btn btn-primary"
                @click="submitForm"
                :disabled="form.processing"
            >
                <i
                    v-if="form.processing"
                    class="ri-loader-4-line mr-1 animate-spin"
                ></i>
                {{ __('Save') }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    customer: {
        type: Object,
        default: null
    }
})

const { title } = useTitle(props.customer ? 'Edit Customer' : 'Create Customer')
const { isCreate, isEdit } = useFormContext()
const { errorsFields } = useFormErrors()

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Customers', href: route('customer.index') },
    { label: props.customer ? 'Edit Customer' : 'Create Customer', last: true }
]

const genderOptions = [
    { value: '', label: 'Select Gender' },
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' }
]

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'blocked', label: 'Blocked' }
]

const form = useForm({
    first_name: props.customer?.first_name || '',
    last_name: props.customer?.last_name || '',
    gender: props.customer?.gender || '',
    date_of_birth: props.customer?.date_of_birth || '',
    email: props.customer?.email || '',
    phone: props.customer?.phone || '',
    address: props.customer?.address || '',
    city: props.customer?.city || '',
    state: props.customer?.state || '',
    postal_code: props.customer?.postal_code || '',
    country: props.customer?.country || '',
    nid_no: props.customer?.nid_no || '',
    passport_no: props.customer?.passport_no || '',
    passport_expiry: props.customer?.passport_expiry || '',
    status: props.customer?.status || 'active',
    avatar: null,
    remove_avatar: false,
    nid_front: null,
    remove_nid_front: false,
    nid_back: null,
    remove_nid_back: false,
    passport: null,
    remove_passport: false
})

const avatarPreview = ref(null)

const handleAvatarChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.avatar = file
        form.remove_avatar = false
        avatarPreview.value = URL.createObjectURL(file)
    }
}

const handleDocumentChange = (event, field) => {
    const file = event.target.files[0]
    if (file) {
        form[field] = file
        form[`remove_${field}`] = false
    }
}

const getExistingDocument = (type) => {
    if (!props.customer?.documents) return null
    return props.customer.documents.find((doc) => doc.type === type)
}

const getDocumentIcon = (mimeType) => {
    if (!mimeType) return 'ri-file-line text-neutral-500'
    if (mimeType.includes('pdf')) return 'ri-file-pdf-line text-red-500'
    if (mimeType.includes('image')) return 'ri-image-line text-blue-500'
    return 'ri-file-line text-neutral-500'
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('customer.store'))
    }

    if (isEdit.value) {
        form.post(route('customer.update', props.customer.uuid), {
            _method: 'PUT'
        })
    }
}
</script>
