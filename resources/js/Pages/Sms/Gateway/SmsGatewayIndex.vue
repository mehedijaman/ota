<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('sms-gateway-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('sms.gateways.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Add Gateway
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="gateways.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in gateways.data" :key="item.id">
                    <AppDataTableData>
                        <span class="font-medium">{{ item.name }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                        >
                            {{ item.provider_name }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            v-if="item.is_default"
                            class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"
                        >
                            <i class="ri-star-fill mr-1"></i>
                            Default
                        </span>
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
                        {{ item.created_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('sms-gateway-edit') && !item.is_default"
                            text="Set as Default"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-warning"
                                @click="setDefault(item.id)"
                            >
                                <i class="ri-star-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('sms-gateway-edit')"
                            text="Test Gateway"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="openTestModal(item)"
                            >
                                <i class="ri-send-plane-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('sms-gateway-edit')"
                            text="Edit Gateway"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('sms.gateways.edit', item.id)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('sms-gateway-delete') && !item.is_default"
                            text="Delete Gateway"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('sms.gateways.destroy', item.id)
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
        v-if="gateways.data.length"
        :links="gateways.links"
        :from="gateways.from || 0"
        :to="gateways.to || 0"
        :total="gateways.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!gateways.data.length">
        <template #title>No SMS Gateways</template>
        <template #description>
            Configure your first SMS gateway to start sending messages.
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>

    <!-- Test Modal -->
    <AppModal v-model:open="showTestModal" title="Test SMS Gateway">
        <div class="space-y-4">
            <div>
                <AppLabel for="test_phone"
                    >Phone Number <span class="text-red-500">*</span></AppLabel
                >
                <AppInputText
                    id="test_phone"
                    v-model="testForm.test_phone"
                    placeholder="e.g., 01XXXXXXXXX"
                />
            </div>
            <div>
                <AppLabel for="test_message">Message (optional)</AppLabel>
                <AppTextArea
                    id="test_message"
                    v-model="testForm.test_message"
                    placeholder="Test message content"
                    :rows="3"
                />
            </div>
        </div>
        <template #footer>
            <AppButton class="btn btn-secondary" @click="showTestModal = false">
                Cancel
            </AppButton>
            <AppButton
                class="btn btn-primary"
                :disabled="testForm.processing"
                @click="sendTest"
            >
                {{ testForm.processing ? 'Sending...' : 'Send Test SMS' }}
            </AppButton>
        </template>
    </AppModal>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppModal from '@/Components/Overlay/AppModal.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'

const { title } = useTitle('SMS Gateways')
const { can } = useAuthCan()

defineProps({
    gateways: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'SMS', last: false },
    { label: 'Gateways', last: true }
]

const headers = ['Name', 'Provider', 'Default', 'Status', 'Created', 'Actions']

const confirmDialogRef = ref(null)
const showTestModal = ref(false)
const selectedGateway = ref(null)

const testForm = useForm({
    test_phone: '',
    test_message: ''
})

const confirmDelete = (url) => {
    confirmDialogRef.value.openModal(url)
}

const setDefault = (id) => {
    router.post(route('sms.gateways.setDefault', id))
}

const openTestModal = (gateway) => {
    selectedGateway.value = gateway
    testForm.reset()
    showTestModal.value = true
}

const sendTest = () => {
    testForm.post(route('sms.gateways.test', selectedGateway.value.id), {
        onSuccess: () => {
            showTestModal.value = false
        }
    })
}
</script>
