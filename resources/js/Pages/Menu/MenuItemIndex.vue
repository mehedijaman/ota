<script setup>
import { ref, watch, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import axios from 'axios'

const title = 'Menu Builder'

const props = defineProps({
    menu: {
        type: Object,
        required: true
    },
    menuItems: {
        type: Array,
        default: () => []
    },
    availablePages: {
        type: Array,
        default: () => []
    }
})

const localMenuItems = ref([])
const isSaving = ref(false)
const isDeleting = ref(null)

// Accordion states
const pagesExpanded = ref(true)
const customLinkExpanded = ref(true)

// Custom link form
const customLinkTitle = ref('')
const customLinkUrl = ref('')
const isAddingLink = ref(false)

// Selected pages for bulk add
const selectedPages = ref([])
const isAddingPages = ref(false)

// Expanded item states (for editing)
const expandedItems = ref(new Set())

// Dragging state - only show drop zones while dragging
const isDragging = ref(false)

// Delete confirmation
const showDeleteConfirm = ref(false)
const itemToDelete = ref(null)

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Menus', href: route('menu.index') },
    { label: props.menu.name, last: true }
]

const totalItemsCount = computed(() => {
    const countItems = (items) => {
        return items.reduce((count, item) => {
            return count + 1 + (item.children ? countItems(item.children) : 0)
        }, 0)
    }
    return countItems(localMenuItems.value)
})

// Ensure all items have a children array for nesting
const ensureChildren = (items) => {
    return items.map(item => ({
        ...item,
        children: item.children ? ensureChildren(item.children) : []
    }))
}

watch(
    () => props.menuItems,
    (newItems) => {
        localMenuItems.value = ensureChildren([...newItems])
    },
    { immediate: true }
)

const toggleItemExpanded = (itemId) => {
    if (expandedItems.value.has(itemId)) {
        expandedItems.value.delete(itemId)
    } else {
        expandedItems.value.add(itemId)
    }
    expandedItems.value = new Set(expandedItems.value)
}

const isItemExpanded = (itemId) => {
    return expandedItems.value.has(itemId)
}

const flattenItems = (items, parentId = null) => {
    return items.flatMap((item, index) => [
        {
            id: item.id,
            order: index,
            parent_id: parentId,
            url: item.url,
            target: item.target
        },
        ...(item.children ? flattenItems(item.children, item.id) : [])
    ])
}

const saveOrder = async () => {
    isSaving.value = true
    try {
        const response = await axios.post(
            route('menu.items.updateOrder', { menu: props.menu.id }),
            { items: flattenItems(localMenuItems.value) }
        )
        localMenuItems.value = ensureChildren(response.data.menuItems)
    } catch (error) {
        console.error('Failed to save order:', error)
    } finally {
        isSaving.value = false
    }
}

const addCustomLink = async () => {
    if (!customLinkTitle.value.trim()) return

    isAddingLink.value = true
    try {
        const response = await axios.post(
            route('menu.items.store', { menu: props.menu.id }),
            {
                title: customLinkTitle.value,
                url: customLinkUrl.value || '#',
                target: '_self',
                type: 'custom'
            }
        )
        localMenuItems.value = ensureChildren(response.data.menuItems)
        customLinkTitle.value = ''
        customLinkUrl.value = ''
    } catch (error) {
        console.error('Failed to add custom link:', error)
    } finally {
        isAddingLink.value = false
    }
}

const addSelectedPages = async () => {
    if (selectedPages.value.length === 0) return

    isAddingPages.value = true
    const pagesToAdd = [...selectedPages.value]
    selectedPages.value = []

    try {
        for (const pageId of pagesToAdd) {
            const page = props.availablePages.find(p => p.id === pageId)
            if (page) {
                const response = await axios.post(
                    route('menu.items.store', { menu: props.menu.id }),
                    {
                        title: page.title,
                        url: `/${page.slug}`,
                        target: '_self',
                        type: 'page'
                    }
                )
                localMenuItems.value = ensureChildren(response.data.menuItems)
            }
        }
    } catch (error) {
        console.error('Failed to add pages:', error)
    } finally {
        isAddingPages.value = false
    }
}

const updateItem = async (item) => {
    isSaving.value = true
    try {
        const response = await axios.put(
            route('menu.items.update', { menu: props.menu.id, menuItem: item.id }),
            {
                title: item.title,
                url: item.url,
                target: item.target
            }
        )
        localMenuItems.value = ensureChildren(response.data.menuItems)
    } catch (error) {
        console.error('Failed to update item:', error)
    } finally {
        isSaving.value = false
    }
}

