# Esquema de la Base de Datos

## Tabla: `users`

Usuarios del sistema con autenticación y roles.

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `name` | String | No | Nombre del usuario. |
| `email` | String (Unique) | No | Correo electrónico. |
| `password` | String | No | Contraseña encriptada. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

> Los roles y permisos se gestionan mediante las tablas de **Spatie Laravel Permission** (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`).

---

## Tabla: `bienes` (Bienes DTIC)

Almacena los bienes pertenecientes a la oficina de DTIC.

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `equipo` | String | No | Nombre del equipo (ej: Monitor, Escritorio, Laptop). |
| `marca` | String | Sí | Marca del equipo. |
| `modelo` | String | Sí | Modelo del equipo. |
| `serial` | String | Sí | Número de serie del fabricante. |
| `color` | String | Sí | Color del bien. |
| `numero_bien` | String (Unique) | No | Código de inventario interno. |
| `categoria_bien_id` | BigInt (FK) | Sí | Categoría del bien → `categoria_bienes`. |
| `estado_id` | BigInt (FK) | Sí | Estado físico del bien → `estados`. |
| `observaciones` | Text | Sí | Notas adicionales. |
| `area_id` | BigInt (FK) | Sí | Ubicación dentro de DTIC → `areas`. |
| `user_id` | BigInt (FK) | Sí | Usuario que registró el bien → `users`. |
| `created_at` | Timestamp | No | Fecha de creación del registro. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

---

## Tabla: `bienes_externos` (Bienes Externos)

Almacena los bienes provenientes de otros departamentos/servicios del hospital.

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `equipo` | String | No | Nombre del equipo. |
| `marca` | String | Sí | Marca del equipo. |
| `modelo` | String | Sí | Modelo del equipo. |
| `serial` | String | Sí | Número de serie. |
| `color` | String | Sí | Color del bien. |
| `numero_bien` | String (Unique) | No | Código de inventario interno. |
| `categoria_bien_id` | BigInt (FK) | No | Categoría del bien → `categoria_bienes`. |
| `estado_id` | BigInt (FK) | No | Estado físico → `estados`. |
| `observaciones` | Text | Sí | Notas adicionales. |
| `departamento_id` | BigInt (FK) | No | Departamento de procedencia → `departamentos`. |
| `user_id` | BigInt (FK) | No | Usuario que registró → `users`. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

---

## Tabla: `transferencias_internas`

Registra las transferencias de bienes entre departamentos/servicios.

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `numero_bien` | String | No | Número de bien transferido. |
| `descripcion` | String | No | Descripción del bien. |
| `serial` | String | Sí | Número de serie. |
| `procedencia_id` | BigInt (FK) | No | Departamento de origen → `departamentos`. |
| `destino_id` | BigInt (FK) | No | Departamento de destino → `departamentos`. |
| `fecha` | Date | No | Fecha de la transferencia. |
| `estatus` | String (Enum) | No | Estado del acta (Actas Listas / Acta Firmada falta Copia / Pendiente). |
| `fecha_firma` | Date | Sí | Fecha en que se firmó el acta. |
| `bien_id` | BigInt (FK) | Sí | Bien DTIC importado → `bienes`. |
| `bien_externo_id` | BigInt (FK) | Sí | Bien externo importado → `bienes_externos`. |
| `user_id` | BigInt (FK) | No | Usuario que registró → `users`. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

---

## Tabla: `desincorporaciones`

Registra los bienes que han sido desincorporados (dados de baja).

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `numero_bien` | String | No | Número de bien desincorporado. |
| `descripcion` | String | No | Descripción del bien. |
| `serial` | String | Sí | Número de serie. |
| `procedencia_id` | BigInt (FK) | No | Departamento de procedencia → `departamentos`. |
| `fecha` | Date | No | Fecha de la desincorporación. |
| `numero_informe` | String | No | Número de informe (formato: "00-00-00"). |
| `estatus` | String (Enum) | No | Estado del acta (Actas Listas / Acta Firmada falta Copia / Pendiente). |
| `observaciones` | Text | Sí | Notas adicionales. |
| `bien_id` | BigInt (FK) | Sí | Bien DTIC importado → `bienes`. |
| `bien_externo_id` | BigInt (FK) | Sí | Bien externo importado → `bienes_externos`. |
| `user_id` | BigInt (FK) | No | Usuario que registró → `users`. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

---

## Tabla: `distribuciones_direccion`

Registra las distribuciones de bienes realizadas por la Dirección.

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `numero_bien` | String | No | Número de bien distribuido. |
| `descripcion` | String | No | Descripción del bien. |
| `marca` | String | Sí | Marca del bien. |
| `serial` | String | Sí | Número de serie. |
| `procedencia_id` | BigInt (FK) | No | Departamento de procedencia → `departamentos`. |
| `fecha` | Date | No | Fecha de la distribución. |
| `bien_id` | BigInt (FK) | Sí | Bien DTIC importado → `bienes`. |
| `bien_externo_id` | BigInt (FK) | Sí | Bien externo importado → `bienes_externos`. |
| `user_id` | BigInt (FK) | No | Usuario que registró → `users`. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

---

## Tablas de Configuración

### `categoria_bienes`

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `nombre` | String | No | Nombre de la categoría. |
| `descripcion` | Text | Sí | Descripción de la categoría. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

> Valores iniciales: Bien Nacional, Bien Estadal, Bien de Terceros, Comodato.

### `areas`

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `nombre` | String | No | Nombre del área. |
| `descripcion` | Text | Sí | Descripción del área. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

> Valores iniciales: Jefatura, Asistente, Programación, Soporte Técnico, Carnetización, Secretaría, Cuarto de Servidores, Central Telefónica, Área Común.

### `estados`

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `nombre` | String | No | Nombre del estado. |
| `descripcion` | Text | Sí | Descripción del estado. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

> Valores iniciales: Bueno, Malo, Regular, En Reparación, Desincorporado.

### `departamentos`

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `nombre` | String | No | Nombre del departamento/servicio. |
| `descripcion` | Text | Sí | Descripción del departamento. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

> Representan los departamentos y servicios del hospital.

### `estatus_actas`

| Campo | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (PK) | No | Identificador único. |
| `nombre` | String (Unique) | No | Nombre del estatus. |
| `color` | String | Sí | Color asociado para badges. |
| `created_at` | Timestamp | No | Fecha de creación. |
| `updated_at` | Timestamp | No | Fecha de última actualización. |

> Valores iniciales: Actas Listas, Acta Firmada falta Copia, Pendiente.

---

## Diagrama de Relaciones

```
users ─────────────┬─── bienes (user_id)
                   ├─── bienes_externos (user_id)
                   ├─── transferencias_internas (user_id)
                   ├─── desincorporaciones (user_id)
                   └─── distribuciones_direccion (user_id)

categoria_bienes ──┬─── bienes (categoria_bien_id)
                   └─── bienes_externos (categoria_bien_id)

estados ───────────┬─── bienes (estado_id)
                   └─── bienes_externos (estado_id)

areas ─────────────── bienes (area_id)

departamentos ─────┬─── bienes_externos (departamento_id)
                   ├─── transferencias_internas (procedencia_id, destino_id)
                   ├─── desincorporaciones (procedencia_id)
                   └─── distribuciones_direccion (procedencia_id)

bienes ────────────┬─── transferencias_internas (bien_id)
                   ├─── desincorporaciones (bien_id)
                   └─── distribuciones_direccion (bien_id)

bienes_externos ───┬─── transferencias_internas (bien_externo_id)
                   ├─── desincorporaciones (bien_externo_id)
                   └─── distribuciones_direccion (bien_externo_id)
```
