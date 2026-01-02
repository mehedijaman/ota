<template>
    <div class="relative" ref="selectRef">
        <!-- Searchable Input -->
        <div v-if="searchable" class="relative">
            <input
                type="text"
                :placeholder="placeholder"
                v-model="searchQuery"
                class="w-full rounded-lg border border-neutral-5 bg-neutral-2 px-3 py-2 pr-8 text-sm text-neutral-12 placeholder-neutral-9 transition-all focus:border-primary-7 focus:ring-2 focus:ring-primary-4 focus:outline-none"
                @focus="open = true"
                @keydown.down.prevent="highlightNext"
                @keydown.up.prevent="highlightPrev"
                @keydown.enter.prevent="selectHighlighted"
                @keydown.escape="open = false"
            />
            <i class="ri-search-line absolute right-3 top-1/2 -translate-y-1/2 text-neutral-9"></i>
        </div>

        <!-- Select Button -->
        <button
            v-else
            type="button"
            @click="toggleDropdown"
            class="flex w-full items-center justify-between gap-2 rounded-lg border border-neutral-5 bg-neutral-2 px-3 py-2 text-left text-sm transition-all hover:border-neutral-6 hover:bg-neutral-3 focus:border-primary-7 focus:ring-2 focus:ring-primary-4 focus:outline-none"
            :class="{ 'border-primary-7 ring-2 ring-primary-4': open }"
        >
            <span :class="selectedLabel ? 'text-neutral-12' : 'text-neutral-9'">
                {{ selectedLabel || placeholder }}
            </span>
            <i 
                class="ri-arrow-down-s-line text-neutral-10 transition-transform duration-200"
                :class="{ 'rotate-180': open }"
            ></i>
        </button>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <ul
                v-show="open"
                class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-neutral-5 bg-neutral-1 py-1 shadow-lg"
            >
                <li
                    v-for="(option, index) in filteredOptions"
                    :key="option[optionValue]"
                    @click="selectOption(option)"
                    class="flex cursor-pointer items-center gap-2 px-3 py-2 text-sm transition-colors"
                    :class="[
                        index === highlightedIndex ? 'bg-primary-4 text-primary-11' : 'text-neutral-12 hover:bg-neutral-3',
                        option[optionValue] === modelValue ? 'font-medium' : ''
                    ]"
                >
                    <span class="flex-1">{{ option[optionLabel] }}</span>
                    <i 
                        v-if="option[optionValue] === modelValue" 
                        class="ri-check-line text-primary-9"
                    ></i>
                </li>
                <li v-if="!filteredOptions.length" class="px-3 py-3 text-center text-sm text-neutral-9">
                    <i class="ri-search-line mr-1"></i>
                    No results found
                </li>
            </ul>
        </Transition>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    modelValue: [String, Number, Object, null],
    options: {
        type: Array,
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
        default: 'Select...'
    },
    searchable: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const selectRef = ref(null)
const open = ref(false)
const searchQuery = ref('')
const highlightedIndex = ref(0)

const selectedLabel = computed(() => {
    const selected = props.options.find(
        (o) => o[props.optionValue] === props.modelValue
    )
    return selected ? selected[props.optionLabel] : ''
})

const filteredOptions = computed(() => {
    if (!props.searchable || !searchQuery.value) return props.options
    return props.options.filter((o) =>
        String(o[props.optionLabel])
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase())
    )
})

const toggleDropdown = () => {
    open.value = !open.value
}

const selectOption = (option) => {
    emit('update:modelValue', option[props.optionValue])
    searchQuery.value = ''
    open.value = false
}

const highlightNext = () => {
    if (highlightedIndex.value < filteredOptions.value.length - 1)
        highlightedIndex.value++
}
const highlightPrev = () => {
    if (highlightedIndex.value > 0) highlightedIndex.value--
}
const selectHighlighted = () => {
    const option = filteredOptions.value[highlightedIndex.value]
    if (option) selectOption(option)
}

// Close dropdown when clicked outside
const handleClickOutside = (e) => {
    if (selectRef.value && !selectRef.value.contains(e.target)) {
        open.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

// Reset highlight when options change
watch(() => props.options, () => {
    highlightedIndex.value = 0
})
</script>
