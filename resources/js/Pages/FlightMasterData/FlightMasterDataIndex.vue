<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <AppCard class="hover:border-primary-500 transition-colors cursor-pointer" @click="$inertia.visit(route('flight.airline.index'))">
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-blue-50 text-blue-600">
                        <i class="ri-plane-line text-xl"></i>
                    </div>
                    <span>Airlines</span>
                </div>
            </template>
            <template #content>
                <p class="text-neutral-500 text-sm">Manage airlines, their codes, and details.</p>
            </template>
        </AppCard>

        <AppCard class="hover:border-primary-500 transition-colors cursor-pointer" @click="$inertia.visit(route('flight.airport.index'))">
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="ri-flight-land-line text-xl"></i>
                    </div>
                    <span>Airports</span>
                </div>
            </template>
            <template #content>
                <p class="text-neutral-500 text-sm">Manage airports, locations, and codes.</p>
            </template>
        </AppCard>

        <AppCard class="hover:border-primary-500 transition-colors cursor-pointer opacity-75">
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-purple-50 text-purple-600">
                        <i class="ri-plane-fill text-xl"></i>
                    </div>
                    <span>Aircraft Types</span>
                </div>
            </template>
            <template #content>
                <p class="text-neutral-500 text-sm">Manage aircraft models and manufacturers. (Coming Soon)</p>
            </template>
        </AppCard>

        <AppCard class="hover:border-primary-500 transition-colors cursor-pointer opacity-75">
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-amber-50 text-amber-600">
                        <i class="ri-building-line text-xl"></i>
                    </div>
                    <span>Suppliers</span>
                </div>
            </template>
            <template #content>
                <p class="text-neutral-500 text-sm">Manage flight data suppliers and APIs. (Coming Soon)</p>
            </template>
        </AppCard>

        <AppCard class="hover:border-primary-500 transition-colors cursor-pointer opacity-75">
            <template #title>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-rose-50 text-rose-600">
                        <i class="ri-price-tag-3-line text-xl"></i>
                    </div>
                    <span>Fare Classes</span>
                </div>
            </template>
            <template #content>
                <p class="text-neutral-500 text-sm">Manage cabin classes and fare rules. (Coming Soon)</p>
            </template>
        </AppCard>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const { title } = useTitle('Master Data')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Master Data', last: true }
]
</script>
