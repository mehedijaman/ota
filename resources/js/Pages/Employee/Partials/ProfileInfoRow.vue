<template>
    <tr class="border-b border-t border-gray-200 dark:border-gray-700">
        <th
            scope="row"
            class="whitespace-nowrap bg-gray-50 px-6 py-4 font-medium text-gray-900 dark:bg-gray-800 dark:text-white"
        >
            {{ title }}
        </th>
        <td class="px-6 py-4">
            {{ value }}
        </td>
    </tr>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: null
    },
    value: {
        type: String || Number,
        default: null
    }
})
</script>
