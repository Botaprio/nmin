<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { SparklesIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    credits: Array,
});

const showModal = ref(false);
const editingCredit = ref(null);

const form = useForm({
    service: 'kling',
    total_credits: 1000,
    used_credits: 0,
    billing_period_start: '',
    billing_period_end: '',
    notes: '',
});

const openCreateModal = () => {
    editingCredit.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (credit) => {
    editingCredit.value = credit;
    form.service = credit.service;
    form.total_credits = credit.total_credits;
    form.used_credits = credit.used_credits;
    form.billing_period_start = credit.billing_period_start;
    form.billing_period_end = credit.billing_period_end;
    form.notes = credit.notes || '';
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingCredit.value = null;
    form.reset();
};

const submit = () => {
    if (editingCredit.value) {
        form.put(route('ai-credits.update', editingCredit.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('ai-credits.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteCredit = (credit) => {
    if (confirm(`¿Estás seguro de eliminar los créditos de ${credit.service}?`)) {
        form.delete(route('ai-credits.destroy', credit.id));
    }
};

const getServiceName = (service) => {
    const names = {
        'kling': 'Kling AI',
        'midjourney': 'MidJourney',
        'suno': 'Suno',
    };
    return names[service] || service;
};

const getPercentageClass = (credit) => {
    const percentage = (credit.remaining_credits / credit.total_credits) * 100;
    if (percentage < 20) return 'text-red-600 dark:text-red-400';
    if (percentage < 50) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-green-600 dark:text-green-400';
};
</script>

<template>
    <Head title="Créditos IA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <SparklesIcon class="w-6 h-6" />
                    Gestión de Créditos IA
                </h2>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition gap-2"
                >
                    <PlusIcon class="w-5 h-5" />
                    Agregar Créditos
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Credits Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div
                        v-for="credit in credits"
                        :key="credit.id"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ getServiceName(credit.service) }}
                            </h3>
                            <div class="flex gap-2">
                                <button
                                    @click="openEditModal(credit)"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400"
                                >
                                    <PencilIcon class="w-5 h-5" />
                                </button>
                                <button
                                    @click="deleteCredit(credit)"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400"
                                >
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Disponibles</span>
                                    <span :class="getPercentageClass(credit)" class="font-semibold">
                                        {{ credit.remaining_credits }} / {{ credit.total_credits }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div
                                        class="h-3 rounded-full transition-all"
                                        :class="{
                                            'bg-red-500': (credit.remaining_credits / credit.total_credits * 100) < 20,
                                            'bg-yellow-500': (credit.remaining_credits / credit.total_credits * 100) >= 20 && (credit.remaining_credits / credit.total_credits * 100) < 50,
                                            'bg-green-500': (credit.remaining_credits / credit.total_credits * 100) >= 50
                                        }"
                                        :style="{ width: `${(credit.remaining_credits / credit.total_credits) * 100}%` }"
                                    ></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Usados</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ credit.used_credits }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Porcentaje</p>
                                    <p :class="getPercentageClass(credit)" class="font-semibold">
                                        {{ Math.round((credit.remaining_credits / credit.total_credits) * 100) }}%
                                    </p>
                                </div>
                            </div>

                            <div v-if="credit.billing_period_end" class="text-xs text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-200 dark:border-gray-700">
                                Renueva: {{ new Date(credit.billing_period_end).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </div>

                            <div v-if="credit.notes" class="text-sm text-gray-600 dark:text-gray-400 italic">
                                {{ credit.notes }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!credits || credits.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <SparklesIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        No hay créditos configurados
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Agrega tus créditos de IA para realizar seguimiento del uso
                    </p>
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition gap-2"
                    >
                        <PlusIcon class="w-5 h-5" />
                        Agregar Créditos
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ editingCredit ? 'Editar Créditos' : 'Agregar Créditos' }}
                    </h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Service -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Servicio
                            </label>
                            <select
                                v-model="form.service"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                required
                            >
                                <option value="kling">Kling AI</option>
                                <option value="midjourney">MidJourney</option>
                                <option value="suno">Suno</option>
                            </select>
                            <div v-if="form.errors.service" class="text-red-600 text-sm mt-1">
                                {{ form.errors.service }}
                            </div>
                        </div>

                        <!-- Total Credits -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Créditos Totales
                            </label>
                            <input
                                v-model.number="form.total_credits"
                                type="number"
                                min="0"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                required
                            />
                            <div v-if="form.errors.total_credits" class="text-red-600 text-sm mt-1">
                                {{ form.errors.total_credits }}
                            </div>
                        </div>

                        <!-- Used Credits -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Créditos Usados
                            </label>
                            <input
                                v-model.number="form.used_credits"
                                type="number"
                                min="0"
                                :max="form.total_credits"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                required
                            />
                            <div v-if="form.errors.used_credits" class="text-red-600 text-sm mt-1">
                                {{ form.errors.used_credits }}
                            </div>
                        </div>

                        <!-- Billing Period -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Inicio de período
                                </label>
                                <input
                                    v-model="form.billing_period_start"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fin de período
                                </label>
                                <input
                                    v-model="form.billing_period_end"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notas (opcional)
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                placeholder="Ej: Plan mensual, renovación automática"
                            ></textarea>
                        </div>

                        <!-- Actions -->
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
                                {{ form.processing ? 'Guardando...' : (editingCredit ? 'Actualizar' : 'Crear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
