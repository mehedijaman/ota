<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-2/3">
        <template #title>{{ isEdit ? 'Edit Destination' : 'Create Destination' }}</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="name">Name *</AppLabel>
                        <AppInputText
                            id="name"
                            v-model="form.name"
                            type="text"
                            @input="generateSlug"
                            :class="{ 'input-error': errorsFields.includes('name') }"
                        />
                    </div>

                    <div>
                        <AppLabel for="slug">Slug *</AppLabel>
                        <AppInputText
                            id="slug"
                            v-model="form.slug"
                            type="text"
                            :class="{ 'input-error': errorsFields.includes('slug') }"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="country">Country</AppLabel>
                        <AppInputText
                            id="country"
                            v-model="form.country"
                            type="text"
                        />
                    </div>

                    <div>
                        <AppLabel for="city">City</AppLabel>
                        <AppInputText
                            id="city"
                            v-model="form.city"
                            type="text"
                        />
                    </div>
                </div>

                <div>
                    <AppLabel for="description">Description</AppLabel>
                    <AppTextArea
                        id="description"
                        v-model="form.description"
                        rows="3"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <AppLabel for="latitude">Latitude</AppLabel>
                        <AppInputText
                            id="latitude"
                            v-model="form.latitude"
                            type="number"
                            step="0.00000001"
                            min="-90"
                            max="90"
                        />
                    </div>

                    <div>
                        <AppLabel for="longitude">Longitude</AppLabel>
                        <AppInputText
                            id="longitude"
                            v-model="form.longitude"
                            type="number"
                            step="0.00000001"
                            min="-180"
                            max="180"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <AppCheckbox
                        id="is_featured"
                        v-model="form.is_featured"
                        :checked="form.is_featured"
                        label="Featured Destination"
                    />
                    <AppCheckbox
                        id="is_active"
                        v-model="form.is_active"
                        :checked="form.is_active"
                        label="Active"
                    />
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-medium mb-2">SEO Settings</h4>
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
                                rows="2"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-secondary mr-2" @click="$inertia.visit(route('tourDestination.index'))">
                Cancel
            </AppButton>
            <AppButton class="btn btn-primary" @click="submitForm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save' }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'

const { title } = useTitle('Tour Destination')

const props = defineProps({
    destination: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Destinations', href: route('tourDestination.index') },
    { label: props.destination ? 'Edit' : 'Create', last: true }
]

const form = useForm({
    name: props.destination?.name || '',
    slug: props.destination?.slug || '',
    country: props.destination?.country || 'Bangladesh',
    city: props.destination?.city || '',
    description: props.destination?.description || '',
    latitude: props.destination?.latitude || null,
    longitude: props.destination?.longitude || null,
    is_featured: props.destination?.is_featured ?? false,
    is_active: props.destination?.is_active ?? true,
    meta_title: props.destination?.meta_title || '',
    meta_description: props.destination?.meta_description || '',
})

const { isCreate, isEdit } = useFormContext()

const generateSlug = () => {
    if (isCreate.value) {
        form.slug = form.name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '')
    }
}

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('tourDestination.store'))
    }
    if (isEdit.value) {
        form.put(route('tourDestination.update', props.destination.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
