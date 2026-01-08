<template>
    <AppSectionHeader title="Pages" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('page-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('page.create'))"
                >
                    Create Page
                </AppButton>

                <AppButton
                    v-if="can('page-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('page.recycleBin.index'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="pages.data.length || route().params.searchTerm"
        :url="route('page.index')"
        fields-to-search="title"
    ></AppDataSearch>

    <AppDataTable v-if="pages.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in pages.data" :key="item.id">
                    <AppDataTableData>
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            class="h-10 w-10 rounded"
                        />

                        <AppImageNotAvailable v-else />
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.title }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.slug }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.published_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getpagestatusClass(item.status)"
                        >
                            {{ item.status }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- edit -->
                        <AppTooltip
                            v-if="can('page-edit')"
                            text="Edit Page"
                            class="mr-3"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(route('page.edit', item.id))
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- delete -->
                        <AppTooltip
                            v-if="can('page-delete')"
                            text="Delete Page"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('page.destroy', item.id)
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
        v-if="pages.data.length"
        :links="pages.links"
        :from="pages.from || 0"
        :to="pages.to || 0"
        :total="pages.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!pages.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

defineProps({
    pages: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Pages', last: true }
]

const headers = ['Image', 'Title', 'Slug', 'Published', 'Status', 'Actions']

const getpagestatusClass = (status) => {
    return status === 'Published' ? 'published' : 'draft'
}

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>