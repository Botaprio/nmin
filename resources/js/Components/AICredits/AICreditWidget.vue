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
    const percentage = credit.percentage_remaining || 0;
    if (percentage < 20) return 'bg-red-500';
    if (percentage < 50) return 'bg-yellow-500';
    return 'bg-green-500';
};

const hasLowCredits = computed(() => {
    return props.credits?.some(credit => {
        const percentage = credit.percentage_remaining || 0;
        return percentage < 20;
    });
});
</script>

<template>
    <div class="card-glass rounded-2xl p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <SparklesIcon class="w-5 h-5" :style="{ color: 'var(--accent-purple)' }" />
                <h3 class="text-lg font-semibold" :style="{ color: 'var(--text-primary)' }">
                    Créditos IA
                </h3>
            </div>
            <Link
                :href="route('ai-credits.index')"
                class="text-sm hover:opacity-80"
                :style="{ color: 'var(--accent-blue)' }"
            >
                Gestionar
            </Link>
        </div>

        <!-- Low Credits Alert -->
        <div
            v-if="hasLowCredits"
            class="mb-4 p-3 rounded-xl flex items-start gap-2"
            :style="{ background: 'rgba(239, 68, 68, 0.1)', border: '1px solid rgba(239, 68, 68, 0.3)' }"
        >
            <ExclamationTriangleIcon class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" />
            <div class="flex-1">
                <p class="text-sm font-medium text-red-300">
                    ⚠️ Créditos bajos
                </p>
                <p class="text-xs text-red-400 mt-1">
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
                    <span class="text-sm font-medium" :style="{ color: 'var(--text-primary)' }">
                        {{ getServiceName(credit.service) }}
                    </span>
                    <span class="text-sm" :style="{ color: 'var(--text-secondary)' }">
                        {{ credit.remaining_credits }} / {{ credit.total_credits }}
                    </span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full rounded-full h-2" :style="{ background: 'var(--bg-tertiary)' }">
                    <div
                        :class="getPercentageClass(credit)"
                        class="h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${credit.percentage_remaining || 0}%` }"
                    ></div>
                </div>

                <div class="flex justify-between items-center text-xs" :style="{ color: 'var(--text-secondary)' }">
                    <span>
                        {{ credit.percentage_remaining || 0 }}% disponible
                    </span>
                    <span v-if="credit.billing_period_end">
                        Renueva: {{ new Date(credit.billing_period_end).toLocaleDateString() }}
                    </span>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <p class="text-sm mb-3" :style="{ color: 'var(--text-secondary)' }">
                No hay créditos configurados
            </p>
            <Link
                :href="route('ai-credits.index')"
                class="inline-flex items-center px-3 py-2 text-sm rounded-md transition hover:opacity-80"
                :style="{ background: 'var(--bg-secondary)', border: '1px solid var(--border-primary)', color: 'var(--text-primary)' }"
            >
                Configurar Créditos
            </Link>
        </div>
    </div>
</template>
