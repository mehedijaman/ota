<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('slider-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('slider.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="can('slider-recycle-bin-restore')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('slider.recycleBin.restoreAll'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="can('slider-recycle-bin-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('slider.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty All
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="sliders.data.length || route().params.searchTerm"
        :url="route('slider.recycleBin.index')"
        fields-to-search="title"
    ></AppDataSearch>

    <!-- Mobile Cards Layout -->
    <div class="block sm:hidden space-y-4" v-if="sliders.data.length">
        <template v-for="item in sliders.data" :key="item.id">
            <div class="card bg-base-100 shadow-md">
                <div class="card-body p-4">
                    <div class="flex items-start gap-3">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded bg-base-200">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                :alt="item.title"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center">
                                <i class="ri-image-line text-2xl text-base-content/20"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium truncate">{{ item.title || 'Untitled' }}</h3>
                            <div class="mt-1 flex flex-wrap gap-2 text-xs text-base-content/60">
                                <span>
                                    <i class="ri-calendar-line mr-1"></i>{{ item.deleted_at }}
                                </span>
                                <span v-if="item.deletedBy">
                                    <i class="ri-user-line mr-1"></i>{{ item.deletedBy.name }}
                                </span>
                            </div>
                            <span
                                class="badge badge-sm mt-2"
                                :class="item.is_active ? 'badge-success' : 'badge-error'"
                            >
                                {{ item.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-actions justify-end mt-3 pt-3 border-t border-base-200">
                        <AppButton
                            v-if="can('slider-recycle-bin-restore')"
                            class="btn btn-sm btn-primary"
                            @click="$inertia.visit(route('slider.recycleBin.restore', item.id))"
                        >
                            <i class="ri-recycle-fill mr-1"></i>Restore
                        </AppButton>
                        <AppButton
                            v-if="can('slider-recycle-bin-delete')"
                            class="btn btn-sm btn-destructive"
                            @click="confirmDelete(route('slider.recycleBin.destroyForce', item.id))"
                        >
                            <i class="ri-delete-bin-line mr-1"></i>Delete
                        </AppButton>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Desktop Table Layout -->
    <AppDataTable v-if="sliders.data.length" :headers="headers" class="hidden sm:block">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in sliders.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ (sliders.current_page - 1) * sliders.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded bg-base-200">
                                <img
                                    v-if="item.image_url"
                                    :src="item.image_url"
                                    :alt="item.title"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center">
                                    <i class="ri-image-line text-xl text-base-content/20"></i>
                                </div>
                            </div>
                            <p class="font-medium">{{ item.title || 'Untitled' }}</p>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="badge badge-sm" :class="item.is_active ? 'badge-success' : 'badge-error'">
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deleted_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deletedBy?.name ?? '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex gap-2">
                            <AppTooltip v-if="can('slider-recycle-bin-restore')" text="Restore">
                                <AppButton
                                    class="btn btn-icon btn-primary"
                                    @click="$inertia.visit(route('slider.recycleBin.restore', item.id))"
                                >
                                    <i class="ri-recycle-fill"></i>
                                </AppButton>
                            </AppTooltip>

                            <AppTooltip v-if="can('slider-recycle-bin-delete')" text="Permanently Delete">
                                <AppButton
                                    class="btn btn-icon btn-destructive"
                                    @click="confirmDelete(route('slider.recycleBin.destroyForce', item.id))"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </AppButton>
                            </AppTooltip>
                        </div>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        :links="sliders.links"
        :from="sliders.from ?? 0"
        :to="sliders.to ?? 0"
        :total="sliders.total ?? 0"
        class="mt-6 justify-center"
    ></AppPaginator>

    <!-- Empty State -->
    <div v-if="!sliders.data.length" class="mt-8 flex flex-col items-center justify-center py-12">
        <div class="rounded-full bg-base-200 p-6 mb-4">
            <i class="ri-inbox-unarchive-line text-4xl text-base-content/40"></i>
        </div>
        <h3 class="text-lg font-medium text-base-content/70 mb-2">Recycle Bin is Empty</h3>
        <p class="text-sm text-base-content/50 mb-4">Deleted sliders will appear here</p>
        <AppButton
            v-if="can('slider-list')"
            class="btn btn-secondary btn-sm"
            @click="$inertia.visit(route('slider.index'))"
        >
            <i class="ri-arrow-left-s-line mr-1"></i>
            Back to Sliders
        </AppButton>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Slider Recycle Bin')

defineProps({
    sliders: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Sliders', href: route('slider.index') },
    { label: title, last: true }
]

const headers = ['SL', 'Slider', 'Status', 'Deleted At', 'Deleted By', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>


