<template>
    <AppCard class="w-full">
        <template #title>Product Settings</template>
        <template #content>
            <AppFormErrors class="mb-4" />
            <form
                class="grid grid-cols-1 gap-4 md:grid-cols-2"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
            >
                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_view_details_button"
                        v-model="form.show_view_details_button"
                        name="show_view_details_button"
                        :value="true"
                    />
                    <AppLabel for="show_view_details_button" class="ml-3">
                        Show View Details Button
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_add_to_cart_button"
                        v-model="form.show_add_to_cart_button"
                        name="show_add_to_cart_button"
                        :value="true"
                    />
                    <AppLabel for="show_add_to_cart_button" class="ml-3">
                        Show Add to Cart Button
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_add_to_wishlist_button"
                        v-model="form.show_add_to_wishlist_button"
                        name="show_add_to_wishlist_button"
                        :value="true"
                    />
                    <AppLabel for="show_add_to_wishlist_button" class="ml-3">
                        Show Add to Wishlist Button
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_add_to_compare_button"
                        v-model="form.show_add_to_compare_button"
                        name="show_add_to_compare_button"
                        :value="true"
                    />
                    <AppLabel for="show_add_to_compare_button" class="ml-3">
                        Show Add to Compare Button
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_product_rating"
                        v-model="form.show_product_rating"
                        name="show_product_rating"
                        :value="true"
                    />
                    <AppLabel for="show_product_rating" class="ml-3">
                        Show Product Rating
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_product_review"
                        v-model="form.show_product_review"
                        name="show_product_review"
                        :value="true"
                    />
                    <AppLabel for="show_product_review" class="ml-3">
                        Show Product Review
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_product_discount"
                        v-model="form.show_product_discount"
                        name="show_product_discount"
                        :value="true"
                    />
                    <AppLabel for="show_product_discount" class="ml-3">
                        Show Product Discount
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_product_shipping"
                        v-model="form.show_product_shipping"
                        name="show_product_shipping"
                        :value="true"
                    />
                    <AppLabel for="show_product_shipping" class="ml-3">
                        Show Product Shipping
                    </AppLabel>
                </div>

                <div class="mt-5 flex items-center">
                    <AppCheckbox
                        id="show_product_stock"
                        v-model="form.show_product_stock"
                        name="show_product_stock"
                        :value="true"
                    />
                    <AppLabel for="show_product_stock" class="ml-3">
                        Show Product Stock
                    </AppLabel>
                </div>
            </form>
        </template>
        <template #footer>
            <AppButton class="btn btn-primary" @click="submitForm">
                {{ __('Update Product Setting') }}
            </AppButton>
        </template>
    </AppCard>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import useFormErrors from '@/Composables/useFormErrors'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    }
})

const form = useForm({
    show_view_details_button: props.settings?.show_view_details_button || false,
    show_add_to_cart_button: props.settings?.show_add_to_cart_button || false,
    show_add_to_wishlist_button:
        props.settings?.show_add_to_wishlist_button || false,
    show_add_to_compare_button:
        props.settings?.show_add_to_compare_button || false,
    show_product_rating: props.settings?.show_product_rating || false,
    show_product_review: props.settings?.show_product_review || false,
    show_product_discount: props.settings?.show_product_discount || false,
    show_product_shipping: props.settings?.show_product_shipping || false,
    show_product_stock: props.settings?.show_product_stock || false
})

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT'
    })).post(route('setting.update'), {
        forceFormData: true // Ensures files are sent as FormData
    })
}

const { errorsFields } = useFormErrors()
</script>
