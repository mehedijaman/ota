<template>
    <AppDataSearch
        v-if="employees.data.length || route().params.searchTerm"
        :url="route('employee.index')"
        fields-to-search="name,code,email,phone"
    ></AppDataSearch>

    <AppDataTable v-if="employees.data.length" :headers="headers">
        <template #TableBody>
            <tbody class="divide-y divide-neutral-4">
                <AppDataTableRow
                    v-for="(item, index) in employees.data"
                    :key="item.id"
                    class="group transition-colors hover:bg-neutral-2"
                >
                    <AppDataTableData class="text-neutral-10 text-center w-12">
                        {{ (employees.current_page - 1) * employees.per_page + (index + 1) }}
                    </AppDataTableData>

                    <!-- Employee Info (Name, Code, Designation) -->
                    <AppDataTableData>
                        <div class="flex items-center gap-3">
                            <div v-if="item.photo_url" class="h-10 w-10 shrink-0 overflow-hidden rounded-full shadow-sm">
                                <img :src="item.photo_url" :alt="item.name" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-indigo-600 text-white font-semibold text-sm shadow-sm">
                                {{ getInitials(item.name) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-neutral-12 truncate">{{ item.name }}</div>
                                <div class="flex items-center gap-2 text-xs text-neutral-10">
                                    <span class="inline-flex items-center gap-1 font-mono text-primary-10">
                                        <i class="ri-id-card-line text-xs"></i>
                                        {{ item.code }}
                                    </span>
                                    <span v-if="item.designation" class="text-neutral-8">•</span>
                                    <span v-if="item.designation">{{ item.designation?.name }}</span>
                                </div>
                            </div>
                        </div>
                    </AppDataTableData>

                    <!-- Joining Date & Service -->
                    <AppDataTableData>
                        <div class="flex flex-col">
                            <span class="flex items-center gap-1.5 text-sm text-neutral-12">
                                <i class="ri-calendar-line text-neutral-9"></i>
                                {{ item.joining_date }}
                            </span>
                            <span v-if="item.service_length" class="text-xs text-neutral-10 mt-0.5">
                                {{ item.service_length }}
                            </span>
                        </div>
                    </AppDataTableData>

                    <!-- Contact -->
                    <AppDataTableData>
                        <div class="flex flex-col gap-0.5">
                            <span v-if="item.phone" class="flex items-center gap-1.5 text-sm text-neutral-11">
                                <i class="ri-phone-line text-neutral-9"></i>
                                {{ item.phone }}
                            </span>
                            <span v-if="item.email" class="flex items-center gap-1.5 text-xs text-neutral-10">
                                <i class="ri-mail-line text-neutral-9"></i>
                                {{ item.email }}
                            </span>
                            <span v-if="!item.phone && !item.email" class="text-sm text-neutral-9">-</span>
                        </div>
                    </AppDataTableData>

                    <!-- Status -->
                    <AppDataTableData>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="item.is_active 
                                ? 'bg-emerald-100 text-emerald-700' 
                                : 'bg-neutral-100 text-neutral-600'"
                        >
                            <span 
                                class="h-1.5 w-1.5 rounded-full"
                                :class="item.is_active ? 'bg-emerald-500' : 'bg-neutral-400'"
                            ></span>
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </AppDataTableData>

                    <!-- Actions Dropdown -->
                    <AppDataTableData class="overflow-visible text-right" @click.stop>
                        <AppDropdown placement="bottom-end">
                            <template #trigger>
                                <AppButton
                                    class="btn btn-icon btn-secondary"
                                    size="sm"
                                    aria-label="More actions"
                                >
                                    <i class="ri-more-2-fill"></i>
                                </AppButton>
                            </template>

                            <div class="py-1">
                                <AppDropdownItem 
                                    v-if="can('employee-view')" 
                                    @click="$inertia.visit(route('employee.show', item.uuid))"
                                >
                                    <i class="ri-eye-line mr-2 text-neutral-9"></i>
                                    View Details
                                </AppDropdownItem>

                                <AppDropdownItem 
                                    v-if="can('employee-edit')" 
                                    @click="$inertia.visit(route('employee.edit', item.uuid))"
                                >
                                    <i class="ri-edit-line mr-2 text-neutral-9"></i>
                                    Edit Employee
                                </AppDropdownItem>

                                <AppDropdownItem 
                                    v-if="can('employee-edit') && !item.has_user && item.email" 
                                    @click="openCreateUserModal(item)"
                                >
                                    <i class="ri-user-add-line mr-2 text-neutral-9"></i>
                                    Create User
                                </AppDropdownItem>

                                <div class="border-t border-neutral-4 my-1"></div>

                                <AppDropdownItem 
                                    v-if="can('employee-delete')" 
                                    class="text-danger hover:bg-danger-light"
                                    @click="confirmDelete(route('employee.destroy', item.uuid))"
                                >
                                    <i class="ri-delete-bin-line mr-2"></i>
                                    Delete Employee
                                </AppDropdownItem>
                            </div>
                        </AppDropdown>
                    </AppDataTableData>
                </AppDataTableRow>
            </tbody>
        </template>
    </AppDataTable>

    <AppPaginator
        v-if="employees.data.length"
        :links="employees.links"
        :from="employees.from ?? 0"
        :to="employees.to ?? 0"
        :total="employees.total ?? 0"
        class="mt-4 justify-center"
    ></AppPaginator>

    <AppEmptyState v-if="!employees.data.length" class="mt-4">
    </AppEmptyState>

    <AppConfirmDialog ref="confirmDialogRef"></AppConfirmDialog>

    <!-- Create User Modal -->
    <AppModal 
        :isModalOpen="showCreateUserModal" 
        @modal:toggle="showCreateUserModal = false"
        size="sm"
    >
        <template #header>
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <i class="ri-user-add-line text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Create User Account</h3>
                            <p class="text-xs text-blue-100">Set up login credentials for this employee</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        class="text-white/70 hover:text-white transition-colors"
                        @click="showCreateUserModal = false"
                    >
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
            </div>
        </template>

        <template #body>
            <form @submit.prevent="submitCreateUser" class="space-y-5">
                <!-- Employee Info Card -->
                <div class="flex items-center gap-4 p-4 rounded-xl bg-neutral-2 border border-neutral-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-semibold text-sm shadow-lg">
                        {{ selectedEmployee?.name?.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-neutral-12 truncate">{{ selectedEmployee?.name }}</p>
                        <p class="text-sm text-neutral-10 truncate">{{ selectedEmployee?.email }}</p>
                    </div>
                </div>

                <!-- Generate Password Section -->
                <div class="text-center">
                    <AppButton
                        type="button"
                        class="btn btn-secondary"
                        @click="generatePassword"
                    >
                        <i class="ri-magic-line mr-2"></i>
                        Generate Secure Password
                    </AppButton>
                    <p class="text-xs text-neutral-9 mt-2">Click to auto-generate a strong password</p>
                </div>

                <!-- Generated Password Display -->
                <div 
                    v-if="generatedPasswordValue" 
                    class="flex items-center gap-3 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 px-4 py-4 cursor-pointer hover:from-emerald-100 hover:to-teal-100 transition-all shadow-sm"
                    @click="copyPassword"
                    title="Click to copy"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 shadow-sm">
                        <i class="ri-key-line text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-emerald-600 font-medium mb-0.5">Generated Password</p>
                        <p class="font-mono text-sm text-emerald-900 font-semibold tracking-wide">{{ generatedPasswordValue }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-emerald-600">
                        <span v-if="copiedMessage" class="text-xs font-medium bg-emerald-100 px-2 py-1 rounded-full">
                            <i class="ri-check-line mr-1"></i>Copied!
                        </span>
                        <i v-else class="ri-file-copy-line text-lg hover:scale-110 transition-transform"></i>
                    </div>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-neutral-4"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-3 text-neutral-9">or enter manually</span>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="space-y-4">
                    <div>
                        <AppLabel for="password" required>Password</AppLabel>
                        <div class="relative mt-1">
                            <AppInputText
                                id="password"
                                v-model="createUserForm.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter password (min 8 characters)"
                                :error="createUserForm.errors.password"
                                class="pr-10"
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-9 hover:text-neutral-11 transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <AppLabel for="password_confirmation" required>Confirm Password</AppLabel>
                        <div class="relative mt-1">
                            <AppInputText
                                id="password_confirmation"
                                v-model="createUserForm.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Re-enter password"
                                class="pr-10"
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-9 hover:text-neutral-11 transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-2">
                    <AppButton 
                        type="button" 
                        class="btn btn-secondary flex-1"
                        @click="showCreateUserModal = false"
                    >
                        <i class="ri-close-line mr-1"></i>
                        Cancel
                    </AppButton>
                    <AppButton 
                        type="submit" 
                        class="btn btn-primary flex-1"
                        :loading="createUserForm.processing"
                    >
                        <i class="ri-user-add-line mr-1"></i>
                        Create User
                    </AppButton>
                </div>
            </form>
        </template>
    </AppModal>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'
import AppDropdown from '@/Components/Form/AppDropdown.vue'
import AppDropdownItem from '@/Components/Form/AppDropdownItem.vue'
import AppModal from '@/Components/Overlay/AppModal.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'

const { can } = useAuthCan()

const props = defineProps({
    employees: {
        type: Object,
        default: () => ({})
    }
})

const getInitials = (name) => {
    if (!name) return '?'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.substring(0, 2).toUpperCase()
}

const headers = [
    'SL',
    'Employee',
    'Joining',
    'Contact',
    'Status',
    'Actions'
]

const confirmDialogRef = ref(null)
const confirmDelete = (deleteRoute) => {
    confirmDialogRef.value.openModal(deleteRoute)
}

// Create User Modal
const showCreateUserModal = ref(false)
const selectedEmployee = ref(null)
const showPassword = ref(false)
const generatedPasswordValue = ref('')
const copiedMessage = ref(false)
const createUserForm = useForm({
    password: '',
    password_confirmation: ''
})

const openCreateUserModal = (employee) => {
    selectedEmployee.value = employee
    showPassword.value = false
    generatedPasswordValue.value = ''
    copiedMessage.value = false
    createUserForm.reset()
    showCreateUserModal.value = true
}

// Password generator (follows UserForm.vue pattern)
const generatePassword = () => {
    const length = 12
    const lowercase = 'abcdefghijklmnopqrstuvwxyz'
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    const numbers = '0123456789'
    const symbols = '!@#$%^&*'
    const allChars = lowercase + uppercase + numbers + symbols
    
    let password = ''
    // Ensure at least one of each type
    password += lowercase.charAt(Math.floor(Math.random() * lowercase.length))
    password += uppercase.charAt(Math.floor(Math.random() * uppercase.length))
    password += numbers.charAt(Math.floor(Math.random() * numbers.length))
    password += symbols.charAt(Math.floor(Math.random() * symbols.length))
    
    // Fill remaining characters
    for (let i = password.length; i < length; i++) {
        password += allChars.charAt(Math.floor(Math.random() * allChars.length))
    }
    
    // Shuffle the password
    password = password.split('').sort(() => Math.random() - 0.5).join('')
    
    generatedPasswordValue.value = password
    createUserForm.password = password
    createUserForm.password_confirmation = password
    showPassword.value = true
    copiedMessage.value = false
}

const copyPassword = () => {
    navigator.clipboard.writeText(createUserForm.password)
    copiedMessage.value = true
    setTimeout(() => {
        copiedMessage.value = false
    }, 2000)
}

const submitCreateUser = () => {
    createUserForm.post(route('employee.createUser', selectedEmployee.value.uuid), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateUserModal.value = false
            generatedPasswordValue.value = ''
            createUserForm.reset()
        }
    })
}
</script>
