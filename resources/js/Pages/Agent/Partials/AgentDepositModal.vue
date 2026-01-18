<template>
    <AppModal :isModalOpen="isOpen" @modal:toggle="closeModal" max-width="lg">
        <template #header>
            <div class="flex items-center gap-2 text-lg font-semibold">
                <i class="ri-wallet-3-line text-emerald-600"></i>
                <span>{{ isEditing ? 'Edit Transaction' : 'Manual Wallet Deposit' }}</span>
            </div>
        </template>
        <template #body>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <AppLabel for="amount" required>Amount (BDT)</AppLabel>
                    <AppInputText
                        id="amount"
                        v-model="form.amount"
                        type="number"
                        min="1"
                        step="0.01"
                        placeholder="Enter amount"
                        class="mt-1"
                    />
                    <p v-if="form.errors.amount" class="mt-1 text-xs text-red-600">
                        {{ form.errors.amount }}
                    </p>
                </div>
                
                <div>
                    <AppLabel for="reference">Reference / Transaction ID</AppLabel>
                    <AppInputText
                        id="reference"
                        v-model="form.reference"
                        placeholder="e.g. Bank Ref, Check No"
                        class="mt-1"
                    />
                    <p v-if="form.errors.reference" class="mt-1 text-xs text-red-600">
                        {{ form.errors.reference }}
                    </p>
                </div>

                <div>
                    <AppLabel for="details">Notes / Details</AppLabel>
                    <AppTextArea
                        id="details"
                        v-model="form.details"
                        placeholder="Reason for deposit..."
                        class="mt-1"
                    />
                    <p v-if="form.errors.details" class="mt-1 text-xs text-red-600">
                        {{ form.errors.details }}
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <AppButton
                        type="button"
                        variant="secondary"
                        class="btn btn-secondary"
                        @click="closeModal"
                        :disabled="form.processing"
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        type="submit"
                        variant="primary"
                        class="btn btn-primary"
                        :loading="form.processing"
                    >
                        {{ isEditing ? 'Update Transaction' : 'Deposit Amount' }}
                    </AppButton>
                </div>
            </form>
        </template>
    </AppModal>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch, computed } from 'vue'
import AppModal from '@/Components/Overlay/AppModal.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'

const props = defineProps({
    isOpen: Boolean,
    agent: Object,
    transaction: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close'])

const isEditing = computed(() => !!props.transaction)

const form = useForm({
    amount: '',
    reference: '',
    details: '',
})

watch(() => props.isOpen, (isOpen) => {
    if (isOpen) {
        if (props.transaction) {
            form.amount = props.transaction.amount
            form.reference = props.transaction.reference
            // Handle details parsing if it's an object or string
            let details = props.transaction.details
            if (typeof details === 'object' && details !== null) {
                details = details.notes || JSON.stringify(details)
            }
            form.details = details
        } else {
            form.reset()
        }
        form.clearErrors()
    }
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('agent.wallet.transaction.update', [props.agent.uuid, props.transaction.id]), {
            onSuccess: () => {
                closeModal()
                form.reset()
            },
        })
    } else {
        form.post(route('agent.wallet.deposit', props.agent.uuid), {
            onSuccess: () => {
                closeModal()
                form.reset()
            },
        })
    }
}

const closeModal = () => {
    emit('close')
    form.reset()
    form.clearErrors()
}
</script>
