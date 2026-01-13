<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('visa.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm">
        <!-- Progress Tabs -->
        <div class="border-neutral-6 bg-neutral-1 mb-6 overflow-hidden rounded-xl border shadow-sm">
            <div class="overflow-x-auto p-4">
                <nav class="flex min-w-max gap-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all',
                            activeTab === tab.id
                                ? 'bg-linear-to-r from-blue-600 to-teal-600 text-white shadow-lg shadow-blue-500/25'
                                : 'bg-neutral-3 text-neutral-11 hover:bg-neutral-4 hover:text-neutral-12'
                        ]"
                    >
                        <i :class="[tab.icon, activeTab === tab.id ? 'text-white' : 'text-neutral-9']"></i>
                        <span class="hidden sm:inline">{{ tab.label }}</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Basic Info -->
        <div v-show="activeTab === 'basic'">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <AppCard class="mb-4">
                        <template #title> Basic Information </template>
                        <template #content>
                            <AppFormErrors class="mb-4" />
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <AppLabel for="name">Visa Name <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="e.g. US Tourist Visa B1/B2"
                                        @input="generateSlug"
                                        :class="{ 'input-error': errorsFields.includes('name') }"
                                    />
                                </div>
                                
                                <div>
                                    <AppLabel for="country_id">Country <span class="text-red-500">*</span></AppLabel>
                                    <AppCombobox
                                        v-model="form.country_id"
                                        :options="countryOptions"
                                        combo-label="Select Country"
                                        emit-value
                                        :class="{ 'input-error': errorsFields.includes('country_id') }"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="category_id">Category</AppLabel>
                                    <AppCombobox
                                        v-model="form.category_id"
                                        :options="categoryOptions"
                                        combo-label="Select Category"
                                        emit-value
                                    />
                                </div>

                                <div>
                                    <AppLabel for="slug">Slug <span class="text-red-500">*</span></AppLabel>
                                    <AppInputText
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        :class="{ 'input-error': errorsFields.includes('slug') }"
                                    />
                                </div>

                                <div>
                                    <AppLabel for="code">Visa Code</AppLabel>
                                    <AppInputText
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        placeholder="Internal code e.g. US-B1"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard class="mb-4">
                        <template #title> Description </template>
                        <template #content>
                            <div class="space-y-4">
                                <div>
                                    <AppLabel for="excerpt">Short Excerpt</AppLabel>
                                    <AppTextArea
                                        id="excerpt"
                                        v-model="form.excerpt"
                                        rows="2"
                                        placeholder="Brief summary for listings..."
                                    />
                                </div>
                                <div>
                                    <AppLabel for="description">Full Description</AppLabel>
                                    <AppTipTapEditor
                                        id="description"
                                        v-model="form.description"
                                        editor-id="description"
                                    />
                                </div>
                            </div>
                        </template>
                    </AppCard>
                </div>
                
                <div class="lg:col-span-1">
                     <AppCard class="mb-4">
                        <template #title> Settings </template>
                        <template #content>
                            <div class="flex flex-col gap-4">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="h-4 w-4 rounded text-primary-600"
                                    />
                                    <span class="text-neutral-12 font-medium">Active</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_featured"
                                        class="h-4 w-4 rounded text-amber-500"
                                    />
                                    <span class="text-neutral-12 font-medium">Featured</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_popular"
                                        class="h-4 w-4 rounded text-purple-500"
                                    />
                                    <span class="text-neutral-12 font-medium">Popular</span>
                                </label>
                            </div>
                        </template>
                    </AppCard>

                    <AppCard class="mb-4">
                        <template #title> Thumbnail </template>
                        <template #content>
                            <input
                                type="file"
                                @change="handleThumbnailChange"
                                class="hidden"
                                ref="thumbnailInput"
                                accept="image/*"
                            />

                            <div v-if="thumbnailPreview || imageUrl" class="relative mb-3 h-48 w-full overflow-hidden rounded-lg">
                                <img
                                    :src="thumbnailPreview || imageUrl"
                                    alt="Thumbnail Preview"
                                    class="h-full w-full object-cover"
                                />
                                <button
                                    type="button"
                                    @click="removeThumbnail"
                                    class="bg-neutral-10/50 absolute top-2 right-2 rounded-full p-1 text-white transition-colors hover:bg-red-500"
                                >
                                    <i class="ri-close-line text-lg"></i>
                                </button>
                            </div>

                            <AppButton
                                type="button"
                                class="btn btn-secondary w-full"
                                @click="$refs.thumbnailInput.click()"
                            >
                                <i class="ri-upload-2-line mr-2"></i>
                                {{ thumbnailPreview || imageUrl ? 'Change Thumbnail' : 'Upload Thumbnail' }}
                            </AppButton>
                        </template>
                    </AppCard>

                    <AppCard>
                        <template #title> Banner </template>
                        <template #content>
                            <input
                                type="file"
                                @change="handleBannerChange"
                                class="hidden"
                                ref="bannerInput"
                                accept="image/*"
                            />

                            <div v-if="bannerPreview || bannerUrl" class="relative mb-3 h-32 w-full overflow-hidden rounded-lg">
                                <img
                                    :src="bannerPreview || bannerUrl"
                                    alt="Banner Preview"
                                    class="h-full w-full object-cover"
                                />
                                <button
                                    type="button"
                                    @click="removeBanner"
                                    class="bg-neutral-10/50 absolute top-2 right-2 rounded-full p-1 text-white transition-colors hover:bg-red-500"
                                >
                                    <i class="ri-close-line text-lg"></i>
                                </button>
                            </div>

                            <AppButton
                                type="button"
                                class="btn btn-secondary w-full"
                                @click="$refs.bannerInput.click()"
                            >
                                <i class="ri-upload-2-line mr-2"></i>
                                {{ bannerPreview || bannerUrl ? 'Change Banner' : 'Upload Banner' }}
                            </AppButton>
                        </template>
                    </AppCard>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div v-show="activeTab === 'details'">
            <AppCard class="mb-4">
                <template #title> Visa Specifics </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                         <div>
                            <AppLabel for="entry_type">Entry Type</AppLabel>
                            <AppCombobox
                                v-model="form.entry_type"
                                :options="entryTypeOptions"
                                combo-label="Select Entry Type"
                                emit-value
                            />
                        </div>
                        <div>
                            <AppLabel for="validity_days">Validity (Days)</AppLabel>
                            <AppInputText
                                id="validity_days"
                                v-model="form.validity_days"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="stay_days">Max Stay (Days)</AppLabel>
                            <AppInputText
                                id="stay_days"
                                v-model="form.stay_days"
                                type="number"
                                min="0"
                            />
                        </div>
                        <div>
                             <AppLabel>Processing Time (Days)</AppLabel>
                             <div class="flex items-center gap-2">
                                <AppInputText
                                    v-model="form.processing_days_min"
                                    type="number"
                                    min="0"
                                    placeholder="Min"
                                />
                                <span class="text-neutral-9">-</span>
                                <AppInputText
                                    v-model="form.processing_days_max"
                                    type="number"
                                    min="0"
                                    placeholder="Max"
                                />
                             </div>
                        </div>
                    </div>
                </template>
            </AppCard>

             <AppCard class="mb-4">
                <template #title> Requirements & Rules </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_express_available" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Express Service Available</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_interview_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Interview Required</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_biometric_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Biometric Required</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_appointment_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Appointment Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_travel_insurance_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Travel Insurance Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_bank_statement_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Bank Statement Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_invitation_letter_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Invitation Letter Required</span>
                        </label>
                         <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" v-model="form.is_hotel_booking_required" class="h-4 w-4 rounded text-blue-600" />
                            <span class="text-neutral-12">Hotel Booking Required</span>
                        </label>
                    </div>
                </template>
            </AppCard>
            
            <AppCard class="mb-4">
                <template #title> Processing Times </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.processing_times" :key="index" class="border-neutral-5 bg-neutral-2 relative rounded-lg border p-4">
                            <button
                                type="button"
                                @click="removeProcessingTime(index)"
                                class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <AppLabel>Name</AppLabel>
                                    <AppInputText v-model="item.name" placeholder="e.g. Express Service" />
                                </div>
                                <div>
                                    <AppLabel>Type</AppLabel>
                                    <AppCombobox
                                        v-model="item.type"
                                        :options="[
                                            { label: 'Normal', value: 'normal' },
                                            { label: 'Express', value: 'express' },
                                            { label: 'Urgent', value: 'urgent' },
                                            { label: 'Super Urgent', value: 'super_urgent' }
                                        ]"
                                        emit-value
                                    />
                                </div>
                                <div>
                                    <AppLabel>Processing Days (Min - Max)</AppLabel>
                                    <div class="flex gap-2">
                                        <AppInputText v-model="item.processing_days_min" type="number" placeholder="Min" />
                                        <AppInputText v-model="item.processing_days_max" type="number" placeholder="Max" />
                                    </div>
                                </div>
                                <div>
                                    <AppLabel>Additional Fee</AppLabel>
                                    <AppInputText v-model="item.additional_fee" type="number" step="0.01" />
                                </div>
                                <div class="md:col-span-2">
                                    <AppLabel>Description</AppLabel>
                                    <AppInputText v-model="item.description" placeholder="Optional details" />
                                </div>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-secondary w-full" @click="addProcessingTime">
                            <i class="ri-add-line mr-1"></i> Add Processing Option
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <AppCard class="mb-4">
                <template #title> Visa Requirements </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.requirements" :key="index" class="border-neutral-5 bg-neutral-2 relative rounded-lg border p-4">
                            <button
                                type="button"
                                @click="removeRequirement(index)"
                                class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <AppLabel>Requirement Name</AppLabel>
                                    <AppInputText v-model="item.name" @input="generateRequirementSlug(index)" placeholder="e.g. Passport" />
                                </div>
                                <div>
                                    <AppLabel>Slug</AppLabel>
                                    <AppInputText v-model="item.slug" placeholder="auto-generated" />
                                </div>
                                <div>
                                    <AppLabel>Type</AppLabel>
                                    <AppCombobox
                                        v-model="item.type"
                                        :options="[
                                            { label: 'Document', value: 'document' },
                                            { label: 'Information', value: 'information' },
                                            { label: 'Form', value: 'form' },
                                            { label: 'Photo', value: 'photo' },
                                            { label: 'Biometric', value: 'biometric' },
                                            { label: 'Financial', value: 'financial' },
                                            { label: 'Travel', value: 'travel' },
                                            { label: 'Personal', value: 'personal' },
                                            { label: 'Other', value: 'other' }
                                        ]"
                                        emit-value
                                    />
                                </div>
                                <div class="flex items-center gap-4 pt-6">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="item.is_mandatory" class="h-4 w-4 rounded text-blue-600" />
                                        <span class="text-neutral-12">Mandatory</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="item.needs_upload" class="h-4 w-4 rounded text-blue-600" />
                                        <span class="text-neutral-12">Needs Upload</span>
                                    </label>
                                </div>
                                <div class="md:col-span-2">
                                    <AppLabel>Description</AppLabel>
                                    <AppTextArea v-model="item.description" rows="2" />
                                </div>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-secondary w-full" @click="addRequirement">
                            <i class="ri-add-line mr-1"></i> Add Requirement
                        </AppButton>
                    </div>
                </template>
            </AppCard>

            <AppCard class="mb-4">
                <template #title> Visa Fees </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.fees" :key="index" class="border-neutral-5 bg-neutral-2 relative rounded-lg border p-4">
                            <button
                                type="button"
                                @click="removeFee(index)"
                                class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <AppLabel>Fee Name</AppLabel>
                                    <AppInputText v-model="item.name" placeholder="e.g. Courier Fee" />
                                </div>
                                <div>
                                    <AppLabel>Type</AppLabel>
                                    <AppCombobox
                                        v-model="item.fee_type"
                                        :options="[
                                            { label: 'Embassy', value: 'embassy' },
                                            { label: 'Service', value: 'service' },
                                            { label: 'VFS', value: 'vfs' },
                                            { label: 'Courier', value: 'courier' },
                                            { label: 'Insurance', value: 'insurance' },
                                            { label: 'Translation', value: 'translation' },
                                            { label: 'Express', value: 'express' },
                                            { label: 'Other', value: 'other' }
                                        ]"
                                        emit-value
                                    />
                                </div>
                                <div>
                                    <AppLabel>Amount</AppLabel>
                                    <AppInputText v-model="item.amount" type="number" step="0.01" />
                                </div>
                                <div class="flex items-center gap-4 pt-6">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="item.is_per_person" class="h-4 w-4 rounded text-blue-600" />
                                        <span class="text-neutral-12">Per Person</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="item.is_optional" class="h-4 w-4 rounded text-blue-600" />
                                        <span class="text-neutral-12">Optional</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-secondary w-full" @click="addFee">
                            <i class="ri-add-line mr-1"></i> Add Fee
                        </AppButton>
                    </div>
                </template>
            </AppCard>
            
            <AppCard>
                <template #title> Criteria & Terms </template>
                <template #content>
                     <div class="space-y-4">
                        <div>
                            <AppLabel for="eligibility_criteria">Eligibility Criteria</AppLabel>
                            <AppTextArea
                                id="eligibility_criteria"
                                v-model="form.eligibility_criteria"
                                rows="4"
                            />
                        </div>
                        <div>
                            <AppLabel for="terms_conditions">Terms & Conditions</AppLabel>
                            <AppTextArea
                                id="terms_conditions"
                                v-model="form.terms_conditions"
                                rows="4"
                            />
                        </div>
                         <div>
                            <AppLabel for="important_notes">Important Notes</AppLabel>
                            <AppTextArea
                                id="important_notes"
                                v-model="form.important_notes"
                                rows="3"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- Pricing -->
        <div v-show="activeTab === 'pricing'">
            <AppCard>
                <template #title> Pricing Breakdown </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <AppLabel for="base_price">Base Price <span class="text-red-500">*</span></AppLabel>
                            <AppInputText
                                id="base_price"
                                v-model="form.base_price"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="service_fee">Service Fee</AppLabel>
                            <AppInputText
                                id="service_fee"
                                v-model="form.service_fee"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="embassy_fee">Embassy Fee</AppLabel>
                            <AppInputText
                                id="embassy_fee"
                                v-model="form.embassy_fee"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                        <div>
                            <AppLabel for="vfs_fee">VFS Fee</AppLabel>
                            <AppInputText
                                id="vfs_fee"
                                v-model="form.vfs_fee"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                        </div>
                    </div>
                    
                    <div class="bg-neutral-2 border-neutral-4 mt-6 flex justify-between rounded-lg border p-4">
                        <span class="text-neutral-12 font-medium">Total Cost:</span>
                        <span class="font-bold text-emerald-600 text-xl">
                            ৳{{ (Number(form.base_price || 0) + Number(form.service_fee || 0) + Number(form.embassy_fee || 0) + Number(form.vfs_fee || 0)).toFixed(2) }}
                        </span>
                    </div>
                </template>
            </AppCard>
        </div>
        
        <!-- Services -->
        <div v-show="activeTab === 'services'">
             <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <AppCard>
                    <template #title> Included Services </template>
                    <template #content>
                         <div class="space-y-2">
                            <div v-for="(item, index) in form.included_services" :key="index" class="flex gap-2">
                                <AppInputText
                                    v-model="form.included_services[index]"
                                    placeholder="Service included"
                                />
                                <AppButton type="button" class="btn btn-icon btn-destructive" size="sm" @click="removeIncluded(index)">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </div>
                            <AppButton type="button" class="btn btn-sm btn-secondary w-full" @click="addIncluded">
                                <i class="ri-add-line mr-1"></i> Add Service
                            </AppButton>
                        </div>
                    </template>
                </AppCard>
                
                <AppCard>
                    <template #title> Excluded Services </template>
                    <template #content>
                         <div class="space-y-2">
                            <div v-for="(item, index) in form.excluded_services" :key="index" class="flex gap-2">
                                <AppInputText
                                    v-model="form.excluded_services[index]"
                                    placeholder="Service excluded"
                                />
                                <AppButton type="button" class="btn btn-icon btn-destructive" size="sm" @click="removeExcluded(index)">
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </div>
                            <AppButton type="button" class="btn btn-sm btn-secondary w-full" @click="addExcluded">
                                <i class="ri-add-line mr-1"></i> Add Exclusion
                            </AppButton>
                        </div>
                    </template>
                </AppCard>
             </div>
        </div>

        <!-- FAQs -->
        <div v-show="activeTab === 'faqs'">
             <AppCard>
                <template #title> Frequently Asked Questions </template>
                <template #content>
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.faqs" :key="index" class="border-neutral-5 bg-neutral-2 relative rounded-lg border p-4">
                            <button
                                type="button"
                                @click="removeFaq(index)"
                                class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <div class="space-y-4">
                                <div>
                                    <AppLabel>Question</AppLabel>
                                    <AppInputText v-model="item.question" placeholder="e.g. How long does it take?" />
                                </div>
                                <div>
                                    <AppLabel>Answer</AppLabel>
                                    <AppTextArea v-model="item.answer" rows="2" />
                                </div>
                            </div>
                        </div>
                        <AppButton type="button" class="btn btn-secondary w-full" @click="addFaq">
                            <i class="ri-add-line mr-1"></i> Add FAQ
                        </AppButton>
                    </div>
                </template>
            </AppCard>
        </div>

        <!-- SEO -->
        <div v-show="activeTab === 'seo'">
            <AppCard>
                <template #title> SEO Configuration </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <AppLabel for="meta_title">Meta Title</AppLabel>
                            <AppInputText
                                id="meta_title"
                                v-model="form.meta_title"
                                type="text"
                            />
                        </div>
                        <div>
                            <AppLabel for="meta_description">Meta Description</AppLabel>
                            <AppTextArea
                                id="meta_description"
                                v-model="form.meta_description"
                                rows="3"
                            />
                        </div>
                        <div>
                            <AppLabel for="meta_keywords">Meta Keywords</AppLabel>
                            <AppInputText
                                id="meta_keywords"
                                v-model="form.meta_keywords"
                                type="text"
                                placeholder="comma, separated, keywords"
                            />
                        </div>
                    </div>
                </template>
            </AppCard>
        </div>

        <div class="mt-6 flex justify-end">
            <AppButton class="btn btn-primary" :disabled="form.processing" type="submit">
                <i class="ri-save-line mr-1"></i>
                {{ isCreate ? 'Create Visa' : 'Update Visa' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import AppTipTapEditor from '@/Components/Form/AppTipTapEditor.vue'

const { isCreate, isEdit } = useFormContext()
const { title } = useTitle(isCreate.value ? 'Create Visa' : 'Edit Visa')

const props = defineProps({
    visa: { type: Object, default: null },
    countries: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] }
})

