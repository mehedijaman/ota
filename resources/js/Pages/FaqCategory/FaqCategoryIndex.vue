<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                v-if="can('faq-category-list')"
                class="btn btn-secondary mr-2"
                @click="$inertia.visit(route('faqCategory.recycleBin'))"
            >
                <i class="ri-recycle-line mr-1"></i>
                Recycle Bin
            </AppButton>
            <AppButton
                v-if="can('faq-category-create')"
                class="btn btn-primary"
                @click="$inertia.visit(route('faqCategory.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Category
            </AppButton>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="categories.data.length || route().params.searchTerm"
        :url="route('faqCategory.index')"
        fields-to-search="name,slug"
    ></AppDataSearch>

    <AppDataTable v-if="categories.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="item in categories.data" :key="item.id">
                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="font-medium">{{ item.name }}</span>
                            <span class="text-xs text-neutral-500">{{
                                item.slug
                            }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-600">{{
                            item.faqs_count
                        }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span class="text-neutral-600">{{
                            item.sort_order
                        }}</span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            :class="
                                item.is_active
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.updated_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <AppTooltip
                            v-if="can('faq-category-edit')"
                            text="Edit Category"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    $inertia.visit(
                                        route('faqCategory.edit', item.uuid)
                                    )
                                "
                            >
                                <i class="ri-edit-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <AppTooltip
                            v-if="can('faq-category-delete')"
                            text="Delete Category"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('faqCategory.destroy', item.uuid)
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
        v-if="categories.data.length"
        :links="categories.links"
        :from="categories.from || 0"
        :to="categories.to || 0"
        :total="categories.total || 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!categories.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { title } = useTitle('FAQ Categories')
const { can } = useAuthCan()

defineProps({
    categories: {
        type: Object,
        required: true
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'FAQ Categories', last: true }
]

const headers = ['Name', 'FAQs', 'Order', 'Status', 'Updated', 'Actions']

const confirmDialogRef = ref(null)

const confirmDelete = (url) => {
    confirmDialogRef.value.openModal(url)
}
</script>
