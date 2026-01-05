<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb" />

    <form @submit.prevent="submitForm">
        <AppCard class="w-full md:w-3/4 xl:w-1/2">
            <template #title>
                <i class="ri-user-3-line"></i> Employee Bulk Import
            </template>

            <template #content>
                <AppFormErrors class="mb-4" />

                <div>
                    <AppLabel for="file">
                        {{ __('Employee Import File')
                        }}<span class="text-red-700">*</span>
                    </AppLabel>

                    <AppInputText
                        id="file"
                        type="file"
                        @change="handleFileChange"
                        :class="{
                            'input-error': errorsFields.includes('file')
                        }"
                    />
                </div>
            </template>

            <template #footer>
                <div class="flex gap-4">
                    <AppButton class="btn btn-primary" type="submit">
                        {{ __('Start Import') }}
                    </AppButton>

                    <AppButton
                        class="btn btn-secondary"
                        @click="downloadTemplate"
                    >
                        {{ __('Download Template') }}
                    </AppButton>
                </div>
            </template>
        </AppCard>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormErrors from '@/Composables/useFormErrors'

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Employees', href: route('employee.index') },
    { label: 'Bulk Import', last: true }
]

const { title } = useTitle('Employee Import')

const form = useForm({
    file: null
})

const { errorsFields } = useFormErrors()

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.file = file
    }
}

const submitForm = () => {
    if (!form.file) {
        // Handle the case where no file is selected
        alert('Please select a file to upload.')
        return
    }

    const formData = new FormData()
    formData.append('file', form.file)

    form.post(route('employee.import'), {
        data: formData,
        onError: (errors) => {
            console.log(errors)
        },
        onStart: () => {
            // You can add a loading indicator here if needed
        },
        onFinish: () => {
            // Handle any post-submission tasks (e.g., resetting form)
        }
    })
}

const downloadTemplate = () => {
    window.location.href = route('employee.downloadTemplate')
}
</script>
