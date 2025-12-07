<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PlusIcon, RectangleGroupIcon } from '@heroicons/vue/24/outline';

defineProps({
    boards: Array,
});
</script>

<template>
    <Head title="Tableros" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Mis Tableros
                </h2>
                <Link
                    :href="route('boards.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Nuevo Tablero
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="boards.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <RectangleGroupIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        No tienes tableros aún
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Crea tu primer tablero para comenzar a gestionar tus videos
                    </p>
                    <Link
                        :href="route('boards.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition"
                    >
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Crear Primer Tablero
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="board in boards"
                        :key="board.id"
                        :href="route('boards.show', board.id)"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer group"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                {{ board.name }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                {{ board.cards_count || 0 }} tarjetas
                            </span>
                        </div>
                        
                        <p v-if="board.description" class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                            {{ board.description }}
                        </p>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ board.columns?.length || 0 }} columnas</span>
                            <span>Actualizado {{ new Date(board.updated_at).toLocaleDateString() }}</span>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
