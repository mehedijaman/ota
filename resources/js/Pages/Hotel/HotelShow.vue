<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <div class="flex gap-2">
                <AppButton v-if="can('hotel.edit')" class="btn btn-secondary" @click="$inertia.visit(route('hotel.availability.show', hotel.id))">
                    <i class="ri-calendar-check-line mr-1"></i>
                    Availability
                </AppButton>
                <AppButton v-if="can('hotel.edit')" class="btn btn-primary" @click="$inertia.visit(route('hotel.edit', hotel.id))">
                    <i class="ri-edit-line mr-1"></i>
                    Edit Hotel
                </AppButton>
                <AppButton class="btn btn-secondary" @click="$inertia.visit(route('hotel.index'))">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </AppButton>
            </div>
        </template>
    </AppSectionHeader>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Hotel Card -->
            <AppCard>
                <template #title>
                    <div class="flex items-center gap-4">
                        <img
                            v-if="hotel.thumbnail_url"
                            :src="hotel.thumbnail_url"
                            :alt="hotel.name"
                            class="h-20 w-28 rounded-lg object-cover"
                        />
                        <div v-else class="flex h-20 w-28 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 text-white">
                            <i class="ri-hotel-line text-3xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <i v-for="n in hotel.star_rating" :key="n" class="ri-star-fill text-amber-500"></i>
                            </div>
                            <h2 class="text-xl font-bold text-neutral-12">{{ hotel.name }}</h2>
                            <p class="text-neutral-9 text-sm">{{ hotel.code }}</p>
                        </div>
                    </div>
                </template>
                <template #content>
                    <div class="prose max-w-none text-neutral-11">
                        <p>{{ hotel.description || 'No description provided.' }}</p>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium ring-1"
                            :class="hotel.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-red-50 text-red-700 ring-red-200'"
                        >
                            <span class="h-2 w-2 rounded-full" :class="hotel.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                            {{ hotel.is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span v-if="hotel.is_featured" class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 ring-1 ring-amber-200">
                            <i class="ri-star-fill text-amber-500"></i>
                            Featured
                        </span>
                    </div>
                </template>
            </AppCard>

            <!-- Location -->
            <AppCard>
                <template #title>
                    <i class="ri-map-pin-line mr-2 text-teal-500"></i> Location
                </template>
                <template #content>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-neutral-9">Address</span>
                            <p class="text-neutral-12 font-medium">{{ hotel.full_address || 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-neutral-9">City</span>
                            <p class="text-neutral-12 font-medium">{{ hotel.city?.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-neutral-9">Country</span>
                            <p class="text-neutral-12 font-medium">{{ hotel.country?.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-neutral-9">Coordinates</span>
                            <p class="text-neutral-12 font-medium">{{ hotel.latitude }}, {{ hotel.longitude }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Amenities -->
            <AppCard v-if="hotel.amenities?.length">
                <template #title>
                    <i class="ri-service-line mr-2 text-emerald-500"></i> Amenities
                </template>
                <template #content>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="amenity in hotel.amenities"
                            :key="amenity.id"
                            class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-3 py-1 text-sm text-neutral-700"
                        >
                            <i :class="amenity.icon || 'ri-check-line'" class="text-emerald-500"></i>
                            {{ amenity.name }}
                        </span>
                    </div>
                </template>
            </AppCard>

            <!-- Rooms -->
            <AppCard>
                <template #title>
                    <div class="flex items-center justify-between">
                        <span><i class="ri-door-line mr-2 text-purple-500"></i> Rooms ({{ hotel.rooms?.length || 0 }})</span>
                        <AppButton v-if="can('hotel.room.create')" class="btn btn-sm btn-primary" @click="$inertia.visit(route('hotel.room.create', hotel.id))">
                            <i class="ri-add-line mr-1"></i> Add Room
                        </AppButton>
                    </div>
                </template>
                <template #content>
                    <div v-if="hotel.rooms?.length" class="space-y-3">
                        <div
                            v-for="room in hotel.rooms"
                            :key="room.id"
                            class="flex items-center justify-between border-neutral-5 rounded-lg border p-4"
                        >
                            <div>
                                <p class="font-medium text-neutral-12">{{ room.name }}</p>
                                <p class="text-sm text-neutral-9">{{ room.room_type?.name }} • Max {{ room.max_occupancy }} guests</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">৳{{ formatNumber(room.base_rate) }}</p>
                                <p class="text-xs text-neutral-9">per night</p>
                            </div>
                        </div>
                    </div>
                    <AppEmptyState v-else icon="ri-door-line" title="No rooms" description="Add rooms to this hotel." />
                </template>
            </AppCard>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <!-- Quick Stats -->
            <AppCard>
                <template #title>Quick Stats</template>
                <template #content>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-9">Rooms</span>
                            <span class="font-bold text-neutral-12">{{ hotel.rooms_count || 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-9">Reviews</span>
                            <span class="font-bold text-neutral-12">{{ hotel.reviews_count || 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-9">Avg Rating</span>
                            <span class="font-bold text-amber-600">{{ hotel.avg_rating || '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-neutral-9">Property Type</span>
                            <span class="font-medium text-neutral-12 capitalize">{{ hotel.property_type?.replace('_', ' ') }}</span>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Contact -->
            <AppCard>
                <template #title>Contact</template>
                <template #content>
                    <div class="space-y-3 text-sm">
                        <div v-if="hotel.phone" class="flex items-center gap-2">
                            <i class="ri-phone-line text-neutral-9"></i>
                            <a :href="'tel:' + hotel.phone" class="text-blue-600 hover:underline">{{ hotel.phone }}</a>
                        </div>
                        <div v-if="hotel.email" class="flex items-center gap-2">
                            <i class="ri-mail-line text-neutral-9"></i>
                            <a :href="'mailto:' + hotel.email" class="text-blue-600 hover:underline">{{ hotel.email }}</a>
                        </div>
                        <div v-if="hotel.website" class="flex items-center gap-2">
                            <i class="ri-global-line text-neutral-9"></i>
                            <a :href="hotel.website" target="_blank" class="text-blue-600 hover:underline">{{ hotel.website }}</a>
                        </div>
                    </div>
                </template>
            </AppCard>

            <!-- Timings -->
            <AppCard>
                <template #title>Check-in / Check-out</template>
                <template #content>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-emerald-50 rounded-lg p-4">
                            <i class="ri-login-box-line text-emerald-600 text-2xl"></i>
                            <p class="text-sm text-neutral-9 mt-1">Check-in</p>
                            <p class="font-bold text-neutral-12">{{ hotel.check_in_time }}</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <i class="ri-logout-box-line text-red-600 text-2xl"></i>
                            <p class="text-sm text-neutral-9 mt-1">Check-out</p>
                            <p class="font-bold text-neutral-12">{{ hotel.check_out_time }}</p>
                        </div>
                    </div>
                </template>
            </AppCard>
        </aside>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'
import useAuthCan from '@/Composables/useAuthCan'
import AppEmptyState from '@/Components/Misc/AppEmptyState.vue'

const { can } = useAuthCan()

const props = defineProps({
    hotel: { type: Object, required: true }
})

const { title } = useTitle(props.hotel.name)

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Hotels', href: route('hotel.index') },
    { label: props.hotel.name, last: true }
]

const formatNumber = (num) => num ? new Intl.NumberFormat('en-BD').format(num) : '0'
</script>
