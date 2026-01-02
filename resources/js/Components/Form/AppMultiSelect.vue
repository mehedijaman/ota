<template>
    <div ref="wrapperRef" class="relative">
        <!-- Button to open dropdown -->
        <button
            type="button"
            class="bg-skin-neutral-1 text-skin-neutral-12 ring-skin-neutral-7 focus:ring-skin-neutral-7 mt-1 flex w-full flex-wrap justify-between rounded-md border-0 px-3 py-2 text-left shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
            :class="[
                disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
            ]"
            aria-haspopup="true"
            :aria-expanded="isOpen"
            :disabled="disabled"
            @click="toggleState"
        >
            <!-- Selected items display -->
            <div class="flex flex-wrap gap-1 flex-1">
                <span
                    v-for="selectedId in modelValue"
                    :key="selectedId"
                    class="bg-skin-neutral-3 text-skin-neutral-12 flex items-center rounded px-2 py-1 text-xs"
                >
                    {{ getOptionLabel(selectedId) }}
                    <i
                        v-if="!disabled"
                        class="ri-close-line ml-1 cursor-pointer hover:text-red-500"
                        @click.stop="removeItem(selectedId)"
                    />
                </span>

                <span
                    v-if="!modelValue || modelValue.length === 0"
                    class="text-skin-neutral-9"
                >
                    {{ placeholder }}
                </span>
            </div>

            <span class="ml-2 flex items-center">
                <i
                    class="ri-arrow-down-s-line"
                    :class="{ 'rotate-180': isOpen }"
                ></i>
            </span>
        </button>

        <!-- Dropdown -->
        <transition name="slide-fade">
            <div
                v-show="isOpen"
                class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md border border-skin-neutral-7 max-h-60 overflow-y-auto"
            >
                <!-- Options -->
                <ul role="listbox" class="py-1">
                    <li
                        v-for="option in options"
                        :key="getOptionValue(option)"
                        role="option"
                        class="hover:bg-skin-neutral-3 hover:text-skin-neutral-12 flex items-center gap-2 px-4 py-2 text-sm cursor-pointer"
                        :class="{
                            'bg-skin-neutral-3': isSelected(
                                getOptionValue(option)
                            )
                        }"
                        @click="toggleSelect(option)"
                    >
                        <input
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            :checked="isSelected(getOptionValue(option))"
                            @change.stop="toggleSelect(option)"
                        />
                        <span>{{ getOptionLabel(getOptionValue(option)) }}</span>
                    </li>
                    <li
                        v-if="options.length === 0"
                        class="px-4 py-2 text-sm text-skin-neutral-9"
                    >
                        No options available
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import useClickOutside from '@resources/js/Composables/useClickOutside'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    options: {
        type: Array,
        required: true,
        default: () => []
    },
    optionLabel: {
        type: String,
        default: 'label'
    },
    optionValue: {
        type: String,
        default: 'value'
    },
    placeholder: {
        type: String,
        default: 'Select options'
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const wrapperRef = ref(null)
const { isClickOutside } = useClickOutside(wrapperRef)
const isOpen = ref(false)

watch(isClickOutside, (val) => {
    if (val) {
        isOpen.value = false
    }
})

const toggleState = () => {
    if (!props.disabled) {
        isOpen.value = !isOpen.value
    }
}

const getOptionValue = (option) => {
    if (typeof option === 'object' && option !== null) {
        return option[props.optionValue]
    }
    return option
}

const getOptionLabel = (optionOrValue) => {
    // If passed an option object directly
    if (typeof optionOrValue === 'object' && optionOrValue !== null) {
        return optionOrValue[props.optionLabel]
    }
    
    // If passed a value, find the option
    const option = props.options.find(
        (opt) => getOptionValue(opt) === optionOrValue
    )
    
    if (option) {
        if (typeof option === 'object' && option !== null) {
            return option[props.optionLabel]
        }
        return option
    }
    
    return optionOrValue
}

const isSelected = (value) => {
    return props.modelValue && props.modelValue.includes(value)
}

const toggleSelect = (option) => {
    const value = getOptionValue(option)
    let newValue = [...(props.modelValue || [])]
    
    if (isSelected(value)) {
        newValue = newValue.filter((item) => item !== value)
    } else {
        newValue.push(value)
    }
    
    emit('update:modelValue', newValue)
}

const removeItem = (value) => {
    const newValue = (props.modelValue || []).filter((item) => item !== value)
    emit('update:modelValue', newValue)
}
</script>

<style scoped>

</style>
