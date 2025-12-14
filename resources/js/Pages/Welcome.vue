<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome to NMIN" />
    
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-purple-900 via-blue-900 to-teal-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img 
                src="/images/hero-monkey.jpg" 
                alt="Creative Background" 
                class="w-full h-full object-cover opacity-30"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/70"></div>
        </div>

        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-teal-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Header with Auth Buttons -->
            <header class="w-full px-6 py-6">
                <nav class="max-w-7xl mx-auto flex justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl font-bold text-white">N</span>
                        </div>
                        <span class="text-2xl font-bold text-white tracking-tight">NMIN</span>
                    </div>

                    <!-- Auth Buttons -->
                    <div v-if="canLogin" class="flex items-center gap-4">
                        <Link
                            :href="route('login')"
                            class="px-6 py-2.5 text-white font-medium rounded-lg backdrop-blur-md bg-white/10 border border-white/20 hover:bg-white/20 transition-all duration-300 shadow-lg hover:shadow-xl"
                        >
                            Iniciar Sesión
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                        >
                            Registrarse
                        </Link>
                    </div>
                </nav>
            </header>

            <!-- Hero Section -->
            <main class="flex-1 flex items-center justify-center px-6">
                <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left space-y-8">
                        <!-- Glassmorphism Card -->
                        <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl p-10 shadow-2xl">
                            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 leading-tight">
                                Gestiona tus
                                <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 text-transparent bg-clip-text">
                                    Proyectos Creativos
                                </span>
                            </h1>
                            <p class="text-xl text-gray-200 mb-8 leading-relaxed">
                                Organiza tu workflow de producción de contenido con tableros Kanban inteligentes. 
                                Gestiona prompts de IA, edición, post-producción y más.
                            </p>
                            
                            <!-- Features -->
                            <div class="grid sm:grid-cols-2 gap-4 mb-8">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 rounded-full bg-purple-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Tableros Kanban</span>
                                </div>
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 rounded-full bg-blue-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Drag & Drop</span>
                                </div>
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 rounded-full bg-pink-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Gestión de Escenas</span>
                                </div>
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 rounded-full bg-teal-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">IA Integration</span>
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold text-lg rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 shadow-2xl hover:shadow-purple-500/50 transform hover:scale-105"
                                >
                                    Comenzar Gratis
                                </Link>
                                <Link
                                    v-if="canLogin"
                                    :href="route('login')"
                                    class="px-8 py-4 text-white font-bold text-lg rounded-xl backdrop-blur-md bg-white/10 border-2 border-white/30 hover:bg-white/20 transition-all duration-300 shadow-xl"
                                >
                                    Iniciar Sesión
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Right Content - Image Card -->
                    <div class="hidden lg:block">
                        <div class="backdrop-blur-xl bg-white/5 border border-white/20 rounded-3xl p-8 shadow-2xl transform hover:scale-105 transition-all duration-500">
                            <img 
                                src="/images/hero-monkey.jpg" 
                                alt="Creative Workspace" 
                                class="w-full h-auto rounded-2xl shadow-2xl"
                            />
                            <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                                <div class="backdrop-blur-md bg-white/10 rounded-xl p-4">
                                    <div class="text-3xl font-bold text-white">∞</div>
                                    <div class="text-sm text-gray-300 mt-1">Proyectos</div>
                                </div>
                                <div class="backdrop-blur-md bg-white/10 rounded-xl p-4">
                                    <div class="text-3xl font-bold text-white">✨</div>
                                    <div class="text-sm text-gray-300 mt-1">IA Powered</div>
                                </div>
                                <div class="backdrop-blur-md bg-white/10 rounded-xl p-4">
                                    <div class="text-3xl font-bold text-white">🚀</div>
                                    <div class="text-sm text-gray-300 mt-1">Productivo</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="w-full px-6 py-8">
                <div class="max-w-7xl mx-auto">
                    <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-2xl p-6">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
                            <div class="text-gray-300">
                                <p class="font-semibold text-white mb-1">NMIN - Content Production Manager</p>
                                <p class="text-sm">Gestiona tu flujo de trabajo creativo de manera eficiente</p>
                            </div>
                            <div class="flex items-center gap-6">
                                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 0.1;
        transform: scale(1);
    }
    50% {
        opacity: 0.2;
        transform: scale(1.05);
    }
}

.animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
