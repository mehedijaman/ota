<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <form>
        <!-- Company Information -->
        <AppCard class="mb-4 w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-primary-2 flex h-8 w-8 items-center justify-center rounded-lg"
                    >
                        <i class="ri-building-line text-primary-11 text-lg"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Company Information</span
                    >
                </div>
            </template>
            <template #content>
                <AppFormErrors class="mb-4" />

                <!-- Logo Upload Section -->
                <div class="mb-6 flex items-start gap-6">
                    <div class="shrink-0">
                        <div
                            v-if="logoPreview || props.agent?.logo_url"
                            class="relative"
                        >
                            <img
                                :src="logoPreview || props.agent?.logo_url"
                                alt="Logo"
                                class="ring-neutral-3 h-24 w-24 rounded-lg object-cover shadow-lg ring-2"
                            />
                        </div>
                        <div
                            v-else
                            class="ring-neutral-3 flex h-24 w-24 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-3xl font-bold text-white shadow-lg ring-2"
                        >
                            {{
                                form.company_name?.charAt(0)?.toUpperCase() ||
                                'A'
                            }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <input
                            ref="logoInput"
                            type="file"
                            accept="image/jpg,image/jpeg,image/png,image/webp,image/gif"
                            class="hidden"
                            @change="handleLogoChange"
                        />
                        <AppButton
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="$refs.logoInput.click()"
                        >
                            <i class="ri-upload-2-line mr-1"></i>
                            {{ __('Choose Logo') }}
                        </AppButton>
                        <label
                            v-if="props.agent?.logo_url && !logoPreview"
                            class="text-neutral-10 flex cursor-pointer select-none items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                v-model="form.remove_logo"
                                class="border-neutral-5 rounded"
                            />
                            {{ __('Remove logo') }}
                        </label>
                        <p v-if="form.errors.logo" class="text-xs text-red-600">
                            {{ form.errors.logo }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <AppLabel for="company_name">
                            {{ __('Company Name') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="company_name"
                            v-model="form.company_name"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('company_name')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="contact_person">
                            {{ __('Contact Person') }}
                            <span class="text-red-700">*</span>
                        </AppLabel>
                        <AppInputText
                            id="contact_person"
                            v-model="form.contact_person"
                            type="text"
                            :class="{
                                'input-error':
                                    errorsFields.includes('contact_person')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="established_at">
                            {{ __('Established Date') }}
                        </AppLabel>
                        <AppInputText
                            id="established_at"
                            v-model="form.established_at"
                            type="date"
                            :class="{
                                'input-error':
                                    errorsFields.includes('established_at')
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

        <!-- Legal & Compliance Information -->
        <AppCard class="mb-4 w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100"
                    >
                        <i class="ri-file-text-line text-lg text-amber-600"></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Legal & Compliance</span
                    >
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Trade License -->
                    <div
                        class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <i
                                class="ri-file-list-2-line text-lg text-amber-600"
                            ></i>
                            <span class="text-neutral-12 font-medium">{{
                                __('Trade License')
                            }}</span>
                        </div>
                        <div class="mb-3">
                            <AppLabel for="trade_license_no">
                                {{ __('License Number') }}
                            </AppLabel>
                            <AppInputText
                                id="trade_license_no"
                                v-model="form.trade_license_no"
                                type="text"
                                :class="{
                                    'input-error':
                                        errorsFields.includes(
                                            'trade_license_no'
                                        )
                                }"
                            />
                        </div>
                        <div
                            v-if="
                                getExistingDocument('trade_license') &&
                                !form.trade_license &&
                                !form.remove_trade_license
                            "
                            class="mb-3"
                        >
                            <div
                                class="bg-neutral-1 border-neutral-5 flex items-center gap-3 rounded-lg border p-3"
                            >
                                <i
                                    :class="
                                        getDocumentIcon(
                                            getExistingDocument('trade_license')
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
                                            getExistingDocument('trade_license')
                                                .name
                                        }}
                                    </p>
                                    <p class="text-neutral-9 text-xs">
                                        {{
                                            getExistingDocument('trade_license')
                                                .size_formatted
                                        }}
                                    </p>
                                </div>
                                <a
                                    :href="
                                        getExistingDocument('trade_license').url
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
                                    v-model="form.remove_trade_license"
                                    class="border-neutral-5 rounded"
                                />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input
                                ref="tradeLicenseInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden"
                                @change="
                                    (e) =>
                                        handleDocumentChange(e, 'trade_license')
                                "
                            />
                            <AppButton
                                type="button"
                                class="btn btn-secondary btn-sm w-full"
                                @click="$refs.tradeLicenseInput.click()"
                            >
                                <i class="ri-upload-2-line mr-1"></i>
                                {{
                                    form.trade_license
                                        ? 'Change File'
                                        : 'Upload Document'
                                }}
                            </AppButton>
                            <p
                                v-if="form.trade_license"
                                class="mt-2 text-xs text-emerald-600"
                            >
                                <i class="ri-check-line mr-1"></i
                                >{{ form.trade_license.name }}
                            </p>
                        </div>
                        <p
                            v-if="form.errors.trade_license"
                            class="mt-2 text-xs text-red-600"
                        >
                            {{ form.errors.trade_license }}
                        </p>
                    </div>

                    <!-- MOCAT Certificate -->
                    <div
                        class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <i class="ri-award-line text-lg text-blue-600"></i>
                            <span class="text-neutral-12 font-medium">{{
                                __('MOCAT Certificate')
                            }}</span>
                        </div>
                        <div class="mb-3">
                            <AppLabel for="mocat_no">
                                {{ __('MOCAT Number') }}
                            </AppLabel>
                            <AppInputText
                                id="mocat_no"
                                v-model="form.mocat_no"
                                type="text"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('mocat_no')
                                }"
                            />
                        </div>
                        <div
                            v-if="
                                getExistingDocument('mocat_certificate') &&
                                !form.mocat_certificate &&
                                !form.remove_mocat_certificate
                            "
                            class="mb-3"
                        >
                            <div
                                class="bg-neutral-1 border-neutral-5 flex items-center gap-3 rounded-lg border p-3"
                            >
                                <i
                                    :class="
                                        getDocumentIcon(
                                            getExistingDocument(
                                                'mocat_certificate'
                                            ).mime_type
                                        )
                                    "
                                    class="text-neutral-10 text-2xl"
                                ></i>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-neutral-12 truncate text-sm font-medium"
                                    >
                                        {{
                                            getExistingDocument(
                                                'mocat_certificate'
                                            ).name
                                        }}
                                    </p>
                                    <p class="text-neutral-9 text-xs">
                                        {{
                                            getExistingDocument(
                                                'mocat_certificate'
                                            ).size_formatted
                                        }}
                                    </p>
                                </div>
                                <a
                                    :href="
                                        getExistingDocument('mocat_certificate')
                                            .url
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
                                    v-model="form.remove_mocat_certificate"
                                    class="border-neutral-5 rounded"
                                />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input
                                ref="mocatInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden"
                                @change="
                                    (e) =>
                                        handleDocumentChange(
                                            e,
                                            'mocat_certificate'
                                        )
                                "
                            />
                            <AppButton
                                type="button"
                                class="btn btn-secondary btn-sm w-full"
                                @click="$refs.mocatInput.click()"
                            >
                                <i class="ri-upload-2-line mr-1"></i>
                                {{
                                    form.mocat_certificate
                                        ? 'Change File'
                                        : 'Upload Document'
                                }}
                            </AppButton>
                            <p
                                v-if="form.mocat_certificate"
                                class="mt-2 text-xs text-emerald-600"
                            >
                                <i class="ri-check-line mr-1"></i
                                >{{ form.mocat_certificate.name }}
                            </p>
                        </div>
                        <p
                            v-if="form.errors.mocat_certificate"
                            class="mt-2 text-xs text-red-600"
                        >
                            {{ form.errors.mocat_certificate }}
                        </p>
                    </div>

                    <!-- TIN Certificate -->
                    <div
                        class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <i
                                class="ri-file-list-3-line text-lg text-emerald-600"
                            ></i>
                            <span class="text-neutral-12 font-medium">{{
                                __('TIN Certificate')
                            }}</span>
                        </div>
                        <div class="mb-3">
                            <AppLabel for="tin_no">
                                {{ __('TIN Number') }}
                            </AppLabel>
                            <AppInputText
                                id="tin_no"
                                v-model="form.tin_no"
                                type="text"
                                :class="{
                                    'input-error':
                                        errorsFields.includes('tin_no')
                                }"
                            />
                        </div>
                        <div
                            v-if="
                                getExistingDocument('tin_certificate') &&
                                !form.tin_certificate &&
                                !form.remove_tin_certificate
                            "
                            class="mb-3"
                        >
                            <div
                                class="bg-neutral-1 border-neutral-5 flex items-center gap-3 rounded-lg border p-3"
                            >
                                <i
                                    :class="
                                        getDocumentIcon(
                                            getExistingDocument(
                                                'tin_certificate'
                                            ).mime_type
                                        )
                                    "
                                    class="text-neutral-10 text-2xl"
                                ></i>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-neutral-12 truncate text-sm font-medium"
                                    >
                                        {{
                                            getExistingDocument(
                                                'tin_certificate'
                                            ).name
                                        }}
                                    </p>
                                    <p class="text-neutral-9 text-xs">
                                        {{
                                            getExistingDocument(
                                                'tin_certificate'
                                            ).size_formatted
                                        }}
                                    </p>
                                </div>
                                <a
                                    :href="
                                        getExistingDocument('tin_certificate')
                                            .url
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
                                    v-model="form.remove_tin_certificate"
                                    class="border-neutral-5 rounded"
                                />
                                {{ __('Remove document') }}
                            </label>
                        </div>
                        <div v-else>
                            <input
                                ref="tinInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden"
                                @change="
                                    (e) =>
                                        handleDocumentChange(
                                            e,
                                            'tin_certificate'
                                        )
                                "
                            />
                            <AppButton
                                type="button"
                                class="btn btn-secondary btn-sm w-full"
                                @click="$refs.tinInput.click()"
                            >
                                <i class="ri-upload-2-line mr-1"></i>
                                {{
                                    form.tin_certificate
                                        ? 'Change File'
                                        : 'Upload Document'
                                }}
                            </AppButton>
                            <p
                                v-if="form.tin_certificate"
                                class="mt-2 text-xs text-emerald-600"
                            >
                                <i class="ri-check-line mr-1"></i
                                >{{ form.tin_certificate.name }}
                            </p>
                        </div>
                        <p
                            v-if="form.errors.tin_certificate"
                            class="mt-2 text-xs text-red-600"
                        >
                            {{ form.errors.tin_certificate }}
                        </p>
                    </div>

                    <!-- NID -->
                    <div
                        class="border-neutral-5 bg-neutral-2/50 rounded-xl border p-4"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <i
                                class="ri-id-card-line text-lg text-purple-600"
                            ></i>
                            <span class="text-neutral-12 font-medium">{{
                                __('NID Card')
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
                        <div class="grid grid-cols-2 gap-3">
                            <!-- NID Front -->
                            <div>
                                <p class="text-neutral-10 mb-2 text-xs">
                                    Front Side
                                </p>
                                <div
                                    v-if="
                                        getExistingDocument('nid_front') &&
                                        !form.nid_front &&
                                        !form.remove_nid_front
                                    "
                                    class="mb-2"
                                >
                                    <div
                                        class="bg-neutral-1 border-neutral-5 flex items-center gap-2 rounded-lg border p-2"
                                    >
                                        <i
                                            :class="
                                                getDocumentIcon(
                                                    getExistingDocument(
                                                        'nid_front'
                                                    ).mime_type
                                                )
                                            "
                                            class="text-neutral-10 text-lg"
                                        ></i>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-neutral-12 truncate text-xs font-medium"
                                            >
                                                {{
                                                    getExistingDocument(
                                                        'nid_front'
                                                    ).name
                                                }}
                                            </p>
                                        </div>
                                        <a
                                            :href="
                                                getExistingDocument('nid_front')
                                                    .url
                                            "
                                            target="_blank"
                                            class="btn btn-icon btn-secondary btn-xs"
                                        >
                                            <i class="ri-eye-line text-xs"></i>
                                        </a>
                                    </div>
                                    <label
                                        class="text-neutral-10 mt-1 flex cursor-pointer items-center gap-1 text-xs"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="form.remove_nid_front"
                                            class="border-neutral-5 h-3 w-3 rounded"
                                        />
                                        {{ __('Remove') }}
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
                                                handleDocumentChange(
                                                    e,
                                                    'nid_front'
                                                )
                                        "
                                    />
                                    <AppButton
                                        type="button"
                                        class="btn btn-secondary btn-xs w-full"
                                        @click="$refs.nidFrontInput.click()"
                                    >
                                        <i class="ri-upload-2-line mr-1"></i>
                                        {{
                                            form.nid_front ? 'Change' : 'Upload'
                                        }}
                                    </AppButton>
                                    <p
                                        v-if="form.nid_front"
                                        class="mt-1 truncate text-xs text-emerald-600"
                                    >
                                        <i class="ri-check-line mr-1"></i
                                        >{{ form.nid_front.name }}
                                    </p>
                                </div>
                                <p
                                    v-if="form.errors.nid_front"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.nid_front }}
                                </p>
                            </div>

                            <!-- NID Back -->
                            <div>
                                <p class="text-neutral-10 mb-2 text-xs">
                                    Back Side
                                </p>
                                <div
                                    v-if="
                                        getExistingDocument('nid_back') &&
                                        !form.nid_back &&
                                        !form.remove_nid_back
                                    "
                                    class="mb-2"
                                >
                                    <div
                                        class="bg-neutral-1 border-neutral-5 flex items-center gap-2 rounded-lg border p-2"
                                    >
                                        <i
                                            :class="
                                                getDocumentIcon(
                                                    getExistingDocument(
                                                        'nid_back'
                                                    ).mime_type
                                                )
                                            "
                                            class="text-neutral-10 text-lg"
                                        ></i>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-neutral-12 truncate text-xs font-medium"
                                            >
                                                {{
                                                    getExistingDocument(
                                                        'nid_back'
                                                    ).name
                                                }}
                                            </p>
                                        </div>
                                        <a
                                            :href="
                                                getExistingDocument('nid_back')
                                                    .url
                                            "
                                            target="_blank"
                                            class="btn btn-icon btn-secondary btn-xs"
                                        >
                                            <i class="ri-eye-line text-xs"></i>
                                        </a>
                                    </div>
                                    <label
                                        class="text-neutral-10 mt-1 flex cursor-pointer items-center gap-1 text-xs"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="form.remove_nid_back"
                                            class="border-neutral-5 h-3 w-3 rounded"
                                        />
                                        {{ __('Remove') }}
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
                                                handleDocumentChange(
                                                    e,
                                                    'nid_back'
                                                )
                                        "
                                    />
                                    <AppButton
                                        type="button"
                                        class="btn btn-secondary btn-xs w-full"
                                        @click="$refs.nidBackInput.click()"
                                    >
                                        <i class="ri-upload-2-line mr-1"></i>
                                        {{
                                            form.nid_back ? 'Change' : 'Upload'
                                        }}
                                    </AppButton>
                                    <p
                                        v-if="form.nid_back"
                                        class="mt-1 truncate text-xs text-emerald-600"
                                    >
                                        <i class="ri-check-line mr-1"></i
                                        >{{ form.nid_back.name }}
                                    </p>
                                </div>
                                <p
                                    v-if="form.errors.nid_back"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.nid_back }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Financial Information -->
        <AppCard class="mb-4 w-full">
            <template #title>
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100"
                    >
                        <i
                            class="ri-money-dollar-circle-line text-lg text-emerald-600"
                        ></i>
                    </div>
                    <span class="text-neutral-12 font-semibold"
                        >Financial Information</span
                    >
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <AppLabel for="balance">
                            {{ __('Balance') }}
                        </AppLabel>
                        <AppInputText
                            id="balance"
                            v-model="form.balance"
                            type="number"
                            step="0.01"
                            :class="{
                                'input-error': errorsFields.includes('balance')
                            }"
                        />
                    </div>

                    <div>
                        <AppLabel for="credit_limit">
                            {{ __('Credit Limit') }}
                        </AppLabel>
                        <AppInputText
                            id="credit_limit"
                            v-model="form.credit_limit"
                            type="number"
                            step="0.01"
                            :class="{
                                'input-error':
                                    errorsFields.includes('credit_limit')
                            }"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <AppLabel for="remarks">
                            {{ __('Remarks') }}
                        </AppLabel>
                        <AppTextarea
                            id="remarks"
                            v-model="form.remarks"
                            rows="3"
                            :class="{
                                'input-error': errorsFields.includes('remarks')
                            }"
                        />
                    </div>
                </div>
            </template>
        </AppCard>

        <!-- Form Actions -->
        <div class="flex justify-end gap-2">
            <AppButton
                type="button"
                class="btn btn-secondary"
                @click="$inertia.visit(route('agent.index'))"
            >
                {{ __('Cancel') }}
            </AppButton>
            <AppButton
                type="button"
                class="btn btn-primary"
                :disabled="form.processing"
                @click="submitForm"
            >
                <i
                    v-if="form.processing"
                    class="ri-loader-4-line mr-1 animate-spin"
                ></i>
                {{ isEdit ? __('Update Agent') : __('Create Agent') }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppSelect from '@/Components/Form/AppSelect.vue'

const props = defineProps({
    agent: {
        type: Object,
        default: null
    }
})

const { isCreate, isEdit } = useFormContext()
const { title } = useTitle(isEdit.value ? 'Edit Agent' : 'Create Agent')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Agents', href: route('agent.index') },
    { label: isEdit.value ? 'Edit' : 'Create', last: true }
]

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'approved', label: 'Approved' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'inactive', label: 'Inactive' }
]