const countryOptions = computed(() => props.countries.map(c => ({ value: c.id, label: c.name })))
const categoryOptions = computed(() => props.categories.map(c => ({ value: c.id, label: c.name })))

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visas', href: route('visa.index') },
    { label: title.value, last: true }
]

const form = useForm({
    name: props.visa?.name ?? '',
    country_id: props.visa?.country_id ?? null,
    category_id: props.visa?.category_id ?? null,
    slug: props.visa?.slug ?? '',
    code: props.visa?.code ?? '',
    excerpt: props.visa?.excerpt ?? '',
    description: props.visa?.description ?? '',
    
    entry_type: props.visa?.entry_type ?? 'single',
    validity_days: props.visa?.validity_days ?? null,
    stay_days: props.visa?.stay_days ?? null,
    processing_days_min: props.visa?.processing_days_min ?? null,
    processing_days_max: props.visa?.processing_days_max ?? null,
    
    base_price: props.visa?.base_price ?? 0,
    service_fee: props.visa?.service_fee ?? 0,
    embassy_fee: props.visa?.embassy_fee ?? 0,
    vfs_fee: props.visa?.vfs_fee ?? 0,
    
    is_express_available: props.visa?.is_express_available ?? false,
    is_interview_required: props.visa?.is_interview_required ?? false,
    is_biometric_required: props.visa?.is_biometric_required ?? false,
    is_appointment_required: props.visa?.is_appointment_required ?? false,
    is_travel_insurance_required: props.visa?.is_travel_insurance_required ?? false,
    is_bank_statement_required: props.visa?.is_bank_statement_required ?? true,
    is_invitation_letter_required: props.visa?.is_invitation_letter_required ?? false,
    is_hotel_booking_required: props.visa?.is_hotel_booking_required ?? false,
    
    eligibility_criteria: props.visa?.eligibility_criteria ?? '',
    terms_conditions: props.visa?.terms_conditions ?? '',
    important_notes: props.visa?.important_notes ?? '',
    
    included_services: props.visa?.included_services ?? [],
    excluded_services: props.visa?.excluded_services ?? [],
    
    is_active: props.visa?.is_active ?? true,
    is_featured: props.visa?.is_featured ?? false,
    is_popular: props.visa?.is_popular ?? false,
    
    meta_title: props.visa?.meta_title ?? '',
    meta_description: props.visa?.meta_description ?? '',
    meta_keywords: props.visa?.meta_keywords ?? '',
    
    thumbnail: null,
    banner: null,
    remove_thumbnail: false,
    remove_banner: false,
    
    // Arrays
    processing_times: props.visa?.processing_times ?? [],
    requirements: props.visa?.requirements ?? [],
    fees: props.visa?.fees ?? [],
    faqs: props.visa?.faqs ?? [],
    
    _method: isEdit.value ? 'PUT' : 'POST'
})

