<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import KanbanColumn from './KanbanColumn.vue';
import KanbanCard from './KanbanCard.vue';
import CardDetailModal from './CardDetailModal.vue';
import { PlusIcon, FolderIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    board: Object,
});

const selectedCard = ref(null);
const showCardModal = ref(false);
const showNewCardModal = ref(false);
const selectedColumnId = ref(null);
const showSceneModal = ref(false);
const sceneForm = ref({
    name: '',
    description: '',
    color: '#6B7280',
});

// Drag and drop handlers
async function onCardDrop({ cardId, columnId }) {
    try {
        // Calculate position - put at the end of the target column
        const targetColumnCards = props.board?.cards?.filter(c => c.column_id === columnId) || [];
        const position = targetColumnCards.length;

        await router.post(`/cards/${cardId}/move`, {
            column_id: columnId,
            position: position,
        });
        await router.reload();
    } catch (error) {
        console.error('Error moving card:', error);
    }
}

const columns = computed(() => {
    if (!props.board || !props.board.columns) return [];
    return props.board.columns;
});

const firstColumn = computed(() => {
    return columns.value[0] || null;
});

const scenes = computed(() => {
    if (!props.board || !props.board.scenes) return [];
    return props.board.scenes;
});

// Check if a scene is complete (all cards in OK column)
const isSceneComplete = (scene) => {
    if (!scene.cards || scene.cards.length === 0) return false;
    
    const okColumn = columns.value.find(col => col.name === 'OK');
    if (!okColumn) return false;
    
    return scene.cards.every(card => card.column_id === okColumn.id);
};

const openCardDetail = (card) => {
    selectedCard.value = card;
    showCardModal.value = true;
};

const openNewCardModal = (columnId, sceneId = null) => {
    // Solo permitir crear tarjetas en la primera columna
    if (!firstColumn.value) {
        alert('Error: No hay columnas disponibles');
        return;
    }
    
    if (columnId !== firstColumn.value.id) {
        alert('Las tarjetas solo pueden crearse en la primera columna (Prompts)');
        return;
    }
    
    selectedColumnId.value = columnId;
    selectedCard.value = null; // Null para nueva tarjeta
    showNewCardModal.value = true;
};

const openSceneModal = () => {
    sceneForm.value = {
        name: '',
        description: '',
        color: '#6B7280',
    };
    showSceneModal.value = true;
};

const closeModal = () => {
    showCardModal.value = false;
    showNewCardModal.value = false;
    showSceneModal.value = false;
    selectedCard.value = null;
    selectedColumnId.value = null;
};