const logoPreview = ref(null)

const handleLogoChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.logo = file
        form.remove_logo = false
        logoPreview.value = URL.createObjectURL(file)
    }
}

const form = useForm({
    company_name: props.agent?.company_name ?? '',
    established_at: props.agent?.established_at ?? '',
    contact_person: props.agent?.contact_person ?? '',
    email: props.agent?.email ?? '',
    phone: props.agent?.phone ?? '',
    address: props.agent?.address ?? '',
    city: props.agent?.city ?? '',
    state: props.agent?.state ?? '',
    postal_code: props.agent?.postal_code ?? '',
    country: props.agent?.country ?? '',
    trade_license_no: props.agent?.trade_license_no ?? '',
    mocat_no: props.agent?.mocat_no ?? '',
    nid_no: props.agent?.nid_no ?? '',
    tin_no: props.agent?.tin_no ?? '',
    status: props.agent?.status ?? 'pending',
    remarks: props.agent?.remarks ?? '',
    balance: props.agent?.balance ?? 0,
    credit_limit: props.agent?.credit_limit ?? 0,

    // Logo upload
    logo: null,
    remove_logo: false,

    // Document uploads
    trade_license: null,
    remove_trade_license: false,
    mocat_certificate: null,
    remove_mocat_certificate: false,
    tin_certificate: null,
    remove_tin_certificate: false,
    nid_front: null,
    remove_nid_front: false,
    nid_back: null,
    remove_nid_back: false
})

// Document handling helpers
const getExistingDocument = (type) => {
    if (!props.agent?.documents) return null
    return props.agent.documents.find((doc) => doc.type === type)
}

const getDocumentIcon = (mimeType) => {
    if (!mimeType) return 'ri-file-line'
    if (mimeType.includes('pdf')) return 'ri-file-pdf-line'
    if (mimeType.includes('image')) return 'ri-image-line'
    return 'ri-file-line'
}

const handleDocumentChange = (e, docType) => {
    const file = e.target.files[0]
    if (file) {
        form[docType] = file
        form[`remove_${docType}`] = false
    }
}

const submitForm = () => {
    const postData = (data) => {
        return isCreate.value ? data : { ...data, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('agent.store'), {
            forceFormData: true
        })
    } else {
        form.transform(postData).post(route('agent.update', props.agent.uuid), {
            forceFormData: true
        })
    }
}

const { errorsFields } = useFormErrors()
</script>
