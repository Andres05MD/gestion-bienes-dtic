# Sistema de Diseño: Dark Premium

Este documento describe los principios y componentes clave de la estética "Dark Premium" implementada en la aplicación.

## 1. Tipografía

-   **Fuente Principal**: `Outfit` (Google Fonts).
-   **Uso**: Se aplica globalmente a través de `app.css` y la configuración de Tailwind.
-   **Características**: Moderna, geométrica, de alta legibilidad en interfaces oscuras.

## 2. Paleta de Colores

### Fondos (Backgrounds)
| Token | Valor Hex | Uso |
| :--- | :--- | :--- |
| Main Body | `#050505` | Fondo principal de la aplicación. |
| Sidebar | `#0a0a0a` | Fondo del menú lateral. |
| Cards / Paneles | `#1e1b2e` | Tarjetas, paneles y contenedores. |
| Cards Alt | `#15121f` | Variación más oscura para anidación. |
| Inputs | `#1a1a1a` | Campos de formulario. |
| Input Focus | `#222222` | Campos de formulario en foco. |

### Acentos (Brand)
| Token | Valor Hex | Uso |
| :--- | :--- | :--- |
| Brand Purple | `#a855f7` | Color principal de marca, botones primarios. |
| Brand Lila | `#c084fc` | Hovers, enlaces, detalles secundarios. |
| Brand Neon | `#d8b4fe` | Brillos, glows y efectos de luz. |

### Texto
| Token | Valor Hex | Uso |
| :--- | :--- | :--- |
| Principal | `#ffffff` | Texto principal (blanco puro). |
| Secundario | `#9ba0b9` | Etiquetas, descripciones. |
| Muted | `#6b7280` | Placeholders, texto desactivado. |

### Badges de Estado
| Estado | Estilo |
| :--- | :--- |
| Bueno | `bg-emerald-500/10 text-emerald-400 border-emerald-500/20` |
| Malo | `bg-rose-500/10 text-rose-400 border-rose-500/20` |
| Regular | `bg-amber-500/10 text-amber-400 border-amber-500/20` |
| En Reparación | `bg-blue-500/10 text-blue-400 border-blue-500/20` |
| Desincorporado | `bg-gray-500/10 text-gray-400 border-gray-500/20` |
| Default | `bg-brand-purple/10 text-brand-lila border-brand-purple/20` |

## 3. Componentes UI

### Inputs (`<x-input-premium>`)
Componente estandarizado para formularios.
-   **Estilo**: Fondo sólido `#1a1a1a`, borde redondeado `2xl`, sin borde visible por defecto.
-   **Estado Focus**: Ring de color `brand-purple/20` y fondo ligeramente más claro (`#222`).
-   **Iconografía**: Iconos a la izquierda (usando `mary-icon`) que se iluminan al hacer foco.
-   **Password**: Incluye toggle de visibilidad integrado.

### Botones
-   **Primario**: Gradiente linear (`from-brand-purple to-indigo-600`), texto blanco, `rounded-2xl`, sombra al hover.
-   **Secundario**: Fondo transparente o borde sutil, texto `brand-lila`.
-   **Eliminar**: Fondo rojo con confirmación via modal.

### Tarjetas (Cards)
-   **Fondo**: `dark:bg-[#1e1b2e]` con bordes `border-white/5`.
-   **Sombra**: `shadow-lg` o glow `shadow-[0_0_20px_rgba(168,85,247,0.15)]` para elementos destacados.
-   **Efecto Glow**: Uso de "blobs" de luz (`blur-[100px]`) detrás de elementos clave.

### Modal de Eliminación
-   **Overlay**: Fondo negro semitransparente con backdrop-blur.
-   **Contenido**: Tarjeta centrada con estética "Dark Premium".
-   **Acción**: Botón rojo de confirmación y botón de cancelar.

### Badges / Etiquetas
-   **Estado del bien**: Con colores diferenciados según la tabla de badges de estado.
-   **Estatus de actas**: Colores configurables desde el CRUD de estatus de actas.

### Tablas
-   **Fondo de encabezado**: Ligeramente más oscuro que el cuerpo.
-   **Filas alternas**: Alternancia sutil para mejorar la legibilidad.
-   **Hover**: Efecto sutil al pasar el mouse sobre las filas.
-   **Búsqueda global**: Barra con icono a la derecha, integrada en el encabezado.

### Selector de Fecha Personalizado
-   **Estilo**: Acorde al tema "Dark Premium".
-   **Auto-flip**: El calendario se posiciona automáticamente arriba o abajo según el espacio disponible.
-   **Implementación**: Alpine.js para la lógica de posicionamiento dinámico.

## 4. Estructura de Layout

-   **Sidebar**: Fijo a la izquierda (escritorio), retráctil en móvil. Borde derecho sutil (`border-white/5`).
    -   Secciones: Dashboard, Usuarios, Inventario, Operaciones, Extras.
-   **Header**: Sticky top, fondo `backdrop-blur-md` para mantener contexto del scroll.
-   **Contenido**: Margen izquierdo `md:ml-64` para compensar el sidebar fijo.
-   **Scrollbar**: Personalizado Webkit, fino (8px), track oscuro `#15121f`, thumb `#2d2943` con hover `#a855f7`.
-   **Mobile**: Header fijo con botón hamburguesa, sidebar con transición deslizante.

## 5. Estructura del Sidebar

```
├── Dashboard
├── Usuarios (solo Admin)
├── ─── Inventario ───
│   ├── Bienes DTIC
│   └── Bienes Externos
├── ─── Operaciones ───
│   ├── Transferencias
│   ├── Desincorporaciones
│   └── Distribución
├── ─── Extras ───
│   ├── Áreas
│   ├── Estados
│   ├── Categorías de Bienes
│   ├── Deptos / Servicios
│   └── Estatus Actas
└── [Perfil de Usuario + Cerrar Sesión]
```

## 6. Gráficos (Chart.js)
-   **Fuente**: Configurada globalmente a `Outfit`.
-   **Colores**: Usan la paleta de la marca (Purple, Lila, Neon) para consistencia visual.
-   **Tipos**: Dona (distribución por estado), Barras (distribución por categoría), Estado de actas.
-   **Responsividad**: Los gráficos se ajustan al contenedor.

## 7. Fechas
-   **Formato de visualización**: `d M, Y` en español (ej: "15 Feb, 2026").
-   **Formato de inputs**: `Y-m-d` para compatibilidad con formularios.
-   **Método**: `translatedFormat()` de Carbon para localización en español.
