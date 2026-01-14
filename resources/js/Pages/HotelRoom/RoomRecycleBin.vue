<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  hotel: Object,
  rooms: Object,
});

const form = useForm({});

const restore = (id) => {
  if (confirm('Are you sure you want to restore this room?')) {
    form.post(route('hotel.room.restore', [props.hotel.id, id]));
  }
};

const destroyForce = (id) => {
  if (confirm('Are you sure you want to permanently delete this room?')) {
    form.delete(route('hotel.room.destroyForce', [props.hotel.id, id]));
  }
};
</script>

<template>
  <AuthenticatedLayout title="Room Recycle Bin">
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          Room Recycle Bin - {{ hotel.name }}
        </h1>
        <Link :href="route('hotel.room.index', hotel.id)" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
          Back to Rooms
        </Link>
      </div>

      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deleted At</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="room in rooms.data" :key="room.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ room.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ room.room_type?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ room.deleted_at }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="restore(room.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Restore</button>
                <button @click="destroyForce(room.id)" class="text-red-600 hover:text-red-900">Force Delete</button>
              </td>
            </tr>
            <tr v-if="rooms.data.length === 0">
              <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No deleted rooms found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
