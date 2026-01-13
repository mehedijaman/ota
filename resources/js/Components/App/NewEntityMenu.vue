<template>
    <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 scale-95 -translate-y-2"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 -translate-y-2"
    >
        <div
            v-if="open"
            class="fixed left-2 right-2 top-20 z-50 rounded-xl border border-neutral-5 bg-neutral-1 p-4 shadow-xl sm:absolute sm:left-0 sm:right-auto sm:top-auto sm:mt-2 sm:w-[480px]"
        >
            <!-- Header -->
            <div class="mb-3 flex items-center justify-between border-b border-neutral-4 pb-3">
                <h2 class="text-sm font-bold text-neutral-11 uppercase tracking-wide">
                    Quick Create
                </h2>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <!-- Sales & Customers -->
                <div class="space-y-1">
                    <h3 class="text-[10px] font-semibold text-neutral-9 uppercase tracking-wider mb-2">
                        Sales & Customers
                    </h3>
                    
                    <MenuButton 
                        icon="ri-shopping-bag-line" 
                        label="New Sale" 
                        color="primary"
                        @click="goTo('sale.create')" 
                    />
                    <MenuButton 
                        icon="ri-arrow-go-back-line" 
                        label="Sale Return" 
                        @click="goTo('saleReturn.create')" 
                    />
                    <MenuButton 
                        icon="ri-user-add-line" 
                        label="New Customer" 
                        @click="goTo('customer.create')" 
                    />
                    <MenuButton 
                        icon="ri-file-list-3-line" 
                        label="New Quotation" 
                        @click="goTo('quotation.create')" 
                    />
                </div>

                <!-- Products & Inventory -->
                <div class="space-y-1">
                    <h3 class="text-[10px] font-semibold text-neutral-9 uppercase tracking-wider mb-2">
                        Products & Inventory
                    </h3>
                    
                    <MenuButton 
                        icon="ri-box-3-line" 
                        label="New Product" 
                        color="success"
                        @click="goTo('product.create')" 
                    />
                    <MenuButton 
                        icon="ri-truck-line" 
                        label="New Purchase" 
                        @click="goTo('purchase.create')" 
                    />
                    <MenuButton 
                        icon="ri-arrow-go-forward-line" 
                        label="Purchase Return" 
                        @click="goTo('purchaseReturn.create')" 
                    />
                    <MenuButton 
                        icon="ri-store-2-line" 
                        label="New Supplier" 
                        @click="goTo('supplier.create')" 
                    />
                </div>
            </div>

            <!-- Divider -->
            <div class="my-3 border-t border-neutral-4"></div>

            <!-- Secondary Actions -->
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                <MiniButton icon="ri-price-tag-3-line" label="Brand" @click="goTo('productBrand.create')" />
                <MiniButton icon="ri-folder-line" label="Category" @click="goTo('productCategory.create')" />
                <MiniButton icon="ri-team-line" label="Employee" @click="goTo('employee.create')" />
                <MiniButton icon="ri-user-settings-line" label="User" @click="goTo('user.create')" />
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { h } from 'vue'

defineProps({
    open: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

const goTo = (routeName) => {
    router.visit(route(routeName))
    emit('close')
}

// Menu Button Component
const MenuButton = (props, { emit }) => {
    const colorClass = props.color === 'primary' 
        ? 'bg-primary-4 text-primary-11 hover:bg-primary-5' 
        : props.color === 'success'
        ? 'bg-success-light text-success hover:bg-success/20'
        : 'bg-neutral-3 text-neutral-11 hover:bg-neutral-4'
    
    return h('button', {
        type: 'button',
        class: `flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors ${colorClass}`,
        onClick: () => emit('click')
    }, [
        h('i', { class: `${props.icon} text-lg` }),
        h('span', props.label)
    ])
}
MenuButton.props = ['icon', 'label', 'color']
MenuButton.emits = ['click']

// Mini Button Component
const MiniButton = (props, { emit }) => {
    return h('button', {
        type: 'button',
        class: 'flex flex-col items-center gap-1 rounded-lg bg-neutral-2 px-2 py-2 text-xs text-neutral-11 transition-colors hover:bg-neutral-3',
        onClick: () => emit('click')
    }, [
        h('i', { class: `${props.icon} text-base text-neutral-9` }),
        h('span', { class: 'font-medium' }, props.label)
    ])
}
MiniButton.props = ['icon', 'label']
MiniButton.emits = ['click']
</script>
