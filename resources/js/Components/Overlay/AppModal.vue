<template>
    <transition name="modal-fade">
        <div
            v-show="isOpen"
            :class="[backdropClasses]"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click="hideModal"
        >
            <transition name="modal-scale">
                <div
                    v-show="isOpen"
                    :class="[
                        modalSizeClass,
                        placementClass,
                        'relative rounded-lg bg-white shadow-xl dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100'
                    ]"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <div v-if="title || $slots.header" class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <slot name="header">
                            <h3 class="text-lg font-semibold">{{ title }}</h3>
                        </slot>
                        <button
                            type="button"
                            class="rounded-lg p-1.5 text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-200"
                            @click="hideModal"
                        >
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="max-h-[80vh] overflow-y-auto px-6 py-4">
                        <slot v-if="$slots.body" name="body" />
                        <slot v-else />
                    </div>

                    <!-- Modal Footer -->
                    <slot v-if="$slots.footer" name="footer" />
                </div>
            </transition>
        </div>
    </transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    backdropClasses: {
        type: String,
        default: 'bg-neutral-900/50'
    },
    placement: {
        type: String,
        default: 'center'
    },
    modelValue: {
        type: Boolean,
        default: false
    },
    isModalOpen: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    maxWidth: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
    },
    size: {
        type: String,
        default: null,
        validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
    }
})

const emit = defineEmits(['update:modelValue', 'modal:toggle'])

const isOpen = computed(() => props.modelValue || props.isModalOpen)

const effectiveSize = computed(() => props.size || props.maxWidth)

const placementClass = computed(() => {
    switch (props.placement) {
        case 'center':
            return ''
        case 'top':
            return 'mb-auto mt-10'
        case 'bottom':
            return 'mt-auto mb-10'
        case 'left':
            return 'mr-auto ml-10'
        case 'right':
            return 'ml-auto mr-10'
        default:
            return ''
    }
})

const modalSizeClass = computed(() => {
    switch (effectiveSize.value) {
        case 'sm':
            return 'w-full max-w-sm'
        case 'md':
            return 'w-full max-w-lg'
        case 'lg':
            return 'w-full max-w-2xl'
        case 'xl':
            return 'w-full max-w-4xl'
        case 'full':
            return 'w-full h-full max-w-none rounded-none'
        default:
            return 'w-full max-w-lg'
    }
})

function hideModal() {
    emit('update:modelValue', false)
    emit('modal:toggle')
}
</script>

<style scoped>
@reference "../../../css/app.css";

.modal-fade-enter-active,
.modal-fade-leave-active {
    @apply transition-opacity duration-200 ease-in-out;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    @apply opacity-0;
}

.modal-scale-enter-active {
    @apply transition-all duration-200 ease-out;
}
.modal-scale-leave-active {
    @apply transition-all duration-150 ease-in;
}
.modal-scale-enter-from {
    @apply scale-95 opacity-0;
}
.modal-scale-leave-to {
    @apply scale-95 opacity-0;
}
</style>
