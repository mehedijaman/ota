<template>
    <AppCard>
        <template #title>
            <div class="flex items-center gap-2">
                <i class="ri-code-s-slash-line text-lg"></i>
                Advanced Settings
            </div>
        </template>
        <template #description>
            Add custom CSS to further customize your site's appearance.
        </template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form @submit.prevent="submitForm">
                <div class="space-y-4">
                    <div>
                        <AppLabel for="custom_css"> Custom CSS </AppLabel>
                        <div class="relative">
                            <textarea
                                id="custom_css"
                                v-model="form.custom_css"
                                rows="15"
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 p-4 font-mono text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                placeholder="/* Add your custom CSS here */

.my-custom-class {
    color: #333;
    background: #fff;
}

/* Example: Change primary button color */
.btn-primary {
    background-color: #your-color;
}"
                            ></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="ri-information-line mr-1"></i>
                            Custom CSS will be injected after the main
                            stylesheet. Use with caution.
                        </p>
                    </div>

                    <!-- CSS Tips -->
                    <div
                        class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950"
                    >
                        <h4
                            class="mb-2 flex items-center gap-2 font-medium text-amber-800 dark:text-amber-200"
                        >
                            <i class="ri-lightbulb-line"></i>
                            Tips for Custom CSS
                        </h4>
                        <ul
                            class="list-inside list-disc space-y-1 text-sm text-amber-700 dark:text-amber-300"
                        >
                            <li>
                                Use browser developer tools to find the correct
                                selectors
                            </li>
                            <li>
                                Add
                                <code
                                    class="rounded bg-amber-100 px-1 dark:bg-amber-900"
                                    >!important</code
                                >
                                if your styles are being overridden
                            </li>
                            <li>Test your changes on different screen sizes</li>
                            <li>Backup your CSS before making major changes</li>
                        </ul>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex items-center justify-between">
                <button
                    type="button"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400"
                    @click="clearCss"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    Clear CSS
                </button>
                <AppButton
                    class="btn btn-primary"
                    :loading="form.processing"
                    @click="submitForm"
                >
                    <i class="ri-save-line mr-1"></i>
                    Save Changes
                </AppButton>
            </div>
        </template>
    </AppCard>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const form = useForm({
    custom_css: props.settings.custom_css ?? ''
})

const clearCss = () => {
    if (confirm('Are you sure you want to clear all custom CSS?')) {
        form.custom_css = ''
    }
}

const submitForm = () => {
    form.put(route('setting.update'), {
        preserveScroll: true
    })
}
</script>
