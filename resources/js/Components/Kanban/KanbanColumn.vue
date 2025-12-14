<script setup>
import { computed } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/outline';
import KanbanCard from './KanbanCard.vue';

const props = defineProps({
    column: Object,
    isFirstColumn: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['add-card', 'card-click', 'card-drop']);

const isOkColumn = computed(() => {
    return props.column.name === 'OK';
});

function handleDragOver(event) {
    event.preventDefault(); // Allow drop
    event.dataTransfer.dropEffect = 'move';
}

function handleDrop(event) {
    event.preventDefault();
    const cardId = event.dataTransfer.getData('text/plain');
    emit('card-drop', { cardId: parseInt(cardId), columnId: props.column.id });
}
</script>

<template>
    <div 
        class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 w-full flex flex-col max-h-[calc(100vh-16rem)]" 
        :data-column-id="column.id"
        @dragover="handleDragOver"
        @drop="handleDrop"
    >
        <!-- Column Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div 
                    class="w-3 h-3 rounded-full"
                    :style="{ backgroundColor: column.color }"
                ></div>
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                    {{ column.name }}
                </h3>
                <span class="px-2 py-0.5 text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                    {{ column.cards?.length || 0 }}
                </span>
            </div>
            <button
                v-if="isFirstColumn"
                @click="$emit('add-card')"
                class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition"
                title="Añadir tarjeta"
            >
                <PlusIcon class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </button>
        </div>

        <!-- WIP Limit Warning -->
        <div
            v-if="column.wip_limit && column.cards?.length >= column.wip_limit"
            class="mb-2 px-3 py-2 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded text-xs text-yellow-800 dark:text-yellow-200"
        >
            ⚠️ Límite WIP alcanzado ({{ column.wip_limit }})
        </div>

        <!-- Cards Container -->
        <div class="flex-1 overflow-y-auto min-h-[100px] space-y-3">
            <KanbanCard
                v-for="card in column.cards"
                :key="card.id"
                :card="card"
                :is-in-ok-column="isOkColumn"
                @click="$emit('card-click', card)"
            />
        </div>
    </div>
</template>
