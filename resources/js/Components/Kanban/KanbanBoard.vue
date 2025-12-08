<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import KanbanColumn from './KanbanColumn.vue';
import KanbanCard from './KanbanCard.vue';
import CardDetailModal from './CardDetailModal.vue';

const props = defineProps({
    board: Object,
});

const selectedCard = ref(null);
const showCardModal = ref(false);
const showNewCardModal = ref(false);
const selectedColumnId = ref(null);

const columns = computed(() => {
    if (!props.board || !props.board.columns) return [];
    return props.board.columns;
});

const openCardDetail = (card) => {
    selectedCard.value = card;
    showCardModal.value = true;
};

const openNewCardModal = (columnId) => {
    selectedColumnId.value = columnId;
    selectedCard.value = null;
    showNewCardModal.value = true;
};

const closeModal = () => {
    showCardModal.value = false;
    showNewCardModal.value = false;
    selectedCard.value = null;
    selectedColumnId.value = null;
};
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Debug info -->
        <div v-if="columns.length === 0" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-4">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">
                ⚠️ No hay columnas en este tablero
            </h3>
            <p class="text-yellow-800 dark:text-yellow-200 text-sm mb-2">
                Board ID: {{ board?.id }} | Board Name: {{ board?.name }}
            </p>
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                Board object: {{ JSON.stringify(board) }}
            </p>
        </div>

        <!-- Kanban Board -->
        <div v-else class="flex gap-4 overflow-x-auto pb-4">
            <div
                v-for="column in columns"
                :key="column.id"
                class="flex-shrink-0"
            >
                <KanbanColumn
                    :column="column"
                    @add-card="openNewCardModal(column.id)"
                >
                    <div
                        v-for="card in (column.cards || [])"
                        :key="card.id"
                        class="mb-2"
                    >
                        <KanbanCard
                            :card="card"
                            @click="openCardDetail(card)"
                        />
                    </div>
                </KanbanColumn>
            </div>
        </div>

        <!-- Modal de detalle de tarjeta -->
        <CardDetailModal
            v-if="showCardModal"
            :card="selectedCard"
            :board="board"
            @close="closeModal"
        />

        <!-- Modal de nueva tarjeta -->
        <CardDetailModal
            v-if="showNewCardModal"
            :column-id="selectedColumnId"
            :board="board"
            @close="closeModal"
        />
    </div>
</template>
