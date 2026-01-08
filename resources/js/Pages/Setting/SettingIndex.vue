<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <!-- <template #right>
            <AppButton
                class="btn btn-primary"
                @click="$inertia.visit(route('setting.create'))"
            >
                <i class="ri-add-fill mr-1"></i>
                Create Setting
            </AppButton>
        </template> -->
    </AppSectionHeader>

    <!-- <div class="mx-4 min-h-screen max-w-screen-xl sm:mx-8 xl:mx-auto"> -->
    <div class="grid grid-cols-8 border-t sm:grid-cols-10">
        <div class="relative my-4 w-56 sm:hidden">
            <input
                class="peer hidden"
                type="checkbox"
                name="select-1"
                id="select-1"
            />
            <label
                for="select-1"
                class="flex w-full cursor-pointer select-none rounded-lg border p-2 px-3 text-sm text-gray-700 ring-blue-700 peer-checked:ring"
                >Settings
            </label>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="pointer-events-none absolute right-0 top-3 ml-auto mr-5 h-4 text-slate-700 transition peer-checked:rotate-180"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
            <ul
                class="max-h-0 select-none flex-col overflow-hidden rounded-b-lg shadow-md transition-all duration-300 peer-checked:max-h-56 peer-checked:py-3"
            >
                <li
                    class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white"
                >
                    Settings
                </li>
                <!-- <li
                    class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white"
                >
                    Team
                </li>
                <li
                    class="cursor-pointer px-3 py-2 text-sm text-slate-600 hover:bg-blue-700 hover:text-white"
                >
                    Others
                </li> -->
            </ul>
        </div>

        <div class="col-span-2 hidden sm:block">
            <ul>
                <li
                    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Settings
                </li>
                <!-- <li
                    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Site Settings
                </li> -->
                <!-- 
                <li
                    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Users
                </li>
                <li
                    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Profile
                </li>
                <li
                    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Billing
                </li>
                <li
                    class="mt-5 cursor-pointer border-l-2 border-l-blue-700 px-2 py-2 font-semibold text-blue-700 transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Notifications
                </li>
                <li
                    class="mt-5 cursor-pointer border-l-2 border-transparent px-2 py-2 font-semibold transition hover:border-l-blue-700 hover:text-blue-700"
                >
                    Integrations
                </li> -->
            </ul>
        </div>

        <div class="col-span-8">
            <SiteSetting :settings="settings" />
            <HomepageSetting :settings="settings" />
            <ProductSetting :settings="settings" />
            <ContactSetting :settings="settings" />
        </div>
    </div>
    <!-- </div> -->

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import { useForm } from '@inertiajs/vue3'
import useFormContext from '@/Composables/useFormContext'
import useFormErrors from '@/Composables/useFormErrors'
import ContactSetting from './Components/ContactSetting.vue'
import SiteSetting from './Components/SiteSetting.vue'
import HomepageSetting from './Components/HomepageSetting.vue'
import ProductSetting from './Components/ProductSetting.vue'

const { title } = useTitle('Setting')
const { can } = useAuthCan()

const props = defineProps({
    settings: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Settings', last: true }
]

const headers = ['ID', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

const form = useForm({
    address: props.setting ? props.setting.address : ''
})

const { isCreate, isEdit } = useFormContext()

const submitForm = () => {
    if (isCreate.value) {
        form.post(route('setting.store'))
    }

    if (isEdit.value) {
        form.put(route('setting.update', props.setting.id))
    }
}

const { errorsFields } = useFormErrors()
</script>
