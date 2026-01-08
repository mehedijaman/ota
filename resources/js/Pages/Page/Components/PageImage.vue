<template>
    <div class="mt-5">
        <AppInputFile
            v-model="pageStore.page.image"
            :image-preview-url="getImagePreviewURL()"
            @remove-file="pageStore.page.remove_previous_image = true"
        ></AppInputFile>
    </div>
</template>

<script setup>
import useFormContext from '@/Composables/useFormContext'
import { usePageStore } from '../PageStore'

const pageStore = usePageStore()

const { isCreate } = useFormContext()

const getImagePreviewURL = () => {
    if (!isCreate.value && pageStore.page.image_url) {
        return pageStore.page.image_url
    }

    return null
}
</script>
