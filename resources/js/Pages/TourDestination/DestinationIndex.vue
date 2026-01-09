<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('tourDestination.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Add Destination
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="destinations.data.length || route().params.searchTerm"
        :url="route('tourDestination.index')"
        fields-to-search="name,country,city"
    ></AppDataSearch>

    <AppDataTable v-if="destinations.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="item in destinations.data"
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
                        <span class="font-medium">{{ item.name }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.country || '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.city || '-' }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.tours_count }}
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
                        <AppTooltip text="Edit" class="mr-2">
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="$inertia.visit(route('tourDestination.edit', item.id))"
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete">
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="confirmDelete(route('tourDestination.destroy', item.id))"
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
        v-if="destinations.data.length"
        :links="destinations.links"
        :from="destinations.from || 0"
        :to="destinations.to || 0"
        :total="destinations.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppAlert v-if="!destinations.data.length" class="mt-4">
        No destinations found.
    </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Tour Destinations')

defineProps({
    destinations: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Tour Destinations', last: true }
]

const headers = ['ID', 'Image', 'Name', 'Country', 'City', 'Tours', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
