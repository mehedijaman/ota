<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('billPayment.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create BillPayment
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="billPayments.data.length || route().params.searchTerm"
        :url="route('billPayment.index')"
        fields-to-search="id"
    ></AppDataSearch>

    <AppDataTable v-if="billPayments.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in billPayments.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <!-- <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData> -->

                    <AppDataTableData>
                        <!-- Edit billPayment -->
                        <AppTooltip text="Edit BillPayment" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'billPayment.edit',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete billPayment -->
                        <AppTooltip text="Delete BillPayment">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'billPayment.destroy',
                                            item.id
                                        )
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
        v-if="billPayments.data.length"
        :links="billPayments.links"
        :from="billPayments.from || 0"
        :to="billPayments.to || 0"
        :total="billPayments.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!billPayments.data.length" class="mt-4">
        No billPayments found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('BillPayment')
const { can } = useAuthCan()

const props = defineProps({
  billPayments: {
    type: Object,
    default: () => {}
  }
})

const breadCrumb = [
  { label: 'Home', href: route('dashboard.index') },
  { label: 'BillPayments', last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
