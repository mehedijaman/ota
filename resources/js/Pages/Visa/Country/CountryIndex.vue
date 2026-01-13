<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 sm:flex-row">
                <AppButton
                    v-if="can('visa-country-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('visaCountry.create'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Country
                </AppButton>

                <AppButton
                    v-if="can('visa-country-delete')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('visaCountry.recycleBin'))"
                >
                    <i class="ri-recycle-line mr-1"></i>
                    Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div v-if="stats" class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg shadow-blue-500/25">
                    <i class="ri-flag-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Total Countries</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="ri-checkbox-circle-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Active</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.active || 0 }}</p>
                </div>
            </div>
        </div>

        <div class="border-neutral-6 bg-neutral-1 rounded-xl border p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/25">
                    <i class="ri-star-line text-xl text-white"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-neutral-9 truncate text-sm font-medium">Popular</p>
                    <p class="text-neutral-12 text-2xl font-bold">{{ stats.popular || 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <AppDataSearch
        v-if="countries.data.length || route().params.searchTerm"
        :url="route('visaCountry.index')"
        fields-to-search="name,code"
    ></AppDataSearch>

    <AppDataTable v-if="countries.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-neutral-4 divide-y">
                <AppDataTableRow
                    v-for="(item, index) in countries.data"
                    :key="item.id"
                    class="hover:bg-neutral-2 group transition-colors"
                >
                    <AppDataTableData class="text-neutral-10 w-12 text-center">
                        {{ (countries.current_page - 1) * countries.per_page + (index + 1) }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div class="shrink-0">
                                <img
                                    v-if="item.thumbnail_url"
                                    :src="item.thumbnail_url"
                                    :alt="item.name"
                                    class="ring-neutral-3 h-10 w-16 rounded-lg object-cover ring-1"
                                />
                                <div
                                    v-else
                                    class="ring-neutral-3 flex h-10 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 text-white ring-1"
                                >
                                    <span v-if="item.flag_icon" class="text-xl">{{ item.flag_icon }}</span>
                                    <i v-else class="ri-flag-line text-lg"></i>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div class="text-neutral-12 truncate font-medium">{{ item.name }}</div>
                                <div class="text-neutral-9 text-xs">{{ item.code }}</div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11">{{ item.continent || '-' }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-11">{{ item.visas_count || 0 }} Visas</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex gap-2">
                             <span
                                v-if="item.is_popular"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200"
                            >
                                <i class="ri-star-fill text-amber-500"></i>
                                Popular
                            </span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1"
                                :class="
                                    item.is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                        : 'bg-red-50 text-red-700 ring-red-200'
                                "
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="item.is_active ? 'bg-emerald-500' : 'bg-red-500'"
                                ></span>
                                {{ item.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData class="text-right">
                        <AppTooltip text="View Details" class="mr-2">
                            <AppButton
                                v-if="can('visa-country-view')"
                                class="btn btn-icon btn-secondary"
                                size="sm"
                                @click="$inertia.visit(route('visaCountry.show', item.id))"
                            >
                                <i class="ri-eye-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Edit Country" class="mr-2">
                            <AppButton
                                v-if="can('visa-country-edit')"
                                class="btn btn-icon btn-primary"
                                size="sm"
                                @click="$inertia.visit(route('visaCountry.edit', item.id))"
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip text="Delete Country">
                            <AppButton
                                v-if="can('visa-country-delete')"
                                class="btn btn-icon btn-destructive"
                                size="sm"
                                @click="confirmDelete(route('visaCountry.destroy', item.id))"
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
        v-if="countries.data.length"
        :links="countries.links"
        :from="countries.from ?? 0"
        :to="countries.to ?? 0"
        :total="countries.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState
        v-if="!countries.data.length"
        icon="ri-flag-line"
        title="No countries found"
        description="Get started by adding your first country."
    >
        <template #actions>
            <AppButton
                v-if="can('visa-country-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('visaCountry.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Country
            </AppButton>
        </template>
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('Visa Countries')

const props = defineProps({
    countries: {
        type: Object,
        default: () => ({})
    },
    stats: {
        type: Object,
        default: () => ({})
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Visa Management', href: '#' },
    { label: 'Countries', last: true }
]

const headers = ['#', 'Country', 'Continent', 'Visas', 'Status', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
