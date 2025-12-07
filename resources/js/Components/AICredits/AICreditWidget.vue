<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { SparklesIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    credits: Array,
});

const getServiceName = (service) => {
    const names = {
        'kling': 'Kling AI',
        'midjourney': 'MidJourney',
        'suno': 'Suno',
    };
    return names[service] || service;
};

const getServiceColor = (service) => {
    const colors = {
        'kling': 'blue',
        'midjourney': 'purple',
        'suno': 'pink',
    };
    return colors[service] || 'gray';
};

const getPercentageClass = (credit) => {
    const percentage = credit.remaining_credits / credit.total_credits * 100;
    if (percentage < 20) return 'bg-red-500';
    if (percentage < 50) return 'bg-yellow-500';
    return 'bg-green-500';
};

const hasLowCredits = computed(() => {
    return props.credits?.some(credit => {
        const percentage = (credit.remaining_credits / credit.total_credits) * 100;
        return percentage < 20;
    });
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <SparklesIcon class="w-5 h-5 text-purple-500" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Créditos IA
                </h3>
            </div>
            <Link
                :href="route('ai-credits.index')"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
            >
                Gestionar
            </Link>
        </div>

        <!-- Low Credits Alert -->
        <div
            v-if="hasLowCredits"
            class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-2"
        >
            <ExclamationTriangleIcon class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                    ⚠️ Créditos bajos
                </p>
                <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                    Algunos servicios tienen menos del 20% de créditos disponibles
                </p>
            </div>
        </div>

        <!-- Credits List -->
        <div v-if="credits && credits.length > 0" class="space-y-4">
            <div
                v-for="credit in credits"
                :key="credit.id"
                class="space-y-2"
            >
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ getServiceName(credit.service) }}
                    </span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ credit.remaining_credits }} / {{ credit.total_credits }}
                    </span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div
                        :class="getPercentageClass(credit)"
                        class="h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${(credit.remaining_credits / credit.total_credits) * 100}%` }"
                    ></div>
                </div>

                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                    <span>
                        {{ Math.round((credit.remaining_credits / credit.total_credits) * 100) }}% disponible
                    </span>
                    <span v-if="credit.billing_period_end">
                        Renueva: {{ new Date(credit.billing_period_end).toLocaleDateString() }}
                    </span>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                No hay créditos configurados
            </p>
            <Link
                :href="route('ai-credits.index')"
                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition"
            >
                Configurar Créditos
            </Link>
        </div>
    </div>
</template>
