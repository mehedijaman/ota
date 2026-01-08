<script setup>
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Menu Items')

const props = defineProps({
    menu: {
        type: Object,
        required: true
    },
    menuItems: {
        type: Array,
        default: () => []
    }
})

const localMenuItems = ref([...props.menuItems])
const newItemTitle = ref('')
const newItemUrl = ref('')
const newItemTarget = ref('_self')
const newItemParentId = ref(null)
const editingItem = ref(null)

watch(
    () => props.menuItems,
    (newItems) => {
        localMenuItems.value = [...newItems]
    }
)

const saveOrder = () => {
    router.post(
        route('menu.items.updateOrder', { menu: props.menu.id }),
        {
            items: flattenItems(localMenuItems.value)
        },
        {
            preserveScroll: true,
            onSuccess: (response) => {
                // Display toast notification
                showToast(
                    response.props.flash.success || 'Order updated successfully'
                )
                console.log('Order updated!')
            },
            onError: (errors) => {
                showToast('Error updating order.', 'error')
                console.error('Error updating order:', errors)
            }
        }
    )
}

const showToast = (message, type = 'success') => {
    // Replace this with your toast implementation
    if (type === 'success') {
        console.log('Success:', message)
        // alert(message); // Example using alert
    } else {
        console.error('Error:', message)
        alert('Error: ' + message)
    }
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

const addNewItem = () => {
    if (newItemTitle.value.trim() === '') return

    router.post(
        route('menu.items.store', { menu: props.menu.id }),
        {
            title: newItemTitle.value,
            parent_id: newItemParentId.value,
            url: newItemUrl.value,
            target: newItemTarget.value
        },
        {
            onSuccess: (page) => {
                localMenuItems.value = page.props.menuItems
                newItemTitle.value = ''
                newItemUrl.value = ''
                newItemTarget.value = '_self'
                newItemParentId.value = null
            }
        }
    )
}

const confirmDelete = (deleteRoute) => {
    if (confirm('Are you sure you want to delete this item?')) {
        router.delete(deleteRoute, {
            onSuccess: (page) => {
                localMenuItems.value = page.props.menuItems
            }
        })
    }
}

const getParentOptions = () => {
    return [
        { id: null, title: 'No Parent' },
        ...localMenuItems.value.map((item) => ({
            id: item.id,
            title: item.title
        }))
    ]
}

const startEditing = (item) => {
    editingItem.value = { ...item } // Create a copy for editing
}

const saveItem = (item) => {
    router.put(
        route('menu.items.update', { menu: props.menu.id, menuItem: item.id }),
        {
            title: item.title,
            url: item.url,
            target: item.target
        },
        {
            onSuccess: (page) => {
                localMenuItems.value = page.props.menuItems
                editingItem.value = null
            }
        }
    )
}
</script>

<template>
    <Head :title="title" />
    <div class="rounded-lg bg-white p-4 shadow">
        <div class="mb-4">
            <input
                v-model="newItemTitle"
                type="text"
                placeholder="New Menu Item"
                class="w-full border p-2"
            />
            <input
                v-model="newItemUrl"
                type="text"
                placeholder="URL"
                class="mt-2 w-full border p-2"
            />
            <select v-model="newItemTarget" class="mt-2 w-full border p-2">
                <option value="_self">Self</option>
                <option value="_blank">Blank</option>
            </select>
            <select v-model="newItemParentId" class="mt-2 w-full border p-2">
                <option
                    v-for="option in getParentOptions()"
                    :key="option.id"
                    :value="option.id"
                >
                    {{ option.title }}
                </option>
            </select>
            <button
                @click="addNewItem"
                class="mt-2 rounded bg-blue-500 px-4 py-2 text-white"
            >
                Add Item
            </button>
        </div>
        <draggable
            v-model="localMenuItems"
            group="menu"
            item-key="id"
            @end="saveOrder"
        >
            <template #item="{ element }">
                <div class="mb-1 rounded border bg-gray-100 p-2">
                    <div v-if="editingItem && editingItem.id === element.id">
                        <input
                            v-model="editingItem.title"
                            type="text"
                            class="w-full border p-2"
                        />
                        <input
                            v-model="editingItem.url"
                            type="text"
                            class="mt-2 w-full border p-2"
                        />
                        <select
                            v-model="editingItem.target"
                            class="mt-2 w-full border p-2"
                        >
                            <option value="_self">Self</option>
                            <option value="_blank">Blank</option>
                        </select>
                        <button
                            @click="saveItem(editingItem)"
                            class="mt-2 rounded bg-green-500 px-4 py-2 text-white"
                        >
                            Save
                        </button>
                        <button
                            @click="editingItem = null"
                            class="mt-2 rounded bg-gray-500 px-4 py-2 text-white"
                        >
                            Cancel
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-between">
                        <span>{{ element.title }}</span>
                        <div>
                            <button
                                @click="startEditing(element)"
                                class="mr-2 text-blue-500"
                            >
                                Edit
                            </button>
                            <button
                                @click="
                                    confirmDelete(
                                        route('menu.items.destroy', {
                                            menu: props.menu.id,
                                            menuItem: element.id
                                        })
                                    )
                                "
                                class="text-red-500"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                    <draggable
                        v-if="element.children"
                        v-model="element.children"
                        group="menu"
                        item-key="id"
                        @end="saveOrder"
                    >
                        <template #item="{ element: childElement }">
                            <div
                                class="mb-1 ml-4 rounded border bg-gray-200 p-2"
                            >
                                <div
                                    v-if="
                                        editingItem &&
                                        editingItem.id === childElement.id
                                    "
                                >
                                    <input
                                        v-model="editingItem.title"
                                        type="text"
                                        class="w-full border p-2"
                                    />
                                    <input
                                        v-model="editingItem.url"
                                        type="text"
                                        class="mt-2 w-full border p-2"
                                    />
                                    <select
                                        v-model="editingItem.target"
                                        class="mt-2 w-full border p-2"
                                    >
                                        <option value="_self">Self</option>
                                        <option value="_blank">Blank</option>
                                    </select>
                                    <button
                                        @click="saveItem(editingItem)"
                                        class="mt-2 rounded bg-green-500 px-4 py-2 text-white"
                                    >
                                        Save
                                    </button>
                                    <button
                                        @click="editingItem = null"
                                        class="mt-2 rounded bg-gray-500 px-4 py-2 text-white"
                                    >
                                        Cancel
                                    </button>
                                </div>
                                <div
                                    v-else
                                    class="flex items-center justify-between"
                                >
                                    <span>{{ childElement.title }}</span>
                                    <div>
                                        <button
                                            @click="startEditing(childElement)"
                                            class="mr-2 text-blue-500"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="
                                                confirmDelete(
                                                    route(
                                                        'menu.items.destroy',
                                                        {
                                                            menu: props.menu.id,
                                                            menuItem:
                                                                childElement.id
                                                        }
                                                    )
                                                )
                                            "
                                            class="text-red-500"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </draggable>
                </div>
            </template>
        </draggable>
    </div>
</template>
