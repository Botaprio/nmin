<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Container, Draggable } from 'vue-dndrop';
import KanbanColumn from './KanbanColumn.vue';
import KanbanCard from './KanbanCard.vue';
import CardDetailModal from './CardDetailModal.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    board: Object,
});

const selectedCard = ref(null);
const showCardModal = ref(false);
const showNewCardModal = ref(false);
const selectedColumnId = ref(null);

const columns = computed(() => props.board.columns || []);

const onCardDrop = (columnId, dropResult) => {
    const { removedIndex, addedIndex, payload } = dropResult;
    
    if (removedIndex === null && addedIndex === null) return;

    const card = payload;
    
    // Actualizar posición en el servidor
    router.post(route('cards.move', card.id), {
        column_id: columnId,
        position: addedIndex,
    }, {
        preserveScroll: true,
    });
};

const openCardDetail = (card) => {
    selectedCard.value = card;
    showCardModal.value = true;
};

const openNewCardModal = (columnId) => {
    selectedColumnId.value = columnId;
    showNewCardModal.value = true;
};

const closeModal = () => {
    showCardModal.value = false;
    showNewCardModal.value = false;
    selectedCard.value = null;
    selectedColumnId.value = null;
};

const getCardPayload = (columnId, index) => {
    const column = columns.value.find(c => c.id === columnId);
    return column?.cards[index];
};
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex gap-4 overflow-x-auto pb-4">
            <Container
                v-for="column in columns"
                :key="column.id"
                group-name="cards"
                @drop="(e) => onCardDrop(column.id, e)"
                :get-child-payload="(index) => getCardPayload(column.id, index)"
                class="flex-shrink-0"
                drag-class="card-ghost"
                drop-class="card-ghost-drop"
            >
                <KanbanColumn
                    :column="column"
                    @add-card="openNewCardModal(column.id)"
                >
                    <Draggable v-for="card in column.cards" :key="card.id">
                        <KanbanCard
                            :card="card"
                            @click="openCardDetail(card)"
                        />
                    </Draggable>
                </KanbanColumn>
            </Container>
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

<style scoped>
.card-ghost {
    opacity: 0.5;
    transform: rotate(3deg);
}

.card-ghost-drop {
    background-color: rgba(59, 130, 246, 0.1);
    border: 2px dashed rgba(59, 130, 246, 0.5);
}
</style>
