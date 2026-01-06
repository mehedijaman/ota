<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton
                    v-if="can('acl-user-list')"
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('user.index'))"
                >
                    <i class="ri-arrow-left-s-line mr-1"></i>
                    Back to List
                </AppButton>

                <AppButton
                    v-if="users.data.length && can('acl-user-create')"
                    class="btn btn-primary"
                    @click="confirmRestore(route('user.restoreRecycleBin'))"
                >
                    <i class="ri-recycle-fill mr-1"></i>
                    Restore Recycle Bin
                </AppButton>

                <AppButton
                    v-if="users.data.length && can('acl-user-delete')"
                    class="btn btn-destructive"
                    @click="confirmDelete(route('user.emptyRecycleBin'))"
                >
                    <i class="ri-delete-bin-7-line mr-1"></i>
                    Empty Recycle Bin
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataSearch
        v-if="users.data.length || route().params.searchTerm"
        :url="route('user.recycleBin')"
        fields-to-search="name,email"
    ></AppDataSearch>

    <AppDataTable v-if="users.data.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow
                    v-for="(item, index) in users.data"
                    :key="item.uuid"
                >
                    <AppDataTableData>
                        {{
                            (users.current_page - 1) * users.per_page +
                            (index + 1)
                        }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600 dark:bg-primary-900 dark:text-primary-300"
                            >
                                {{ item.name?.charAt(0)?.toUpperCase() }}
                            </div>
                            <span>{{ item.name }}</span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.email }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="item.profile_type === 'Employee' 
                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                        >
                            {{ item.profile_type }}
                        </span>
                    </AppDataTableData>

                    <AppDataTableData>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="role in item.roles"
                                :key="role"
                                class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300"
                            >
                                {{ role }}
                            </span>
                            <span v-if="!item.roles || item.roles.length === 0" class="text-sm text-gray-400">
                                No roles
                            </span>
                        </div>
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.deleted_at }}
                    </AppDataTableData>

                    <AppDataTableData>
                        <!-- Restore -->
                        <AppTooltip
                            v-if="can('acl-user-create')"
                            text="Restore"
                            class="mr-2"
                        >
                            <AppButton
                                class="btn btn-icon btn-primary"
                                @click="
                                    confirmRestore(
                                        route('user.restore', { uuid: item.uuid })
                                    )
                                "
                            >
                                <i class="ri-recycle-fill"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete -->
                        <AppTooltip
                            v-if="can('acl-user-delete')"
                            text="Permanently Delete"
                        >
                            <AppButton
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('user.destroyForce', { uuid: item.uuid })
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
        v-if="users.data.length"
        :links="users.links"
        :from="users.from ?? 0"
        :to="users.to ?? 0"
        :total="users.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!users.data.length"></AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()
const { title } = useTitle('User Recycle Bin')

const breadCrumb = [
    { label: 'Dashboard', href: route('dashboard.index') },
    { label: 'Users', href: route('user.index') },
    { label: title, last: true }
]

const props = defineProps({
    users: {
        type: Object,
        default: () => {}
    }
})

const headers = [
    'SL',
    'Name',
    'Email',
    'Type',
    'Roles',
    'Deleted At',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}
const confirmRestore = (restoreRoute) => {
    confirmDialogRef.value.confirmRestore(restoreRoute)
}
</script>
