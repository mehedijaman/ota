<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('sms-template-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('sms.templates.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Add Template
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="templates.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in templates.data" :key="item.id">
                    <AppDataTableData>
                        <span class="font-medium">{{ item.name }}</span>
                        <span
                            v-if="item.slug"
                            class="mt-1 block text-xs text-neutral-500"
                            >{{ item.slug }}</span
                        >
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            v-if="item.category"
                            class="inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                        >
                            {{ item.category }}
                        </span>
                        <span v-else class="text-neutral-400">-</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div
                            class="max-w-xs truncate text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ item.content }}
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div
                            v-if="item.variables && item.variables.length"
                            class="flex flex-wrap gap-1"
                        >
                            <span
                                v-for="variable in item.variables.slice(0, 3)"
                                :key="variable"
                                class="inline-flex items-center rounded bg-neutral-100 px-1.5 py-0.5 font-mono text-xs text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300"
                            >
                                {{ variable }}
                            </span>
                            <span
                                v-if="item.variables.length > 3"
                                class="text-xs text-neutral-500"
                            >
                                +{{ item.variables.length - 3 }} more
                            </span>
                        </div>
                        <span v-else class="text-neutral-400">-</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                            :class="
                                item.is_active
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                            "
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('sms-template-edit')"
                            text="Edit Template"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('sms.templates.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('sms-template-delete')"
                            text="Delete Template"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('sms.templates.destroy', item.id)
                                    )
                                "
                            >
                                <i class="ri-delete-bin-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="templates.data.length"
        :links="templates.links"
        :from="templates.from || 0"
        :to="templates.to || 0"
        :total="templates.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!templates.data.length">
        <template #title>No SMS Templates</template>
        <template #description>
            Create reusable SMS templates with variables for quick message
            composition.
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('SMS Templates')
const { can } = useAuthCan()

defineProps({
    templates: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS', last: false },
    { label: 'Templates', last: true }
]

const headers = [
    'Name',
    'Category',
    'Content',
    'Variables',
    'Status',
    'Actions'
]

const confirmDialogRef = ref(null)

const confirmDelete = (url) => {
    confirmDialogRef.value.openModal(url)
}
</script>
