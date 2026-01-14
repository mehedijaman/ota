<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotel.room.index', hotelId))">
                <i class="ri-arrow-left-line mr-1"></i>
                Back
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submitForm">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <AppCard class="w-full">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
                                <i class="ri-door-line text-lg text-white"></i>
                            </div>
                            <div>
                                <span class="text-neutral-12 font-semibold">Room Details</span>
                                <p class="text-neutral-9 text-xs">Basic room information</p>
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <AppFormErrors class="mb-4" />

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <AppLabel>Room Name <span class="text-red-500">*</span></AppLabel>
                                <AppInputText v-model="form.name" type="text" placeholder="e.g., Deluxe Double Room" />
                            </div>
                            <div>
                                <AppLabel>Room Type <span class="text-red-500">*</span></AppLabel>
                                <AppCombobox v-model="form.room_type_id" :options="roomTypes" combo-label="Select Type" class="w-full" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <AppLabel>Description</AppLabel>
                            <AppTextArea v-model="form.description" rows="3" placeholder="Room description" />
                        </div>
                    </template>
                </AppCard>

                <!-- Capacity -->
                <AppCard class="w-full">
                    <template #title>
                        <i class="ri-user-line mr-2 text-teal-500"></i> Capacity & Beds
                    </template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <AppLabel>Room Size (sqm)</AppLabel>
                                <AppInputText v-model="form.room_size" type="number" min="0" placeholder="30" />
                            </div>
                            <div>
                                <AppLabel>Max Occupancy <span class="text-red-500">*</span></AppLabel>
                                <AppInputText v-model="form.max_occupancy" type="number" min="1" placeholder="2" />
                            </div>
                            <div>
                                <AppLabel>Max Adults</AppLabel>
                                <AppInputText v-model="form.max_adults" type="number" min="1" placeholder="2" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <AppLabel>Max Children</AppLabel>
                                <AppInputText v-model="form.max_children" type="number" min="0" placeholder="1" />
                            </div>
                            <div>
                                <AppLabel>Bed Type</AppLabel>
                                <AppCombobox v-model="form.bed_type" :options="bedTypes" combo-label="Select" class="w-full" />
                            </div>
                            <div>
                                <AppLabel>Bed Count</AppLabel>
                                <AppInputText v-model="form.bed_count" type="number" min="1" placeholder="1" />
                            </div>
                        </div>
                    </template>
                </AppCard>

                <!-- Pricing -->
                <AppCard class="w-full">
                    <template #title>
                        <i class="ri-money-dollar-circle-line mr-2 text-emerald-500"></i> Pricing
                    </template>
                    <template #content>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <AppLabel>Base Rate (৳/night) <span class="text-red-500">*</span></AppLabel>
                                <AppInputText v-model="form.base_rate" type="number" min="0" step="0.01" placeholder="5000" />
                            </div>
                            <div>
                                <AppLabel>Extra Person Charge</AppLabel>
                                <AppInputText v-model="form.extra_person_charge" type="number" min="0" step="0.01" placeholder="1000" />
                            </div>
                        </div>
                    </template>
                </AppCard>

                <!-- Amenities -->
                <AppCard v-if="roomAmenities.length" class="w-full">
                    <template #title>
                        <i class="ri-service-line mr-2 text-amber-500"></i> Room Amenities
                    </template>
                    <template #content>
                        <div v-for="(amenities, category) in groupedAmenities" :key="category" class="mb-6 last:mb-0">
                            <h4 class="text-neutral-11 mb-3 font-medium capitalize">{{ category.replace('_', ' ') }}</h4>
                            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                <label
                                    v-for="amenity in amenities"
                                    :key="amenity.id"
                                    class="border-neutral-5 hover:bg-neutral-2 flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
                                    :class="{ 'border-amber-500 bg-amber-50': form.amenity_ids.includes(amenity.id) }"
                                >
                                    <input type="checkbox" :value="amenity.id" v-model="form.amenity_ids" class="h-4 w-4 rounded text-amber-500" />
                                    <span class="text-neutral-11 text-sm">{{ amenity.name }}</span>
                                </label>
                            </div>
                        </div>
                    </template>
                </AppCard>
            </div>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <AppCard>
                    <template #title>Status</template>
                    <template #content>
                        <div class="space-y-4">
                            <label class="flex cursor-pointer items-center gap-3">
                                <input type="checkbox" v-model="form.is_active" class="h-5 w-5 rounded text-emerald-500" />
                                <div>
                                    <span class="text-neutral-12 font-medium">Active</span>
                                    <p class="text-neutral-9 text-xs">Room is available for booking</p>
                                </div>
                            </label>
                        </div>
                    </template>
                </AppCard>

                <AppCard>
                    <template #title>Inventory</template>
                    <template #content>
                        <div>
                            <AppLabel>Total Rooms</AppLabel>
                            <AppInputText v-model="form.total_rooms" type="number" min="1" placeholder="10" />
                            <p class="text-neutral-9 text-xs mt-1">Number of rooms of this type</p>
                        </div>
                    </template>
                </AppCard>
            </aside>
        </div>

        <!-- Submit -->
        <div class="bg-neutral-1 border-neutral-6 sticky bottom-0 z-10 -mx-4 border-t px-4 py-4 shadow-lg sm:-mx-6 sm:px-6 mt-6">
            <div class="flex justify-end gap-3">
                <AppButton type="button" class="btn btn-secondary" @click="$inertia.visit(route('hotel.room.index', hotelId))">Cancel</AppButton>
                <AppButton type="submit" class="btn btn-primary" :loading="form.processing">
                    <i class="ri-save-line mr-1"></i>
                    {{ isEditing ? 'Update Room' : 'Create Room' }}
                </AppButton>
            </div>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import AppCombobox from '@/Components/Form/AppCombobox.vue'
