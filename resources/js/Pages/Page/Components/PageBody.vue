<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <AppLabel for="title">Title</AppLabel>
            <AppInputText
                id="title"
                v-model="pageStore.page.title"
                type="text"
                :class="{
                    'input-error': errorsFields.includes('title')
                }"
            />
        </div>

        <div>
            <AppLabel for="slug">Slug</AppLabel>
            <AppInputText
                id="slug"
                v-model="pageStore.page.slug"
                type="text"
                :class="{
                    'input-error': errorsFields.includes('slug')
                }"
            />
        </div>
    </div>

    <div class="mt-5">
        <AppLabel for="notes">Content</AppLabel>
        <AppTipTapEditor
            v-model="pageStore.page.content"
            editor-id="notes"
            :class="{
                'app-tip-tap-error': errorsFields.includes('content')
            }"
            :file-upload-url="route('page.uploadEditorImage')"
        />
    </div>
</template>

<script setup>
import useFormErrors from '@/Composables/useFormErrors'
import { usePageStore } from '../PageStore'
const pageStore = usePageStore()
const { errorsFields } = useFormErrors()
</script>
