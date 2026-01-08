<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex items-center gap-2">
                <AppButton
                    v-if="can('testimonial-list')"
                    class="btn btn-ghost btn-sm"
                    @click="$inertia.visit(route('testimonial.index'))"
                >
                    <i class="ri-arrow-left-s-line"></i>
                    <span class="hidden sm:inline ml-1">Back</span>
                </AppButton>

                <AppButton
                    v-if="can('testimonial-recycle-bin-restore') && testimonials.data.length"
                    class="btn btn-primary btn-sm"
                    @click="$inertia.visit(route('testimonial.recycleBin.restoreAll'))"
                >
                    <i class="ri-refresh-line mr-1"></i>
                    Restore All
                </AppButton>

                <AppButton
                    v-if="can('testimonial-recycle-bin-delete') && testimonials.data.length"
                    class="btn btn-error btn-sm"
                    @click="confirmDelete(route('testimonial.recycleBin.empty'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty All
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Info Banner -->
    <div v-if="testimonials.data.length" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-warning/10 border border-warning/20">
        <i class="ri-error-warning-line text-2xl text-warning"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-warning">{{ testimonials.total }} item(s) in recycle bin</p>
            <p class="text-xs text-base-content/60">Items will be permanently deleted after 30 days</p>
        </div>
        <AppDataSearch
            :url="route('testimonial.recycleBin.index')"
            fields-to-search="name"
            class="hidden sm:block"
        ></AppDataSearch>
    </div>

    <!-- Mobile Search -->
    <div v-if="testimonials.data.length" class="mb-4 sm:hidden">
        <AppDataSearch
            :url="route('testimonial.recycleBin.index')"
            fields-to-search="name"
        ></AppDataSearch>
    </div>

    <!-- Mobile Cards View -->
    <div v-if="testimonials.data.length" class="space-y-3 md:hidden">
        <div
            v-for="item in testimonials.data"
            :key="item.id"
            class="bg-base-100 rounded-xl border border-base-200 p-4 shadow-sm"
        >
            <div class="flex items-start gap-3">
                <!-- Avatar -->
                <div class="shrink-0">
                    <div v-if="item.image_url" class="w-12 h-12 rounded-full overflow-hidden ring-2 ring-error/20">
                        <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover opacity-60" />
                    </div>
                    <div v-else class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center">
                        <i class="ri-user-line text-xl text-base-content/30"></i>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-base-content line-clamp-1">{{ item.name }}</h3>
                    <p v-if="item.designation || item.company" class="text-sm text-base-content/60 line-clamp-1">
                        {{ item.designation }}{{ item.designation && item.company ? ' at ' : '' }}{{ item.company }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-base-content/50">
                        <i class="ri-delete-bin-line"></i>
                        <span>{{ item.deleted_at }}</span>
                        <span v-if="item.deletedBy">by {{ item.deletedBy.name }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-base-200">
                <AppButton
                    v-if="can('testimonial-recycle-bin-restore')"
                    class="btn btn-sm btn-primary"
                    @click="$inertia.visit(route('testimonial.recycleBin.restore', item.id))"
                >
                    <i class="ri-refresh-line mr-1"></i>
                    Restore
                </AppButton>
                <AppButton
                    v-if="can('testimonial-recycle-bin-delete')"
                    class="btn btn-sm btn-error btn-outline"
                    @click="confirmDelete(route('testimonial.recycleBin.destroyForce', item.id))"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    Delete
                </AppButton>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div v-if="testimonials.data.length" class="hidden md:block bg-base-100 rounded-xl border border-base-200 overflow-hidden">
        <table class="table table-zebra">
            <thead>
                <tr class="bg-base-200/50">
                    <th class="w-12">#</th>
                    <th>Person</th>
                    <th>Position</th>
                    <th>Deleted</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in testimonials.data" :key="item.id" class="hover">
                    <td class="text-base-content/50">
                        {{ (testimonials.current_page - 1) * testimonials.per_page + (index + 1) }}
                    </td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div v-if="item.image_url" class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-base-200">
                                <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover opacity-60" />
                            </div>
                            <div v-else class="w-10 h-10 rounded-full bg-base-200 flex items-center justify-center">
                                <i class="ri-user-line text-lg text-base-content/30"></i>
                            </div>
                            <span class="font-medium">{{ item.name }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm">
                            <div>{{ item.designation || '-' }}</div>
                            <div class="text-base-content/50">{{ item.company || '' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm">
                            <div class="flex items-center gap-1 text-error">
                                <i class="ri-time-line"></i>
                                {{ item.deleted_at }}
                            </div>
                            <div v-if="item.deletedBy" class="text-base-content/50">
                                by {{ item.deletedBy.name }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <AppButton
                                v-if="can('testimonial-recycle-bin-restore')"
                                class="btn btn-sm btn-ghost text-primary"
                                @click="$inertia.visit(route('testimonial.recycleBin.restore', item.id))"
                            >
                                <i class="ri-refresh-line mr-1"></i>
                                Restore
                            </AppButton>
                            <AppButton
                                v-if="can('testimonial-recycle-bin-delete')"
                                class="btn btn-sm btn-ghost text-error"
                                @click="confirmDelete(route('testimonial.recycleBin.destroyForce', item.id))"
                            >
                                <i class="ri-delete-bin-line mr-1"></i>
                                Delete
                            </AppButton>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <AppPaginator
        v-if="testimonials.data.length && testimonials.links.length > 3"
        :links="testimonials.links"
        :from="testimonials.from ?? 0"
        :to="testimonials.to ?? 0"
        :total="testimonials.total ?? 0"
        class="mt-6 justify-center"
    ></AppPaginator>

    <!-- Empty State -->
    <div v-if="!testimonials.data.length" class="py-16">
        <div class="max-w-md mx-auto text-center">
            <!-- Illustration -->
            <div class="relative w-28 h-28 mx-auto mb-6">
                <div class="absolute inset-0 bg-success/20 rounded-3xl rotate-6"></div>
                <div class="absolute inset-0 bg-base-100 rounded-3xl shadow-lg flex items-center justify-center">
                    <i class="ri-checkbox-circle-line text-5xl text-success"></i>
                </div>
            </div>

            <h2 class="text-xl font-bold text-base-content mb-2">Recycle Bin is Empty</h2>
            <p class="text-base-content/60 mb-6">
                Great! There are no deleted testimonials. All your testimonials are active and visible.
            </p>

            <AppButton
                v-if="can('testimonial-list')"
                class="btn btn-primary"
                @click="$inertia.visit(route('testimonial.index'))"
            >
                <i class="ri-arrow-left-line mr-2"></i>
                Back to Testimonials
            </AppButton>
        </div>
    </div>

    <!-- No Search Results -->
    <div v-if="!testimonials.data.length && route().params.searchTerm" class="py-12">
        <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-4">
                <i class="ri-search-line text-2xl text-base-content/30"></i>
            </div>
            <h3 class="font-medium text-base-content mb-1">No results found</h3>
            <p class="text-sm text-base-content/60 mb-4">
                No deleted testimonials match "{{ route().params.searchTerm }}"
            </p>
            <AppButton
                class="btn btn-ghost btn-sm"
                @click="$inertia.visit(route('testimonial.recycleBin.index'))"
            >
                Clear search
            </AppButton>
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { can } = useAuthCan()
const { title } = useTitle('Testimonial Recycle Bin')

defineProps({
    testimonials: {
        type: Object,
        default: () => ({ data: [], links: [], total: 0 })
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Testimonials', href: route('testimonial.index') },
    { label: title, last: true }
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
