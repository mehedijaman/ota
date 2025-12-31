<template>
    <transition name="modal-fade">
        <div
            v-show="isModalOpen"
            :class="[backdropClasses]"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click="hideModal"
        >
            <transition name="modal-scale">
                <div
                    v-show="isModalOpen"
                    :class="[
                        modalSizeClass,
                        placementClass,
                        'relative rounded-lg bg-white shadow-xl dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100'
                    ]"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <slot v-if="$slots.header" name="header" />

                    <!-- Modal Body -->
                    <div class="max-h-[80vh] overflow-y-auto px-6 py-4">
                        <slot v-if="$slots.body" name="body" />
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
        default: 'center' // center, top, bottom, etc.
    },
    isModalOpen: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg, xl, full
        validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
    }
})

const emit = defineEmits(['modal:toggle'])

const placementClass = computed(() => {
    switch (props.placement) {
        case 'center':
            return '' // Handled by flex parent
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
    switch (props.size) {
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
