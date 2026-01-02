<template>
    <div class="relative" ref="rootRef">
        <div
            @click="toggle"
            @keydown="handleKeydown"
            tabindex="0"
            ref="triggerRef"
            :id="triggerId"
        >
            <slot name="trigger" />
        </div>

        <!-- Use v-show instead of v-if to keep in DOM for measurement -->
        <Teleport to="body">
            <div
                v-show="isOpen"
                ref="dropdownRef"
                class="fixed z-50 min-w-48 origin-top-right rounded-md bg-white dark:bg-neutral-800 shadow-lg ring-1 ring-gray-900/10 dark:ring-gray-100/10 focus:outline-none"
                :style="dropdownPosition"
                role="menu"
                aria-orientation="vertical"
                :aria-labelledby="triggerId"
                tabindex="-1"
                @keydown="handleDropdownKeydown"
            >
                <div class="py-1">
                    <slot />
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    placement: {
        type: String,
        default: 'bottom-end'
    }
})

const isOpen = ref(false)
const rootRef = ref(null)
const triggerRef = ref(null)
const dropdownRef = ref(null)
const triggerId = `dropdown-trigger-${Math.random().toString(36).substr(2, 9)}`
const dropdownPosition = ref({ top: '0px', left: '0px' })

const toggle = async () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        // Wait for DOM update AND Teleport to attach
        await nextTick()
        // Wait a tiny bit more to ensure browser layout is ready
        await new Promise((resolve) => setTimeout(resolve, 0))
        updatePosition()
    }
}

const updatePosition = () => {
    if (!triggerRef.value || !dropdownRef.value) return

    const triggerRect = triggerRef.value.getBoundingClientRect()
    const dropdownRect = dropdownRef.value.getBoundingClientRect()
    const { innerHeight, innerWidth } = window

    // If dropdown has no size, skip (shouldn't happen with v-show)
    if (dropdownRect.width === 0 || dropdownRect.height === 0) return

    let top = 0
    let left = 0

    // Vertical: bottom by default
    if (props.placement.startsWith('top')) {
        top = triggerRect.top - dropdownRect.height
        // Flip to bottom if not enough space above
        if (top < 0) {
            top = triggerRect.bottom
        }
    } else {
        top = triggerRect.bottom
        // Flip to top if not enough space below
        if (top + dropdownRect.height > innerHeight) {
            top = triggerRect.top - dropdownRect.height
        }
    }

    // Horizontal: end (right-aligned) by default
    if (props.placement.endsWith('end')) {
        left = triggerRect.right - dropdownRect.width
        // Stick to left edge if overflow
        if (left < 0) {
            left = 0
        }
    } else {
        left = triggerRect.left
        // Stick to right edge if overflow
        if (left + dropdownRect.width > innerWidth) {
            left = innerWidth - dropdownRect.width
        }
    }

    dropdownPosition.value = {
        top: `${top}px`,
        left: `${left}px`
    }
}

// Rest of methods (close, key handlers, etc.) unchanged...
const close = () => {
    isOpen.value = false
}

const handleKeydown = (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault()
        toggle()
    } else if (e.key === 'Escape') {
        close()
    }
}

const handleClickOutside = (event) => {
    if (
        isOpen.value &&
        rootRef.value &&
        !rootRef.value.contains(event.target)
    ) {
        close()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    window.addEventListener('resize', updatePosition)
    window.addEventListener('scroll', updatePosition, { passive: true })
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    window.removeEventListener('resize', updatePosition)
    window.removeEventListener('scroll', updatePosition)
})
</script>
