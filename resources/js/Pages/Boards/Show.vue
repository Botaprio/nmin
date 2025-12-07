<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import KanbanBoard from '@/Components/Kanban/KanbanBoard.vue';
import { ref } from 'vue';

const props = defineProps({
    board: Object,
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
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ board.name }}
                    </h2>
                    <p v-if="board.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
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

        <div class="py-6">
            <KanbanBoard :board="board" />
        </div>
    </AuthenticatedLayout>
</template>
