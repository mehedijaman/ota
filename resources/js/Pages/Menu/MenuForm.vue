<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton
                class="btn btn-secondary"
                @click="$inertia.visit(route('menu.index'))"
            >
                <i class="ri-arrow-left-line mr-1"></i>
                Back to Menus
            </AppButton>
        </template>
    </AppSectionHeader>

    <div class="grid gap-6 lg:grid-cols-12">
        <!-- Main Form -->
        <div class="lg:col-span-8">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <!-- Header -->
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/20">
                            <i :class="isCreate ? 'ri-add-line' : 'ri-edit-line'" class="text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ isCreate ? 'Create New Menu' : 'Edit Menu' }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ isCreate ? 'Set up a new navigation menu' : 'Update menu settings' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <AppFormErrors class="mb-6" />

                    <form class="space-y-6">
                        <!-- Menu Name -->
                        <div class="space-y-2">
                            <label for="name" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="ri-text text-gray-400"></i>
                                Menu Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Main Navigation, Footer Links"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 transition-all duration-200 placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:bg-gray-700"
                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/10': errorsFields.includes('name') }"
                            />
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                A descriptive name for your menu
                            </p>
                        </div>

                        <!-- Position -->
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="ri-map-pin-line text-gray-400"></i>
                                Position
                            </label>
                            <AppCombobox
                                v-model="form.position"
                                :options="positions"
                                combo-label="Select Menu Position"
                                class="w-full"
                            />
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Choose where this menu will be displayed on your site
                            </p>
                        </div>

                        <!-- Active Status -->
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/30">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="form.active ? 'bg-green-100 dark:bg-green-900/30' : 'bg-gray-200 dark:bg-gray-700'">
                                        <i :class="form.active ? 'ri-checkbox-circle-fill text-green-600 dark:text-green-400' : 'ri-close-circle-line text-gray-400'" class="text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Menu Status</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ form.active ? 'Menu is visible on the site' : 'Menu is hidden from the site' }}
                                        </p>
                                    </div>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input
                                        id="active"
                                        v-model="form.active"
                                        type="checkbox"
                                        class="peer sr-only"
                                    />
                                    <div class="peer h-6 w-11 rounded-full bg-gray-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none dark:bg-gray-600"></div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <AppButton class="btn btn-secondary" @click="$inertia.visit(route('menu.index'))">
                        <i class="ri-close-line mr-1"></i>
                        Cancel
                    </AppButton>
                    <AppButton class="btn btn-primary" @click="submitForm" :disabled="form.processing">
                        <i v-if="form.processing" class="ri-loader-4-line mr-1 animate-spin"></i>
                        <i v-else :class="isCreate ? 'ri-add-line' : 'ri-check-line'" class="mr-1"></i>
                        {{ isCreate ? 'Create Menu' : 'Save Changes' }}
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4">
            <div class="sticky top-6 space-y-4">
                <!-- Tips Card -->
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                            <i class="ri-lightbulb-line text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Tips</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-2">
                            <i class="ri-checkbox-circle-line mt-0.5 text-green-500"></i>
                            <span>Use descriptive names like "Main Navigation" or "Footer Menu"</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ri-map-pin-line mt-0.5 text-blue-500"></i>
                            <span>Position determines where the menu appears on your site</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ri-eye-off-line mt-0.5 text-amber-500"></i>
                            <span>Inactive menus won't be visible to visitors</span>
                        </li>
                    </ul>
                </div>

                <!-- Quick Actions Card -->
                <div v-if="!isCreate && menu" class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <i class="ri-flashlight-line text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>
                    <div class="space-y-2">
                        <AppButton
                            class="btn btn-secondary w-full justify-start"
                            @click="$inertia.visit(route('menu.items.index', menu.id))"
                        >
                            <i class="ri-menu-line mr-2"></i>
                            Manage Menu Items
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import { onMounted } from 'vue'

const props = defineProps({
    menu: {
        type: Object,
        default: null
    },
    positions: {
        type: Array,
        default: () => []
    }
})

const { title } = useTitle('Menu')
const { isCreate } = useFormContext()
const { errorsFields } = useFormErrors()

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Menus', href: route('menu.index') },
    { label: title, last: true }
]

const form = useForm({
    name: props.menu ? props.menu.name : '',
    position: props.menu ? props.menu.position : '',
    active: props.menu ? props.menu.active : true
})

onMounted(() => {
    if (form.position) {
        form.position = props.positions.find(
            (position) => position.value === form.position
        )
    }
})

const getValueFromKey = (data, key) => {
    return data[key] ? data[key].value : null
}

const submitForm = () => {
    const postData = (data) => {
        const commonData = {
            ...data,
            position: getValueFromKey(data, 'position'),
        }

        return isCreate.value ? commonData : { ...commonData, _method: 'PUT' }
    }

    if (isCreate.value) {
        form.transform(postData).post(route('menu.store'))
    } else {
        form.transform(postData).post(route('menu.update', props.menu.id))
    }
}
</script>
