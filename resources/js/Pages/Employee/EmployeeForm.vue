<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    class="btn btn-primary"
                    @click="$inertia.visit(route('employee.import'))"
                >
                    <i class="ri-file-excel-2-line mr-1"></i>
                    Bulk Import
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>
    <form>
        <AppCard class="w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-2">
                        <i class="ri-briefcase-line text-lg text-primary-11"></i>
                    </div>
                    <span class="font-semibold text-neutral-12">Professional Information</span>
                </div>
            </template>
            <template #content>
                <AppFormErrors class="mb-4" />
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <AppLabel for="designation">
                            {{ __('Designation') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.designation"
                            :options="designations"
                            combo-label="Select Designation"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="department">
                            {{ __('Department') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.department"
                            :options="departments"
                            combo-label="Select Department"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="area">
                            {{ __('Area') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.area"
                            :options="areas"
                            combo-label="Select Area"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="code">
                            {{ __('Employee Code') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="code"
                            v-model="form.code"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('code')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="joining_date">
                            {{ __('Joining Date') }}
                        </AppLabel>
                        <AppInputText
                            id="joining_date"
                            v-model="form.joining_date"
                            type="date"
                            :class="{
                                'input-error':
                                    errorsFields.includes('joining_date')
                            }"
                        />
                    </div>

                    <div class="mt-5 flex items-center">
                        <AppCheckbox
                            id="is_active"
                            v-model="form.is_active"
                            name="is_active"
                            :value="true"
                        />
                        <AppLabel for="is_active" class="ml-3">
                            Active
                        </AppLabel>
                    </div>
                </div>
            </template>
        </AppCard>

        <AppCard class="w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                        <i class="ri-user-heart-line text-lg text-blue-600"></i>
                    </div>
                    <span class="font-semibold text-neutral-12">Personal Information</span>
                </div>
            </template>
            <template #content>
                <!-- Photo Upload -->
                <div class="mb-6 pb-6 border-b border-neutral-6">
                    <AppLabel class="mb-2">{{ __('Employee Photo') }}</AppLabel>
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <div
                                v-if="photoPreview || props.employee?.photo_url"
                                class="h-24 w-24 overflow-hidden rounded-full ring-4 ring-neutral-3 shadow-lg"
                            >
                                <img
                                    :src="photoPreview || props.employee?.photo_url"
                                    alt="Photo"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="flex h-24 w-24 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-indigo-600 text-3xl font-bold text-white ring-4 ring-neutral-3 shadow-lg"
                            >
                                {{ form.name?.charAt(0)?.toUpperCase() || 'E' }}
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <input
                                ref="photoInput"
                                type="file"
                                accept="image/jpg,image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="handlePhotoChange"
                            />
                            <AppButton
                                type="button"
                                class="btn btn-secondary btn-sm"
                                @click="$refs.photoInput.click()"
                            >
                                <i class="ri-upload-2-line mr-1"></i>
                                {{ __('Choose Photo') }}
                            </AppButton>
                            <label
                                v-if="props.employee?.photo_url && !photoPreview"
                                class="flex items-center gap-2 text-sm text-neutral-10 cursor-pointer select-none"
                            >
                                <input
                                    type="checkbox"
                                    v-model="form.remove_photo"
                                    class="rounded border-neutral-5"
                                />
                                {{ __('Remove photo') }}
                            </label>
                            <p v-if="form.errors.photo" class="text-xs text-red-600">
                                {{ form.errors.photo }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <AppLabel for="name">
                            {{ __('Employee Name') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="name"
                            v-model="form.name"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="father_name">
                            {{ __("Father's Name") }}
                        </AppLabel>
                        <AppInputText
                            id="father_name"
                            v-model="form.father_name"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('father_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="mother_name">
                            {{ __("Mother's Name") }}
                        </AppLabel>
                        <AppInputText
                            id="mother_name"
                            v-model="form.mother_name"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('mother_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="birthday">
                            {{ __('Date of Birth') }}
                        </AppLabel>
                        <AppInputText
                            id="birthday"
                            v-model="form.birthday"
                            type="date"
                            :class="{
                                'input-error': errorsFields.includes('birthday')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="marital_status">
                            {{ __('Marital Status') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.marital_status"
                            :options="marital_statuses"
                            combo-label="Select Marital Status"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="spouse_name">
                            {{ __('Spouse Name') }}
                        </AppLabel>
                        <AppInputText
                            id="spouse_name"
                            v-model="form.spouse_name"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('spouse_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="nid_no"> {{ __('NID No') }} </AppLabel>
                        <AppInputText
                            id="nid_no"
                            v-model="form.nid_no"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('nid_no')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="passport_no">
                            {{ __('Passport No') }}
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

                    <div>
                        <AppLabel for="dl_no">
                            {{ __('Driving License No') }}
                        </AppLabel>
                        <AppInputText
                            id="dl_no"
                            v-model="form.dl_no"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('dl_no')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="gender"> {{ __('Gender') }} </AppLabel>
                        <AppCombobox
                            v-model="form.gender"
                            :options="genders"
                            combo-label="Select Gender"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="religion">
                            {{ __('Religion') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.religion"
                            :options="religions"
                            combo-label="Select Religion"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="blood_group">
                            {{ __('Blood Group') }}
                        </AppLabel>
                        <AppCombobox
                            v-model="form.blood_group"
                            :options="blood_groups"
                            combo-label="Select Blood Group"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <AppLabel for="email"> {{ __('Email') }} </AppLabel>
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
                        <AppLabel for="phone"> {{ __('Phone') }} </AppLabel>
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
                        <AppLabel for="emergency_contact">
                            {{ __('Emergency Contact') }}
                        </AppLabel>
                        <AppInputText
                            id="emergency_contact"
                            v-model="form.emergency_contact"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('emergency_contact')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="present_address">
                            {{ __('Present Address') }}
                        </AppLabel>

                        <AppTextArea
                            id="present_address"
                            v-model="form.present_address"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('present_address')
                            }"
                            placeholder="Present Address"
                        />
                    </div>

                    <div>
                        <AppLabel for="permanent_address">
                            {{ __('Permanent Address') }}
                        </AppLabel>

                        <AppTextArea
                            id="permanent_address"
                            v-model="form.permanent_address"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('permanent_address')
                            }"
                            placeholder="Permanent Address"
                        />
                    </div>

                    <div>
                        <AppLabel for="note"> {{ __('Notes') }} </AppLabel>

                        <AppTextArea
                            id="note"
                            v-model="form.note"
                            type="text"
                            :class="{
                                'input-error': errorsFields.includes('notes')
                            }"
                            placeholder="Special Notes"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Employee Bank Account Information -->
        <AppCard class="w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <i class="ri-bank-line text-lg text-emerald-600"></i>
                    </div>
                    <span class="font-semibold text-neutral-12">Bank Account Information</span>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <AppLabel for="bank_name">{{
                            __('Bank Name')
                        }}</AppLabel>
                        <AppInputText
                            id="bank_name"
                            v-model="form.bank_name"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error':
                                    errorsFields.includes('bank_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="branch_name">{{
                            __('Branch Name')
                        }}</AppLabel>
                        <AppInputText
                            id="branch_name"
                            v-model="form.branch_name"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error':
                                    errorsFields.includes('branch_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="account_no">{{
                            __('Account Number')
                        }}</AppLabel>
                        <AppInputText
                            id="account_no"
                            v-model="form.account_no"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error':
                                    errorsFields.includes('account_no')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="account_name">{{
                            __('Account Name')
                        }}</AppLabel>
                        <AppInputText
                            id="account_name"
                            v-model="form.account_name"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error':
                                    errorsFields.includes('account_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="routing_no">{{
                            __('Routing No')
                        }}</AppLabel>
                        <AppInputText
                            id="routing_no"
                            v-model="form.routing_no"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error':
                                    errorsFields.includes('routing_no')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="account_type">{{
                            __('Account Type')
                        }}</AppLabel>
                        <AppInputText
                            id="account_type"
                            v-model="form.account_type"
                            type="text"
                            class="w-full"
                            :class="{
                                'input-error':
                                    errorsFields.includes('account_type')
                            }"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Documents Upload Section -->
        <AppCard class="w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100">
                        <i class="ri-file-text-line text-lg text-purple-600"></i>
                    </div>
                    <span class="font-semibold text-neutral-12">Documents</span>
                </div>
            </template>
            <template #content>
                <p class="mb-4 text-sm text-neutral-10">
                    <i class="ri-information-line mr-1"></i>
                    Supported formats: JPG, PNG, PDF, DOC (max 5MB each)
                </p>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- NID Card -->
                    <div class="rounded-xl border border-neutral-5 bg-neutral-2/50 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="ri-id-card-line text-lg text-primary-10"></i>
                            <span class="font-medium text-neutral-12">{{ __('NID Card') }}</span>
                        </div>
                        <div v-if="getExistingDocument('nid_card') && !form.nid_card && !form.remove_nid_card" class="mb-3">
                            <div class="flex items-center gap-3 rounded-lg bg-neutral-1 border border-neutral-5 p-3">
                                <i :class="getDocumentIcon(getExistingDocument('nid_card').mime_type)" class="text-2xl text-neutral-10"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-12 truncate">{{ getExistingDocument('nid_card').name }}</p>
                                    <p class="text-xs text-neutral-9">{{ getExistingDocument('nid_card').size_formatted }}</p>
                                </div>
                                <a :href="getExistingDocument('nid_card').url" target="_blank" class="btn btn-icon btn-secondary btn-sm">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                            <label class="flex items-center gap-2 mt-2 text-sm text-neutral-10 cursor-pointer">
                                <input type="checkbox" v-model="form.remove_nid_card" class="rounded border-neutral-5" />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input ref="nidInput" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="hidden" @change="e => handleDocumentChange(e, 'nid_card')" />
                            <AppButton type="button" class="btn btn-secondary btn-sm w-full" @click="$refs.nidInput.click()">
                                <i class="ri-upload-2-line mr-1"></i>
                                {{ form.nid_card ? 'Change File' : 'Choose File' }}
                            </AppButton>
                            <p v-if="form.nid_card" class="mt-2 text-xs text-emerald-600">
                                <i class="ri-check-line mr-1"></i>{{ form.nid_card.name }}
                            </p>
                        </div>
                        <p v-if="form.errors.nid_card" class="mt-2 text-xs text-red-600">{{ form.errors.nid_card }}</p>
                    </div>

                    <!-- Passport -->
                    <div class="rounded-xl border border-neutral-5 bg-neutral-2/50 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="ri-passport-line text-lg text-blue-600"></i>
                            <span class="font-medium text-neutral-12">{{ __('Passport') }}</span>
                        </div>
                        <div v-if="getExistingDocument('passport') && !form.passport && !form.remove_passport" class="mb-3">
                            <div class="flex items-center gap-3 rounded-lg bg-neutral-1 border border-neutral-5 p-3">
                                <i :class="getDocumentIcon(getExistingDocument('passport').mime_type)" class="text-2xl text-neutral-10"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-12 truncate">{{ getExistingDocument('passport').name }}</p>
                                    <p class="text-xs text-neutral-9">{{ getExistingDocument('passport').size_formatted }}</p>
                                </div>
                                <a :href="getExistingDocument('passport').url" target="_blank" class="btn btn-icon btn-secondary btn-sm">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                            <label class="flex items-center gap-2 mt-2 text-sm text-neutral-10 cursor-pointer">
                                <input type="checkbox" v-model="form.remove_passport" class="rounded border-neutral-5" />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input ref="passportInput" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="hidden" @change="e => handleDocumentChange(e, 'passport')" />
                            <AppButton type="button" class="btn btn-secondary btn-sm w-full" @click="$refs.passportInput.click()">
                                <i class="ri-upload-2-line mr-1"></i>
                                {{ form.passport ? 'Change File' : 'Choose File' }}
                            </AppButton>
                            <p v-if="form.passport" class="mt-2 text-xs text-emerald-600">
                                <i class="ri-check-line mr-1"></i>{{ form.passport.name }}
                            </p>
                        </div>
                        <p v-if="form.errors.passport" class="mt-2 text-xs text-red-600">{{ form.errors.passport }}</p>
                    </div>

                    <!-- Driving Licence -->
                    <div class="rounded-xl border border-neutral-5 bg-neutral-2/50 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="ri-steering-2-line text-lg text-amber-600"></i>
                            <span class="font-medium text-neutral-12">{{ __('Driving Licence') }}</span>
                        </div>
                        <div v-if="getExistingDocument('driving_licence') && !form.driving_licence && !form.remove_driving_licence" class="mb-3">
                            <div class="flex items-center gap-3 rounded-lg bg-neutral-1 border border-neutral-5 p-3">
                                <i :class="getDocumentIcon(getExistingDocument('driving_licence').mime_type)" class="text-2xl text-neutral-10"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-12 truncate">{{ getExistingDocument('driving_licence').name }}</p>
                                    <p class="text-xs text-neutral-9">{{ getExistingDocument('driving_licence').size_formatted }}</p>
                                </div>
                                <a :href="getExistingDocument('driving_licence').url" target="_blank" class="btn btn-icon btn-secondary btn-sm">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                            <label class="flex items-center gap-2 mt-2 text-sm text-neutral-10 cursor-pointer">
                                <input type="checkbox" v-model="form.remove_driving_licence" class="rounded border-neutral-5" />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input ref="dlInput" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="hidden" @change="e => handleDocumentChange(e, 'driving_licence')" />
                            <AppButton type="button" class="btn btn-secondary btn-sm w-full" @click="$refs.dlInput.click()">
                                <i class="ri-upload-2-line mr-1"></i>
                                {{ form.driving_licence ? 'Change File' : 'Choose File' }}
                            </AppButton>
                            <p v-if="form.driving_licence" class="mt-2 text-xs text-emerald-600">
                                <i class="ri-check-line mr-1"></i>{{ form.driving_licence.name }}
                            </p>
                        </div>
                        <p v-if="form.errors.driving_licence" class="mt-2 text-xs text-red-600">{{ form.errors.driving_licence }}</p>
                    </div>

                    <!-- TIN Certificate -->
                    <div class="rounded-xl border border-neutral-5 bg-neutral-2/50 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="ri-file-list-3-line text-lg text-emerald-600"></i>
                            <span class="font-medium text-neutral-12">{{ __('TIN Certificate') }}</span>
                        </div>
                        <div v-if="getExistingDocument('tin_certificate') && !form.tin_certificate && !form.remove_tin_certificate" class="mb-3">
                            <div class="flex items-center gap-3 rounded-lg bg-neutral-1 border border-neutral-5 p-3">
                                <i :class="getDocumentIcon(getExistingDocument('tin_certificate').mime_type)" class="text-2xl text-neutral-10"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-neutral-12 truncate">{{ getExistingDocument('tin_certificate').name }}</p>
                                    <p class="text-xs text-neutral-9">{{ getExistingDocument('tin_certificate').size_formatted }}</p>
                                </div>
                                <a :href="getExistingDocument('tin_certificate').url" target="_blank" class="btn btn-icon btn-secondary btn-sm">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                            <label class="flex items-center gap-2 mt-2 text-sm text-neutral-10 cursor-pointer">
                                <input type="checkbox" v-model="form.remove_tin_certificate" class="rounded border-neutral-5" />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input ref="tinInput" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="hidden" @change="e => handleDocumentChange(e, 'tin_certificate')" />
                            <AppButton type="button" class="btn btn-secondary btn-sm w-full" @click="$refs.tinInput.click()">
                                <i class="ri-upload-2-line mr-1"></i>
                                {{ form.tin_certificate ? 'Change File' : 'Choose File' }}
                            </AppButton>
                            <p v-if="form.tin_certificate" class="mt-2 text-xs text-emerald-600">
                                <i class="ri-check-line mr-1"></i>{{ form.tin_certificate.name }}
                            </p>
                        </div>
                        <p v-if="form.errors.tin_certificate" class="mt-2 text-xs text-red-600">{{ form.errors.tin_certificate }}</p>
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center justify-end gap-4">
                    <AppButton
                        class="btn btn-secondary"
                        @click="$inertia.visit(route('employee.index'))"
                    >
                        {{ __('Cancel') }}
                    </AppButton>
                    <AppButton
                        @click="submitForm"
                        class="btn btn-primary"
                        :disabled="form.processing"
                    >
                        <i class="ri-save-line mr-1"></i>
                        {{ form.processing ? __('Saving...') : __('Save') }}
                    </AppButton>
                </div>
            </template>
        </AppCard>
    </form>
</template>

<script setup>
    import { ref } from 'vue' 
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AppCombobox from '@/Components/Form/AppCombobox.vue'

import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import { onMounted } from 'vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'

const props = defineProps({
    employee: {
        type: Object,
        default: null
    },

    designations: {
        type: Object,
        default: null
    },

    departments: {
        type: Object,
        default: null
    },

    areas: {
        type: Object,
        default: null
    },

    genders: {
        type: Object,
        default: null
    },
    religions: {
        type: Object,
        default: null
    },
    blood_groups: {
        type: Object,
        default: null
    },
    marital_statuses: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Employees', href: route('employee.index') },
    { label: 'Employee', last: true }
]

const { title } = useTitle('Employee')

const photoPreview = ref(null)

const handlePhotoChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.photo = file
        form.remove_photo = false
        photoPreview.value = URL.createObjectURL(file)
    }
}

const form = useForm({
    code: props.employee ? props.employee.code : null,
    joining_date: props.employee ? props.employee.joining_date : null,
    designation: props.employee ? props.employee.designation_id : null,
    department: props.employee ? props.employee.department_id : null,
    area: props.employee ? props.employee.area_id : null,
    is_active: props.employee ? props.employee.is_active : true,

    name: props.employee ? props.employee.name : null,
    father_name: props.employee ? props.employee.father_name : null,
    mother_name: props.employee ? props.employee.mother_name : null,
    birthday: props.employee ? props.employee.birthday : null,
    marital_status: props.employee ? props.employee.marital_status : null,
    spouse_name: props.employee ? props.employee.spouse_name : null,
    nid_no: props.employee ? props.employee.nid_no : null,
    passport_no: props.employee ? props.employee.passport_no : null,
    dl_no: props.employee ? props.employee.dl_no : null,
    gender: props.employee ? props.employee.gender : null,
    religion: props.employee ? props.employee.religion : null,
    blood_group: props.employee ? props.employee.blood_group : null,
    email: props.employee ? props.employee.email : null,
    phone: props.employee ? props.employee.phone : null,
    emergency_contact: props.employee ? props.employee.emergency_contact : null,
    present_address: props.employee ? props.employee.present_address : null,
    permanent_address: props.employee ? props.employee.permanent_address : null,
    note: props.employee ? props.employee.note : null,
    rfid_no: props.employee ? props.employee.rfid_no : null,

    bank_name: props.employee ? props.employee.bank_account?.bank_name : null,
    branch_name: props.employee
        ? props.employee.bank_account?.branch_name
        : null,
    account_no: props.employee ? props.employee.bank_account?.account_no : null,
    account_name: props.employee
        ? props.employee.bank_account?.account_name
        : null,
    routing_no: props.employee ? props.employee.bank_account?.routing_no : null,
    account_type: props.employee
        ? props.employee.bank_account?.account_type
        : null,

    // Photo upload
    photo: null,
    remove_photo: false,

    // Document uploads
    nid_card: null,
    remove_nid_card: false,
    passport: null,
    remove_passport: false,
    driving_licence: null,
    remove_driving_licence: false,
    tin_certificate: null,
    remove_tin_certificate: false
})

// Document handling helpers
const getExistingDocument = (type) => {
    if (!props.employee?.documents) return null
    return props.employee.documents.find(doc => doc.type === type)
}

const getDocumentIcon = (mimeType) => {
    if (!mimeType) return 'ri-file-line'
    if (mimeType.includes('pdf')) return 'ri-file-pdf-line'
    if (mimeType.includes('image')) return 'ri-image-line'
    if (mimeType.includes('word') || mimeType.includes('doc')) return 'ri-file-word-line'
    return 'ri-file-line'
}

const handleDocumentChange = (e, docType) => {
    const file = e.target.files[0]
    if (file) {
        form[docType] = file
        form[`remove_${docType}`] = false
    }
}

onMounted(() => {
    form.designation =
        props.designations.find(
            (designation) => designation.value === form.designation
        ) || null

    form.department =
        props.departments.find(
            (department) => department.value === form.department
        ) || null

    form.area =
        props.areas.find((area) => area.value === form.area) || null

    form.marital_status =
        props.marital_statuses.find(
            (marital_status) => marital_status.value === form.marital_status
        ) || null

    form.gender =
        props.genders.find((gender) => gender.value === form.gender) || null

    form.religion =
        props.religions.find((religion) => religion.value === form.religion) ||
        null

    form.blood_group =
        props.blood_groups.find(
            (blood_group) => blood_group.value === form.blood_group
        ) || null
})

const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    const postData = (data) => {
        const commonData = {
            ...data,
            designation_id: getValueFromKey(data, 'designation'),
            department_id: getValueFromKey(data, 'department'),
            area_id: getValueFromKey(data, 'area'),
            marital_status: getValueFromKey(data, 'marital_status'),
            gender: getValueFromKey(data, 'gender'),
            religion: getValueFromKey(data, 'religion'),
            blood_group: getValueFromKey(data, 'blood_group')
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('employee.store'), {
            forceFormData: true
        })
    } else {
        form.transform(postData).post(
            route('employee.update', props.employee.uuid),
            { forceFormData: true }
        )
    }
}

const { errorsFields } = useFormErrors()
</script>

