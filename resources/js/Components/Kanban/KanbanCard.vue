<script setup>
import { computed } from 'vue';
import { 
    CalendarIcon, 
    ClockIcon, 
    UserCircleIcon,
    ChatBubbleLeftIcon,
    PaperClipIcon,
    VideoCameraIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    card: Object,
    isInOkColumn: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['dragstart', 'dragend']);

const priorityColors = {
    low: 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    medium: 'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    high: 'bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    urgent: 'bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const priorityLabel = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    urgent: 'Urgente',
};

const isOverdue = computed(() => {
    if (!props.card.due_date) return false;
    return new Date(props.card.due_date) < new Date();
});

const daysUntilDue = computed(() => {
    if (!props.card.due_date) return null;
    const days = Math.ceil((new Date(props.card.due_date) - new Date()) / (1000 * 60 * 60 * 24));
    return days;
});

function handleDragStart(event) {
    event.dataTransfer.setData('text/plain', props.card.id.toString());
    event.dataTransfer.effectAllowed = 'move';
    emit('dragstart', props.card);
}

function handleDragEnd(event) {
    emit('dragend', props.card);
}
</script>

<template>
    <div 
        draggable="true"
        @dragstart="handleDragStart"
        @dragend="handleDragEnd"
        class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow cursor-move border border-gray-200 dark:border-gray-600"
    >
        <!-- Compact view for OK column -->
        <div v-if="isInOkColumn" class="flex items-center justify-between">
            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm line-clamp-1 flex-1">
                {{ card.title }}
            </h4>
            <div v-if="card.assigned_users && card.assigned_users.length > 0" class="flex -space-x-1 ml-2">
                <img
                    v-for="user in card.assigned_users.slice(0, 2)"
                    :key="user.id"
                    :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
                    :alt="user.name"
                    :title="user.name"
                    class="w-5 h-5 rounded-full border border-white dark:border-gray-700"
                />
            </div>
        </div>

        <!-- Full view for other columns -->
        <div v-else>
            <!-- Priority Badge -->
            <div v-if="card.priority" class="flex items-center justify-between mb-2">
                <span 
                    :class="priorityColors[card.priority]"
                    class="px-2 py-0.5 text-xs font-medium rounded"
                >
                    {{ priorityLabel[card.priority] }}
                </span>
                <span
                    v-if="isOverdue"
                    class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded"
                >
                    Vencida
                </span>
            </div>

            <!-- Card Title -->
            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2 line-clamp-2">
                {{ card.title }}
            </h4>

            <!-- Card Description -->
            <p v-if="card.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                {{ card.description }}
            </p>

            <!-- Tags -->
            <div v-if="card.tags && card.tags.length > 0" class="flex flex-wrap gap-1 mb-3">
                <span
                    v-for="tag in card.tags.slice(0, 3)"
                    :key="tag.id"
                    class="px-2 py-0.5 text-xs rounded"
                    :style="{ backgroundColor: tag.color + '40', color: tag.color }"
                >
                    {{ tag.name }}
                </span>
                <span
                    v-if="card.tags.length > 3"
                    class="px-2 py-0.5 text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded"
                >
                    +{{ card.tags.length - 3 }}
                </span>
            </div>

            <!-- Card Footer -->
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-3">
                    <!-- Due Date -->
                    <div 
                        v-if="card.due_date" 
                        class="flex items-center gap-1"
                        :class="{ 'text-red-600 dark:text-red-400 font-medium': isOverdue }"
                    >
                        <CalendarIcon class="w-4 h-4" />
                        <span>{{ daysUntilDue }}d</span>
                    </div>

                    <!-- Comments -->
                    <div v-if="card.comments_count > 0" class="flex items-center gap-1">
                        <ChatBubbleLeftIcon class="w-4 h-4" />
                        <span>{{ card.comments_count }}</span>
                    </div>

                    <!-- Attachments -->
                    <div v-if="card.google_drive_files_count > 0" class="flex items-center gap-1">
                        <PaperClipIcon class="w-4 h-4" />
                        <span>{{ card.google_drive_files_count }}</span>
                    </div>

                    <!-- YouTube Video -->
                    <div v-if="card.youtube_video" class="flex items-center gap-1 text-red-600 dark:text-red-400">
                        <VideoCameraIcon class="w-4 h-4" />
                    </div>
                </div>

                <!-- Assigned Users -->
                <div v-if="card.assigned_users && card.assigned_users.length > 0" class="flex -space-x-2">
                    <img
                        v-for="user in card.assigned_users.slice(0, 3)"
                        :key="user.id"
                        :src="user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
                        :alt="user.name"
                        :title="user.name"
                        class="w-6 h-6 rounded-full border-2 border-white dark:border-gray-700"
                    />
                    <div
                        v-if="card.assigned_users.length > 3"
                        class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-white dark:border-gray-700 flex items-center justify-center text-xs font-medium"
                    >
                        +{{ card.assigned_users.length - 3 }}
                    </div>
                </div>
            </div>

            <!-- AI Specific Indicators -->
            <div v-if="card.animation_prompts || card.music_notes" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 flex gap-2 text-xs">
                <span v-if="card.animation_prompts" class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded" title="Tiene prompts de animación">
                    🎨 IA
                </span>
                <span v-if="card.music_notes" class="px-2 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded" title="Tiene notas de música">
                    🎵 Música
                </span>
            </div>
        </div>
    </div>
</template>
