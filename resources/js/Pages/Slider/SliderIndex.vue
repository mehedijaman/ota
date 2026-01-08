<template>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('slider-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('slider.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add slider
                </AppButton>

                <AppButton
                    v-if="can('slider-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="
                        $inertia.visit(route('slider.recycleBin.index'))
                    "
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="sliders.data.length || route().params.searchTerm"
        :url="route('slider.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="sliders.data.length" >
        <template #TableBody>
            <tbody>
                <template v-for="item in sliders.data" :key="item.id">
                    <AppDataTableRow>
                        <AppDataTableData colspan="3">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                class="h-96 w-full rounded"
                            />

                            <AppImageNotAvailable v-else class="!h-16 !w-16" />
                        </AppDataTableData>
                    </AppDataTableRow>
                    <AppDataTableRow>                        
                        <AppDataTableData colspan="2">
                            {{ item.title }}
                        </AppDataTableData>

                        <AppDataTableData >
                            <!-- edit category -->
                            <AppTooltip
                                v-if="can('slider-edit')"
                                text="Edit"
                                class="mr-3"
                            >
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="
                                        $inertia.visit(
                                            route('slider.edit', item.id)
                                        )
                                    "
                                >
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>

                            <!-- delete -->
                            <AppTooltip
                                v-if="can('slider-delete')"
                                text="Delete"
                            >
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="
                                        confirmDelete(
                                            route(
                                                'slider.destroy',
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
                    
                </template>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="sliders.links"
        :from="sliders.from || 0"
        :to="sliders.to || 0"
        :total="sliders.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!sliders.data.length" class="mt-4">
        No sliders found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import useTitle from '@/Composables/useTitle'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

defineProps({
    sliders: {
        type: Object,
        default: () => {}
    }
})

const { title } = useTitle('Sliders')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: title, last: true }
]

const headers = ['Image', 'Title', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
