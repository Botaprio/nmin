<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';

defineProps({
    column: Object,
});

defineEmits(['add-card']);
</script>

<template>
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 w-80 flex flex-col max-h-[calc(100vh-16rem)]">
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
        <div class="flex-1 overflow-y-auto space-y-3 min-h-[100px]">
            <slot></slot>
        </div>
    </div>
</template>
