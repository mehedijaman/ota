<template>
    <AppCard>
        <template #title>
            <div class="flex items-center gap-2">
                <i class="ri-contacts-line text-lg"></i>
                Contact & Social Media
            </div>
        </template>
        <template #description>
            Manage your contact information and social media links.
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <!-- Addresses -->
                <div class="mb-6">
                    <div class="mb-3 flex items-center justify-between">
                        <AppLabel>Addresses</AppLabel>
                        <button
                            type="button"
                            class="text-primary-600 hover:text-primary-700 text-sm"
                            @click="addAddress"
                        >
                            <i class="ri-add-line"></i> Add Address
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div
                            v-for="(addr, index) in form.addresses"
                            :key="index"
                            class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <span
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Address {{ index + 1 }}
                                </span>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700"
                                    @click="removeAddress(index)"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <div>
                                    <AppInputText
                                        v-model="addr.label"
                                        placeholder="Label (e.g., Head Office)"
                                    />
                                </div>
                                <div>
                                    <AppInputText
                                        v-model="addr.address"
                                        placeholder="Full address"
                                    />
                                </div>
                            </div>
                            <div class="mt-3">
                                <AppInputText
                                    v-model="addr.google_map"
                                    placeholder="Google Maps embed URL or link"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="ri-map-pin-line mr-1"></i>
                                    Paste Google Maps embed URL for this
                                    location
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phones -->
                <div class="mb-6">
                    <div class="mb-3 flex items-center justify-between">
                        <AppLabel>Phone Numbers</AppLabel>
                        <button
                            type="button"
                            class="text-primary-600 hover:text-primary-700 text-sm"
                            @click="addPhone"
                        >
                            <i class="ri-add-line"></i> Add Phone
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="(phone, index) in form.phones"
                            :key="index"
                            class="flex gap-3"
                        >
                            <div class="w-1/3">
                                <AppInputText
                                    v-model="phone.label"
                                    placeholder="Label (e.g., Hotline)"
                                />
                            </div>
                            <div class="flex-1">
                                <AppInputText
                                    v-model="phone.number"
                                    placeholder="Phone number"
                                />
                            </div>
                            <button
                                type="button"
                                class="text-red-500 hover:text-red-700"
                                @click="removePhone(index)"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Emails -->
                <div class="mb-6">
                    <div class="mb-3 flex items-center justify-between">
                        <AppLabel>Email Addresses</AppLabel>
                        <button
                            type="button"
                            class="text-primary-600 hover:text-primary-700 text-sm"
                            @click="addEmail"
                        >
                            <i class="ri-add-line"></i> Add Email
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="(email, index) in form.emails"
                            :key="index"
                            class="flex gap-3"
                        >
                            <div class="w-1/3">
                                <AppInputText
                                    v-model="email.label"
                                    placeholder="Label (e.g., Support)"
                                />
                            </div>
                            <div class="flex-1">
                                <AppInputText
                                    v-model="email.email"
                                    type="email"
                                    placeholder="Email address"
                                />
                            </div>
                            <button
                                type="button"
                                class="text-red-500 hover:text-red-700"
                                @click="removeEmail(index)"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp & Telegram -->
                <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <AppLabel for="whatsapp">
                            <i class="ri-whatsapp-line mr-1 text-green-500"></i>
                            WhatsApp Number
                        </AppLabel>
                        <AppInputText
                            id="whatsapp"
                            v-model="form.whatsapp"
                            placeholder="+880 1700-000000"
                        />
                    </div>
                    <div>
                        <AppLabel for="telegram">
                            <i class="ri-telegram-line mr-1 text-blue-400"></i>
                            Telegram
                        </AppLabel>
                        <AppInputText
                            id="telegram"
                            v-model="form.telegram"
                            placeholder="@username or +880 1700-000000"
                        />
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="border-t pt-6 dark:border-gray-700">
                    <h4
                        class="mb-4 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Social Media Links
                    </h4>
                    <div
                        class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div>
                            <AppLabel for="facebook">
                                <i
                                    class="ri-facebook-fill mr-1 text-blue-600"
                                ></i>
                                Facebook
                            </AppLabel>
                            <AppInputText
                                id="facebook"
                                v-model="form.facebook"
                                placeholder="https://facebook.com/..."
                            />
                        </div>
                        <div>
                            <AppLabel for="twitter">
                                <i class="ri-twitter-x-fill mr-1"></i>
                                Twitter / X
                            </AppLabel>
                            <AppInputText
                                id="twitter"
                                v-model="form.twitter"
                                placeholder="https://twitter.com/..."
                            />
                        </div>
                        <div>
                            <AppLabel for="instagram">
                                <i
                                    class="ri-instagram-line mr-1 text-pink-500"
                                ></i>
                                Instagram
                            </AppLabel>
                            <AppInputText
                                id="instagram"
                                v-model="form.instagram"
                                placeholder="https://instagram.com/..."
                            />
                        </div>
                        <div>
                            <AppLabel for="youtube">
                                <i
                                    class="ri-youtube-fill mr-1 text-red-600"
                                ></i>
                                YouTube
                            </AppLabel>
                            <AppInputText
                                id="youtube"
                                v-model="form.youtube"
                                placeholder="https://youtube.com/..."
                            />
                        </div>
                        <div>
                            <AppLabel for="linkedin">
                                <i
                                    class="ri-linkedin-fill mr-1 text-blue-700"
                                ></i>
                                LinkedIn
                            </AppLabel>
                            <AppInputText
                                id="linkedin"
                                v-model="form.linkedin"
                                placeholder="https://linkedin.com/..."
                            />
                        </div>
                        <div>
                            <AppLabel for="pinterest">
                                <i
                                    class="ri-pinterest-fill mr-1 text-red-700"
                                ></i>
                                Pinterest
                            </AppLabel>
                            <AppInputText
                                id="pinterest"
                                v-model="form.pinterest"
                                placeholder="https://pinterest.com/..."
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex justify-end">
                <AppButton
                    class="btn btn-primary"
                    :loading="form.processing"
                    @click="submitForm"
                >
                    <i class="ri-save-line mr-1"></i>
                    Save Changes
                </AppButton>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const contact = props.settings.contact ?? {}
const social = props.settings.social ?? {}

const form = useForm({
    _method: 'PUT',
    addresses: contact.addresses ?? [
        { label: '', address: '', google_map: '' }
    ],
    phones: contact.phones ?? [{ label: '', number: '' }],
    emails: contact.emails ?? [{ label: '', email: '' }],
    whatsapp: contact.whatsapp ?? '',
    telegram: contact.telegram ?? '',
    facebook: social.facebook ?? '',
    twitter: social.twitter ?? '',
    instagram: social.instagram ?? '',
    youtube: social.youtube ?? '',
    linkedin: social.linkedin ?? '',
    pinterest: social.pinterest ?? ''
})

const addAddress = () => {
    form.addresses.push({ label: '', address: '', google_map: '' })
}

const removeAddress = (index) => {
    if (form.addresses.length > 1) {
        form.addresses.splice(index, 1)
    }
}

const addPhone = () => {
    form.phones.push({ label: '', number: '' })
}

const removePhone = (index) => {
    if (form.phones.length > 1) {
        form.phones.splice(index, 1)
    }
}

const addEmail = () => {
    form.emails.push({ label: '', email: '' })
}

const removeEmail = (index) => {
    if (form.emails.length > 1) {
        form.emails.splice(index, 1)
    }
}

const submitForm = () => {
    form.post(route('setting.update'), {
        preserveScroll: true
    })
}
</script>
