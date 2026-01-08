<template>
    <Head :title="title"></Head>

    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex items-center gap-3">
                <AppButton
                    v-if="can('slider-recycle-bin-list')"
                    class="btn btn-ghost btn-sm"
                    @click="$inertia.visit(route('slider.recycleBin.index'))"
                >
                    <i class="ri-delete-bin-line"></i>
                    <span class="hidden sm:inline ml-1">Recycle Bin</span>
                </AppButton>

                <AppButton
                    v-if="can('slider-create')"
                    class="btn btn-primary"
                    @click="$inertia.visit(route('slider.create'))"
                >
                    <i class="ri-add-line mr-1"></i>
                    New Slider
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- Toolbar -->
    <div v-if="sliders.data.length || route().params.searchTerm" class="bg-base-100 rounded-xl shadow-sm border border-base-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <!-- Stats Pills -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-base-200/80 text-sm">
                    <i class="ri-stack-line text-primary"></i>
                    <span class="font-medium">{{ sliders.total }}</span>
                    <span class="text-base-content/50">total</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-success/10 text-sm text-success">
                    <i class="ri-checkbox-circle-line"></i>
                    <span class="font-medium">{{ activeCount }}</span>
                    <span class="opacity-70">active</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-base-200/80 text-sm text-base-content/60">
                    <i class="ri-eye-off-line"></i>
                    <span class="font-medium">{{ inactiveCount }}</span>
                    <span class="opacity-70">hidden</span>
                </div>
            </div>

            <!-- Search -->
            <AppDataSearch
                :url="route('slider.index')"
                fields-to-search="title"
            ></AppDataSearch>
        </div>
    </div>

    <!-- Slider Grid -->
    <div v-if="sliders.data.length" class="grid gap-6 grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3">
        <div
            v-for="item in sliders.data"
            :key="item.id"
            class="group bg-base-100 rounded-xl overflow-hidden shadow-sm hover:shadow-lg border border-base-200 hover:border-primary/20 transition-all duration-300"
        >
            <!-- Image Area -->
            <div class="relative h-52 overflow-hidden">
                <!-- Background Pattern for no image -->
                <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-100 to-base-200">
                    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%239C92AC&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm-30 30v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>

                <!-- Actual Image -->
                <img
                    v-if="item.image_url"
                    :src="item.image_url"
                    :alt="item.title"
                    class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                />

                <!-- No Image State -->
                <div v-else class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-2xl bg-base-300/50 flex items-center justify-center mx-auto mb-3">
                            <i class="ri-image-line text-3xl text-base-content/20"></i>
                        </div>
                        <span class="text-sm text-base-content/30">No image uploaded</span>
                    </div>
                </div>

                <!-- Overlay on Hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-base-content/80 via-base-content/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <!-- Status Badge -->
                <div class="absolute top-4 left-4">
                    <div
                        class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium shadow-lg"
                        :class="item.is_active
                            ? 'bg-success text-success-content'
                            : 'bg-base-100 text-base-content/70'"
                    >
                        <span class="relative flex h-2 w-2">
                            <span
                                v-if="item.is_active"
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-content opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex rounded-full h-2 w-2"
                                :class="item.is_active ? 'bg-success-content' : 'bg-base-content/30'"
                            ></span>
                        </span>
                        {{ item.is_active ? 'Live' : 'Draft' }}
                    </div>
                </div>

                <!-- Order Badge -->
                <div v-if="item.order" class="absolute top-4 right-4">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-base-100/95 shadow-lg text-sm font-bold text-base-content">
                        {{ item.order }}
                    </div>
                </div>

                <!-- Hover Actions -->
                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                    <div class="flex items-center gap-2">
                        <AppButton
                            v-if="can('slider-edit')"
                            class="btn btn-sm bg-base-100 hover:bg-primary hover:text-primary-content border-0 shadow-lg"
                            @click="$inertia.visit(route('slider.edit', item.id))"
                        >
                            <i class="ri-pencil-line mr-1"></i>
                            Edit
                        </AppButton>
                        <AppButton
                            v-if="can('slider-delete')"
                            class="btn btn-sm btn-square bg-base-100 hover:bg-error hover:text-error-content border-0 shadow-lg"
                            @click="confirmDelete(route('slider.destroy', item.id))"
                        >
                            <i class="ri-delete-bin-6-line"></i>
                        </AppButton>
                    </div>

                    <a
                        v-if="item.url"
                        :href="item.url"
                        target="_blank"
                        class="btn btn-sm bg-base-100 border-0 shadow-lg"
                        @click.stop
                    >
                        <i class="ri-external-link-line mr-1"></i>
                        Preview
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <!-- Title Row -->
                <div class="flex items-start justify-between gap-3 mb-3">
                    <h3 class="font-semibold text-base-content line-clamp-1 flex-1">
                        {{ item.title || 'Untitled Slider' }}
                    </h3>
                    <span class="text-xs text-base-content/40 shrink-0">#{{ item.id }}</span>
                </div>

                <!-- Description -->
                <p class="text-sm text-base-content/60 line-clamp-2 mb-4 min-h-[2.5rem]">
                    {{ item.description || 'No description added yet.' }}
                </p>

                <!-- Tags Row -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span
                        v-if="item.button_text"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs bg-primary/8 text-primary border border-primary/10"
                    >
                        <i class="ri-cursor-line text-[10px]"></i>
                        {{ item.button_text }}
                    </span>

                    <span
                        v-if="item.url"
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs bg-base-200 text-base-content/60"
                    >
                        <i class="ri-link text-[10px]"></i>
                        Has Link
                    </span>

                    <span
                        v-if="!item.button_text && !item.url"
                        class="text-xs text-base-content/30 italic"
                    >
                        No call-to-action set
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div v-if="sliders.data.length && sliders.links.length > 3" class="mt-8">
        <AppPaginator
            :links="sliders.links"
            :from="sliders.from || 0"
            :to="sliders.to || 0"
            :total="sliders.total || 0"
            class="justify-center"
        ></AppPaginator>
    </div>

    <!-- Empty State -->
    <div v-if="!sliders.data.length && !route().params.searchTerm" class="py-16">
        <div class="max-w-lg mx-auto text-center">
            <!-- Illustration -->
            <div class="relative w-32 h-32 mx-auto mb-8">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-3xl rotate-6"></div>
                <div class="absolute inset-0 bg-base-100 rounded-3xl shadow-lg flex items-center justify-center">
                    <i class="ri-slideshow-3-line text-5xl text-primary"></i>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-base-content mb-3">Create Your First Slider</h2>
            <p class="text-base-content/60 mb-8 max-w-sm mx-auto">
                Sliders help showcase your best content with beautiful images, compelling titles, and call-to-action buttons.
            </p>

            <AppButton
                v-if="can('slider-create')"
                class="btn btn-primary btn-lg"
                @click="$inertia.visit(route('slider.create'))"
            >
                <i class="ri-add-line mr-2"></i>
                Create Slider
            </AppButton>
        </div>
    </div>

    <!-- No Results State -->
    <div v-if="!sliders.data.length && route().params.searchTerm" class="py-16">
        <div class="max-w-lg mx-auto text-center">
            <div class="w-20 h-20 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-6">
                <i class="ri-search-line text-3xl text-base-content/30"></i>
            </div>
            <h3 class="text-lg font-semibold text-base-content mb-2">No results found</h3>
            <p class="text-base-content/60 mb-6">
                We couldn't find any sliders matching "{{ route().params.searchTerm }}"
            </p>
            <AppButton
                class="btn btn-ghost"
                @click="$inertia.visit(route('slider.index'))"
            >
                Clear search
            </AppButton>
        </div>
    </div>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import useTitle from '@/Composables/useTitle'

const props = defineProps({
    sliders: {
        type: Object,
        default: () => ({ data: [], links: [], total: 0 })
    }
})

const { title } = useTitle('Sliders')
const { can } = useAuthCan()

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: title, last: true }
]

const activeCount = computed(() =>
    props.sliders.data?.filter(s => s.is_active).length || 0
)

const inactiveCount = computed(() =>
    props.sliders.data?.filter(s => !s.is_active).length || 0
)

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
</script>