const imageUrl = computed(() => props.visa?.thumbnail_url)
const bannerUrl = computed(() => props.visa?.banner_url)
const { errorsFields } = useFormErrors()

const activeTab = ref('basic')

const thumbnailPreview = ref(null)
const bannerPreview = ref(null)

const tabs = [
    { id: 'basic', label: 'Basic Info', icon: 'ri-information-line' },
    { id: 'details', label: 'Details', icon: 'ri-file-text-line' },
    { id: 'pricing', label: 'Pricing', icon: 'ri-money-dollar-circle-line' },
    { id: 'services', label: 'Services', icon: 'ri-list-check' },
    { id: 'faqs', label: 'FAQs', icon: 'ri-question-line' },
    { id: 'seo', label: 'SEO', icon: 'ri-search-line' }
]

const entryTypeOptions = [
    { value: 'single', label: 'Single Entry' },
    { value: 'double', label: 'Double Entry' },
    { value: 'multiple', label: 'Multiple Entry' }
]

const generateSlug = () => {
    if (isCreate.value && form.name) {
        form.slug = form.name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')
    }
}

const generateRequirementSlug = (index) => {
    if (form.requirements[index].name) {
        form.requirements[index].slug = form.requirements[index].name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')
    }
}

const addIncluded = () => form.included_services.push('')
const removeIncluded = (index) => form.included_services.splice(index, 1)

