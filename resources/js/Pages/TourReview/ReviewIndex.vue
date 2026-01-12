<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('tour-review-delete')"
                class="btn btn-secondary"
                @click="$inertia.visit(route('tourReview.recycleBin'))"
            >
                <i class="ri-delete-bin-line mr-1"></i>
                Recycle Bin
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="reviews.data.length || route().params.searchTerm"
        :url="route('tourReview.index')"
        fields-to-search="tour,reviewer_name"
    ></AppDataSearch>

    <AppDataTable v-if="reviews.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in reviews.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="font-medium">{{ item.tour }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.reviewer_name || item.user || 'Anonymous' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-1">
                            <i v-for="n in 5" :key="n" :class="n <= item.rating ? 'ri-star-fill text-amber-400' : 'ri-star-line text-slate-300'" class="text-sm"></i>
                            <span class="ml-1 text-sm">({{ item.rating }})</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span 
                            v-if="item.is_verified"
                            class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium mr-1"
                        >
                            Verified
                        </span>
                        <span 
                            :class="item.is_approved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ item.is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span 
                            v-if="item.is_featured"
                            class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs font-medium"
                        >
                            Featured
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.created_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip text="View" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-secondary"
                                @click="$inertia.visit(route('tourReview.show', item.id))"
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip v-if="!item.is_approved" text="Approve" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-success"
                                @click="approve(item.id)"
                            >
                                <i class="ri-check-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip v-else text="Reject" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-warning"
                                @click="reject(item.id)"
                            >
                                <i class="ri-close-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('tourReview.destroy', item.id))"
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
        v-if="reviews.data.length"
        :links="reviews.links"
        :from="reviews.from || 0"
        :to="reviews.to || 0"
        :total="reviews.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!reviews.data.length" class="mt-4">
        No reviews found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Tour Reviews')

defineProps({
    reviews: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Reviews', last: true }
]

const headers = ['ID', 'Tour', 'Reviewer', 'Rating', 'Status', 'Featured', 'Date', 'Actions']

const approve = (id) => {
    router.patch(route('tourReview.approve', id))
}

const reject = (id) => {
    router.patch(route('tourReview.reject', id))
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
