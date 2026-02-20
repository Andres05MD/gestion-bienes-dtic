# Requerimientos Funcionales

## 1. Módulo de Autenticación y Seguridad

- [x] **Inicio de Sesión (Login):** Acceso seguro mediante correo electrónico y contraseña.
- [x] **Registro de Usuarios:** Creación de nuevas cuentas de usuario.
- [x] **Recuperación de Contraseña:** Flujo completo para restablecer contraseñas olvidadas.
- [x] **Control de Sesión:** Cierre de sesión seguro.
- [x] **Roles y Permisos:** Sistema basado en Spatie Laravel Permission con roles Admin y Operador.
- [x] **Permisos Granulares:** Permisos de `ver`, `crear`, `editar`, `eliminar` por cada módulo.
- [x] **Protección de Rutas:** Middleware `can:` aplicado a todas las rutas protegidas.

## 2. Gestión de Bienes DTIC

- [x] **Listado de Bienes:** Visualización tabular con paginación, búsqueda global y filtros.
- [x] **Detalle de Bienes:** Vista completa con diseño "Dark Premium" y badges de estado.
- [x] **Creación de Bienes:** Formulario detallado (Equipo, Marca, Modelo, Serial, Color, Número de Bien, Categoría, Estado, Observaciones, Área).
- [x] **Campos Opcionales:** Categoría y Número de Bien pueden omitirse, asignándose "PENDIENTE POR CATEGORÍA" y "S/N" respectivamente.
- [x] **Edición de Bienes:** Actualización de información con formulario pre-poblado.
- [x] **Eliminación de Bienes:** Eliminación permanente con modal de confirmación.
- [x] **Visualización en Listados:** Muestra la categoría al lado del número de bien en las tablas.
- [x] **Estandarización de Registros:** Los textos se guardan automáticamente en mayúsculas, y "S/B" se normaliza a "S/N" ignorando validaciones de unicidad redundantes.

## 3. Gestión de Bienes Externos

- [x] **Listado de Bienes Externos:** Visualización tabular con paginación, búsqueda global y filtros.
- [x] **Detalle de Bienes Externos:** Vista completa con diseño "Dark Premium".
- [x] **Creación de Bienes Externos:** Formulario con campos similares a Bienes DTIC, pero usando Departamento en lugar de Área.
- [x] **Edición de Bienes Externos:** Actualización de información.
- [x] **Eliminación de Bienes Externos:** Eliminación permanente con modal de confirmación.

## 4. Operaciones

### 4.1. Transferencias Internas

- [x] **Listado:** Tabla con N° Bien, Descripción, Serial, Destino/Procedencia, Fecha, Estatus, Fecha de Firma.
- [x] **CRUD Completo:** Crear, ver, editar y eliminar transferencias.
- [x] **Importación de Bienes:** Importar datos automáticamente desde Bienes DTIC o Bienes Externos.
- [x] **Detección de Procedencia:** Al importar, se detecta automáticamente si es DTIC o Departamento externo, asignando "DTIC" por defecto cuando aplica.
- [x] **Lógica de Transferencia Física:** Mueve el registro en base de datos entre `bienes` y `bienes_externos` según la naturaleza de la transferencia.
- [x] **Búsqueda y Filtros:** Búsqueda global y filtros por columna.

### 4.2. Desincorporaciones

- [x] **Listado:** Tabla con N° Bien, Descripción, Serial, Procedencia, Fecha, N° Informe, Estatus, Observaciones.
- [x] **CRUD Completo:** Crear, ver, editar y eliminar desincorporaciones.
- [x] **Número de Informe:** Formato estandarizado "00-00-00" (Opcional).
- [x] **Importación de Bienes:** Importar datos desde Bienes DTIC o Bienes Externos.
- [x] **Búsqueda y Filtros:** Búsqueda global y filtros por columna.

### 4.3. Distribución de Dirección

- [x] **Listado:** Tabla con N° Bien, Descripción, Marca, Serial, Procedencia, Fecha.
- [x] **CRUD Completo:** Crear, ver, editar y eliminar distribuciones.
- [x] **Importación de Bienes:** Importar datos desde Bienes DTIC o Bienes Externos.
- [x] **Búsqueda y Filtros:** Búsqueda global y filtros por columna.

## 5. Módulos de Configuración (Extras)

- [x] **Áreas:** CRUD para gestionar las áreas/ubicaciones dentro de DTIC.
- [x] **Estados:** CRUD para los estados físicos de los bienes (Bueno, Malo, Regular, En Reparación, Desincorporado).
- [x] **Categorías de Bienes:** CRUD para las categorías de propiedad (Bien Nacional, Bien Estadal, etc.).
- [x] **Departamentos / Servicios:** CRUD para las unidades organizativas del hospital.
- [x] **Estatus de Actas:** CRUD para los estados de los documentos de operaciones (Actas Listas, Acta Firmada falta Copia, Pendiente).

## 6. Dashboard y Reportes

- [x] **KPIs de Bienes:** Total Bienes DTIC, Total Bienes Externos, Total General.
- [x] **KPIs de Operaciones:** Total Transferencias, Total Desincorporaciones, Total Distribuciones.
- [x] **Tarjetas Interactivas:** Los KPIs y estados actúan como accesos directos para pre-filtrar el inventario.
- [x] **Gráfico de Estado de Bienes:** Distribución de bienes por estado (dona/chart).
- [x] **Gráfico de Categorías:** Distribución de bienes por categoría.
- [x] **Gráfico de Estado de Actas:** Combinación del estado de actas de Transferencias y Desincorporaciones.
- [x] **Actividad Reciente:** Últimos bienes registrados (DTIC + Externos).
- [x] **Fechas en Español:** Formato `d M, Y` usando `translatedFormat()`.

## 7. Gestión de Usuarios (Solo Admin)

- [x] **Listado de Usuarios:** Visualización con roles asignados.
- [x] **Creación de Usuarios:** Formulario con asignación de rol.
- [x] **Edición de Usuarios:** Actualización de datos y cambio de rol.
- [x] **Eliminación de Usuarios:** Eliminación con modal de confirmación.
- [x] **Protección:** Solo accesible para usuarios con rol Admin.

## 8. Interfaz de Usuario (UI/UX)

- [x] **Estilo "Dark Premium":** Interfaz oscura cohesiva con fondos #050505, #0a0a0a, #1e1b2e.
- [x] **Acentos Púrpura:** Paleta de marca con #a855f7, #c084fc, #d8b4fe.
- [x] **Fuente Outfit:** Tipografía moderna aplicada globalmente.
- [x] **Componentes Reactivos:** Livewire para interacciones dinámicas sin recarga.
- [x] **Selector de Fecha Personalizado:** Con auto-flip según espacio disponible en pantalla.
- [x] **Modal de Eliminación:** Modal de confirmación centrado con estética "Dark Premium".
- [x] **Búsqueda Global:** Barra de búsqueda con icono a la derecha en todas las secciones.
- [x] **Feedback Visual:** Notificaciones y estados de carga claros.
- [x] **Badges de Estado:** Colores diferenciados por estado del bien.
- [x] **Scrollbar Personalizado:** Estilizado con colores de marca.
