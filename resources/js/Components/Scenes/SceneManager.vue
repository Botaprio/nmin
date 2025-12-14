<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PlusIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    board: Object,
});

const showModal = ref(false);
const editingScene = ref(null);

const form = useForm({
    board_id: props.board.id,
    name: '',
    description: '',
    color: '#6B7280',
});

const openCreateModal = () => {
    editingScene.value = null;
    form.reset();
    form.board_id = props.board.id;
    showModal.value = true;
};

const openEditModal = (scene) => {
    editingScene.value = scene;
    form.name = scene.name;
    form.description = scene.description || '';
    form.color = scene.color;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingScene.value = null;
    form.reset();
};

const submit = () => {
    if (editingScene.value) {
        form.put(route('scenes.update', editingScene.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('scenes.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteScene = (scene) => {
    if (confirm(`¿Eliminar la escena "${scene.name}"? Las tarjetas no se eliminarán.`)) {
        form.delete(route('scenes.destroy', scene.id));
    }
};

const getSceneCompletion = (scene) => {
    if (!scene.cards || scene.cards.length === 0) return 0;
    
    const okColumn = props.board.columns?.find(c => c.name === 'OK');
    if (!okColumn) return 0;
    
    const completedCards = scene.cards.filter(card => card.column_id === okColumn.id).length;
    return Math.round((completedCards / scene.cards.length) * 100);
};

const isSceneComplete = (scene) => {
    return getSceneCompletion(scene) === 100;
};

const getBoardCompletion = () => {
    if (!props.board.scenes || props.board.scenes.length === 0) return 0;
    
    const completedScenes = props.board.scenes.filter(scene => isSceneComplete(scene)).length;
    return Math.round((completedScenes / props.board.scenes.length) * 100);
};
</script>

<template>
    <div class="rounded-lg shadow-sm p-4 mb-4" :style="{ background: 'var(--bg-secondary)', border: '1px solid var(--border-secondary)' }">
        <!-- Header con progreso general -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold mb-2" :style="{ color: 'var(--text-primary)' }">
                    Escenas del Proyecto
                </h3>
                <div class="flex items-center gap-3">
                    <div class="flex-1 rounded-full h-3" :style="{ background: 'var(--bg-tertiary)' }">
                        <div
                            class="h-3 rounded-full transition-all duration-300"
                            :class="getBoardCompletion() === 100 ? 'bg-green-500' : 'bg-blue-500'"
                            :style="{ width: `${getBoardCompletion()}%` }"
                        ></div>
                    </div>
                    <span class="text-sm font-semibold min-w-[60px]" :style="{ color: 'var(--text-primary)' }">
                        {{ getBoardCompletion() }}% Completo
                    </span>
                </div>
            </div>
            <button
                @click="openCreateModal"
                class="ml-4 inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition gap-1"
            >
                <PlusIcon class="w-4 h-4" />
                Nueva Escena
            </button>
        </div>

        <!-- Lista de escenas -->
        <div v-if="board.scenes && board.scenes.length > 0" class="space-y-2">
            <div
                v-for="scene in board.scenes"
                :key="scene.id"
                class="rounded-lg p-3 transition-all"
                :style="isSceneComplete(scene) 
                    ? { background: 'var(--bg-tertiary)', border: '1px solid var(--accent-teal)' } 
                    : { background: 'var(--bg-tertiary)', border: '1px solid var(--border-secondary)' }"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <div
                                class="w-3 h-3 rounded-full"
                                :style="{ backgroundColor: scene.color }"
                            ></div>
                            <h4 class="font-semibold" :style="{ color: 'var(--text-primary)' }">
                                {{ scene.name }}
                            </h4>
                            <span
                                v-if="isSceneComplete(scene)"
                                class="px-2 py-0.5 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 text-xs font-medium rounded"
                            >
                                ✓ Completa
                            </span>
                        </div>
                        <p v-if="scene.description" class="text-sm mb-2" :style="{ color: 'var(--text-primary)' }">
                            {{ scene.description }}
                        </p>
                        <div class="flex items-center gap-3 text-xs" :style="{ color: 'var(--text-muted)' }">
                            <span>{{ scene.cards?.length || 0 }} tarjetas</span>
                            <div class="flex items-center gap-2">
                                <div class="w-20 rounded-full h-1.5" :style="{ background: 'var(--bg-tertiary)' }">
                                    <div
                                        class="h-1.5 rounded-full transition-all"
                                        :class="getSceneCompletion(scene) === 100 ? 'bg-green-500' : 'bg-blue-500'"
                                        :style="{ width: `${getSceneCompletion(scene)}%` }"
                                    ></div>
                                </div>
                                <span class="font-medium">{{ getSceneCompletion(scene) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-1 ml-2">
                        <button
                            @click="openEditModal(scene)"
                            class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition"
                        >
                            <PencilIcon class="w-4 h-4" />
                        </button>
                        <button
                            @click="deleteScene(scene)"
                            class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition"
                        >
                            <TrashIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p class="mb-2">No hay escenas creadas</p>
            <p class="text-sm">Las escenas te ayudan a organizar tarjetas por grupos temáticos</p>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ editingScene ? 'Editar Escena' : 'Nueva Escena' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="Ej: Escena 1 - Introducción"
                                required
                            />
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descripción
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="Describe el contenido de esta escena..."
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Color
                            </label>
                            <div class="flex gap-2">
                                <input
                                    v-model="form.color"
                                    type="color"
                                    class="w-12 h-10 rounded border border-gray-300 dark:border-gray-700"
                                />
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    placeholder="#6B7280"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Guardando...' : (editingScene ? 'Actualizar' : 'Crear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