const confirmDeleteItem = (item) => {
    itemToDelete.value = item
    showDeleteConfirm.value = true
}

const deleteItem = async () => {
    if (!itemToDelete.value) return

    isDeleting.value = itemToDelete.value.id
    try {
        const response = await axios.delete(
            route('menu.items.destroy', { menu: props.menu.id, menuItem: itemToDelete.value.id })
        )
        localMenuItems.value = ensureChildren(response.data.menuItems)
        showDeleteConfirm.value = false
        itemToDelete.value = null
    } catch (error) {
        console.error('Failed to delete item:', error)
    } finally {
        isDeleting.value = null
    }
}

const cancelDelete = () => {
    showDeleteConfirm.value = false
    itemToDelete.value = null
}

const copyItem = async (item) => {
    isSaving.value = true
    try {
        const response = await axios.post(
            route('menu.items.store', { menu: props.menu.id }),
            {
                title: `${item.title} (Copy)`,
                url: item.url,
                target: item.target,
                parent_id: item.parent_id
            }
        )
        localMenuItems.value = ensureChildren(response.data.menuItems)
    } catch (error) {
        console.error('Failed to copy item:', error)
    } finally {
        isSaving.value = false
    }
}

const toggleSelectAll = () => {
    if (selectedPages.value.length === props.availablePages.length) {
        selectedPages.value = []
    } else {
        selectedPages.value = props.availablePages.map(p => p.id)
    }
}
</script>

