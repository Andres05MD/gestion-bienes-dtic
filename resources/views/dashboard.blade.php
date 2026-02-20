<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-brand-purple/20 rounded-2xl shadow-inner border border-brand-purple/20">
                    <x-mary-icon name="o-presentation-chart-line" class="w-10 h-10 text-brand-lila drop-shadow-sm" />
                </div>
                <div>
                    <h2 class="font-black text-4xl text-gray-900 dark:text-white leading-tight tracking-tighter">
                        {{ __('Pantalla Principal') }}
                    </h2>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-[0.2em] mt-1.5 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-neon animate-pulse"></span>
                        Monitoreo de Activos Tiempo Real
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Filtro de período --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-dark-850 hover:bg-gray-50 dark:hover:bg-dark-800 rounded-xl border border-gray-200 dark:border-white/10 transition-all text-sm font-bold text-gray-600 dark:text-gray-300">
                        <x-mary-icon name="o-calendar-days" class="w-4 h-4 text-brand-lila" />
                        <span>
                            @switch($periodoActual)
                            @case('hoy') Hoy @break
                            @case('semana') Última semana @break
                            @case('mes') Último mes @break
                            @case('trimestre') Último trimestre @break
                            @default Todo el tiempo
                            @endswitch
                        </span>
                        <x-mary-icon name="o-chevron-down" class="w-3 h-3 transition-transform" ::class="open && 'rotate-180'" />
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 top-full mt-2 w-52 bg-white dark:bg-dark-850 rounded-2xl border border-gray-200 dark:border-white/10 shadow-2xl shadow-black/20 z-50 overflow-hidden py-2">
                        @foreach([
                        ['all', 'Todo el tiempo', 'o-clock'],
                        ['hoy', 'Hoy', 'o-sun'],
                        ['semana', 'Última semana', 'o-calendar'],
                        ['mes', 'Último mes', 'o-calendar-days'],
                        ['trimestre', 'Último trimestre', 'o-chart-bar'],
                        ] as [$val, $label, $icon])
                        <a href="{{ route('dashboard', ['periodo' => $val]) }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold transition-all {{ $periodoActual === $val ? 'bg-brand-purple/10 text-brand-lila' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                            <x-mary-icon name="{{ $icon }}" class="w-4 h-4" />
                            {{ $label }}
                            @if($periodoActual === $val)
                            <x-mary-icon name="o-check" class="w-4 h-4 ml-auto text-brand-neon" />
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="hidden sm:flex flex-col items-end mr-2">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ now()->translatedFormat('l') }}</span>
                    <span class="text-lg font-black text-gray-900 dark:text-white">{{ now()->format('d') }} {{ Str::upper(substr(str_replace('.', '', now()->translatedFormat('M')), 0, 3)) }}, {{ now()->format('Y') }}</span>
                </div>
                <div class="h-10 w-px bg-gray-200 dark:bg-white/10 hidden sm:block mx-2"></div>
                <button onclick="window.location.reload()" class="p-3 bg-white dark:bg-dark-850 hover:bg-gray-50 dark:hover:bg-dark-800 rounded-xl border border-gray-200 dark:border-white/5 transition-all group">
                    <x-mary-icon name="o-arrow-path" class="w-5 h-5 text-gray-400 group-hover:text-brand-lila group-hover:rotate-180 transition-all duration-700" />
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 relative min-h-screen overflow-hidden">
        <!-- background decorative glows -->
        <div class="absolute top-0 right-0 -mr-64 -mt-64 w-[600px] h-[600px] bg-brand-purple/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-64 -mb-64 w-[600px] h-[600px] bg-brand-lila/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">

            {{-- Acciones Rápidas --}}
            <section class="flex flex-wrap gap-3">
                @can('ver transferencias')
                <a href="{{ route('transferencias-internas.create') }}" class="group flex items-center gap-3 px-5 py-3 bg-white dark:bg-dark-850 hover:bg-blue-500/5 dark:hover:bg-blue-500/10 rounded-2xl border border-gray-200 dark:border-white/5 hover:border-blue-500/30 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-blue-500/5">
                    <div class="p-2 bg-blue-500/10 rounded-xl group-hover:bg-blue-500/20 transition-colors">
                        <x-mary-icon name="o-arrows-right-left" class="w-4 h-4 text-blue-500" />
                    </div>
                    <span class="text-xs font-black text-gray-600 dark:text-gray-300 uppercase tracking-wider group-hover:text-blue-500 transition-colors">Nueva Transferencia</span>
                </a>
                @endcan
                @can('ver desincorporaciones')
                <a href="{{ route('desincorporaciones.create') }}" class="group flex items-center gap-3 px-5 py-3 bg-white dark:bg-dark-850 hover:bg-rose-500/5 dark:hover:bg-rose-500/10 rounded-2xl border border-gray-200 dark:border-white/5 hover:border-rose-500/30 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-rose-500/5">
                    <div class="p-2 bg-rose-500/10 rounded-xl group-hover:bg-rose-500/20 transition-colors">
                        <x-mary-icon name="o-archive-box-x-mark" class="w-4 h-4 text-rose-500" />
                    </div>
                    <span class="text-xs font-black text-gray-600 dark:text-gray-300 uppercase tracking-wider group-hover:text-rose-500 transition-colors">Nueva Desincorporación</span>
                </a>
                @endcan
                @can('ver distribuciones')
                <a href="{{ route('distribuciones-direccion.create') }}" class="group flex items-center gap-3 px-5 py-3 bg-white dark:bg-dark-850 hover:bg-purple-500/5 dark:hover:bg-purple-500/10 rounded-2xl border border-gray-200 dark:border-white/5 hover:border-brand-purple/30 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-brand-purple/5">
                    <div class="p-2 bg-brand-purple/10 rounded-xl group-hover:bg-brand-purple/20 transition-colors">
                        <x-mary-icon name="o-truck" class="w-4 h-4 text-brand-lila" />
                    </div>
                    <span class="text-xs font-black text-gray-600 dark:text-gray-300 uppercase tracking-wider group-hover:text-brand-lila transition-colors">Nueva Distribución</span>
                </a>
                @endcan
                @can('ver bienes')
                <a href="{{ route('bienes.create') }}" class="group flex items-center gap-3 px-5 py-3 bg-white dark:bg-dark-850 hover:bg-emerald-500/5 dark:hover:bg-emerald-500/10 rounded-2xl border border-gray-200 dark:border-white/5 hover:border-emerald-500/30 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-emerald-500/5">
                    <div class="p-2 bg-emerald-500/10 rounded-xl group-hover:bg-emerald-500/20 transition-colors">
                        <x-mary-icon name="o-plus-circle" class="w-4 h-4 text-emerald-500" />
                    </div>
                    <span class="text-xs font-black text-gray-600 dark:text-gray-300 uppercase tracking-wider group-hover:text-emerald-500 transition-colors">Registrar Bien</span>
                </a>
                @endcan
            </section>

            <!-- 1. ALERTAS CRÍTICAS (Solo si hay pendientes) -->
            @if($operacionesPendientes->count() > 0)
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1.5 h-5 bg-amber-500 rounded-full"></div>
                    <h3 class="text-xs font-black text-amber-500 uppercase tracking-[0.3em]">Atención Requerida</h3>
                    <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 rounded-full text-[10px] font-bold">{{ $operacionesPendientes->count() }}</span>
                </div>

                <div class="bg-white/40 dark:bg-dark-850/40 backdrop-blur-xl rounded-3xl border border-amber-500/20 overflow-hidden shadow-2xl shadow-amber-500/5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-amber-500/5 text-xs uppercase text-amber-600/70 dark:text-amber-400/50 font-black tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Prioridad</th>
                                    <th class="px-6 py-4">Operación</th>
                                    <th class="px-6 py-4">Bien</th>
                                    <th class="px-6 py-4">Ubicación</th>
                                    <th class="px-6 py-4">Días</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach($operacionesPendientes->take(3) as $op)
                                <tr class="group hover:bg-amber-500/5 transition-colors duration-300">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($op->nivel_urgencia === 'critico')
                                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                            <span class="text-xs font-black text-rose-500 uppercase">Crítico</span>
                                            @else
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span class="text-xs font-black text-amber-500 uppercase">Pendiente</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                            {{ $op->tipo_operacion }}
                                        </div>
                                        <div class="text-xs text-gray-500 font-bold uppercase">{{ $op->estatusActa?->nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ $op->tipo_operacion === 'Desincorporación' ? route('desincorporaciones.index', ['buscar' => $op->numero_bien]) : route('transferencias-internas.index', ['buscar' => $op->numero_bien]) }}"
                                            class="group block hover:opacity-75 transition-opacity">
                                            <div class="text-base font-black text-gray-900 dark:text-white group-hover:text-brand-purple transition-colors">{{ $op->numero_bien }}</div>
                                            <div class="text-xs text-gray-500 font-bold truncate max-w-[150px] uppercase">{{ $op->nombre_bien }}</div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 min-w-[200px]">
                                        @if($op->tipo_operacion === 'Transferencia')
                                        <div class="flex flex-wrap items-center gap-1.5 text-xs font-black uppercase whitespace-normal leading-relaxed">
                                            <span class="text-gray-700 dark:text-gray-300">{{ $op->procedencia?->nombre ?? 'DTIC' }}</span>
                                            <x-mary-icon name="o-arrow-right" class="w-3 h-3 text-brand-lila shrink-0" />
                                            <span class="text-brand-lila">{{ $op->destino?->nombre ?? 'DTIC' }}</span>
                                        </div>
                                        @else
                                        <div class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase whitespace-normal leading-relaxed">
                                            {{ $op->procedencia?->nombre ?? 'DTIC' }}
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-black {{ $op->nivel_urgencia === 'critico' ? 'text-rose-500' : 'text-amber-500' }}">
                                            {{ $op->dias_transcurridos }}d
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                        <a href="{{ $op->tipo_operacion === 'Desincorporación' ? route('desincorporaciones.index', ['buscar' => $op->numero_bien]) : route('transferencias-internas.index', ['buscar' => $op->numero_bien]) }}"
                                            class="p-2 bg-gray-100 dark:bg-white/5 hover:bg-sky-500 hover:text-white rounded-lg transition-all inline-block"
                                            title="Ver en listado general">
                                            <x-mary-icon name="o-list-bullet" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ $op->tipo_operacion === 'Desincorporación' ? route('desincorporaciones.show', $op) : route('transferencias-internas.show', $op) }}"
                                            class="p-2 bg-gray-100 dark:bg-white/5 hover:bg-brand-purple hover:text-white rounded-lg transition-all inline-block"
                                            title="Ver detalle del acta">
                                            <x-mary-icon name="o-eye" class="w-4 h-4" />
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            @endif

            <!-- 2. KPIs PRINCIPALES -->
            <section>
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-1.5 h-5 bg-brand-purple rounded-full"></div>
                    <h3 class="text-xs font-black text-brand-lila uppercase tracking-[0.3em]">Resumen de Inventario</h3>
                    <div class="flex-1 h-px bg-linear-to-r from-brand-purple/20 to-transparent"></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Card Inventario Total (Destacada) -->
                    <a href="{{ route('bienes.index') }}" class="col-span-2 md:col-span-1 lg:col-span-2 bg-linear-to-br from-brand-purple to-purple-700 rounded-3xl p-6 shadow-xl shadow-brand-purple/20 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
                        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                            <x-mary-icon name="o-rectangle-group" class="w-36 h-36 text-white" />
                        </div>
                        <p class="text-[10px] font-black text-white/60 uppercase tracking-[0.2em] mb-1">Inventario Total</p>
                        <h3 class="text-5xl font-black text-white tracking-tighter leading-none">{{ number_format($totalBienes) }}</h3>
                        <div class="mt-4 flex items-center gap-2 flex-wrap">
                            <span class="text-[9px] font-bold text-white/70 uppercase tracking-widest px-2.5 py-1 bg-white/15 rounded-full backdrop-blur-sm">
                                Resumen Global
                            </span>
                        </div>
                    </a>

                    <!-- Card Bienes DTIC -->
                    <a href="{{ route('bienes.index') }}" class="col-span-2 md:col-span-1 lg:col-span-2 bg-linear-to-br from-blue-600 to-blue-800 rounded-3xl p-6 shadow-xl shadow-blue-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
                        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                            <x-mary-icon name="o-cpu-chip" class="w-36 h-36 text-white" />
                        </div>
                        <p class="text-[10px] font-black text-white/60 uppercase tracking-[0.2em] mb-1">Bienes DTIC</p>
                        <h3 class="text-5xl font-black text-white tracking-tighter leading-none">{{ number_format($totalBienesDTIC) }}</h3>
                        <div class="mt-4 flex items-center gap-2 flex-wrap">
                            <span class="text-[9px] font-bold text-white/70 uppercase tracking-widest px-2.5 py-1 bg-white/15 rounded-full backdrop-blur-sm">
                                Activos Internos
                            </span>
                        </div>
                    </a>

                    <!-- Card Bienes Externos -->
                    <a href="{{ route('bienes-externos.index') }}" class="col-span-2 md:col-span-1 lg:col-span-2 bg-linear-to-br from-indigo-600 to-indigo-800 rounded-3xl p-6 shadow-xl shadow-indigo-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
                        <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                            <x-mary-icon name="o-building-office" class="w-36 h-36 text-white" />
                        </div>
                        <p class="text-[10px] font-black text-white/60 uppercase tracking-[0.2em] mb-1">Bienes Externos</p>
                        <h3 class="text-5xl font-black text-white tracking-tighter leading-none">{{ number_format($totalBienesExternos) }}</h3>
                        <div class="mt-4 flex items-center gap-2 flex-wrap">
                            <span class="text-[9px] font-bold text-white/70 uppercase tracking-widest px-2.5 py-1 bg-white/15 rounded-full backdrop-blur-sm">
                                Activos Fuera de DTIC
                            </span>
                        </div>
                    </a>

                    @php
                    // Mapa de colores hardcodeados para evitar problemas de purge en Tailwind
                    $estadoStyles = [
                    'Bueno' => [
                    'icon' => 'o-check-circle',
                    'bg' => 'bg-emerald-500/10',
                    'text' => 'text-emerald-500',
                    'border' => 'border-emerald-500/20',
                    'iconBg' => 'bg-emerald-500/15',
                    ],
                    'Regular' => [
                    'icon' => 'o-exclamation-circle',
                    'bg' => 'bg-amber-500/10',
                    'text' => 'text-amber-500',
                    'border' => 'border-amber-500/20',
                    'iconBg' => 'bg-amber-500/15',
                    ],
                    'Malo' => [
                    'icon' => 'o-x-circle',
                    'bg' => 'bg-rose-500/10',
                    'text' => 'text-rose-500',
                    'border' => 'border-rose-500/20',
                    'iconBg' => 'bg-rose-500/15',
                    ],
                    'En Reparación' => [
                    'icon' => 'o-wrench-screwdriver',
                    'bg' => 'bg-blue-500/10',
                    'text' => 'text-blue-500',
                    'border' => 'border-blue-500/20',
                    'iconBg' => 'bg-blue-500/15',
                    ],
                    'Desincorporado' => [
                    'icon' => 'o-trash',
                    'bg' => 'bg-gray-500/10',
                    'text' => 'text-gray-500',
                    'border' => 'border-gray-500/20',
                    'iconBg' => 'bg-gray-500/15',
                    ],
                    ];
                    $defaultStyle = [
                    'icon' => 'o-question-mark-circle',
                    'bg' => 'bg-brand-purple/10',
                    'text' => 'text-brand-lila',
                    'border' => 'border-brand-purple/20',
                    'iconBg' => 'bg-brand-purple/15',
                    ];
                    @endphp

                    <!-- Cards de Estado Dinámicas (desde $porEstado del controller) -->
                    @foreach($porEstado as $estadoData)
                    @php
                    $estadoNombre = is_array($estadoData) ? $estadoData['estado'] : $estadoData->estado;
                    $estadoCount = is_array($estadoData) ? $estadoData['count'] : $estadoData->count;
                    $style = $estadoStyles[$estadoNombre] ?? $defaultStyle;
                    $porcentaje = $totalBienes > 0 ? round(($estadoCount / $totalBienes) * 100, 1) : 0;
                    @endphp
                    @php
                    $estadoId = \App\Models\Estado::where('nombre', $estadoNombre)->first()?->id;
                    @endphp
                    <a href="{{ route('bienes.index', ['estado_id' => $estadoId]) }}" class="bg-white dark:bg-dark-850 p-5 rounded-3xl border {{ $style['border'] }} shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex items-start justify-between mb-3">
                            <div class="p-2.5 {{ $style['iconBg'] }} rounded-xl {{ $style['text'] }}">
                                <x-mary-icon name="{{ $style['icon'] }}" class="w-5 h-5" />
                            </div>
                            <span class="text-[10px] font-bold {{ $style['text'] }} px-2 py-0.5 {{ $style['bg'] }} rounded-full">
                                {{ $porcentaje }}%
                            </span>
                        </div>
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">{{ $estadoNombre }}</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white tabular-nums tracking-tighter leading-none">{{ $estadoCount }}</h3>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- 3. GESTIÓN Y SALUD (Charts & Operations) -->
            <section>
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-1.5 h-5 bg-brand-neon rounded-full"></div>
                    <h3 class="text-xs font-black text-brand-neon uppercase tracking-[0.3em]">Análisis & Gestión Operativa</h3>
                    <div class="flex-1 h-px bg-linear-to-r from-brand-neon/20 to-transparent"></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- Salud del Inventario (Donut) -->
                    <div class="lg:col-span-5 bg-white dark:bg-dark-850 p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm flex flex-col">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Salud del Inventario</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Estado de Activos</p>
                            </div>
                            <div class="p-2 bg-brand-neon/10 rounded-xl text-brand-neon">
                                <x-mary-icon name="o-heart" class="w-5 h-5 shadow-[0_0_15px_rgba(216,180,254,0.4)]" />
                            </div>
                        </div>
                        <div class="relative flex-1 min-h-[300px] flex items-center justify-center">
                            <canvas id="estadoChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-4xl font-black text-gray-900 dark:text-white">{{ number_format($totalBienes) }}</span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total</span>
                            </div>
                        </div>
                        <!-- Leyenda del donut -->
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            @foreach($porEstado as $estadoData)
                            @php
                            $nombre = is_array($estadoData) ? $estadoData['estado'] : $estadoData->estado;
                            $cnt = is_array($estadoData) ? $estadoData['count'] : $estadoData->count;
                            $s = $estadoStyles[$nombre] ?? $defaultStyle;
                            @endphp
                            <div class="flex items-center gap-2 px-2 py-1">
                                <div class="w-2.5 h-2.5 rounded-full {{ $s['bg'] }} {{ $s['border'] }} border"></div>
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">{{ $nombre }}</span>
                                <span class="text-[10px] font-black text-gray-900 dark:text-white ml-auto">{{ $cnt }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Gestión Operativa (Vertical Stats & Quick Links) -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Card Desincorporaciones -->
                            <div class="bg-linear-to-br from-rose-500/5 to-rose-600/10 dark:from-rose-500/10 dark:to-transparent p-6 rounded-3xl border border-rose-500/20 group hover:border-rose-500/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-rose-500/5">
                                <div class="p-3 bg-rose-500/20 text-rose-500 rounded-2xl w-fit mb-4">
                                    <x-mary-icon name="o-archive-box-x-mark" class="w-6 h-6" />
                                </div>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-1 tabular-nums">{{ number_format($totalDesincorporaciones) }}</h4>
                                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Desincorporaciones</p>
                            </div>

                            <!-- Card Transferencias -->
                            <div class="bg-linear-to-br from-blue-500/5 to-blue-600/10 dark:from-blue-500/10 dark:to-transparent p-6 rounded-3xl border border-blue-500/20 group hover:border-blue-500/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/5">
                                <div class="p-3 bg-blue-500/20 text-blue-500 rounded-2xl w-fit mb-4">
                                    <x-mary-icon name="o-arrows-right-left" class="w-6 h-6" />
                                </div>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-1 tabular-nums">{{ number_format($totalTransferencias) }}</h4>
                                <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Transferencias</p>
                            </div>

                            <!-- Card Distribuciones -->
                            <div class="bg-linear-to-br from-brand-purple/5 to-brand-purple/10 dark:from-brand-purple/10 dark:to-transparent p-6 rounded-3xl border border-brand-purple/20 group hover:border-brand-purple/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-brand-purple/5">
                                <div class="p-3 bg-brand-purple/20 text-brand-lila rounded-2xl w-fit mb-4">
                                    <x-mary-icon name="o-truck" class="w-6 h-6" />
                                </div>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-1 tabular-nums">{{ number_format($totalDistribuciones) }}</h4>
                                <p class="text-[10px] font-black text-brand-lila uppercase tracking-widest">Distribuciones</p>
                            </div>
                        </div>

                        <!-- Analytics: Estatus de Trámites -->
                        <div class="bg-white dark:bg-dark-850 p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Estado de Trámites</h3>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Administrativos</p>
                                </div>
                                <span class="px-3 py-1 bg-brand-purple/10 text-brand-lila rounded-full text-[10px] font-black uppercase tracking-widest">Eficiencia</span>
                            </div>
                            <div class="h-64">
                                <canvas id="tramiteChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. ACTIVIDAD RECIENTE & CATEGORÍAS -->
            <section>
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-xs font-black text-emerald-500 uppercase tracking-[0.3em]">Historial & Distribución</h3>
                    <div class="flex-1 h-px bg-linear-to-r from-emerald-500/20 to-transparent"></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Timeline de Actividad -->
                    <div class="bg-white dark:bg-dark-850 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-gray-100 dark:border-white/5 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Actividad Reciente</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Últimas acciones del sistema</p>
                            </div>
                            @can('gestionar usuarios')
                            <a href="{{ route('activity-log.index') }}" class="p-2 hover:bg-brand-purple/10 text-gray-400 hover:text-brand-lila rounded-xl transition-all" title="Ver todo el historial">
                                <x-mary-icon name="o-arrow-top-right-on-square" class="w-5 h-5" />
                            </a>
                            @endcan
                        </div>
                        <div class="p-6 space-y-0">
                            @forelse($actividadesRecientes as $activity)
                            @php
                            $eventIcon = match($activity->event) {
                            'created' => 'o-plus-circle',
                            'updated' => 'o-pencil-square',
                            'deleted' => 'o-trash',
                            default => 'o-information-circle',
                            };
                            $eventColor = match($activity->event) {
                            'created' => 'bg-emerald-500/15 text-emerald-500 border-emerald-500/30',
                            'updated' => 'bg-amber-500/15 text-amber-500 border-amber-500/30',
                            'deleted' => 'bg-rose-500/15 text-rose-500 border-rose-500/30',
                            default => 'bg-gray-500/15 text-gray-500 border-gray-500/30',
                            };
                            $eventLineColor = match($activity->event) {
                            'created' => 'bg-emerald-500/30',
                            'updated' => 'bg-amber-500/30',
                            'deleted' => 'bg-rose-500/30',
                            default => 'bg-gray-500/30',
                            };
                            $moduleColor = match($activity->log_name) {
                            'bienes' => 'bg-brand-purple/10 text-brand-lila',
                            'bienes-externos' => 'bg-amber-500/10 text-amber-400',
                            'transferencias' => 'bg-blue-500/10 text-blue-400',
                            'desincorporaciones' => 'bg-rose-500/10 text-rose-400',
                            'distribuciones' => 'bg-emerald-500/10 text-emerald-400',
                            default => 'bg-gray-500/10 text-gray-400',
                            };
                            @endphp
                            <div class="flex gap-4 group">
                                {{-- Línea vertical + ícono --}}
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-xl {{ $eventColor }} border flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                        <x-mary-icon name="{{ $eventIcon }}" class="w-4 h-4" />
                                    </div>
                                    @if(!$loop->last)
                                    <div class="w-px flex-1 min-h-[24px] {{ $eventLineColor }}"></div>
                                    @endif
                                </div>
                                {{-- Contenido --}}
                                <div class="pb-5 flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white leading-snug truncate">{{ $activity->description }}</p>
                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-lg {{ $moduleColor }} uppercase tracking-widest">
                                            {{ ucfirst(str_replace('-', ' ', $activity->log_name ?? 'general')) }}
                                        </span>
                                        <span class="text-[10px] font-bold text-gray-400">
                                            {{ $activity->causer?->name ?? 'Sistema' }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">·</span>
                                        <span class="text-[10px] text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-3">
                                    <x-mary-icon name="o-clock" class="w-6 h-6 text-gray-400" />
                                </div>
                                <p class="text-sm font-bold text-gray-400">Sin actividad registrada aún</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Bienes por Categoría Chart -->
                    <div class="bg-white dark:bg-dark-850 p-8 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Bienes por Categoría</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Distribución del Inventario</p>
                            </div>
                            <div class="p-2 bg-brand-purple/10 text-brand-lila rounded-xl">
                                <x-mary-icon name="o-tag" class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="h-[340px]">
                            <canvas id="categoriaChart"></canvas>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>

    <!-- Chart.js and Data Orchestration -->
    @php
    $estadoLabels = $porEstado->pluck('estado');
    $estadoCounts = $porEstado->pluck('count');

    $categoriaLabels = $porCategoria->pluck('categoria');
    $categoriaCounts = $porCategoria->pluck('count');

    $tramiteLabels = $porEstatusTramite->pluck('estatus');
    $estatusActaColors = \App\Models\EstatusActa::pluck('color', 'nombre');
    $tramiteColors = $porEstatusTramite->pluck('estatus')->map(function($nombre) use ($estatusActaColors) {
    return $estatusActaColors[$nombre] ?? '#71717a';
    });
    $totalTramitesCount = $porEstatusTramite->sum('count');
    $tramiteCounts = $porEstatusTramite->pluck('count');
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Registrar plugin de datalabels
            Chart.register(ChartDataLabels);

            // Global Chart Defaults
            Chart.defaults.color = '#71717a';
            Chart.defaults.font.family = "'Outfit', sans-serif";
            Chart.defaults.font.weight = 'bold';

            // Colores para el chart de estado (alineados con las cards)
            const estadoColorMap = {
                'Bueno': '#10b981',
                'Regular': '#f59e0b',
                'Malo': '#ef4444',
                'En Reparación': '#3b82f6',
                'Desincorporado': '#71717a'
            };
            const estadoLabels = {
                {
                    \
                    Illuminate\ Support\ Js::from($estadoLabels)
                }
            };

            const estadoData = {
                {
                    \
                    Illuminate\ Support\ Js::from($estadoCounts)
                }
            };

            const estadoColors = estadoLabels.map(label => estadoColorMap[label] || '#a855f7');
            const totalBienes = estadoData.reduce((a, b) => a + b, 0);

            // 1. Chart Estado (Donut)
            const ctxEstado = document.getElementById('estadoChart').getContext('2d');
            new Chart(ctxEstado, {
                type: 'doughnut',
                data: {
                    labels: estadoLabels,
                    datasets: [{
                        data: estadoData,
                        backgroundColor: estadoColors,
                        borderWidth: 0,
                        hoverOffset: 15,
                        borderRadius: 20,
                        spacing: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#18181b',
                            titleFont: {
                                size: 13,
                                weight: 'black'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 14,
                            cornerRadius: 12,
                            displayColors: true,
                            boxPadding: 6,
                            callbacks: {
                                label: function(ctx) {
                                    const val = ctx.parsed;
                                    const pct = totalBienes > 0 ? ((val / totalBienes) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: ${val} bienes (${pct}%)`;
                                }
                            }
                        }
                    },
                    cutout: '80%'
                }
            });

            // 2. Chart Categoría
            const ctxCategoria = document.getElementById('categoriaChart').getContext('2d');
            const catGradient = ctxCategoria.createLinearGradient(0, 0, 0, 400);
            catGradient.addColorStop(0, '#a855f7');
            catGradient.addColorStop(1, 'rgba(168, 85, 247, 0.1)');

            new Chart(ctxCategoria, {
                type: 'bar',
                data: {
                    labels: {
                        {
                            \
                            Illuminate\ Support\ Js::from($categoriaLabels)
                        }
                    },

                    datasets: [{
                        label: 'Bienes por Categoría',
                        data: {
                            {
                                \
                                Illuminate\ Support\ Js::from($categoriaCounts)
                            }
                        },

                        backgroundColor: catGradient,
                        borderRadius: 12,
                        barThickness: 28,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#94a3b8'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#c084fc',
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            formatter: (value) => value > 0 ? value : ''
                        },
                        tooltip: {
                            backgroundColor: '#18181b',
                            cornerRadius: 12,
                            padding: 12,
                            callbacks: {
                                label: function(ctx) {
                                    const pct = totalBienes > 0 ? ((ctx.parsed.y / totalBienes) * 100).toFixed(1) : 0;
                                    return ` ${ctx.parsed.y} bienes (${pct}% del total)`;
                                }
                            }
                        }
                    }
                }
            });

            // 3. Chart Trámites
            const ctxTramite = document.getElementById('tramiteChart').getContext('2d');
            const totalTramites = {
                {
                    $totalTramitesCount
                }
            };


            new Chart(ctxTramite, {
                type: 'bar',
                data: {
                    labels: {
                        {
                            \
                            Illuminate\ Support\ Js::from($tramiteLabels)
                        }
                    },

                    datasets: [{
                        label: 'Trámites por estatus',
                        data: {
                            {
                                \
                                Illuminate\ Support\ Js::from($tramiteCounts)
                            }
                        },

                        backgroundColor: {
                            {
                                \
                                Illuminate\ Support\ Js::from($tramiteColors)
                            }
                        },

                        borderRadius: 50,
                        barThickness: 14,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: 'black'
                                },
                                color: '#94a3b8'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'right',
                            color: '#e2e8f0',
                            font: {
                                size: 11
                            },
                            formatter: (value) => {
                                const pct = totalTramites > 0 ? ((value / totalTramites) * 100).toFixed(0) : 0;
                                return `${value} (${pct}%)`;
                            }
                        },
                        tooltip: {
                            backgroundColor: '#18181b',
                            cornerRadius: 12,
                            padding: 12,
                            callbacks: {
                                label: function(ctx) {
                                    const pct = totalTramites > 0 ? ((ctx.parsed.x / totalTramites) * 100).toFixed(1) : 0;
                                    return ` ${ctx.parsed.x} trámites (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>