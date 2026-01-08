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

                <div>
                    <p class="mb-1 mt-5">Position</p>
                    <AppCombobox
                        v-model="form.position"
                        :options="positions"
                        combo-label="Select Menu Position"
                        class="w-64 xl:w-full"
                    />
                </div>

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
    menu: {
        type: Object,
        default: null
    },
    positions: {
        type: Array,
        default: () => []
    }
})



const { title } = useTitle('Menu')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Menus', href: route('menu.index') },
    { label: title, last: true }
]

const form = useForm({
    name: props.menu ? props.menu.name : '',
    position: props.menu ? props.menu.position : '',
    active: props.menu ? props.menu.active : true
})

onMounted(() => {
    if (form.position) {
        form.position = props.positions.find(
            (position) => position.value === form.position
        )
    }
})

const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    const postData = (data) => {
        const commonData = {
            ...data,
            position: getValueFromKey(data, 'position'),
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('menu.store'))
    } else {
        form.transform(postData).post(route('menu.update', props.menu.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
