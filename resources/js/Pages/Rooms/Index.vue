<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import RoomFilter from './Partials/RoomFilter.vue';
import RoomForm from './Partials/RoomForm.vue';
import RoomTable from './Partials/RoomTable.vue';
import RoomCardList from './Partials/RoomCardList.vue';
import Pagination from './Partials/Pagination.vue';

const props = defineProps({
    rooms: { type: Object, required: true },
    categories: { type: Array, required: true },
    filters: { type: Object, required: true },
    isAdmin: { type: Boolean, default: false },
    hasRequiredInfo: { type: Boolean, default: false },
});

const selectedCategory = ref(props.filters.category_id || '');
const searchQuery = ref(props.filters.search || '');
const availableOnly = ref(props.filters.available_only === 'true' || props.filters.available_only === true);
const showForm = ref(false);
const viewMode = ref('table');
const editing = ref(null);

const filteredRooms = computed(() => {
    return props.rooms.data;
});

const onFilterChange = () => {
    router.get(route('rooms.index'), {
        category_id: selectedCategory.value || null,
        search: searchQuery.value || null,
        available_only: availableOnly.value ? 'true' : null
    }, { preserveState: true, replace: true });
};

const resetForm = () => {
    editing.value = null;
    showForm.value = false;
};

const startEdit = (room) => {
    editing.value = room;
    showForm.value = true;
};

const removeRoom = (id) => {
    if (!confirm('Are you sure you want to delete this room?')) return;
    router.delete(route('rooms.destroy', id));
};
</script>

<template>

    <Head title="Manage Rooms" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold tracking-tight text-slate-800">Rooms Management</h2>
            </div>
        </template>

        <div class="py-6 bg-slate-50 min-h-screen">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- View: Room List (Filters + Table) -->
                <template v-if="!showForm">
                    <!-- Filter Section -->
                    <RoomFilter
                        v-model="selectedCategory"
                        v-model:search="searchQuery"
                        :categories="categories"
                        :is-admin="isAdmin"
                        :show-form="showForm"
                        @change="onFilterChange"
                        @toggle-form="showForm = $event"
                        v-model:view-mode="viewMode"
                        v-model:available-only="availableOnly"
                    />

                    <!-- Main Rooms Table Matrix -->
                    <div v-if="viewMode === 'table'">
                        <RoomTable
                            :rooms="filteredRooms"
                            :is-admin="isAdmin"
                            :has-required-info="hasRequiredInfo"
                            @edit="startEdit"
                            @delete="removeRoom"
                        />
                    </div>

                    <!-- Main Rooms Card Grid -->
                    <div v-else>
                        <RoomCardList
                            :rooms="filteredRooms"
                            :is-admin="isAdmin"
                            :has-required-info="hasRequiredInfo"
                            @edit="startEdit"
                            @delete="removeRoom"
                        />
                    </div>

                    <!-- Pagination links -->
                    <Pagination :links="rooms.links" />
                </template>

                <!-- View: Registration/Edit Form -->
                <template v-else>
                    <RoomForm
                        v-if="isAdmin"
                        :categories="categories"
                        :editing="editing"
                        @success="resetForm"
                        @cancel="resetForm"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