<template>
    <Head :title="title" />

    <AppSectionHeader :title="`Menu Builder: ${menu.name}`" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex items-center gap-3">
                <!-- Saving Indicator -->
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="isSaving"
                        class="flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-500/10 to-indigo-500/10 px-4 py-2 text-sm font-medium text-blue-600 ring-1 ring-blue-500/20 dark:from-blue-500/20 dark:to-indigo-500/20 dark:text-blue-400"
                    >
                        <i class="ri-loader-4-line animate-spin"></i>
                        <span>Saving changes...</span>
                    </div>
                </transition>

                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('menu.index'))"
                >
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back to Menus
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <!-- WordPress-style Two Panel Layout -->
    <div class="grid gap-6 lg:grid-cols-12">
        <!-- Left Panel: Add Items -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="sticky top-6 space-y-4">
                <!-- Panel Header -->
                <div class="flex items-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 shadow-lg shadow-blue-500/25">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                        <i class="ri-add-circle-line text-lg text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">Add Menu Items</h3>
                        <p class="text-xs text-blue-100">Pages & Custom Links</p>
                    </div>
                </div>

                <!-- Pages Section -->
                <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-700/80 dark:bg-gray-800">
                    <button
                        @click="pagesExpanded = !pagesExpanded"
                        class="flex w-full items-center justify-between border-b border-gray-100 px-4 py-3.5 text-left transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
                    >
                        <span class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                <i class="ri-file-list-3-line text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Pages</span>
                                <span class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ availablePages.length }}</span>
                            </div>
                        </span>
                        <i :class="pagesExpanded ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-lg text-gray-400"></i>
                    </button>
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-show="pagesExpanded">
                            <div v-if="availablePages.length" class="max-h-64 overflow-y-auto p-4">
                                <div class="mb-3 flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-700/50">
                                    <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <input
                                            type="checkbox"
                                            :checked="selectedPages.length === availablePages.length && availablePages.length > 0"
                                            @change="toggleSelectAll"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                        />
                                        Select All
                                    </label>
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/50 dark:text-blue-400">{{ selectedPages.length }} selected</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        v-for="page in availablePages"
                                        :key="page.id"
                                        class="flex cursor-pointer items-center gap-3 rounded-lg border border-transparent px-3 py-2.5 transition hover:border-blue-200 hover:bg-blue-50/50 dark:hover:border-blue-800 dark:hover:bg-blue-900/20"
                                        :class="{ 'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/30': selectedPages.includes(page.id) }"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="selectedPages"
                                            :value="page.id"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                        />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ page.title }}</span>
                                    </label>
                                </div>
                            </div>
                            <div v-else class="p-6 text-center">
                                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                    <i class="ri-file-list-3-line text-gray-400"></i>
                                </div>
                                <p class="text-sm text-gray-500">No pages available</p>
                            </div>
                            <div class="border-t border-gray-100 p-3 dark:border-gray-700">
                                <button
                                    @click="addSelectedPages"
                                    :disabled="selectedPages.length === 0 || isAddingPages"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:from-blue-700 hover:to-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <i v-if="isAddingPages" class="ri-loader-4-line animate-spin"></i>
                                    <i v-else class="ri-add-line"></i>
                                    Add to Menu
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Custom Links Section -->
                <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-700/80 dark:bg-gray-800">
                    <button
                        @click="customLinkExpanded = !customLinkExpanded"
                        class="flex w-full items-center justify-between border-b border-gray-100 px-4 py-3.5 text-left transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
                    >
                        <span class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                <i class="ri-link text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">Custom Links</span>
                        </span>
                        <i :class="customLinkExpanded ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-lg text-gray-400"></i>
                    </button>
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-show="customLinkExpanded">
                            <div class="space-y-4 p-4">
                                <div>
                                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                        <i class="ri-link text-gray-400"></i>
                                        URL
                                    </label>
                                    <input
                                        v-model="customLinkUrl"
                                        type="text"
                                        placeholder="https://example.com"
                                        class="w-full rounded-lg border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm transition placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:focus:bg-gray-600"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                        <i class="ri-text text-gray-400"></i>
                                        Link Text
                                    </label>
                                    <input
                                        v-model="customLinkTitle"
                                        type="text"
                                        placeholder="Enter menu label"
                                        class="w-full rounded-lg border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm transition placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:focus:bg-gray-600"
                                    />
                                </div>
                            </div>
                            <div class="border-t border-gray-100 p-3 dark:border-gray-700">
                                <button
                                    @click="addCustomLink"
                                    :disabled="!customLinkTitle.trim() || isAddingLink"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:from-purple-700 hover:to-purple-800 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <i v-if="isAddingLink" class="ri-loader-4-line animate-spin"></i>
                                    <i v-else class="ri-add-line"></i>
                                    Add to Menu
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Tips Card -->
                <div class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-4 dark:border-amber-900/50 dark:from-amber-900/20 dark:to-orange-900/20">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/50">
                            <i class="ri-lightbulb-line text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-amber-800 dark:text-amber-300">Quick Tips</h4>
                            <ul class="mt-1.5 space-y-1 text-xs text-amber-700 dark:text-amber-400/80">
                                <li>• Drag items to reorder</li>
                                <li>• Drop onto items to nest</li>
                                <li>• Click to expand & edit</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Menu Structure -->
        <div class="lg:col-span-8 xl:col-span-9">
            <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-700/80 dark:bg-gray-800">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50 px-5 py-4 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800/50">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30">
                            <i class="ri-menu-2-line text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menu Structure</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Drag to reorder • Click to edit</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 px-4 py-1.5 text-sm font-semibold text-white shadow-sm">
                            {{ totalItemsCount }} {{ totalItemsCount === 1 ? 'item' : 'items' }}
                        </span>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="p-5">
                    <draggable
                        v-if="localMenuItems.length"
                        v-model="localMenuItems"
                        group="menu"
                        item-key="id"
                        tag="div"
                        handle=".drag-handle"
                        ghost-class="ghost-item"
                        animation="200"
                        @start="isDragging = true"
                        @end="isDragging = false; saveOrder()"
                        class="space-y-3"
                    >
                        <template #item="{ element, index }">
                            <div class="menu-item-wp">
                                <!-- Menu Item Card -->
                                <div
                                    class="group overflow-hidden rounded-xl border transition-all duration-200"
                                    :class="isItemExpanded(element.id) 
                                        ? 'border-blue-300 bg-blue-50/30 shadow-md shadow-blue-500/10 dark:border-blue-700 dark:bg-blue-900/20' 
                                        : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600'"
                                >
                                    <!-- Collapsed Header -->
                                    <div class="flex items-center">
                                        <!-- Drag Handle -->
                                        <div class="drag-handle flex h-14 cursor-grab items-center border-r border-gray-100 px-4 text-gray-400 transition hover:bg-gray-50 hover:text-gray-600 active:cursor-grabbing dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                            <i class="ri-draggable text-xl"></i>
                                        </div>

                                        <!-- Item Number -->
                                        <div class="flex h-14 w-10 items-center justify-center border-r border-gray-100 text-sm font-medium text-gray-400 dark:border-gray-700">
                                            {{ index + 1 }}
                                        </div>

                                        <!-- Item Content -->
                                        <div
                                            @click="toggleItemExpanded(element.id)"
                                            class="flex flex-1 cursor-pointer items-center justify-between px-4 py-3"
                                        >
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                                    <i class="ri-file-line text-gray-500 dark:text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ element.title }}</span>
                                                    <span class="ml-2 text-xs text-gray-400">{{ element.url }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span v-if="element.target === '_blank'" class="rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                                    New Tab
                                                </span>
                                                <span v-if="element.children?.length" class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                    {{ element.children.length }} sub
                                                </span>
                                                <i :class="isItemExpanded(element.id) ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-xl text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Expanded Edit Form -->
                                    <transition
                                        enter-active-class="transition ease-out duration-200"
                                        enter-from-class="opacity-0"
                                        enter-to-class="opacity-100"
                                        leave-active-class="transition ease-in duration-150"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0"
                                    >
                                        <div
                                            v-if="isItemExpanded(element.id)"
                                            class="border-t border-blue-200 bg-gradient-to-b from-blue-50/50 to-white p-5 dark:border-blue-800 dark:from-blue-900/10 dark:to-gray-800"
                                        >
                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div>
                                                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                                        <i class="ri-text text-gray-400"></i>
                                                        Navigation Label
                                                    </label>
                                                    <input
                                                        v-model="element.title"
                                                        type="text"
                                                        class="w-full rounded-lg border-gray-200 bg-white px-3.5 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                                        @blur="updateItem(element)"
                                                    />
                                                </div>
                                                <div>
                                                    <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                                        <i class="ri-link text-gray-400"></i>
                                                        URL
                                                    </label>
                                                    <input
                                                        v-model="element.url"
                                                        type="text"
                                                        class="w-full rounded-lg border-gray-200 bg-white px-3.5 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                                        @blur="updateItem(element)"
                                                    />
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label class="mb-1.5 flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                                    <i class="ri-external-link-line text-gray-400"></i>
                                                    Open link in
                                                </label>
                                                <select
                                                    v-model="element.target"
                                                    class="w-full rounded-lg border-gray-200 bg-white px-3.5 py-2.5 text-sm shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 md:w-48"
                                                    @change="updateItem(element)"
                                                >
                                                    <option value="_self">Same window</option>
                                                    <option value="_blank">New tab</option>
                                                </select>
                                            </div>
                                            <div class="mt-5 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        @click="copyItem(element)"
                                                        class="flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                                    >
                                                        <i class="ri-file-copy-line"></i>
                                                        Duplicate
                                                    </button>
                                                    <button
                                                        @click="confirmDeleteItem(element)"
                                                        class="flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50"
                                                    >
                                                        <i class="ri-delete-bin-line"></i>
                                                        Remove
                                                    </button>
                                                </div>
                                                <button
                                                    @click="toggleItemExpanded(element.id)"
                                                    class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                                                >
                                                    <i class="ri-close-line"></i>
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </transition>
                                </div>

                                <!-- Nested Children Drop Zone -->
                                <draggable
                                    v-show="element.children?.length || isDragging"
                                    v-model="element.children"
                                    group="menu"
                                    item-key="id"
                                    tag="div"
                                    handle=".drag-handle"
                                    ghost-class="ghost-item"
                                    animation="200"
                                    @start="isDragging = true"
                                    @end="isDragging = false; saveOrder()"
                                    class="ml-10 mt-2 space-y-2 border-l-2 pl-5 transition-all"
                                    :class="element.children?.length
                                        ? 'border-indigo-200 dark:border-indigo-800'
                                        : 'min-h-[50px] rounded-lg border-dashed border-blue-300 bg-blue-50/50 dark:border-blue-700 dark:bg-blue-900/10'"
                                >
                                    <template #item="{ element: child, index: childIndex }">
                                        <div class="menu-item-wp">
                                            <div
                                                class="group overflow-hidden rounded-lg border transition-all duration-200"
                                                :class="isItemExpanded(child.id)
                                                    ? 'border-indigo-300 bg-indigo-50/30 shadow-sm dark:border-indigo-700 dark:bg-indigo-900/20'
                                                    : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600'"
                                            >
                                                <div class="flex items-center">
                                                    <div class="drag-handle flex h-11 cursor-grab items-center border-r border-gray-100 px-3 text-gray-400 transition hover:bg-gray-50 hover:text-gray-600 active:cursor-grabbing dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                                        <i class="ri-draggable"></i>
                                                    </div>
                                                    <div class="flex h-11 w-8 items-center justify-center border-r border-gray-100 text-xs text-gray-400 dark:border-gray-700">
                                                        {{ index + 1 }}.{{ childIndex + 1 }}
                                                    </div>
                                                    <div
                                                        @click="toggleItemExpanded(child.id)"
                                                        class="flex flex-1 cursor-pointer items-center justify-between px-3 py-2"
                                                    >
                                                        <div class="flex items-center gap-2">
                                                            <i class="ri-corner-down-right-line text-indigo-400"></i>
                                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ child.title }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span v-if="child.target === '_blank'" class="rounded bg-amber-100 px-1.5 py-0.5 text-xs text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                                                New Tab
                                                            </span>
                                                            <i :class="isItemExpanded(child.id) ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-gray-400"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Child Expanded Edit Form -->
                                                <transition
                                                    enter-active-class="transition ease-out duration-200"
                                                    enter-from-class="opacity-0"
                                                    enter-to-class="opacity-100"
                                                    leave-active-class="transition ease-in duration-150"
                                                    leave-from-class="opacity-100"
                                                    leave-to-class="opacity-0"
                                                >
                                                    <div
                                                        v-if="isItemExpanded(child.id)"
                                                        class="border-t border-indigo-200 bg-gradient-to-b from-indigo-50/50 to-white p-4 dark:border-indigo-800 dark:from-indigo-900/10 dark:to-gray-800"
                                                    >
                                                        <div class="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-400">Label</label>
                                                                <input
                                                                    v-model="child.title"
                                                                    type="text"
                                                                    class="w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                                                    @blur="updateItem(child)"
                                                                />
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-400">URL</label>
                                                                <input
                                                                    v-model="child.url"
                                                                    type="text"
                                                                    class="w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                                                    @blur="updateItem(child)"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <label class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-400">Open link in</label>
                                                            <select
                                                                v-model="child.target"
                                                                class="w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 md:w-40"
                                                                @change="updateItem(child)"
                                                            >
                                                                <option value="_self">Same window</option>
                                                                <option value="_blank">New tab</option>
                                                            </select>
                                                        </div>
                                                        <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
                                                            <div class="flex items-center gap-2">
                                                                <button
                                                                    @click="copyItem(child)"
                                                                    class="flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                                                >
                                                                    <i class="ri-file-copy-line"></i>
                                                                    Duplicate
                                                                </button>
                                                                <button
                                                                    @click="confirmDeleteItem(child)"
                                                                    class="flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30"
                                                                >
                                                                    <i class="ri-delete-bin-line"></i>
                                                                    Remove
                                                                </button>
                                                            </div>
                                                            <button
                                                                @click="toggleItemExpanded(child.id)"
                                                                class="flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                                            >
                                                                <i class="ri-close-line"></i>
                                                                Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </transition>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </template>
                    </draggable>

                    <!-- Empty State -->
                    <div v-else class="py-16 text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                            <i class="ri-menu-add-line text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                            Your menu is empty
                        </h3>
                        <p class="mx-auto max-w-sm text-gray-500 dark:text-gray-400">
                            Start building your menu by adding pages or custom links from the panel on the left.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                            <i class="ri-delete-bin-line text-xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Delete Menu Item</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400">
                        Are you sure you want to delete "<strong class="text-gray-900 dark:text-white">{{ itemToDelete?.title }}</strong>"? This action cannot be undone.
                    </p>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    <button
                        @click="cancelDelete"
                        class="rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteItem"
                        :disabled="isDeleting"
                        class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:from-red-600 hover:to-red-700 disabled:opacity-50"
                    >
                        <i v-if="isDeleting" class="ri-loader-4-line animate-spin"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.ghost-item {
    opacity: 0.5;
    background: linear-gradient(135deg, rgb(219, 234, 254), rgb(224, 231, 255));
    border: 2px dashed rgb(96, 165, 250) !important;
    border-radius: 0.75rem;
}

.ghost-item * {
    opacity: 0;
}

.menu-item-wp {
    will-change: transform;
}
</style>
