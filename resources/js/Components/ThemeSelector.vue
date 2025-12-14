<script setup>
import { ref, onMounted } from 'vue';

const emit = defineEmits(['themeChanged']);

const themes = [
    {
        id: 'glassmorphism',
        name: 'Glassmorphism',
        description: 'Estilo moderno con efectos de vidrio',
        icon: '✨'
    },
    {
        id: 'white-formal',
        name: 'Formal Claro',
        description: 'Colores muted en blanco',
        icon: '📄'
    },
    {
        id: 'dark-formal',
        name: 'Formal Oscuro',
        description: 'Alto contraste formal',
        icon: '🌙'
    }
];

const currentTheme = ref('glassmorphism');

const setTheme = (themeId) => {
    currentTheme.value = themeId;
    document.documentElement.setAttribute('data-theme', themeId);
    localStorage.setItem('nmin-theme', themeId);
    emit('themeChanged', themeId);
};

const loadTheme = () => {
    const savedTheme = localStorage.getItem('nmin-theme') || 'glassmorphism';
    setTheme(savedTheme);
};

onMounted(() => {
    loadTheme();
});
</script>

<template>
    <div class="flex items-center gap-1">
        <button
            v-for="theme in themes"
            :key="theme.id"
            @click="setTheme(theme.id)"
            :class="[
                'p-2 rounded-md text-lg transition-all duration-200',
                currentTheme === theme.id
                    ? 'bg-white/20 text-white shadow-sm'
                    : 'text-white/70 hover:text-white hover:bg-white/10'
            ]"
            :title="theme.description"
        >
            {{ theme.icon }}
        </button>
    </div>
</template>