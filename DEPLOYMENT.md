# Guía de Despliegue en HostGator cPanel

Esta guía detalla el proceso completo para desplegar la aplicación YouTube Video Kanban en un hosting HostGator con cPanel y phpMyAdmin.

## 📋 Requisitos del Hosting

- **Plan HostGator**: Shared Hosting, Business, o VPS
- **PHP**: Versión 8.2 o superior
- **MySQL**: Versión 5.7 o superior
- **Espacio en disco**: Mínimo 500MB
- **Memoria PHP**: Mínimo 256MB (recomendado 512MB)
- **SSL**: Certificado SSL (Let's Encrypt gratis en cPanel)

## 🔧 Paso 1: Configuración Inicial en cPanel

### 1.1 Verificar Versión de PHP

1. Ingresa a tu cPanel
2. Ve a **"Select PHP Version"** o **"MultiPHP Manager"**
3. Selecciona **PHP 8.2** o superior
4. Habilita las siguientes extensiones:
   - `mbstring`
   - `xml`
   - `pdo_mysql`
   - `tokenizer`
   - `json`
   - `bcmath`
   - `ctype`
   - `fileinfo`
   - `openssl`
   - `zip`

### 1.2 Crear Base de Datos MySQL

1. En cPanel → **MySQL® Databases**
2. **Crear nueva base de datos:**
   - Nombre: `usuario_kanban` (ej: `miusuario_kanban`)
   - Click en **"Create Database"**

3. **Crear nuevo usuario:**
   - Username: `usuario_kanban_user`
   - Password: **Genera una contraseña segura** (guárdala)
   - Click en **"Create User"**

4. **Añadir usuario a la base de datos:**
   - Selecciona el usuario creado
   - Selecciona la base de datos creada
   - Click en **"Add"**
   - Marca **"ALL PRIVILEGES"**
   - Click en **"Make Changes"**

5. **Anota estos datos:**
   ```
   DB_DATABASE: usuario_kanban
   DB_USERNAME: usuario_kanban_user
   DB_PASSWORD: [tu-contraseña-segura]
   DB_HOST: localhost
   ```

## 📤 Paso 2: Subir Archivos al Servidor

### Opción A: Via File Manager (Más Fácil)

1. **Preparar archivos localmente:**
   ```bash
   # En tu computadora local
   composer install --optimize-autoloader --no-dev
   npm run build
   
   # Crear archivo ZIP (excluye node_modules y .git)
   # Windows PowerShell:
   Compress-Archive -Path * -DestinationPath kanban-app.zip -Force -Exclude node_modules,.git
   ```

2. **Subir a cPanel:**
   - Ve a **File Manager** en cPanel
   - Navega a `public_html` (o carpeta de tu subdominio)
   - Click en **"Upload"**
   - Sube el archivo `kanban-app.zip`
   - Click derecho en el archivo → **"Extract"**
   - Elimina el archivo ZIP después de extraer

### Opción B: Via FTP (FileZilla)

1. **Configurar FileZilla:**
   - Host: `ftp.tudominio.com`
   - Username: Tu usuario de cPanel
   - Password: Tu contraseña de cPanel
   - Port: 21

2. **Subir archivos:**
   - Conecta al servidor
   - Navega a `public_html`
   - Sube todos los archivos **excepto**:
     - `/node_modules/`
     - `/.git/`
     - `.env` (se creará manualmente)

### Opción C: Via SSH (Avanzado)

```bash
# Conectar via SSH
ssh usuario@tudominio.com

# Navegar a public_html
cd public_html

# Si ya tienes Git en el servidor
git clone https://github.com/tu-usuario/tu-repo.git .

# Instalar dependencias
php composer.phar install --optimize-autoloader --no-dev
```

## ⚙️ Paso 3: Configuración del Document Root

### 3.1 Cambiar Document Root

1. En cPanel → **"Domains"**
2. Click en el dominio que deseas configurar
3. En **"Document Root"**, cambiar a:
   - Si está en root: `/home/usuario/public_html/public`
   - Si está en subdirectorio: `/home/usuario/public_html/kanban/public`
4. Click en **"Submit"**

### 3.2 Configurar .htaccess en Root (Si es necesario)

Si no puedes cambiar el Document Root, crea un archivo `.htaccess` en la raíz:

**Archivo: `public_html/.htaccess`**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 📝 Paso 4: Configurar Archivo .env

### 4.1 Crear archivo .env

1. En **File Manager**, navega a la raíz de tu aplicación
2. Click derecho en espacio vacío → **"Create New File"**
3. Nombre: `.env`
4. Click derecho en `.env` → **"Edit"**

### 4.2 Contenido del archivo .env

```env
APP_NAME="YouTube Video Kanban"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=America/Santiago
APP_URL=https://tudominio.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Base de datos (usa los datos del Paso 1.2)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usuario_kanban
DB_USERNAME=usuario_kanban_user
DB_PASSWORD=tu-contraseña-segura

# Sesiones y caché
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

# Mail (configurar más tarde si es necesario)
MAIL_MAILER=log

# Google OAuth (configurar después)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://tudominio.com/auth/google/callback

# YouTube Data API v3
YOUTUBE_API_KEY=

# AI Services
KLING_API_KEY=
MIDJOURNEY_API_KEY=
SUNO_API_KEY=

# AI Credit Thresholds
AI_CREDIT_LOW_THRESHOLD=20

VITE_APP_NAME="${APP_NAME}"
```

## 🔑 Paso 5: Generar APP_KEY

### Via Terminal SSH
```bash
php artisan key:generate
```

### Via cPanel Terminal (Si está disponible)
1. En cPanel → **"Terminal"**
2. Navega a tu aplicación:
   ```bash
   cd public_html
   php artisan key:generate
   ```

### Manualmente (Si no tienes acceso a terminal)
1. En tu computadora local, ejecuta:
   ```bash
   php artisan key:generate --show
   ```
2. Copia la clave generada (ej: `base64:xxxxxxxxxxxx`)
3. Pégala en tu archivo `.env` en el servidor:
   ```env
   APP_KEY=base64:xxxxxxxxxxxx
   ```

## 🗄️ Paso 6: Ejecutar Migraciones de Base de Datos

### Via SSH/Terminal
```bash
cd public_html
php artisan migrate --force
```

### Via phpMyAdmin (Alternativa)
Si no tienes acceso SSH:

1. En tu computadora local, ejecuta:
   ```bash
   php artisan migrate
   php artisan schema:dump
   ```

2. Abre `database/schema/mysql-schema.sql`
3. En cPanel → **phpMyAdmin**
4. Selecciona tu base de datos
5. Ve a la pestaña **"SQL"**
6. Copia y pega el contenido de `mysql-schema.sql`
7. Click en **"Go"**

## 🔒 Paso 7: Establecer Permisos

### Via File Manager
1. Navega a la carpeta `storage`
2. Click derecho → **"Change Permissions"**
3. Establece: **755** (o marca Read, Write, Execute para Owner y Read, Execute para Group y Others)
4. Marca **"Recurse into subdirectories"**
5. Click en **"Change Permissions"**

6. Repite para `bootstrap/cache`

### Via SSH
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 🔗 Paso 8: Crear Enlace Simbólico para Storage

### Via SSH
```bash
php artisan storage:link
```

### Manualmente (Si no tienes SSH)
1. En File Manager, navega a `public/`
2. Crea un enlace simbólico llamado `storage` que apunte a `../storage/app/public`

## 🚀 Paso 9: Optimizar para Producción

### Via SSH
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Via cPanel Terminal
Ejecuta los mismos comandos uno por uno.

## 🔐 Paso 10: Configurar SSL (HTTPS)

1. En cPanel → **"SSL/TLS Status"**
2. Click en **"Run AutoSSL"** (certificado Let's Encrypt gratis)
3. Espera unos minutos hasta que se instale
4. Actualiza `APP_URL` en `.env` a `https://tudominio.com`
5. Verifica que Google OAuth redirect URI use `https://`

## 🧪 Paso 11: Verificar Instalación

1. Visita: `https://tudominio.com`
2. Deberías ver la página de bienvenida
3. Click en **"Login"** o **"Register"**
4. Crea una cuenta de prueba

### Checklist de Verificación

- [ ] Página principal carga sin errores
- [ ] Puedes registrar un usuario
- [ ] Puedes iniciar sesión
- [ ] Puedes crear un tablero
- [ ] Los estilos se cargan correctamente
- [ ] No hay errores 500 o 404

## 🔍 Solución de Problemas

### Error: "The stream or file could not be opened"
**Solución:**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Error: "No application encryption key"
**Solución:**
- Verifica que `APP_KEY` en `.env` tenga un valor
- Ejecuta `php artisan key:generate`

### Error: "SQLSTATE[HY000] [1045] Access denied"
**Solución:**
- Verifica credenciales de base de datos en `.env`
- Asegúrate que el usuario tenga privilegios

### Error: "419 Page Expired" al hacer login
**Solución:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
Luego, cierra sesión y vuelve a intentar.

### Archivos CSS/JS no cargan (404)
**Solución:**
- Verifica que Document Root apunte a `/public`
- Verifica que archivos existan en `public/build/`
- Ejecuta `npm run build` localmente y vuelve a subir archivos

## 🔄 Actualizar la Aplicación

### Proceso de Actualización

1. **Hacer backup:**
   ```bash
   # Backup de base de datos en phpMyAdmin
   # Backup de archivos via File Manager o FTP
   ```

2. **Subir nuevos archivos:**
   - Sube archivos actualizados via FTP/File Manager
   - No sobreescribas `.env`

3. **Ejecutar migraciones:**
   ```bash
   php artisan migrate --force
   ```

4. **Limpiar cachés:**
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

## 📊 Monitoreo y Mantenimiento

### Logs de Error
- Ubicación: `storage/logs/laravel.log`
- Revisar regularmente para detectar problemas

### Backup Automático
1. En cPanel → **"Backup"**
2. Configura backups automáticos
3. Descarga backups regularmente

### Optimización de Base de Datos
```bash
php artisan optimize:clear
php artisan db:show  # Ver estadísticas
```

## 📞 Soporte Adicional

Si encuentras problemas:

1. **Revisa logs:** `storage/logs/laravel.log`
2. **Contacta soporte HostGator:** Para problemas de servidor
3. **Documentación Laravel:** https://laravel.com/docs
4. **GitHub Issues:** Crea un issue en el repositorio

---

**¡Felicitaciones! Tu aplicación debería estar funcionando en producción** 🎉

Última actualización: Diciembre 2025
