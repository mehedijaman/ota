<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('sms-campaign-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('sms.campaigns.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                New Campaign
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="campaigns.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in campaigns.data" :key="item.id">
                    <AppDataTableData>
                        <span class="font-medium">{{ item.name }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            v-if="item.template"
                            class="text-sm text-neutral-600 dark:text-neutral-400"
                        >
                            {{ item.template.name }}
                        </span>
                        <span v-else class="text-neutral-400">Custom</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="text-sm">
                            <span class="font-medium">{{
                                item.recipient_count
                            }}</span>
                            recipients
                        </div>
                        <div class="text-xs text-neutral-500">
                            <span class="text-green-600"
                                >{{ item.sent_count }} sent</span
                            >
                            <span
                                v-if="item.failed_count > 0"
                                class="ml-1 text-red-600"
                                >• {{ item.failed_count }} failed</span
                            >
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                            :class="statusClass(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span v-if="item.scheduled_at" class="text-sm">
                            {{ item.scheduled_at }}
                        </span>
                        <span v-else class="text-neutral-400">Immediate</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.created_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip text="View Details" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="
                                    $inertia.visit(
                                        route('sms.campaigns.show', item.id)
                                    )
                                "
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="campaigns.data.length"
        :links="campaigns.links"
        :from="campaigns.from || 0"
        :to="campaigns.to || 0"
        :total="campaigns.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!campaigns.data.length">
        <template #title>No SMS Campaigns</template>
        <template #description>
            Create a bulk SMS campaign to reach multiple recipients at once.
        </template>
    </AppEmptyState>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('SMS Campaigns')
const { can } = useAuthCan()

defineProps({
    campaigns: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS', last: false },
    { label: 'Campaigns', last: true }
]

const headers = [
    'Campaign',
    'Template',
    'Recipients',
    'Status',
    'Scheduled',
    'Created',
    'Actions'
]

const statusClass = (status) => {
    const classes = {
        pending:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        processing:
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        completed:
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        scheduled:
            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
    }
    return classes[status] || classes.pending
}
</script>
