# Bienes Manager 🏥

Sistema integral de gestión de bienes hospitalarios con una estética moderna "Dark Premium". Este sistema permite el control detallado de inventario, transferencias, desincorporaciones y distribuciones de bienes.

---

## 🛠️ Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu sistema:

1.  **PHP 8.4 o superior** (Requerido por Laravel 12).
2.  **Composer** (Gestor de dependencias de PHP).
3.  **Node.js (v18+)** y **NPM** (Para compilar los assets del frontend).
4.  **Base de Datos**: MySQL, MariaDB o SQLite.
5.  **Git** (Para clonar el repositorio).

---

## 🚀 Guía de Instalación Paso a Paso

Sigue estos pasos detalladamente para poner en marcha el sistema en un nuevo computador:

### 1. Clonar el Repositorio
Abre tu terminal y descarga el proyecto:
```bash
git clone <url-del-repositorio>
cd Bienes_Manager/gestion-bienes
```

### 2. Instalar Dependencias de PHP
Descarga todas las librerías necesarias del framework:
```bash
composer install
```

### 3. Instalar Dependencias de Frontend
Instala los paquetes de Node.js necesarios para la interfaz visual:
```bash
npm install
```

### 4. Configurar el Archivo de Entorno
Crea una copia del archivo de ejemplo para configurar tu entorno local:
```bash
cp .env.example .env
```

Luego, genera la clave de seguridad de la aplicación:
```bash
php artisan key:generate
```

### 5. Configurar la Base de Datos
Abre el archivo `.env` recién creado con un editor de texto y busca la sección de base de datos.

**Para SQLite (Más simple para desarrollo):**
```env
DB_CONNECTION=sqlite
# Asegúrate de que las líneas de DB_HOST, DB_PORT, etc., estén comentadas con #
```
*Nota: Laravel creará automáticamente el archivo `database/database.sqlite` si no existe.*

**Para MySQL/MariaDB:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Ejecutar Migraciones y Seeders
Este paso crea las tablas necesarias y carga los datos iniciales (roles, permisos y usuario administrador):
```bash
php artisan migrate --seed
```

### 7. Compilar Assets de Estilo
Prepara la interfaz visual "Dark Premium":
```bash
npm run build
```

---

## 💻 Ejecución del Sistema

Para iniciar el sistema en tu entorno local, debes ejecutar dos comandos (puedes usar el comando `dev` incluido):

**Opción rápida (desarrollo):**
```bash
npm run dev
```
*Este comando iniciará tanto el servidor de PHP como el de Vite de forma concurrente.*

**Opción manual:**
1. Servidor PHP: `php artisan serve`
2. Compilador de Estilos: `npm run dev` (en otra terminal)

---

## 👤 Acceso al Sistema

Una vez instalado, puedes entrar con las siguientes credenciales por defecto:

- **URL**: `http://localhost:8000` (o la que indique la terminal)
- **Usuario**: `admin@hospital.com`
- **Contraseña**: `password`

### Crear un Administrador Personalizado
Si deseas crear un nuevo usuario administrador manualmente, utiliza el comando interactivo:
```bash
php artisan make:admin
```

---

## 🎨 Características del Diseño
El sistema utiliza un Tech Stack moderno:
- **Backend**: Laravel 12 + Livewire 3.
- **Frontend**: Flux UI + Mary UI + TailwindCSS 4.
- **Estilo**: Tema oscuro con acentos en gradientes púrpuras y lila.

---

## 📄 Notas adicionales
- Si encuentras problemas con los permisos en Linux, asegúrate de dar permisos de escritura a las carpetas `storage` y `bootstrap/cache`.
- Para ver los registros de actividad, el sistema utiliza `spatie/laravel-activitylog` integrado en el dashboard.

---
© 2026 Hospital - Gestión de Bienes.
