<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('tourCategory.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Add Category
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="categories.data.length || route().params.searchTerm"
        :url="route('tourCategory.index')"
        fields-to-search="name,slug"
    ></AppDataSearch>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in categories.data"
                    :key="item.id"
                >
                    <AppDataTableData>
                        {{ item.id }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="h-10 w-14 overflow-hidden rounded-lg bg-slate-100">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                :alt="item.name"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center text-slate-400">
                                <i class="ri-image-line"></i>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-2">
                            <i :class="'ri-' + (item.icon || 'folder') + '-line'" class="text-lg text-primary-600"></i>
                            <span class="font-medium">{{ item.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <code class="text-xs bg-slate-100 px-2 py-1 rounded">{{ item.slug }}</code>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.tours_count }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span 
                            :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip text="Edit" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="$inertia.visit(route('tourCategory.edit', item.id))"
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('tourCategory.destroy', item.id))"
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
        v-if="categories.data.length"
        :links="categories.links"
        :from="categories.from || 0"
        :to="categories.to || 0"
        :total="categories.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!categories.data.length" class="mt-4">
        No categories found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Tour Categories')

defineProps({
    categories: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Categories', last: true }
]

const headers = ['ID', 'Image', 'Name', 'Slug', 'Tours', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
