<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Web Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Artisan Web Interface</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Crear Migración -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Crear Migración</h2>
                    <form id="migrationForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la migración</label>
                            <input type="text" id="migrationName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="create_users_table" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la tabla (opcional)</label>
                            <input type="text" id="migrationTable" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="users">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="migrationCreate" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="migrationCreate" class="ml-2 block text-sm text-gray-700">Crear tabla</label>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-primary">
                            Crear Migración
                        </button>
                    </form>
                </div>

                <!-- Crear Seeder -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Crear Seeder</h2>
                    <form id="seederForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del seeder</label>
                            <input type="text" id="seederName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="UserSeeder" required>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-primary">
                            Crear Seeder
                        </button>
                    </form>
                </div>

                <!-- Ejecutar Migraciones -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Ejecutar Migraciones</h2>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="migrateFresh" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="migrateFresh" class="ml-2 block text-sm text-gray-700">Migrar desde cero (fresh)</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="migrateSeed" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="migrateSeed" class="ml-2 block text-sm text-gray-700">Ejecutar seeders después</label>
                        </div>
                        <button id="runMigrationsBtn" class="w-full bg-success text-white py-2 px-4 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-success">
                            Ejecutar Migraciones
                        </button>
                    </div>
                </div>

                <!-- Ejecutar Seeders -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Ejecutar Seeders</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Clase específica (opcional)</label>
                            <input type="text" id="seederClass" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="UserSeeder">
                        </div>
                        <button id="runSeedersBtn" class="w-full bg-warning text-white py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-warning">
                            Ejecutar Seeders
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Migraciones -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Migraciones Ejecutadas</h2>
                    <button id="refreshMigrationsBtn" class="bg-secondary text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-secondary">
                        Actualizar
                    </button>
                </div>
                <div id="migrationsList" class="space-y-2">
                    <!-- Las migraciones se cargarán aquí -->
                </div>
            </div>

            <!-- Resultados -->
            <div id="results" class="mt-6 hidden">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Resultado</h3>
                    <pre id="resultOutput" class="bg-gray-100 p-4 rounded-md text-sm overflow-x-auto"></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funciones de utilidad
        function showResult(success, message, output = '') {
            const resultsDiv = document.getElementById('results');
            const outputPre = document.getElementById('resultOutput');

            resultsDiv.classList.remove('hidden');
            outputPre.className = success ? 'bg-green-100 p-4 rounded-md text-sm overflow-x-auto text-green-800' : 'bg-red-100 p-4 rounded-md text-sm overflow-x-auto text-red-800';
            outputPre.textContent = message + (output ? '\n\n' + output : '');

            // Scroll to results
            resultsDiv.scrollIntoView({ behavior: 'smooth' });
        }

        function hideResult() {
            document.getElementById('results').classList.add('hidden');
        }

        // Crear Migración
        document.getElementById('migrationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            hideResult();

            const formData = new FormData();
            formData.append('name', document.getElementById('migrationName').value);
            formData.append('table', document.getElementById('migrationTable').value);
            formData.append('create', document.getElementById('migrationCreate').checked);

            try {
                const response = await fetch('/artisan/make-migration', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showResult(true, data.message, data.output);
                    document.getElementById('migrationForm').reset();
                    loadMigrations();
                } else {
                    showResult(false, data.message);
                }
            } catch (error) {
                showResult(false, 'Error de conexión: ' + error.message);
            }
        });

        // Crear Seeder
        document.getElementById('seederForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            hideResult();

            const formData = new FormData();
            formData.append('name', document.getElementById('seederName').value);

            try {
                const response = await fetch('/artisan/make-seeder', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showResult(true, data.message, data.output);
                    document.getElementById('seederForm').reset();
                } else {
                    showResult(false, data.message);
                }
            } catch (error) {
                showResult(false, 'Error de conexión: ' + error.message);
            }
        });

        // Ejecutar Migraciones
        document.getElementById('runMigrationsBtn').addEventListener('click', async () => {
            hideResult();

            const formData = new FormData();
            formData.append('fresh', document.getElementById('migrateFresh').checked);
            formData.append('seed', document.getElementById('migrateSeed').checked);

            try {
                const response = await fetch('/artisan/run-migrations', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showResult(true, data.message, data.output);
                    loadMigrations();
                } else {
                    showResult(false, data.message);
                }
            } catch (error) {
                showResult(false, 'Error de conexión: ' + error.message);
            }
        });

        // Ejecutar Seeders
        document.getElementById('runSeedersBtn').addEventListener('click', async () => {
            hideResult();

            const formData = new FormData();
            const seederClass = document.getElementById('seederClass').value;
            if (seederClass) {
                formData.append('class', seederClass);
            }

            try {
                const response = await fetch('/artisan/run-seeders', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showResult(true, data.message, data.output);
                } else {
                    showResult(false, data.message);
                }
            } catch (error) {
                showResult(false, 'Error de conexión: ' + error.message);
            }
        });

        // Cargar y mostrar migraciones
        async function loadMigrations() {
            try {
                const response = await fetch('/artisan/list-migrations');
                const data = await response.json();

                const migrationsList = document.getElementById('migrationsList');

                if (data.success && data.migrations.length > 0) {
                    migrationsList.innerHTML = data.migrations.map(migration => `
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm font-mono">${migration.migration}</span>
                            <span class="text-xs text-gray-500">Batch: ${migration.batch}</span>
                        </div>
                    `).join('');
                } else {
                    migrationsList.innerHTML = '<p class="text-gray-500 text-sm">No hay migraciones ejecutadas</p>';
                }
            } catch (error) {
                console.error('Error loading migrations:', error);
            }
        }

        // Actualizar migraciones
        document.getElementById('refreshMigrationsBtn').addEventListener('click', loadMigrations);

        // Cargar migraciones al inicio
        loadMigrations();

        // Agregar CSRF token meta tag si no existe
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
</body>
</html>