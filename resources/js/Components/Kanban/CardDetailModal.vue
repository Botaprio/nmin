<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { 
    XMarkIcon, 
    TrashIcon,
    PaperClipIcon,
    ChatBubbleLeftIcon,
    UserPlusIcon,
    CalendarIcon,
    TagIcon,
    SparklesIcon,
    MusicalNoteIcon,
    VideoCameraIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    card: Object,
    columnId: Number,
    board: Object,
});

const emit = defineEmits(['close']);

const isNewCard = computed(() => !props.card);

const form = useForm({
    column_id: props.columnId || props.card?.column_id,
    title: props.card?.title || '',
    description: props.card?.description || '',
    priority: props.card?.priority || 'medium',
    due_date: props.card?.due_date || '',
    start_date: props.card?.start_date || '',
    estimated_hours: props.card?.estimated_hours || '',
    video_idea: props.card?.video_idea || '',
    script_notes: props.card?.script_notes || '',
    animation_prompts: props.card?.animation_prompts || '',
    music_notes: props.card?.music_notes || '',
});

const activeTab = ref('details');

const tabs = [
    { id: 'details', label: 'Detalles', icon: CalendarIcon },
    { id: 'ai', label: 'IA & Video', icon: SparklesIcon },
    { id: 'comments', label: 'Comentarios', icon: ChatBubbleLeftIcon },
    { id: 'files', label: 'Archivos', icon: PaperClipIcon },
];

const saveCard = () => {
    if (isNewCard.value) {
        form.post(route('cards.store'), {
            onSuccess: () => emit('close'),
        });
    } else {
        form.put(route('cards.update', props.card.id), {
            onSuccess: () => emit('close'),
        });
    }
};

const deleteCard = () => {
    if (confirm('¿Estás seguro de eliminar esta tarjeta?')) {
        router.delete(route('cards.destroy', props.card.id), {
            onSuccess: () => emit('close'),
        });
    }
};

const commentForm = useForm({
    content: '',
});

const addComment = () => {
    commentForm.post(route('comments.store', props.card.id), {
        onSuccess: () => {
            commentForm.reset();
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Modal :show="true" @close="emit('close')" max-width="4xl">
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ isNewCard ? 'Nueva Tarjeta' : 'Detalle de Tarjeta' }}
                </h3>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <XMarkIcon class="w-6 h-6" />
                </button>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            activeTab === tab.id
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm'
                        ]"
                    >
                        <component :is="tab.icon" class="w-5 h-5 mr-2" />
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <form @submit.prevent="saveCard">
                <!-- Details Tab -->
                <div v-show="activeTab === 'details'" class="space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Título *
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            placeholder="Nombre del video..."
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            placeholder="Descripción del proyecto..."
                        ></textarea>
                    </div>

                    <!-- Priority and Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prioridad
                            </label>
                            <select
                                v-model="form.priority"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            >
                                <option value="low">Baja</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha inicio
                            </label>
                            <input
                                v-model="form.start_date"
                                type="date"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha límite
                            </label>
                            <input
                                v-model="form.due_date"
                                type="date"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            />
                        </div>
                    </div>

                    <!-- Estimated Hours -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Horas estimadas
                        </label>
                        <input
                            v-model="form.estimated_hours"
                            type="number"
                            min="0"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            placeholder="Ej: 8"
                        />
                    </div>
                </div>

                <!-- AI & Video Tab -->
                <div v-show="activeTab === 'ai'" class="space-y-4">
                    <!-- Video Idea -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            💡 Idea del Video
                        </label>
                        <textarea
                            v-model="form.video_idea"
                            rows="2"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            placeholder="Concepto principal del video..."
                        ></textarea>
                    </div>

                    <!-- Script Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📝 Notas del Guion
                        </label>
                        <textarea
                            v-model="form.script_notes"
                            rows="4"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            placeholder="Estructura del guion, puntos clave..."
                        ></textarea>
                    </div>

                    <!-- Animation Prompts -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🎨 Prompts de Animación (Kling / MidJourney)
                        </label>
                        <textarea
                            v-model="form.animation_prompts"
                            rows="4"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm font-mono text-sm"
                            placeholder="Prompts para generar animaciones con IA..."
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Escribe los prompts que usarás en Kling AI o MidJourney
                        </p>
                    </div>

                    <!-- Music Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🎵 Notas de Música (Suno)
                        </label>
                        <textarea
                            v-model="form.music_notes"
                            rows="3"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                            placeholder="Estilo musical, prompts para Suno, referencias..."
                        ></textarea>
                    </div>
                </div>

                <!-- Comments Tab -->
                <div v-show="activeTab === 'comments'" class="space-y-4">
                    <div v-if="!isNewCard">
                        <!-- Comment Form -->
                        <div class="mb-4">
                            <textarea
                                v-model="commentForm.content"
                                rows="3"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                placeholder="Escribe un comentario..."
                            ></textarea>
                            <button
                                @click="addComment"
                                type="button"
                                :disabled="!commentForm.content"
                                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                            >
                                Añadir Comentario
                            </button>
                        </div>

                        <!-- Comments List -->
                        <div v-if="card.comments && card.comments.length > 0" class="space-y-3">
                            <div
                                v-for="comment in card.comments"
                                :key="comment.id"
                                class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4"
                            >
                                <div class="flex items-start gap-3">
                                    <img
                                        :src="comment.user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}`"
                                        :alt="comment.user.name"
                                        class="w-8 h-8 rounded-full"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                                {{ comment.user.name }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ new Date(comment.created_at).toLocaleDateString() }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ comment.content }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No hay comentarios aún
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        Guarda la tarjeta para añadir comentarios
                    </div>
                </div>

                <!-- Files Tab -->
                <div v-show="activeTab === 'files'" class="space-y-4">
                    <div v-if="!isNewCard">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Los archivos se gestionan a través de Google Drive
                        </p>
                        <div v-if="card.google_drive_files && card.google_drive_files.length > 0" class="space-y-2">
                            <div
                                v-for="file in card.google_drive_files"
                                :key="file.id"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg"
                            >
                                <div class="flex items-center gap-3">
                                    <PaperClipIcon class="w-5 h-5 text-gray-400" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ file.name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ file.file_type }} • {{ (file.size / 1024 / 1024).toFixed(2) }} MB
                                        </p>
                                    </div>
                                </div>
                                <a
                                    :href="file.web_view_link"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                >
                                    Ver
                                </a>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No hay archivos adjuntos
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        Guarda la tarjeta para subir archivos
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        v-if="!isNewCard"
                        @click="deleteCard"
                        type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex items-center gap-2"
                    >
                        <TrashIcon class="w-4 h-4" />
                        Eliminar
                    </button>
                    <div v-else></div>
                    
                    <div class="flex gap-2">
                        <button
                            @click="emit('close')"
                            type="button"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition"
                        >
                            {{ isNewCard ? 'Crear Tarjeta' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </Modal>
</template>
