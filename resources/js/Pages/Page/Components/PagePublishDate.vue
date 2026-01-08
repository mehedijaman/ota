<template>
    <div class="mt-4">
        <AppInputDate
            v-model="pageStore.page.published_at"
            label="Publish Date"
            name="published_at"
        ></AppInputDate>
    </div>

    {{ pageStore.page.published_at }}
</template>

<script setup>
// import useFormErrors from '@/Composables/useFormErrors'
import { usePageStore } from '../PageStore'
const pageStore = usePageStore()
// const { errorsFields } = useFormErrors()
</script>
