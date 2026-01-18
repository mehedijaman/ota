<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('flight.airport.index'))">
                <i class="ri-arrow-left-line mr-1"></i> Back
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submit">
        <AppCard class="mb-6">
            <template #title>Airport Details</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <AppLabel for="iata_code" required>IATA Code</AppLabel>
                        <AppInput v-model="form.iata_code" maxlength="3" :error="form.errors.iata_code" placeholder="e.g. DAC" />
                    </div>
                    <div>
                        <AppLabel for="icao_code">ICAO Code</AppLabel>
                        <AppInput v-model="form.icao_code" maxlength="4" :error="form.errors.icao_code" placeholder="e.g. VGZR" />
                    </div>
                    <div>
                        <AppLabel for="name" required>Airport Name</AppLabel>
                        <AppInput v-model="form.name" :error="form.errors.name" />
                    </div>
                    <div>
                        <AppLabel for="city" required>City</AppLabel>
                        <AppInput v-model="form.city" :error="form.errors.city" />
                    </div>
                    <div>
                        <AppLabel for="country_id" required>Country</AppLabel>
                        <AppCombobox v-model="form.country_id" :options="countryOptions" emit-value :error="form.errors.country_id" />
                    </div>
                    <div>
                        <AppLabel for="timezone">Timezone</AppLabel>
                        <AppInput v-model="form.timezone" placeholder="e.g. Asia/Dhaka" />
                    </div>
                </div>
            </template>
        </AppCard>

        <AppCard class="mb-6">
            <template #title>Settings</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <AppLabel for="search_priority">Search Priority</AppLabel>
                        <AppInput type="number" v-model="form.search_priority" min="0" />
                    </div>
                    <div class="flex items-center gap-4 pt-6">
                        <AppCheckbox v-model="form.is_popular" label="Popular Airport" />
                    </div>
                    <div class="flex items-center gap-4 pt-6">
                        <AppCheckbox v-model="form.is_active" label="Active" />
                    </div>
                </div>
            </template>
        </AppCard>

        <div class="flex justify-end gap-3">
            <AppButton type="button" class="btn btn-secondary" @click="$inertia.visit(route('flight.airport.index'))">Cancel</AppButton>
            <AppButton type="submit" class="btn btn-primary" :loading="form.processing">
                <i class="ri-save-line mr-1"></i> {{ isEdit ? 'Update' : 'Create' }}
            </AppButton>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import useTitle from '@/Composables/useTitle'

const props = defineProps({
    airport: { type: Object, default: () => null },
    countries: { type: Array, default: () => [] }
})

const isEdit = computed(() => !!props.airport)
const { title } = useTitle(isEdit.value ? 'Edit Airport' : 'Add Airport')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Airports', href: route('flight.airport.index') },
    { label: isEdit.value ? 'Edit' : 'Add', last: true }
]

const countryOptions = computed(() => props.countries.map(c => ({ value: c.id, label: c.name })))

const form = useForm({
    iata_code: props.airport?.iata_code ?? '',
    icao_code: props.airport?.icao_code ?? '',
    name: props.airport?.name ?? '',
    city: props.airport?.city ?? '',
    country_id: props.airport?.country_id ?? null,
    timezone: props.airport?.timezone ?? '',
    search_priority: props.airport?.search_priority ?? 0,
    is_popular: props.airport?.is_popular ?? false,
    is_active: props.airport?.is_active ?? true
})

const submit = () => {
    if (isEdit.value) {
        form.put(route('flight.airport.update', props.airport.id))
    } else {
        form.post(route('flight.airport.store'))
    }
}
</script>