const createScene = () => {
    router.post(route('scenes.store'), {
        board_id: props.board.id,
        ...sceneForm.value,
    }, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const deleteScene = (sceneId) => {
    if (confirm('¿Estás seguro de eliminar esta escena? Las tarjetas no se eliminarán.')) {
        router.delete(route('scenes.destroy', sceneId), {
            preserveScroll: true,
        });
    }
};

// Get cards for a specific scene and column
const getSceneCards = (sceneId, columnId) => {
    const column = columns.value.find(c => c.id === columnId);
    if (!column || !column.cards) return [];
    return column.cards.filter(card => card.scene_id === sceneId);
};

// Get cards without scene for a column
const getUnassignedCards = (columnId) => {
    const column = columns.value.find(c => c.id === columnId);
    if (!column || !column.cards) return [];
    return column.cards.filter(card => !card.scene_id);
};
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Scene Management Header -->
        <div class="mb-6 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <FolderIcon class="w-5 h-5" :style="{ color: 'var(--text-secondary)' }" />
                <h3 class="text-lg font-semibold" :style="{ color: 'var(--text-primary)' }">
                    Escenas ({{ scenes.length }})
                </h3>
            </div>
            <button
                @click="openSceneModal"
                class="inline-flex items-center px-3 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition gap-2"
            >
                <PlusIcon class="w-4 h-4" />
                Nueva Escena
            </button>
        </div>

        <!-- Kanban Board -->
        <div class="space-y-8">
            <!-- Each Scene as a Group -->
            <div
                v-for="scene in scenes"
                :key="scene.id"
                class="border-2 rounded-lg p-4 transition-all duration-300"
                :class="{
                    'border-green-400 bg-green-50/50 dark:bg-green-900/10': isSceneComplete(scene),
                    'border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800': !isSceneComplete(scene)
                }"
            >
                <!-- Scene Header -->
                <div class="mb-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-4 h-4 rounded-full"
                            :style="{ backgroundColor: scene.color }"
                        ></div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                {{ scene.name }}
                                <span v-if="isSceneComplete(scene)" class="text-green-600 dark:text-green-400">
                                    ✓ Completa
                                </span>
                            </h4>
                            <p v-if="scene.description" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ scene.description }}
                            </p>
                        </div>
                    </div>
                    <button
                        @click="deleteScene(scene.id)"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 text-sm"
                    >
                        Eliminar
                    </button>
                </div>

                <!-- Columns for this Scene -->
                <div class="grid grid-cols-4 gap-6">
                    <div
                        v-for="(column, index) in columns"
                        :key="column.id"
                        class="min-w-0 relative"
                        :class="{
                            'border-r-2 border-gray-300 dark:border-gray-600': index < columns.length - 1,
                            'pr-6': index < columns.length - 1
                        }"
                    >
                        <KanbanColumn
                            :column="{ ...column, cards: getSceneCards(scene.id, column.id) }"
                            :is-first-column="index === 0"
                            @add-card="openNewCardModal(column.id, scene.id)"
                            @card-click="openCardDetail"
                            @card-drop="onCardDrop"
                        />
                    </div>
                </div>
            </div>

            <!-- Unassigned Cards (without scene) -->
            <div
                v-if="columns.some(col => getUnassignedCards(col.id).length > 0)"
                class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50"
            >
                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-4">
                    Tarjetas sin asignar a escena
                </h4>
                <div class="grid grid-cols-4 gap-6">
                    <div
                        v-for="(column, index) in columns"
                        :key="column.id"
                        class="min-w-0 relative"
                        :class="{
                            'border-r-2 border-gray-300 dark:border-gray-600': index < columns.length - 1,
                            'pr-6': index < columns.length - 1
                        }"
                    >
                        <KanbanColumn
                            :column="{ ...column, cards: getUnassignedCards(column.id) }"
                            :is-first-column="index === 0"
                            @add-card="openNewCardModal(column.id, null)"
                            @card-click="openCardDetail"
                            @card-drop="onCardDrop"
                        />
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="scenes.length === 0" class="text-center py-12 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                <FolderIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    No hay escenas creadas
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Crea tu primera escena para organizar las tarjetas
                </p>
                <button
                    @click="openSceneModal"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition gap-2"
                >
                    <PlusIcon class="w-5 h-5" />
                    Crear Primera Escena
                </button>
            </div>
        </div>

        <!-- Card Detail Modal -->
        <CardDetailModal
            v-if="showCardModal"
            :card="selectedCard"
            :board="board"
            @close="closeModal"
        />

        <!-- New Card Modal -->
        <CardDetailModal
            v-if="showNewCardModal"
            :card="null"
            :column-id="selectedColumnId"
            :board="board"
            :initial-scene-id="selectedCard?.scene_id"
            @close="closeModal"
        />

        <!-- Scene Creation Modal -->
        <div v-if="showSceneModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Nueva Escena
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre de la Escena
                            </label>
                            <input
                                v-model="sceneForm.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="Ej: Escena 1 - Introducción"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descripción (opcional)
                            </label>
                            <textarea
                                v-model="sceneForm.description"
                                rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="Describe esta escena..."
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Color
                            </label>
                            <input
                                v-model="sceneForm.color"
                                type="color"
                                class="w-full h-10 rounded-md border-gray-300 dark:border-gray-700"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button
                            @click="closeModal"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="createScene"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition"
                        >
                            Crear Escena
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>