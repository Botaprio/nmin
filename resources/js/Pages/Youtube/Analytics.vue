<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
    PlayCircleIcon, 
    EyeIcon, 
    HandThumbUpIcon, 
    ChatBubbleLeftIcon,
    UserGroupIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    channel: Object,
    videos: Array,
});

const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num?.toString() || '0';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="YouTube Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <PlayCircleIcon class="w-6 h-6 text-red-600" />
                YouTube Analytics
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Channel Stats -->
                <div v-if="channel" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ channel.title }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 p-4 rounded-lg">
                            <div class="flex items-center gap-3">
                                <UserGroupIcon class="w-8 h-8 text-red-600 dark:text-red-400" />
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Suscriptores</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(channel.subscriber_count) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-lg">
                            <div class="flex items-center gap-3">
                                <PlayCircleIcon class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Videos</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(channel.video_count) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-4 rounded-lg">
                            <div class="flex items-center gap-3">
                                <EyeIcon class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Vistas Totales</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(channel.view_count) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-lg">
                            <div class="flex items-center gap-3">
                                <HandThumbUpIcon class="w-8 h-8 text-green-600 dark:text-green-400" />
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Promedio</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(Math.round(channel.view_count / channel.video_count)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Videos List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Videos Recientes
                        </h3>

                        <div v-if="videos && videos.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Video
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Vistas
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Likes
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Comentarios
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="video in videos" :key="video.video_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ video.title }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ID: {{ video.video_id }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ formatDate(video.published_at) }}
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <EyeIcon class="w-4 h-4 text-gray-400" />
                                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ formatNumber(video.views) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <HandThumbUpIcon class="w-4 h-4 text-gray-400" />
                                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ formatNumber(video.likes) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <ChatBubbleLeftIcon class="w-4 h-4 text-gray-400" />
                                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ formatNumber(video.comments) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <a
                                                :href="`https://www.youtube.com/watch?v=${video.video_id}`"
                                                target="_blank"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm"
                                            >
                                                Ver en YouTube
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-else class="text-center py-12">
                            <PlayCircleIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                            <p class="text-gray-600 dark:text-gray-400">
                                No se encontraron videos
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
