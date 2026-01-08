<template>
    <AppSectionHeader title="Pages" :bread-crumb="breadCrumb">
        <AppButton
            v-if="can(isCreate ? 'page-create' : 'page-edit')"
            class="btn btn-primary mt-6"
            @click="submitForm"
        >
            Save
        </AppButton>
    </AppSectionHeader>

    <AppCard class="w-full">
        <template #title> {{ title }} </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form class="pt-4">
                <PageBody />
                
                <PageImage />

                <PagePublishDate />

                <PageSeo />
            </form>
        </template>
    </AppCard>

    <AppButton
        v-if="can(isCreate ? 'page-create' : 'page-edit')"
        class="btn btn-primary mt-6"
        @click="submitForm"
    >
        Save
    </AppButton>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'

import { onUnmounted } from 'vue'

import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import PageBody from './Components/PageBody.vue'
import PageImage from './Components/PageImage.vue'
import PageSeo from './Components/PageSeo.vue'
import PagePublishDate from './Components/PagePublishDate.vue'
import { usePageStore } from './PageStore'

const pageStore = usePageStore()
const { can } = useAuthCan()

const props = defineProps({
    page: {
        type: Object,
        default: null
    }
})

if (props.page) {
    pageStore.setPage(props.page)
}

onUnmounted(() => {
    pageStore.$reset()
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Pages', href: route('page.index') },
    { label: 'Page', last: true }
]

const { title } = useTitle('Page')
const { isCreate } = useFormContext()

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    const form = useForm(pageStore.page)

    const pageData = (data) => {
        const commonData = {
            ...data
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(pageData).post(route('page.store'))
    } else {
        form.transform(pageData).post(route('page.update', props.page.id))
    }
}
</script>
