<template>
    <Head :title="title"></Head>
    <AppSectionHeader :title="title" :bread-crumb="breadCrumb">
        <template #right>
            <AppButton class="btn btn-secondary" @click="$inertia.visit(route('flight.airline.index'))">
                <i class="ri-arrow-left-line mr-1"></i> Back
            </AppButton>
        </template>
    </AppSectionHeader>

    <form @submit.prevent="submit">
        <AppCard class="mb-6">
            <template #title>Airline Details</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <AppLabel for="iata_code" required>IATA Code</AppLabel>
                        <AppInput v-model="form.iata_code" maxlength="2" :error="form.errors.iata_code" placeholder="e.g. BG" />
                    </div>
                    <div>
                        <AppLabel for="icao_code">ICAO Code</AppLabel>
                        <AppInput v-model="form.icao_code" maxlength="3" :error="form.errors.icao_code" placeholder="e.g. BBC" />
                    </div>
                    <div>
                        <AppLabel for="name" required>Airline Name</AppLabel>
                        <AppInput v-model="form.name" :error="form.errors.name" />
                    </div>
                    <div>
                        <AppLabel for="country_id">Country</AppLabel>
                        <AppCombobox v-model="form.country_id" :options="countryOptions" emit-value :error="form.errors.country_id" />
                    </div>
                    <div>
                        <AppLabel for="alliance">Alliance</AppLabel>
                        <AppCombobox v-model="form.alliance" :options="allianceOptions" emit-value />
                    </div>
                    <div>
                        <AppLabel for="callsign">Callsign</AppLabel>
                        <AppInput v-model="form.callsign" placeholder="e.g. BANGLA" />
                    </div>
                </div>
            </template>
        </AppCard>

        <AppCard class="mb-6">
            <template #title>Branding</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <AppLabel for="logo_url">Logo URL</AppLabel>
                        <AppInput v-model="form.logo_url" placeholder="https://..." />
                    </div>
                    <div>
                        <AppLabel for="website">Website</AppLabel>
                        <AppInput v-model="form.website" placeholder="https://..." />
                    </div>
                </div>
            </template>
        </AppCard>

        <AppCard class="mb-6">
            <template #title>Settings</template>
            <template #content>
                <div class="flex flex-wrap gap-6">
                    <AppCheckbox v-model="form.is_active" label="Active" />
                    <AppCheckbox v-model="form.is_lcc" label="Low-cost Carrier (LCC)" />
                </div>
            </template>
        </AppCard>

        <div class="flex justify-end gap-3">
            <AppButton type="button" class="btn btn-secondary" @click="$inertia.visit(route('flight.airline.index'))">Cancel</AppButton>
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
    airline: { type: Object, default: () => null },
    countries: { type: Array, default: () => [] }
})

const isEdit = computed(() => !!props.airline)
const { title } = useTitle(isEdit.value ? 'Edit Airline' : 'Add Airline')

const breadCrumb = [
    { label: 'Home', href: route('dashboard.index') },
    { label: 'Flights', href: route('flight.index') },
    { label: 'Airlines', href: route('flight.airline.index') },
    { label: isEdit.value ? 'Edit' : 'Add', last: true }
]

const countryOptions = computed(() => props.countries.map(c => ({ value: c.id, label: c.name })))

const allianceOptions = [
    { value: 'Star Alliance', label: 'Star Alliance' },
    { value: 'SkyTeam', label: 'SkyTeam' },
    { value: 'oneworld', label: 'oneworld' }
]

const form = useForm({
    iata_code: props.airline?.iata_code ?? '',
    icao_code: props.airline?.icao_code ?? '',
    name: props.airline?.name ?? '',
    country_id: props.airline?.country_id ?? null,
    alliance: props.airline?.alliance ?? null,
    callsign: props.airline?.callsign ?? '',
    logo_url: props.airline?.logo_url ?? '',
    website: props.airline?.website ?? '',
    is_active: props.airline?.is_active ?? true,
    is_lcc: props.airline?.is_lcc ?? false
})

const submit = () => {
    if (isEdit.value) {
        form.put(route('flight.airline.update', props.airline.id))
    } else {
        form.post(route('flight.airline.store'))
    }
}
</script>
