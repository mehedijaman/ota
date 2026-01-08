<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 md:flex-row">
                <AppButton
                    v-if="can('page-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('page.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="can('page-recycle-bin-restore')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('page.recycleBin.restoreAll'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore Recycle Bin
                </AppButton>

                <AppButton
                    v-if="can('page-recycle-bin-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('page.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="pages.data.length || route().params.searchTerm"
        :url="route('page.recycleBin.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="pages.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in pages.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{
                            (pages.current_page - 1) * pages.per_page +
                            (index + 1)
                        }}
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
                        {{ item.deleted_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deletedBy?.name ?? '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Restore -->
                        <AppTooltip
                            v-if="can('page-recycle-bin-restore')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route(
                                            'page.recycleBin.restore',
                                            item.id
                                        )
                                    )
                                "
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('page-recycle-bin-delete')"
                            text="Permanently Delete"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route(
                                            'page.recycleBin.destroyForce',
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
        :links="pages.links"
        :from="pages.from ?? 0"
        :to="pages.to ?? 0"
        :total="pages.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!pages.data.length" class="mt-4"> No data found. </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Slider Recycle Bin')

const props = defineProps({
    pages: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'pages', href: route('page.index') },
    { label: title, last: true }
]

const headers = [
    'SL',
    'Title',
    'Slug',
    'Published At',
    'Deleted At',
    'Deleted By',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
