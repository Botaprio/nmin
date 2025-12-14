<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PlusIcon, RectangleGroupIcon } from '@heroicons/vue/24/outline';

defineProps({
    boards: Array,
});

const deleteBoard = (boardId) => {
    if (confirm('¿Estás seguro de eliminar este tablero? Se eliminarán todas las columnas, tarjetas y escenas asociadas.')) {
        router.delete(route('boards.destroy', boardId), {
            preserveScroll: true,
        });
    }
};
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
                    <div
                        v-for="board in boards"
                        :key="board.id"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 group relative"
                    >
                        <Link
                            :href="route('boards.show', board.id)"
                            class="block cursor-pointer"
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
                        
                        <button
                            @click.stop="deleteBoard(board.id)"
                            class="absolute top-4 right-4 p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Eliminar tablero"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
