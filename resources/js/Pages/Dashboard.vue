<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AICreditWidget from '@/Components/AICredits/AICreditWidget.vue';
import { 
    RectangleGroupIcon, 
    ClipboardDocumentListIcon,
    ClockIcon,
    ExclamationTriangleIcon 
} from '@heroicons/vue/24/outline';

defineProps({
    boards: Array,
    upcomingCards: Array,
    aiCredits: Array,
    recentActivity: Array,
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight" :style="{ color: 'var(--text-primary)' }">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="card-glass rounded-2xl p-6 shadow-xl hover:opacity-90 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" :style="{ color: 'var(--text-secondary)' }">Total Tableros</p>
                                <p class="text-3xl font-bold" :style="{ color: 'var(--text-primary)' }">
                                    {{ stats?.total_boards || 0 }}
                                </p>
                            </div>
                            <RectangleGroupIcon class="w-12 h-12" :style="{ color: 'var(--accent-blue)' }" />
                        </div>
                    </div>

                    <div class="card-glass rounded-2xl p-6 shadow-xl hover:opacity-90 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" :style="{ color: 'var(--text-secondary)' }">Total Tarjetas</p>
                                <p class="text-3xl font-bold" :style="{ color: 'var(--text-primary)' }">
                                    {{ stats?.total_cards || 0 }}
                                </p>
                            </div>
                            <ClipboardDocumentListIcon class="w-12 h-12" :style="{ color: 'var(--accent-teal)' }" />
                        </div>
                    </div>

                    <div class="card-glass rounded-2xl p-6 shadow-xl hover:opacity-90 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" :style="{ color: 'var(--text-secondary)' }">En Progreso</p>
                                <p class="text-3xl font-bold" :style="{ color: 'var(--text-primary)' }">
                                    {{ stats?.cards_in_progress || 0 }}
                                </p>
                            </div>
                            <ClockIcon class="w-12 h-12" :style="{ color: 'var(--accent-purple)' }" />
                        </div>
                    </div>

                    <div class="card-glass rounded-2xl p-6 shadow-xl hover:opacity-90 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" :style="{ color: 'var(--text-secondary)' }">Vencidas</p>
                                <p class="text-3xl font-bold text-red-400">
                                    {{ stats?.overdue_cards || 0 }}
                                </p>
                            </div>
                            <ExclamationTriangleIcon class="w-12 h-12 text-red-500" />
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Boards & Upcoming Cards -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Recent Boards -->
                        <div class="card-glass rounded-2xl p-6 shadow-xl">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold" :style="{ color: 'var(--text-primary)' }">
                                    Tableros Recientes
                                </h3>
                                <Link
                                    :href="route('boards.index')"
                                    class="text-sm hover:opacity-80"
                                    :style="{ color: 'var(--accent-blue)' }"
                                >
                                    Ver todos →
                                </Link>
                            </div>
                            <div v-if="boards && boards.length > 0" class="space-y-3">
                                <Link
                                    v-for="board in boards"
                                    :key="board.id"
                                    :href="route('boards.show', board.id)"
                                    class="block p-4 rounded-xl transition-all duration-300 hover:opacity-80"
                                    :style="{ background: 'var(--bg-tertiary)', border: '1px solid var(--border-secondary)' }"
                                >
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-medium" :style="{ color: 'var(--text-primary)' }">
                                                {{ board.name }}
                                            </h4>
                                            <p class="text-sm" :style="{ color: 'var(--text-secondary)' }">
                                                {{ board.cards_count || 0 }} tarjetas
                                            </p>
                                        </div>
                                        <span class="text-xs" :style="{ color: 'var(--text-muted)' }">
                                            {{ new Date(board.updated_at).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </Link>
                            </div>
                            <div v-else class="text-center py-8" :style="{ color: 'var(--text-muted)' }">
                                No tienes tableros aún
                            </div>
                        </div>

                        <!-- Upcoming Cards -->
                        <div class="card-glass rounded-2xl p-6 shadow-xl">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--text-primary)' }">
                                Próximos Vencimientos
                            </h3>
                            <div v-if="upcomingCards && upcomingCards.length > 0" class="space-y-3">
                                <div
                                    v-for="card in upcomingCards"
                                    :key="card.id"
                                    class="p-4 rounded-xl"
                                    :style="{ background: 'var(--bg-tertiary)', border: '1px solid var(--border-secondary)' }"
                                >
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium" :style="{ color: 'var(--text-primary)' }">
                                                {{ card.title }}
                                            </h4>
                                            <p class="text-sm mt-1" :style="{ color: 'var(--text-secondary)' }">
                                                Vence: {{ new Date(card.due_date).toLocaleDateString() }}
                                            </p>
                                        </div>
                                        <span
                                            :class="[
                                                card.priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                                card.priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' :
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                            ]"
                                            class="px-2 py-1 text-xs font-medium rounded"
                                        >
                                            {{ card.priority === 'urgent' ? 'Urgente' : card.priority === 'high' ? 'Alta' : 'Media' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8" :style="{ color: 'var(--text-muted)' }">
                                No hay tarjetas próximas a vencer
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- AI Credits Widget -->
                        <AICreditWidget :credits="aiCredits" />

                        <!-- Recent Activity -->
                        <div class="card-glass rounded-2xl p-6 shadow-xl">
                            <h3 class="text-lg font-semibold mb-4" :style="{ color: 'var(--text-primary)' }">
                                Actividad Reciente
                            </h3>
                            <div v-if="recentActivity && recentActivity.length > 0" class="space-y-3">
                                <div
                                    v-for="activity in recentActivity.slice(0, 10)"
                                    :key="activity.id"
                                    class="text-sm"
                                >
                                    <p :style="{ color: 'var(--text-secondary)' }">
                                        {{ activity.description }}
                                    </p>
                                    <p class="text-xs mt-1" :style="{ color: 'var(--text-muted)' }">
                                        {{ new Date(activity.created_at).toLocaleString() }}
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-center py-8" :style="{ color: 'var(--text-muted)' }">
                                No hay actividad reciente
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
