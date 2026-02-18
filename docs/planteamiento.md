# Planteamiento del Proyecto: Sistema de Gestión de Bienes

## 1. Descripción General
El objetivo de este proyecto es desarrollar un sistema web integral para la gestión, control y seguimiento de los bienes de la Dirección de Tecnologías de Información y Comunicación (DTIC) de una institución hospitalaria, así como los bienes provenientes de otros departamentos y servicios del hospital (Bienes Externos). El sistema permite un registro detallado de cada activo, facilita las operaciones de transferencia, desincorporación y distribución de bienes, y ofrece reportes visuales para la toma de decisiones.

## 2. Objetivos Principales
- **Gestión de Activos (CRUD):** Módulo completo para Crear, Leer, Actualizar y Eliminar registros de bienes, diferenciando entre Bienes DTIC y Bienes Externos.
- **Operaciones sobre Bienes:** Módulo dedicado para gestionar Transferencias Internas, Desincorporaciones y Distribuciones de Dirección, con importación automática de datos desde las tablas de bienes.
- **Control de Acceso (Usuarios, Roles y Permisos):** Sistema basado en Spatie Laravel Permission que diferencia entre roles (Admin, Operador) con permisos granulares por módulo.
- **Dashboard Analítico:** Panel de control con KPIs, gráficos interactivos y actividad reciente que consolida datos de todos los módulos.
- **Experiencia de Usuario Premium:** Interfaz moderna con estética "Dark Premium" para reducir la fatiga visual y proyectar modernidad institucional.

## 3. Requerimientos Funcionales

### 3.1. Autenticación y Seguridad
- Inicio de sesión seguro mediante correo electrónico y contraseña.
- Registro de nuevas cuentas de usuario.
- Recuperación de contraseña.
- Control de sesión y cierre seguro.
- Sistema de roles (Admin / Operador) y permisos granulares por módulo.

### 3.2. Gestión de Bienes DTIC
- Registro detallado: Equipo, Marca, Modelo, Serial, Color, Número de Bien, Categoría (FK), Estado (FK), Observaciones, Área/Ubicación (FK).
- Listado con paginación, búsqueda global y filtros.
- Vista detallada, edición y eliminación.
- Registro del usuario que creó el bien.

### 3.3. Gestión de Bienes Externos
- Registro detallado: Equipo, Marca, Modelo, Serial, Color, Número de Bien, Categoría (FK), Estado (FK), Observaciones, Departamento (FK).
- Mismo flujo CRUD que Bienes DTIC, pero la ubicación se basa en Departamentos/Servicios en lugar de Áreas.
- Registro del usuario que creó el bien.

### 3.4. Operaciones
- **Transferencias Internas:** Registrar movimientos de bienes entre departamentos, con campos de Procedencia, Destino, Fecha, Estatus del acta y Fecha de firma.
- **Desincorporaciones:** Registrar bienes dados de baja, con campos de Procedencia, Fecha, Número de Informe (formato "00-00-00"), Estatus del acta y Observaciones.
- **Distribución de Dirección:** Registrar la distribución de bienes, con campos de Marca, Procedencia y Fecha.
- Todas las operaciones permiten importar bienes desde Bienes DTIC o Bienes Externos, detectando automáticamente la procedencia.

### 3.5. Módulos de Configuración (Extras)
- CRUD para Áreas (ubicaciones dentro de DTIC).
- CRUD para Estados (condición física del bien).
- CRUD para Categorías de Bienes (tipo de propiedad).
- CRUD para Departamentos / Servicios (unidades del hospital).
- CRUD para Estatus de Actas (estados de los documentos de operaciones).

### 3.6. Dashboard y Reportes
- KPIs: Total Bienes DTIC, Total Bienes Externos, Total General.
- KPIs de Operaciones: Transferencias, Desincorporaciones, Distribuciones.
- Gráficos interactivos: Distribución por estado, distribución por categoría, estado de actas.
- Actividad reciente: Últimos bienes registrados.

## 4. Stack Tecnológico
- **Backend:** PHP 8.4+ con **Laravel 12**.
- **Frontend Interactivo:** **Livewire v3** + **Alpine.js**.
- **Componentes UI:** **Flux UI** + **MaryUI** + **Blade Heroicons**.
- **Estilos:** **Tailwind CSS v4** con estética "Dark Premium".
- **Fuente:** Outfit (Google Fonts).
- **Permisos:** **Spatie Laravel Permission v7**.
- **Base de Datos:** MariaDB / MySQL.
- **Build Tool:** Vite.

## 5. Roles y Permisos
| Rol | Acceso |
| :--- | :--- |
| **Admin** | Acceso total a todas las secciones, incluyendo gestión de usuarios. |
| **Operador** | Acceso a todas las secciones **excepto** gestión de usuarios. |

Cada módulo tiene permisos granulares: `ver`, `crear`, `editar`, `eliminar`.
