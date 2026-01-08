<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title>
            {{ title }}
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="flex flex-col gap-2">
                <div>
                    <AppLabel for="name">{{ __('Name') }}</AppLabel>
                    <AppInputText
                        id="name"
                        v-model="form.name"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('name')
                        }"
                    />
                </div>

                <!-- <div>
                    <AppLabel for="description">
                        {{ __('Description') }}
                    </AppLabel>

                    <AppTextArea
                        id="description"
                        v-model="form.description"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('description')
                        }"
                        placeholder="Description"
                    />
                </div> -->

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="active"
                        v-model="form.active"
                        name="active"
                        :value="true"
                    />
                    <AppLabel for="active" class="ml-3"> Active </AppLabel>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('Save') }}
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
import { onMounted } from 'vue'

const props = defineProps({
    manufacturer: {
        type: Object,
        default: null
    }
})

const { title } = useTitle('manufacturer')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'manufacturers', href: route('manufacturer.index') },
    { label: title, last: true }
]

const form = useForm({
    name: props.category ? props.category.name : '',
    description: props.category ? props.category.description : '',
    active: props.category ? props.category.active : ''
})

const { isCreate } = useFormContext()

const submitForm = () => {
    const postData = (data) => {
        const commonData = {
            ...data
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('manufacturer.store'))
    } else {
        form.transform(postData).post(
            route('manufacturer.update', props.manufacturer.id)
        )
    }
}

const { errorsFields } = useFormErrors()
</script>
