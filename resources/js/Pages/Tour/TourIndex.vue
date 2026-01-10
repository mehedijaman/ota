<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('tour-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('tour.recycleBin'))"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    Recycle Bin
                </AppButton>
                <AppButton
                    v-if="can('tour-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('tour.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Tour
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="tours.data.length || route().params.searchTerm"
        :url="route('tour.index')"
        fields-to-search="name,code"
    ></AppDataSearch>

    <AppDataTable v-if="tours.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in tours.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="h-12 w-16 overflow-hidden rounded-lg bg-slate-100">
                            <img
                                v-if="item.thumbnail_url"
                                :src="item.thumbnail_url"
                                :alt="item.name"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center text-slate-400">
                                <i class="ri-image-line text-xl"></i>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="font-medium text-slate-900">{{ item.name }}</span>
                            <span class="text-xs text-slate-500">{{ item.code }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.category || '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.destination || '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.duration }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="font-semibold text-primary-600">৳{{ formatNumber(item.base_price) }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span 
                            v-if="item.is_featured"
                            class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-medium mr-1"
                        >
                            Featured
                        </span>
                        <span 
                            :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip text="View" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="$inertia.visit(route('tour.show', item.id))"
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Edit" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="$inertia.visit(route('tour.edit', item.id))"
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('tour.destroy', item.id))"
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
        v-if="tours.data.length"
        :links="tours.links"
        :from="tours.from || 0"
        :to="tours.to || 0"
        :total="tours.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!tours.data.length" class="mt-4">
        No tours found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Tours')

defineProps({
    tours: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tours', last: true }
]

const headers = ['ID', 'Image', 'Tour', 'Category', 'Destination', 'Duration', 'Price', 'Status', 'Actions']

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-BD').format(num)
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
