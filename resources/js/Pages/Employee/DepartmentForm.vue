<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
    </AppSectionHeader>

    <AppCard class="w-full md:w-3/4 xl:w-1/2">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form>
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

                <div>
                    <AppLabel for="code">{{ __('Code') }}</AppLabel>
                    <AppInputText
                        id="code"
                        v-model="form.code"
                        type="text"
                        :class="{
                            'input-error': errorsFields.includes('code')
                        }"
                    />
                </div>

                <div class="mt-5 flex items-center">
                    <input type="checkbox" id="is_active" v-model="form.is_active" />
                    <AppLabel for="is_active" class="ml-3"> Active </AppLabel>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton
                @click="submitForm"
                class="btn btn-primary"
                :disabled="form.processing"
            >
                <i class="ri-save-line mr-1"></i>
                {{ form.processing ? __('Saving...') : __('Save') }}
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

const props = defineProps({
    department: {
        type: Object,
        default: null
    }
})

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Departments', href: route('department.index') },
    { label: 'Department', last: true }
]

const { title } = useTitle('Department')

const form = useForm({
    name: props.department ? props.department.name : '',
    code: props.department ? props.department.code : '',
    is_active: props.department ? props.department.is_active : true
})

const { isCreate } = useFormContext()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('department.store'))
    } else {
        form.put(route('department.update', props.department.uuid))
    }
}

const { errorsFields } = useFormErrors()
</script>
