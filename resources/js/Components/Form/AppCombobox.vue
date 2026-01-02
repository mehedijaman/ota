<template>
    <div ref="wrapperRef" class="relative w-48">
        <!-- Button -->
        <AppButton
            class="bg-skin-neutral-1 text-skin-neutral-12 placeholder-skin-neutral-9 mt-1 flex w-full justify-between rounded-md border-0 px-3 py-2 align-middle shadow-sm ring-1 ring-blue-100 ring-inset hover:cursor-pointer focus:ring-1 focus:ring-blue-500 focus:ring-inset sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-700 dark:focus:ring-blue-500"
            aria-haspopup="true"
            :aria-expanded="isOpen"
            @click="toggleState"
        >
            <span class="mr-2 inline-block">{{ comboLabelText }}</span>
            <span>
                <i
                    v-if="hasSelection"
                    class="ri-close-circle-line hover:text-skin-neutral-9 mr-2 inline-block dark:hover:text-gray-300"
                    @click.prevent="clearSelection"
                ></i>
                <i class="ri-arrow-down-line hover:text-skin-neutral-9 dark:hover:text-gray-300"></i>
            </span>
        </AppButton>

        <!-- Dropdown -->
        <transition name="slide-fade">
            <div v-show="isOpen" class="absolute z-50 mt-1 w-full rounded-md shadow-lg dark:shadow-none">
                <!-- Search -->
                <div v-if="useSearch" class="bg-white p-1 shadow rounded-t-md dark:bg-gray-800 dark:border-gray-700">
                    <label :for="getElementId()" class="sr-only">Search </label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <i class="ri-search-line dark:text-gray-400" aria-hidden="true"></i>
                        </div>
                        <AppInputText
                            :id="getElementId()"
                            ref="searchInputRef"
                            v-model="searchOptionText"
                            role="searchbox"
                            aria-autocomplete="list"
                            type="text"
                            class="pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            :placeholder="searchPlaceholder"
                            @keypress.enter="validateOptionHighlighted"
                            @keydown="handleArrowKeys"
                            @keydown.esc="toggleState"
                        />
                    </div>
                </div>

                <!-- Options -->
                <ul class="bg-white p-1 shadow rounded-b-md dark:bg-gray-800 max-h-60 overflow-y-auto" role="listbox">
                    <li
                        v-for="(option, index) in filteredOptions"
                        :key="option.value"
                        role="option"
                        :aria-selected="index === highlightedIndex"
                        class="hover:text-skin-neutral-12 block px-4 py-2 text-sm hover:cursor-pointer hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                        :class="{
                            'bg-skin-neutral-3 text-skin-neutral-12 dark:bg-gray-700':
                                index === highlightedIndex
                        }"
                        @click="updateModelValue(option)"
                    >
                        {{ option.label }}
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import slug from '@resources/js/Utils/slug.js'
import useClickOutside from '@resources/js/Composables/useClickOutside'

const props = defineProps({
    modelValue: {
        type: [Object, Number, String, null],
        required: true
    },
    comboLabel: {
        type: String,
        default: 'Dropdown search'
    },
    useSearch: {
        type: Boolean,
        default: true
    },
    searchPlaceholder: {
        type: String,
        default: 'Search'
    },
    options: {
        type: Array,
        default: () => []
    },
    emitValue: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const wrapperRef = ref(null)
const { isClickOutside } = useClickOutside(wrapperRef)

const isOpen = ref(false)
const searchInputRef = ref(null)
const searchOptionText = ref('')
const highlightedIndex = ref(0)

// Close when clicked outside
watch(isClickOutside, (val) => {
    if (val) isOpen.value = false
})

onMounted(() => {
    if (isOpen.value) highlightedIndex.value = 0
})

// Utils
const getElementId = () => slug(props.comboLabel)

const toggleState = () => {
    isOpen.value = !isOpen.value
    highlightedIndex.value = 0

    window.setTimeout(() => {
        if (isOpen.value && props.useSearch) {
            searchOptionText.value = ''
            searchInputRef.value?.focusInput()
        }
    }, 100)
}

const updateModelValue = (option) => {
    emit('update:modelValue', props.emitValue ? option.value : option)
    toggleState()
}

const clearSelection = (e) => {
    e.stopPropagation()
    highlightedIndex.value = 0
    emit('update:modelValue', null)
    isOpen.value = false
}

// Filtering
const filteredOptions = computed(() => {
    if (searchOptionText.value) {
        return props.options.filter((option) =>
            option.label
                .toLowerCase()
                .includes(searchOptionText.value.toLowerCase())
        )
    }
    return props.options
})

// Keyboard
const validateOptionHighlighted = () => {
    if (filteredOptions.value[highlightedIndex.value]) {
        updateModelValue(filteredOptions.value[highlightedIndex.value])
    }
}

const handleArrowKeys = (event) => {
    switch (event.key) {
        case 'ArrowUp':
            if (highlightedIndex.value > 0) highlightedIndex.value--
            break
        case 'ArrowDown':
            if (highlightedIndex.value < filteredOptions.value.length - 1)
                highlightedIndex.value++
            break
    }
}

const hasSelection = computed(() => {
    if (props.emitValue) {
        return (
            props.modelValue !== null &&
            props.modelValue !== '' &&
            props.modelValue !== undefined
        )
    }
    return props.modelValue !== null && typeof props.modelValue === 'object'
})

const comboLabelText = computed(() => {
    if (props.modelValue !== null && props.modelValue !== '') {
        if (props.emitValue) {
            // When only value is stored, find the matching option
            const found = props.options.find(
                (o) => o.value === props.modelValue
            )
            return found ? found.label : props.comboLabel
        } else if (props.modelValue.label) {
            // When storing whole object
            return props.modelValue.label
        }
    }
    return props.comboLabel
})
</script>

<style scoped>
/* Add this line to reference your main Tailwind stylesheet */
@reference "../../../css/app.css"; /* Adjust the path as needed */

.slide-fade-enter-active,
.slide-fade-leave-active {
    @apply transition-all duration-200 ease-in;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    @apply -translate-y-2 opacity-0;
}
</style>
