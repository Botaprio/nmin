<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import KanbanBoard from '@/Components/Kanban/KanbanBoard.vue';
import SceneManager from '@/Components/Scenes/SceneManager.vue';
import { ref } from 'vue';

const props = defineProps({
    board: Object,
    completionPercentage: Number,
});

const isEditingBoard = ref(false);
const boardForm = ref({
    name: props.board.name,
    description: props.board.description,
});

const updateBoard = () => {
    router.put(route('boards.update', props.board.id), boardForm.value, {
        onSuccess: () => {
            isEditingBoard.value = false;
        },
    });
};
</script>

<template>
    <Head :title="board.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div v-if="!isEditingBoard" class="flex-1">
                    <div class="flex items-center gap-4">
                        <h2 class="font-semibold text-xl leading-tight" :style="{ color: 'var(--text-primary)' }">
                            {{ board.name }}
                        </h2>
                        <!-- Completion Badge -->
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold"
                             :class="{
                                 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200': completionPercentage === 100,
                                 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200': completionPercentage > 0 && completionPercentage < 100,
                                 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400': completionPercentage === 0
                             }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ completionPercentage }}% Completado
                        </div>
                    </div>
                    <p v-if="board.description" class="text-sm mt-1" :style="{ color: 'var(--text-secondary)' }">
                        {{ board.description }}
                    </p>
                </div>
                <div v-else class="flex-1 flex gap-2">
                    <input
                        v-model="boardForm.name"
                        type="text"
                        class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                        placeholder="Nombre del tablero"
                    />
                    <button
                        @click="updateBoard"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        Guardar
                    </button>
                    <button
                        @click="isEditingBoard = false"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition"
                    >
                        Cancelar
                    </button>
                </div>
                <button
                    v-if="!isEditingBoard"
                    @click="isEditingBoard = true"
                    class="ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm"
                >
                    Editar Tablero
                </button>
            </div>
        </template>

        <div class="py-6 space-y-6">
            <!-- Progress Bar -->
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div
                        class="h-full transition-all duration-500 rounded-full"
                        :class="{
                            'bg-green-500': completionPercentage === 100,
                            'bg-blue-500': completionPercentage > 0 && completionPercentage < 100,
                            'bg-gray-400': completionPercentage === 0
                        }"
                        :style="{ width: `${completionPercentage}%` }"
                    ></div>
                </div>
            </div>
            
            <!-- Board Sections Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Escenas (Kanban Board) - Left Side (takes more space) -->
                <div class="order-2 lg:order-1 lg:col-span-8">
                    <KanbanBoard :board="board" />
                </div>
                
                <!-- Escenas del Proyecto (Scene Manager) - Right Side (takes less space) -->
                <div class="order-1 lg:order-2 lg:col-span-4">
                    <SceneManager :board="board" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
