# YouTube Video Production Kanban Board

Aplicación Kanban completa construida con Laravel 11 y Vue.js 3 para gestionar el flujo de trabajo de producción de videos animados de YouTube con IA. Diseñada para equipos que crean videos con herramientas como Kling, MidJourney y Suno.

## 🎯 Características Principales

### 🎬 Flujo de Trabajo de Producción de Video
- **Columnas Optimizadas para IA**: Ideas → Script Writing → Pre-production → Animating → Editing → Review/Approval → Publishing → Published
- **Tableros Personalizables**: Crea múltiples tableros con configuraciones de columnas personalizadas
- **Drag & Drop**: Movimiento suave de tarjetas entre columnas con seguimiento de posición

### 👥 Colaboración Multi-Usuario
- **Asignaciones Basadas en Roles**: Owner, Editor, Reviewer, Viewer por tarjeta
- **Actualizaciones en Tiempo Real**: Seguimiento de actividad y notificaciones
- **Sistema de Comentarios**: Comentarios anidados para discusiones en equipo

### 🤖 Integración de Servicios IA
- **Seguimiento Manual de Créditos**: Rastrea créditos de Kling, MidJourney y Suno
- **Alertas de Créditos Bajos**: Notificaciones cuando los créditos caen por debajo del 20%
- **Historial de Uso**: Monitorea el consumo de créditos a lo largo del tiempo

### ☁️ Integración con Google
- **Google Drive**: Sube y gestiona assets de video, scripts, miniaturas
- **YouTube Analytics**: Conecta tu canal de YouTube vía OAuth 2.0
- **Seguimiento de Videos**: Monitorea vistas, likes, tiempo de visualización desde las tarjetas

### 📊 Analíticas y Reportes
- **Dashboard**: Vista general de tableros, próximos vencimientos, actividad
- **Gráficos Gantt**: Visualiza líneas de tiempo del proyecto
- **Logs de Actividad**: Registro completo de auditoría de todas las acciones

### 📱 Diseño Responsive
- **Mobile-First**: UI totalmente responsive con Tailwind CSS
- **Soporte para Tablets**: Optimizado para todos los tamaños de pantalla
- **Touch-Friendly**: Drag-and-drop funciona en dispositivos móviles

## 🚀 Stack Tecnológico

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Base de datos
- **Inertia.js** - Server-side rendering
- **Laravel Sanctum** - Autenticación API
- **Laravel Socialite** - Integración OAuth
- **Google API Client** - APIs de YouTube y Drive

### Frontend
- **Vue.js 3** - Framework JavaScript (Composition API)
- **Tailwind CSS** - CSS utility-first
- **Chart.js** - Gráficos
- **vue-draggable-next** - Drag and drop
- **@headlessui/vue** - Componentes UI
- **Heroicons** - Librería de iconos

## 📦 Instalación Local

### Requisitos Previos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Pasos de Instalación

1. **Clonar repositorio**
   ```bash
   git clone <your-repo-url>
   cd nmin
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias Node**
   ```bash
   npm install --legacy-peer-deps
   ```

4. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar base de datos** (editar `.env`)
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nmin_kanban
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Ejecutar migraciones**
   ```bash
   # Crear base de datos (si usas Laragon/MySQL)
   mysql -uroot -e "CREATE DATABASE nmin_kanban"
   
   # Ejecutar migraciones
   php artisan migrate
   ```

7. **Compilar assets**
   ```bash
   # Para producción
   npm run build
   
   # Para desarrollo con hot-reload
   npm run dev
   ```

8. **Iniciar servidor**
   ```bash
   php artisan serve
   ```
   
   Visita: `http://localhost:8000`

## 🔐 Configuración de Google OAuth

### 1. Crear Proyecto en Google Cloud

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto
3. Habilita las APIs:
   - YouTube Data API v3
   - Google Drive API

### 2. Configurar Pantalla de Consentimiento OAuth

1. Ve a "APIs y servicios" → "Pantalla de consentimiento de OAuth"
2. Selecciona "Externo" (para pruebas) o "Interno" (para organización)
3. Completa la información de la aplicación
4. Añade alcances (scopes):
   - `https://www.googleapis.com/auth/youtube.readonly`
   - `https://www.googleapis.com/auth/youtube.upload`
   - `https://www.googleapis.com/auth/drive.file`

### 3. Crear Credenciales OAuth

1. Ve a "APIs y servicios" → "Credenciales"
2. Click en "Crear credenciales" → "ID de cliente de OAuth"
3. Selecciona "Aplicación web"
4. Añade URIs de redireccionamiento autorizados:
   - Local: `http://localhost:8000/auth/google/callback`
   - Producción: `https://tudominio.com/auth/google/callback`
