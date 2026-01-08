<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex flex-col gap-2 md:flex-row">
                <AppButton
                    class="btn btn-primary"
                    @click="$inertia.visit(route('backup.create.full'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Full Backup
                </AppButton>
                <AppButton
                    class="btn btn-primary"
                    @click="$inertia.visit(route('backup.create.database'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create Database Backup
                </AppButton>
                <AppButton
                    class="btn btn-primary"
                    @click="$inertia.visit(route('backup.create.file'))"
                >
                    <i class="ri-add-fill mr-1"></i>
                    Create File Backup
                </AppButton>

                <AppButton
                    class="btn btn-secondary"
                    @click="$inertia.visit(route('backup.clean'))"
                >
                    <i class="ri-delete-bin-line mr-1"></i>
                    Clean Backups
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <AppDataTable v-if="backups.length" :headers="headers">
        <template #TableBody>
            <tbody>
                <AppDataTableRow v-for="(item, index) in backups" :key="index">
                    <AppDataTableData>
                        {{ ++index }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.name }}
                    </AppDataTableData>

                    <AppDataTableData>
                        {{ item.date }}
                    </AppDataTableData>

                    <AppDataTableData> {{ item.size }} MB </AppDataTableData>

                    <AppDataTableData>
                        <!-- Edit backup -->
                        <AppTooltip text="Edit Backup" class="mr-2">
                            <AppButton
                                v-if="can('backup-download')"
                                class="btn btn-icon btn-primary"
                                @click="downloadBackup(item.name)"
                            >
                                <i class="ri-download-2-line"></i>
                            </AppButton>
                        </AppTooltip>

                        <!-- Delete backup -->
                        <AppTooltip text="Delete Backup">
                            <AppButton
                                v-if="can('backup-delete')"
                                class="btn btn-icon btn-destructive"
                                @click="
                                    confirmDelete(
                                        route('backup.destroy', item.name)
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

    <AppAlert v-if="!backups.length" class="mt-4"> No backups found. </AppAlert>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'

const { title } = useTitle('Backup')
const { can } = useAuthCan()

const props = defineProps({
    backups: {
        type: Object,
        default: () => {}
    }
})

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Backups', last: true }
]

const headers = ['SL', 'Path', 'Date', 'Size', 'Actions']

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

function downloadBackup(name) {
    window.open(route('backup.download', name), '_blank')
}
</script>