import AppLabel from '@/Components/Form/AppLabel.vue'
import AppInputText from '@/Components/Form/AppInputText.vue'
import AppTextArea from '@/Components/Form/AppTextArea.vue'
import AppFormErrors from '@/Components/Form/AppFormErrors.vue'

const props = defineProps({
    hotel: { type: Object, required: true },
    room: { type: Object, default: null },
    roomTypes: { type: Array, default: () => [] },
    roomAmenities: { type: Array, default: () => [] }
})

const hotelId = props.hotel.id
const isEditing = computed(() => !!props.room?.id)
const { title } = useTitle(isEditing.value ? 'Edit Room' : 'Add Room')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotels', href: route('hotel.index') },
    { label: props.hotel.name, href: route('hotel.show', hotelId) },
    { label: 'Rooms', href: route('hotel.room.index', hotelId) },
    { label: isEditing.value ? 'Edit' : 'Create', last: true }
]

const bedTypes = [
    { value: 'single', label: 'Single' },
    { value: 'double', label: 'Double' },
    { value: 'queen', label: 'Queen' },
    { value: 'king', label: 'King' },
    { value: 'twin', label: 'Twin' },
    { value: 'bunk', label: 'Bunk' }
]

const form = useForm({
    name: props.room?.name || '',
    room_type_id: props.room?.room_type_id || null,
    description: props.room?.description || '',
    room_size: props.room?.room_size || '',
    max_occupancy: props.room?.max_occupancy || 2,
    max_adults: props.room?.max_adults || 2,
    max_children: props.room?.max_children || 0,
    bed_type: props.room?.bed_type || 'double',
    bed_count: props.room?.bed_count || 1,
    base_rate: props.room?.base_rate || '',
    extra_person_charge: props.room?.extra_person_charge || '',
    total_rooms: props.room?.total_rooms || 1,
    is_active: props.room?.is_active ?? true,
    amenity_ids: props.room?.amenities?.map(a => a.id) || []
})

const groupedAmenities = computed(() => {
    const grouped = {}
    props.roomAmenities.forEach(a => {
        if (!grouped[a.category]) grouped[a.category] = []
        grouped[a.category].push(a)
    })
    return grouped
})

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('hotel.room.update', [hotelId, props.room.id]))
    } else {
        form.post(route('hotel.room.store', hotelId))
    }
}
</script>
