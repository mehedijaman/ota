<template>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('testimonial-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('testimonial.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Add Testimonial
                </AppButton>

                <AppButton
                    v-if="can('testimonial-recycle-bin-list')"
                    class="btn btn-secondary"
                    @click="
                        $inertia.visit(route('testimonial.recycleBin.index'))
                    "
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="testimonials.data.length || route().params.searchTerm"
        :url="route('testimonial.index')"
        fields-to-search="name"
    ></AppDataSearch>

    <AppDataTable v-if="testimonials.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <template v-for="item in testimonials.data" :key="item.id">
                    <AppDataTableRow>
                        <AppDataTableData rowspan="2">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                class="h-16 w-16 rounded"
                            />

                            <AppImageNotAvailable v-else class="!h-16 !w-16" />
                        </AppDataTableData>

                        <AppDataTableData>
                            {{ item.name }}
                        </AppDataTableData>

                        <AppDataTableData>
                            {{ item.designation }}
                        </AppDataTableData>

                        <AppDataTableData>
                            {{ item.company }}
                        </AppDataTableData>

                        <AppDataTableData>
                            {{ item.title }}
                        </AppDataTableData>

                        <!-- <AppDataTableData>
                        <span
                            class="rounded px-3 py-1 text-sm"
                            :class="getCategoryVisibilityClass(item.is_visible)"
                        >
                            {{ item.is_visible ? 'Visible' : 'Invisible' }}
                        </span>
                    </AppDataTableData> -->

                        <AppDataTableData rowspan="2">
                            <!-- edit category -->
                            <AppTooltip
                                v-if="can('testimonial-edit')"
                                text="Edit"
                                class="mr-3"
                            >
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="
                                        $inertia.visit(
                                            route('testimonial.edit', item.id)
                                        )
                                    "
                                >
                                    <i class="ri-edit-line"></i>
                                </AppButton>
                            </AppTooltip>

                            <!-- delete -->
                            <AppTooltip
                                v-if="can('testimonial-delete')"
                                text="Delete"
                            >
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="
                                        confirmDelete(
                                            route(
                                                'testimonial.destroy',
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
                    <AppDataTableRow>
                        <AppDataTableData colspan="6">
                            {{ item.title }}
                        </AppDataTableData>
                    </AppDataTableRow>
                </template>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="testimonials.links"
        :from="testimonials.from || 0"
        :to="testimonials.to || 0"
        :total="testimonials.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!testimonials.data.length" class="mt-4">
        No testimonials found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import useAuthCan from '@/Composables/useAuthCan'
import useTitle from '@/Composables/useTitle'
import AppImageNotAvailable from '@/Components/Modules/Blog/AppImageNotAvailable.vue'

defineProps({
    testimonials: {
        type: Object,
        default: () => {}
    }
})

const { title } = useTitle('Testimonials')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: title, last: true }
]

const headers = ['Image', 'Name', 'Designation', 'Company', 'title', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const { can } = useAuthCan()
</script>
