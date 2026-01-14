<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { format, addDays, startOfToday, parseISO } from 'date-fns';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    hotel: Object,
});

const startDate = ref(format(startOfToday(), 'yyyy-MM-dd'));
const daysToShow = 14;
const roomsData = ref([]);
const loading = ref(false);
const dates = ref([]);

const updateDates = () => {
    const start = parseISO(startDate.value);
    const d = [];
    for (let i = 0; i < daysToShow; i++) {
        d.push(format(addDays(start, i), 'yyyy-MM-dd'));
    }
    dates.value = d;
};

const fetchData = async () => {
    loading.value = true;
    updateDates();
    try {
        const response = await axios.get(route('hotel.inventory.data', props.hotel.id), {
            params: {
                start: dates.value[0],
                // end date for query should cover the last day. 
                // Service uses between [start, end].
                end: dates.value[dates.value.length - 1] 
            }
        });
        roomsData.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

watch(startDate, fetchData);

onMounted(() => {
    fetchData();
});

// Modal Logic
const showBulkModal = ref(false);
const form = useForm({
    room_id: '',
    start_date: '',
    end_date: '',
    total_inventory: null,
    blocked: null,
});

const openBulkUpdate = (room = null, date = null) => {
    form.reset();
    form.clearErrors();
    
    if (room) {
        form.room_id = room.id;
    } else if (roomsData.value.length > 0) {
        form.room_id = roomsData.value[0].id;
    }

    if (date) {
        form.start_date = date;
        form.end_date = date;
    } else {
        form.start_date = startDate.value;
        form.end_date = dates.value[dates.value.length - 1];
    }
    showBulkModal.value = true;
};

const submitBulkUpdate = () => {
    form.post(route('hotel.inventory.update', props.hotel.id), {
        onSuccess: () => {
            showBulkModal.value = false;
            fetchData();
        }
    });
};

const getCellClass = (data) => {
    if (!data.is_persisted) return 'bg-gray-50 text-gray-400';
    if (data.available === 0) return 'bg-red-50 text-red-600';
    if (data.available < 3) return 'bg-orange-50 text-orange-600';
    return 'bg-green-50 text-green-600';
};
</script>

<template>
    <AppLayout :title="`${hotel.name} - Availability`">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Inventory & Availability - {{ hotel.name }}
                </h2>
                <PrimaryButton @click="openBulkUpdate()">
                    Bulk Update
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <!-- Controls -->
                    <div class="flex items-center gap-4 mb-6">
                        <div>
                            <InputLabel value="Start Date" />
                            <TextInput type="date" v-model="startDate" class="mt-1 block w-full" />
                        </div>
                        <div class="flex-1"></div>
                        <div class="flex gap-2">
                             <SecondaryButton @click="startDate = format(addDays(parseISO(startDate), -daysToShow), 'yyyy-MM-dd')">
                                <i class="ri-arrow-left-s-line"></i> Prev {{ daysToShow }} Days
                             </SecondaryButton>
                             <SecondaryButton @click="startDate = format(startOfToday(), 'yyyy-MM-dd')">
                                Today
                             </SecondaryButton>
                             <SecondaryButton @click="startDate = format(addDays(parseISO(startDate), daysToShow), 'yyyy-MM-dd')">
                                Next {{ daysToShow }} Days <i class="ri-arrow-right-s-line"></i>
                             </SecondaryButton>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 border-r w-64">
                                        Room
                                    </th>
                                    <th v-for="date in dates" :key="date" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                        <div>{{ format(parseISO(date), 'MMM d') }}</div>
                                        <div class="text-gray-400 font-normal">{{ format(parseISO(date), 'EEE') }}</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="loading">
                                    <td :colspan="daysToShow + 1" class="px-6 py-10 text-center text-gray-500">
                                        Loading availability data...
                                    </td>
                                </tr>
                                <tr v-else v-for="room in roomsData" :key="room.id">
                                    <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r group cursor-pointer hover:bg-gray-50" @click="openBulkUpdate(room)">
                                        <div class="text-sm font-medium text-gray-900 flex items-center justify-between">
                                            {{ room.name }}
                                            <i class="ri-edit-line text-gray-400 opacity-0 group-hover:opacity-100"></i>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ room.room_type }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Default: {{ room.default_inventory }}</div>
                                    </td>
                                    <td v-for="date in dates" :key="date" 
                                        class="px-2 py-4 whitespace-nowrap text-center text-sm border-r border-gray-100 cursor-pointer hover:brightness-95 transition-all"
                                        :class="getCellClass(room.inventory_data[date])"
                                        @click="openBulkUpdate(room, date)">
                                        
                                        <div v-if="room.inventory_data[date].is_persisted">
                                            <div class="font-bold text-lg">{{ room.inventory_data[date].available }}</div>
                                            <div class="text-xs opacity-75">
                                                of {{ room.inventory_data[date].total_inventory }}
                                            </div>
                                            <div v-if="room.inventory_data[date].blocked > 0" class="text-[10px] text-red-500 font-bold mt-1">
                                                BLK {{ room.inventory_data[date].blocked }}
                                            </div>
                                        </div>
                                        <div v-else class="text-gray-300">
                                            -
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!loading && roomsData.length === 0">
                                    <td :colspan="daysToShow + 1" class="px-6 py-10 text-center text-gray-500">
                                        No rooms found for this hotel.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- Update Modal -->
        <Modal :show="showBulkModal" @close="showBulkModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Update Inventory
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <InputLabel value="Room" />
                        <select v-model="form.room_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option v-for="room in roomsData" :key="room.id" :value="room.id">
                                {{ room.name }} ({{ room.room_type }})
                            </option>
                        </select>
                        <div v-if="form.errors.room_id" class="text-red-500 text-sm mt-1">{{ form.errors.room_id }}</div>
                    </div>

                    <div>
                        <InputLabel value="Start Date" />
                        <TextInput type="date" v-model="form.start_date" class="mt-1 block w-full" />
                        <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">{{ form.errors.start_date }}</div>
                    </div>

                    <div>
                        <InputLabel value="End Date" />
                        <TextInput type="date" v-model="form.end_date" class="mt-1 block w-full" />
                        <div v-if="form.errors.end_date" class="text-red-500 text-sm mt-1">{{ form.errors.end_date }}</div>
                    </div>

                    <div>
                        <InputLabel value="Total Inventory" />
                        <TextInput type="number" v-model="form.total_inventory" class="mt-1 block w-full" placeholder="Existing value" />
                        <div class="text-xs text-gray-500 mt-1">Leave empty to keep existing</div>
                    </div>

                    <div>
                        <InputLabel value="Blocked Count" />
                        <TextInput type="number" v-model="form.blocked" class="mt-1 block w-full" placeholder="Existing value" />
                        <div class="text-xs text-gray-500 mt-1">Set number of blocked rooms</div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showBulkModal = false">Cancel</SecondaryButton>
                    <PrimaryButton @click="submitBulkUpdate" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save Changes
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
