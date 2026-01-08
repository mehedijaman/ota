<template>
    <AppCard class="w-full">
        <template #title>Contact Settings</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Dynamic Address Fields -->
                <div class="col-span-2">
                    <AppLabel>{{ __('Addresses') }}</AppLabel>
                    <div
                        v-for="(address, index) in form.addresses"
                        :key="index"
                        class="mb-4 flex items-center gap-2"
                    >
                        <AppInputText
                            v-model="address.value"
                            type="text"
                            :placeholder="`Address ${index + 1}`"
                        />
                        <button
                            type="button"
                            @click="removeAddress(index)"
                            class="text-red-500 hover:text-red-700"
                        >
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <button
                        type="button"
                        @click="addAddress"
                        class="btn btn-secondary"
                    >
                        <i class="ri-add-line"></i> Add Address
                    </button>
                </div>

                <!-- Dynamic Phone Fields -->
                <div class="col-span-2">
                    <AppLabel>{{ __('Phones') }}</AppLabel>
                    <div
                        v-for="(phone, index) in form.phones"
                        :key="index"
                        class="mb-4 flex items-center gap-2"
                    >
                        <AppInputText
                            v-model="phone.value"
                            type="text"
                            :placeholder="`Phone ${index + 1}`"
                        />
                        <button
                            type="button"
                            @click="removePhone(index)"
                            class="text-red-500 hover:text-red-700"
                        >
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <button
                        type="button"
                        @click="addPhone"
                        class="btn btn-secondary"
                    >
                        <i class="ri-add-line"></i> Add Phone
                    </button>
                </div>

                <!-- Dynamic Email Fields -->
                <div class="col-span-2">
                    <AppLabel>{{ __('Emails') }}</AppLabel>
                    <div
                        v-for="(email, index) in form.emails"
                        :key="index"
                        class="mb-4 flex items-center gap-2"
                    >
                        <AppInputText
                            v-model="email.value"
                            type="text"
                            :placeholder="`Email ${index + 1}`"
                        />
                        <button
                            type="button"
                            @click="removeEmail(index)"
                            class="text-red-500 hover:text-red-700"
                        >
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <button
                        type="button"
                        @click="addEmail"
                        class="btn btn-secondary"
                    >
                        <i class="ri-add-line"></i> Add Email
                    </button>
                </div>

                <!-- Static Fields -->
                <div>
                    <AppLabel for="google_map">{{ __('Google Map') }}</AppLabel>
                    <AppInputText
                        v-model="form.google_map"
                        id="google_map"
                        type="text"
                        placeholder="Google Map"
                    />
                </div>

                <div>
                    <AppLabel for="whatsapp">{{ __('Whatsapp') }}</AppLabel>
                    <AppInputText
                        v-model="form.whatsapp"
                        id="whatsapp"
                        type="text"
                        placeholder="Whatsapp"
                    />
                </div>

                <div>
                    <AppLabel for="facebook">{{ __('Facebook') }}</AppLabel>
                    <AppInputText
                        v-model="form.facebook"
                        id="facebook"
                        type="text"
                        placeholder="Facebook"
                    />
                </div>

                <div>
                    <AppLabel for="twitter">{{ __('Twitter') }}</AppLabel>
                    <AppInputText
                        v-model="form.twitter"
                        id="twitter"
                        type="text"
                        placeholder="Twitter"
                    />
                </div>

                <div>
                    <AppLabel for="instagram">{{ __('Instagram') }}</AppLabel>
                    <AppInputText
                        v-model="form.instagram"
                        id="instagram"
                        type="text"
                        placeholder="Instagram"
                    />
                </div>

                <div>
                    <AppLabel for="youtube">{{ __('Youtube') }}</AppLabel>
                    <AppInputText
                        v-model="form.youtube"
                        id="youtube"
                        type="text"
                        placeholder="Youtube"
                    />
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('Update Setting') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const initialAddresses =
    props.settings && props.settings.addresses
        ? JSON.parse(props.settings.addresses).map((address) => ({
              value: address
          }))
        : [{ value: '' }]

const initialPhones =
    props.settings && props.settings.phones
        ? JSON.parse(props.settings.phones).map((phone) => ({ value: phone }))
        : [{ value: '' }]

const initialEmails =
    props.settings && props.settings.emails
        ? JSON.parse(props.settings.emails).map((email) => ({ value: email }))
        : [{ value: '' }]

const form = useForm({
    addresses: reactive(initialAddresses),
    google_map: props.settings?.google_map || '',
    phones: reactive(initialPhones),
    whatsapp: props.settings?.whatsapp || '',
    emails: reactive(initialEmails),
    facebook: props.settings?.facebook || '',
    twitter: props.settings?.twitter || '',
    instagram: props.settings?.instagram || '',
    youtube: props.settings?.youtube || ''
})

const addAddress = () => {
    form.addresses.push({ value: '' })
}

const removeAddress = (index) => {
    form.addresses.splice(index, 1)
}

const addPhone = () => {
    form.phones.push({ value: '' })
}

const removePhone = (index) => {
    form.phones.splice(index, 1)
}

const addEmail = () => {
    form.emails.push({ value: '' })
}

const removeEmail = (index) => {
    form.emails.splice(index, 1)
}

const submitForm = () => {
    const postData = (data) => ({
        ...data,
        addresses: JSON.stringify(
            data.addresses.map((address) => address.value)
        ),
        phones: JSON.stringify(data.phones.map((phone) => phone.value)),
        emails: JSON.stringify(data.emails.map((email) => email.value))
    })

    form.transform(postData).put(route('setting.update'))
}

const { errorsFields } = useFormErrors()
</script>
