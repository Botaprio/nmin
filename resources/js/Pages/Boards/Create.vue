<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { RectangleGroupIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    description: '',
    is_template: false,
});

const submit = () => {
    form.post(route('boards.store'));
};
</script>

<template>
    <Head title="Crear Tablero" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <RectangleGroupIcon class="w-6 h-6" />
                Crear Nuevo Tablero
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nombre del Tablero <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Videos YouTube - Octubre 2025"
                                required
                                autofocus
                            />
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Describe el propósito de este tablero..."
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">
                                {{ form.errors.description }}
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Opcional: Agrega información sobre qué videos o proyectos manejará este tablero
                            </p>
                        </div>

                        <!-- Is Template -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    id="is_template"
                                    v-model="form.is_template"
                                    type="checkbox"
                                    class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-blue-600 focus:ring-blue-500"
                                />
                            </div>
                            <div class="ml-3">
                                <label for="is_template" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Usar como plantilla
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Los tableros plantilla pueden ser reutilizados para proyectos futuros
                                </p>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">
                                ℹ️ Columnas por defecto
                            </h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                Se crearán automáticamente las siguientes columnas para tu flujo de trabajo:
                            </p>
                            <ul class="text-sm text-blue-700 dark:text-blue-300 mt-2 space-y-1 ml-4 list-disc">
                                <li>Ideas</li>
                                <li>Script Writing (Escritura de guión)</li>
                                <li>Pre-production (Pre-producción)</li>
                                <li>Animating (Animación con IA)</li>
                                <li>Editing (Edición)</li>
                                <li>Review/Approval (Revisión)</li>
                                <li>Publishing (Publicación)</li>
                                <li>Published (Publicado)</li>
                            </ul>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a
                                :href="route('boards.index')"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
                            >
                                Cancelar
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ form.processing ? 'Creando...' : 'Crear Tablero' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