5. Copia el ID de Cliente y Secreto de Cliente

### 4. Actualizar archivo .env

```env
GOOGLE_CLIENT_ID=tu-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
YOUTUBE_API_KEY=tu-youtube-api-key
```

## 🌐 Despliegue en HostGator cPanel

### Requisitos Previos
- Hosting compartido HostGator o VPS con cPanel
- PHP 8.2 o superior
- Base de datos MySQL
- Acceso SSH (recomendado) o Administrador de Archivos

### Paso 1: Preparar Archivos

```bash
# Compilar assets para producción
npm run build

# Optimizar para producción
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 2: Crear Base de Datos

1. En cPanel → Bases de datos MySQL
2. Crear nueva base de datos (ej: `usuario_kanban`)
3. Crear nuevo usuario
4. Añadir usuario a la base de datos con TODOS LOS PRIVILEGIOS

### Paso 3: Subir Archivos

#### Via SSH (Recomendado)
```bash
ssh usuario@tudominio.com
cd public_html
git clone <tu-repo-url> .
```

#### Via Administrador de Archivos cPanel
1. Comprimir proyecto en ZIP
2. Subir a cPanel
3. Extraer en el directorio deseado

### Paso 4: Configurar Document Root

1. En cPanel → Dominios
2. Editar tu dominio
3. Establecer Document Root a `/public_html/public`

### Paso 5: Configurar .env

```env
APP_NAME="YouTube Video Kanban"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usuario_kanban
DB_USERNAME=usuario_dbuser
DB_PASSWORD=tu-password-db

GOOGLE_CLIENT_ID=tu-production-client-id
GOOGLE_CLIENT_SECRET=tu-production-client-secret
GOOGLE_REDIRECT_URI=https://tudominio.com/auth/google/callback
```

### Paso 6: Establecer Permisos

```bash
chmod -R 755 storage bootstrap/cache
php artisan storage:link
```

### Paso 7: Ejecutar Migraciones

```bash
php artisan migrate --force
```

### Paso 8: Optimizar

```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤖 Gestión de Créditos IA

### Entrada Manual

Dado que Kling, MidJourney y Suno no tienen APIs públicas gratuitas:

1. Ve a Dashboard → Créditos IA
2. Click en "Actualizar Créditos"
3. Ingresa información actual de créditos de cada servicio
4. Establece fechas del período de facturación
5. El sistema alertará cuando créditos < 20%

### Notas sobre APIs

- **Kling AI**: API disponible (pago)
- **MidJourney**: Sin API pública oficial
- **Suno AI**: Disponibilidad limitada

## 🐛 Solución de Problemas Comunes

### Error 500 Internal Server Error
- Verifica que `.htaccess` existe en carpeta `public/`
- Verifica permisos de archivos (755/644)
- Revisa logs de error en cPanel
- Asegura que la versión de PHP sea 8.2+

### Error de conexión a base de datos
- Verifica credenciales en `.env`
- Verifica que el host sea `localhost`
- Asegura que el usuario tenga privilegios correctos

### OAuth redirect mismatch
- Actualiza URIs en Google Cloud Console
- URL debe coincidir exactamente (http/https)
- Limpia caché del navegador

### Permisos denegados en Storage/Cache
```bash
chmod -R 755 storage bootstrap/cache
php artisan cache:clear
php artisan config:clear
```

## 📝 Estructura del Proyecto

```
nmin/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── BoardController.php
│   │       ├── CardController.php
│   │       ├── GoogleDriveController.php
│   │       ├── YoutubeController.php
│   │       └── ...
│   └── Models/
│       ├── Board.php
│       ├── Card.php
│       ├── AiCredit.php
│       └── ...
├── database/
│   └── migrations/
│       ├── create_boards_table.php
│       ├── create_cards_table.php
│       └── ...
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   ├── Layouts/
│   │   └── Pages/
│   └── views/
├── routes/
│   └── web.php
└── public/
    └── .htaccess
```

## 🎯 Roadmap

- [ ] Real-time collaboration con WebSockets
- [ ] Exportar reportes PDF/CSV
- [ ] Notificaciones por email
- [ ] Integración oficial API de Kling
- [ ] Gráficos Gantt avanzados
- [ ] App móvil React Native
- [ ] Publicación automatizada a YouTube
- [ ] Biblioteca de plantillas de workflows

## 📄 Licencia

Este proyecto es software de código abierto bajo licencia MIT.

## 🙋 Soporte

Para problemas, preguntas o contribuciones:
- Crea un issue en GitHub
- Contacto: dev@kanban.local

---

**Construido con ❤️ para creadores de YouTube que usan herramientas de animación IA**