const addExcluded = () => form.excluded_services.push('')
const removeExcluded = (index) => form.excluded_services.splice(index, 1)

// Repeaters
const addProcessingTime = () => {
    form.processing_times.push({
        name: '', type: 'normal', processing_days_min: 1, processing_days_max: 1, additional_fee: 0, description: ''
    })
}
const removeProcessingTime = (index) => form.processing_times.splice(index, 1)

const addRequirement = () => {
    form.requirements.push({
        name: '', slug: '', type: 'document', description: '', is_mandatory: true, needs_upload: true
    })
}
const removeRequirement = (index) => form.requirements.splice(index, 1)

const addFee = () => {
    form.fees.push({
        name: '', fee_type: 'service', amount: 0, is_per_person: true, is_optional: false
    })
}
const removeFee = (index) => form.fees.splice(index, 1)

const addFaq = () => form.faqs.push({ question: '', answer: '' })
const removeFaq = (index) => form.faqs.splice(index, 1)

// Image Handlers
const handleThumbnailChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.thumbnail = file
        thumbnailPreview.value = URL.createObjectURL(file)
        form.remove_thumbnail = false
    }
}

const removeThumbnail = () => {
    form.thumbnail = null
    thumbnailPreview.value = null
    form.remove_thumbnail = true
}

const handleBannerChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.banner = file
        bannerPreview.value = URL.createObjectURL(file)
        form.remove_banner = false
    }
}

const removeBanner = () => {
    form.banner = null
    bannerPreview.value = null
    form.remove_banner = true
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('visa.store'), { forceFormData: true })
    } else {
        form.post(route('visa.update', props.visa.id), { forceFormData: true })
    }
}
</script>
