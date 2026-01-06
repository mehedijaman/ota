<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('sms.campaigns.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Campaigns
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Campaign Info Card -->
        <AppCard class="lg:col-span-1">
            <template #title>Campaign Info</template>
            <template #content>
                <dl class="space-y-3">
                    <div>
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Name
                        </dt>
                        <dd class="font-medium">{{ campaign.name }}</dd>
                    </div>

                    <div>
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Status
                        </dt>
                        <dd>
                            <span
                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                :class="statusClass(campaign.status)"
                            >
                                {{ campaign.status }}
                            </span>
                        </dd>
                    </div>

                    <div v-if="campaign.template">
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Template
                        </dt>
                        <dd>{{ campaign.template.name }}</dd>
                    </div>

                    <div>
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Created
                        </dt>
                        <dd>{{ campaign.created_at }}</dd>
                    </div>

                    <div v-if="campaign.scheduled_at">
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Scheduled
                        </dt>
                        <dd>{{ campaign.scheduled_at }}</dd>
                    </div>

                    <div v-if="campaign.started_at">
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Started
                        </dt>
                        <dd>{{ campaign.started_at }}</dd>
                    </div>

                    <div v-if="campaign.completed_at">
                        <dt
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Completed
                        </dt>
                        <dd>{{ campaign.completed_at }}</dd>
                    </div>
                </dl>
            </template>
        </AppCard>

        <!-- Stats Cards -->
        <AppCard class="lg:col-span-2">
            <template #title>Delivery Statistics</template>
            <template #content>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div
                        class="rounded-lg bg-neutral-50 p-4 text-center dark:bg-neutral-800"
                    >
                        <div
                            class="text-2xl font-bold text-neutral-900 dark:text-neutral-100"
                        >
                            {{ campaign.recipient_count }}
                        </div>
                        <div class="text-sm text-neutral-500">
                            Total Recipients
                        </div>
                    </div>

                    <div
                        class="rounded-lg bg-green-50 p-4 text-center dark:bg-green-900/20"
                    >
                        <div
                            class="text-2xl font-bold text-green-600 dark:text-green-400"
                        >
                            {{ campaign.sent_count }}
                        </div>
                        <div class="text-sm text-green-600 dark:text-green-400">
                            Sent
                        </div>
                    </div>

                    <div
                        class="rounded-lg bg-red-50 p-4 text-center dark:bg-red-900/20"
                    >
                        <div
                            class="text-2xl font-bold text-red-600 dark:text-red-400"
                        >
                            {{ campaign.failed_count }}
                        </div>
                        <div class="text-sm text-red-600 dark:text-red-400">
                            Failed
                        </div>
                    </div>

                    <div
                        class="rounded-lg bg-blue-50 p-4 text-center dark:bg-blue-900/20"
                    >
                        <div
                            class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                        >
                            {{ successRate }}%
                        </div>
                        <div class="text-sm text-blue-600 dark:text-blue-400">
                            Success Rate
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div v-if="campaign.status === 'processing'" class="mt-4">
                    <div class="mb-1 flex justify-between text-sm">
                        <span>Progress</span>
                        <span>{{ progressPercent }}%</span>
                    </div>
                    <div
                        class="h-2 w-full rounded-full bg-neutral-200 dark:bg-neutral-700"
                    >
                        <div
                            class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: `${progressPercent}%` }"
                        ></div>
                    </div>
                </div>
            </template>
        </AppCard>
    </div>

    <!-- Message Card -->
    <AppCard class="mt-4">
        <template #title>Message Content</template>
        <template #content>
            <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-800">
                <pre class="whitespace-pre-wrap font-sans text-sm">{{
                    campaign.message
                }}</pre>
            </div>
        </template>
    </AppCard>

    <!-- Recipients Table -->
    <AppCard class="mt-4">
        <template #title>Recipients ({{ recipients.total || 0 }})</template>
        <template #content>
            <AppDataTable
                v-if="recipients.data && recipients.data.length"
                :headers="recipientHeaders"
            >
                <template #TableBody>
                    <tbody>
                        <AppDataTableRow
                            v-for="recipient in recipients.data"
                            :key="recipient.id"
                        >
                            <AppDataTableData>
                                {{ recipient.phone }}
                            </AppDataTableData>

                            <AppDataTableData>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="
                                        recipientStatusClass(recipient.status)
                                    "
                                >
                                    {{ recipient.status }}
                                </span>
                            </AppDataTableData>

                            <AppDataTableData>
                                <span v-if="recipient.sent_at">{{
                                    recipient.sent_at
                                }}</span>
                                <span v-else class="text-neutral-400">-</span>
                            </AppDataTableData>

                            <AppDataTableData>
                                <span
                                    v-if="recipient.error_message"
                                    class="text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ recipient.error_message }}
                                </span>
                                <span v-else class="text-neutral-400">-</span>
                            </AppDataTableData>
                        </AppDataTableRow>
                    </tbody>
                </template>
            </AppDataTable>

            <AppPaginator
                v-if="recipients.data && recipients.data.length"
                :links="recipients.links"
                :from="recipients.from || 0"
                :to="recipients.to || 0"
                :total="recipients.total || 0"
                class="mt-4 justify-center"
            ></AppPaginator>

            <AppEmptyState v-if="!recipients.data || !recipients.data.length">
                <template #title>No Recipients</template>
                <template #description>
                    No recipients have been processed yet.
                </template>
            </AppEmptyState>
        </template>
    </AppCard>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('Campaign Details')

const props = defineProps({
    campaign: {
        type: Object,
        required: true
    },
    recipients: {
        type: Object,
        default: () => ({ data: [], links: [], from: 0, to: 0, total: 0 })
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS Campaigns', href: route('sms.campaigns.index') },
    { label: props.campaign.name, last: true }
]

const recipientHeaders = ['Phone', 'Status', 'Sent At', 'Error']

const successRate = computed(() => {
    if (props.campaign.recipient_count === 0) {
        return 0
    }
    return Math.round(
        (props.campaign.sent_count / props.campaign.recipient_count) * 100
    )
})

const progressPercent = computed(() => {
    if (props.campaign.recipient_count === 0) {
        return 0
    }
    const processed = props.campaign.sent_count + props.campaign.failed_count
    return Math.round((processed / props.campaign.recipient_count) * 100)
})

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

const recipientStatusClass = (status) => {
    const classes = {
        pending:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        sent: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }
    return classes[status] || classes.pending
}
</script>
