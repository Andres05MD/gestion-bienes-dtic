<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-purple/20 rounded-2xl">
                    <x-mary-icon name="o-arrow-path" class="w-8 h-8 text-brand-lila" />
                </div>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                        {{ __('Historial de Movimientos') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Trazabilidad completa de bienes</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 overflow-hidden shadow-2xl sm:rounded-2xl border border-dark-800">
                <div class="p-6">

                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ route('movimientos.index') }}" class="mb-8 space-y-4">
                        <!-- Fila Superior: Búsqueda y Acciones principales -->
                        <div class="flex flex-col md:flex-row gap-4 w-full">
                            <!-- Búsqueda -->
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por número de bien, descripción..." class="block w-full pl-12 pr-4 h-[52px] bg-dark-900/80 border border-dark-800 rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/50 focus:border-transparent transition-all text-sm shadow-inner" />
                            </div>

                            <!-- Acciones Rápidas -->
                            <div class="flex gap-3 h-[52px]">
                                <div class="flex-1 md:flex-none flex items-center justify-center bg-dark-900 border border-dark-800 hover:border-brand-purple/50 transition-colors rounded-2xl px-5 shadow-sm">
                                    <label class="flex items-center justify-center gap-2.5 cursor-pointer w-full h-full">
                                        <input type="checkbox" name="origen_dtic" value="1" {{ request('origen_dtic') ? 'checked' : '' }} class="w-4 h-4 text-brand-purple rounded border-dark-700 bg-dark-800 focus:ring-brand-purple focus:ring-offset-dark-900 transition-colors" onchange="this.form.submit()">
                                        <span class="text-xs font-bold text-gray-300 uppercase tracking-widest whitespace-nowrap">Solo Asg DTIC</span>
                                    </label>
                                </div>

                                <button type="submit" class="w-[52px] shrink-0 bg-brand-purple/20 text-brand-lila rounded-2xl hover:bg-brand-purple/30 transition-colors shadow-lg shadow-brand-purple/5 flex items-center justify-center" title="Buscar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                                @if(request()->anyFilled(['buscar', 'tipo_movimiento', 'departamento_origen_id', 'area_origen_id', 'departamento_destino_id', 'area_destino_id', 'fecha_desde', 'fecha_hasta', 'origen_dtic']))
                                <a href="{{ route('movimientos.index') }}" class="w-[52px] shrink-0 bg-rose-500/10 text-rose-400 rounded-2xl hover:bg-rose-500/20 transition-colors shadow-lg shadow-rose-500/5 flex items-center justify-center" title="Limpiar Filtros">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Fila Inferior: Filtros Desplegables -->
                        <div x-data="{ 
                                dOrigen: '{{ request('departamento_origen_id', '') }}', 
                                dDestino: '{{ request('departamento_destino_id', '') }}',
                                dticId: '{{ $dticId }}'
                             }"
                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4 bg-dark-900/30 rounded-2xl border border-dark-800/60 shadow-inner items-start transition-all duration-300">

                            <x-select-premium
                                name="tipo_movimiento"
                                placeholder="Tipo de Movimiento"
                                icon="o-funnel"
                                :options="collect($tiposMovimiento)->map(fn($label, $value) => ['value' => $value, 'label' => $label])->values()->toArray()"
                                :value="request('tipo_movimiento')" />

                            <x-select-premium
                                name="departamento_origen_id"
                                placeholder="Dpto. Origen"
                                icon="o-building-office"
                                @option-selected="dOrigen = $event.detail"
                                :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                :value="request('departamento_origen_id')" />

                            <template x-if="dOrigen == dticId">
                                <x-select-premium
                                    name="area_origen_id"
                                    placeholder="Área Origen"
                                    icon="o-hashtag"
                                    :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                    :value="request('area_origen_id')" class="animate-fade-in-down" />
                            </template>

                            <x-select-premium
                                name="departamento_destino_id"
                                placeholder="Dpto. Destino"
                                icon="o-building-office"
                                @option-selected="dDestino = $event.detail"
                                :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                :value="request('departamento_destino_id')" />

                            <template x-if="dDestino == dticId">
                                <x-select-premium
                                    name="area_destino_id"
                                    placeholder="Área Destino"
                                    icon="o-hashtag"
                                    :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                    :value="request('area_destino_id')" class="animate-fade-in-down" />
                            </template>

                            <x-date-input-premium
                                name="fecha_desde"
                                placeholder="Desde"
                                icon="o-calendar"
                                :value="request('fecha_desde')" />

                            <x-date-input-premium
                                name="fecha_hasta"
                                placeholder="Hasta"
                                icon="o-calendar"
                                :value="request('fecha_hasta')" />
                        </div>
                    </form>

                    <!-- Mini-Estadísticas Clickeables -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <a href="{{ request()->fullUrlWithQuery(['tipo_movimiento' => null, 'departamento_origen_id' => null, 'departamento_destino_id' => null, 'area_origen_id' => null, 'area_destino_id' => null]) }}" class="bg-dark-900 border {{ !request('tipo_movimiento') && !request('departamento_origen_id') && !request('departamento_destino_id') ? 'border-brand-purple/50 bg-brand-purple/5' : 'border-dark-800' }} rounded-2xl p-4 hover:border-brand-purple/50 transition-all duration-300 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-brand-purple/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Filtro</p>
                                    <h3 class="text-3xl font-black text-white mt-1">{{ number_format($estadisticas['total'] ?? 0) }}</h3>
                                </div>
                                <div class="p-2 {{ !request('tipo_movimiento') && !request('departamento_origen_id') && !request('departamento_destino_id') ? 'bg-brand-purple/20' : 'bg-dark-800 group-hover:bg-brand-purple/20' }} rounded-xl transition-colors">
                                    <x-mary-icon name="o-document-chart-bar" class="w-5 h-5 {{ !request('tipo_movimiento') && !request('departamento_origen_id') && !request('departamento_destino_id') ? 'text-brand-lila' : 'text-gray-400 group-hover:text-brand-lila' }} transition-colors" />
                                </div>
                            </div>
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['tipo_movimiento' => 'transferencia']) }}" class="bg-dark-900 border {{ request('tipo_movimiento') === 'transferencia' ? 'border-blue-500/50 bg-blue-500/5' : 'border-dark-800' }} rounded-2xl p-4 hover:border-blue-500/50 transition-all duration-300 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Transferencias</p>
                                    <h3 class="text-3xl font-black text-blue-500 mt-1">{{ number_format($estadisticas['transferencias'] ?? 0) }}</h3>
                                </div>
                                <div class="p-2 {{ request('tipo_movimiento') === 'transferencia' ? 'bg-blue-500/20' : 'bg-dark-800 group-hover:bg-blue-500/20' }} rounded-xl transition-colors">
                                    <x-mary-icon name="o-arrows-right-left" class="w-5 h-5 {{ request('tipo_movimiento') === 'transferencia' ? 'text-blue-500' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" />
                                </div>
                            </div>
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['tipo_movimiento' => 'mantenimiento']) }}" class="bg-dark-900 border {{ request('tipo_movimiento') === 'mantenimiento' ? 'border-amber-500/50 bg-amber-500/5' : 'border-dark-800' }} rounded-2xl p-4 hover:border-amber-500/50 transition-all duration-300 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-amber-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Mantenimientos</p>
                                    <h3 class="text-3xl font-black text-amber-500 mt-1">{{ number_format($estadisticas['mantenimientos'] ?? 0) }}</h3>
                                </div>
                                <div class="p-2 {{ request('tipo_movimiento') === 'mantenimiento' ? 'bg-amber-500/20' : 'bg-dark-800 group-hover:bg-amber-500/20' }} rounded-xl transition-colors">
                                    <x-mary-icon name="o-wrench-screwdriver" class="w-5 h-5 {{ request('tipo_movimiento') === 'mantenimiento' ? 'text-amber-500' : 'text-gray-400 group-hover:text-amber-500' }} transition-colors" />
                                </div>
                            </div>
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['tipo_movimiento' => 'desincorporacion']) }}" class="bg-dark-900 border {{ request('tipo_movimiento') === 'desincorporacion' ? 'border-rose-500/50 bg-rose-500/5' : 'border-dark-800' }} rounded-2xl p-4 hover:border-rose-500/50 transition-all duration-300 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-rose-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start relative z-10">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Desincorporaciones</p>
                                    <h3 class="text-3xl font-black text-rose-500 mt-1">{{ number_format($estadisticas['desincorporaciones'] ?? 0) }}</h3>
                                </div>
                                <div class="p-2 {{ request('tipo_movimiento') === 'desincorporacion' ? 'bg-rose-500/20' : 'bg-dark-800 group-hover:bg-rose-500/20' }} rounded-xl transition-colors">
                                    <x-mary-icon name="o-trash" class="w-5 h-5 {{ request('tipo_movimiento') === 'desincorporacion' ? 'text-rose-500' : 'text-gray-400 group-hover:text-rose-500' }} transition-colors" />
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Último Mov.</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Bien</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Tipo</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Origen</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Destino</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Usuario</th>
                                    <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-dark-text uppercase tracking-widest">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($movimientos as $mov)
                                @php
                                $tipoColor = match($mov->tipo_movimiento) {
                                'transferencia' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                'desincorporacion' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                'distribucion' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'mantenimiento' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'mantenimiento_devolucion' => 'bg-teal-500/10 text-teal-400 border-teal-500/20',
                                'registro' => 'bg-brand-purple/10 text-brand-lila border-brand-purple/20',
                                default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                };
                                $tipoIcon = match($mov->tipo_movimiento) {
                                'transferencia' => 'o-arrows-right-left',
                                'desincorporacion' => 'o-trash',
                                'distribucion' => 'o-clipboard-document-list',
                                'mantenimiento' => 'o-wrench-screwdriver',
                                'mantenimiento_devolucion' => 'o-arrow-uturn-left',
                                'registro' => 'o-plus-circle',
                                default => 'o-information-circle',
                                };
                                @endphp
                                <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-white">{{ $mov->fecha->format('d/m/Y') }}</div>
                                        <div class="text-[10px] text-gray-500 font-medium">{{ $mov->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <div class="text-sm font-bold text-white">{{ $mov->numero_bien }}</div>
                                                    @if($mov->departamentoOrigen?->nombre === 'DTIC')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[8px] font-black rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 uppercase tracking-tighter" title="Asignado desde DTIC">
                                                        Asg Dtic
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-gray-500 font-medium max-w-[200px] truncate uppercase">{{ $mov->bien?->equipo ?? 'EQUIPO DESCONOCIDO' }}</div>
                                            </div>
                                            @if($mov->total_movimientos > 1)
                                            <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[9px] font-black rounded-lg bg-brand-purple/20 text-brand-lila border border-brand-purple/30" title="{{ $mov->total_movimientos }} movimientos registrados">
                                                {{ $mov->total_movimientos }}
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black rounded-xl border {{ $tipoColor }} uppercase tracking-widest">
                                            <x-mary-icon name="{{ $tipoIcon }}" class="w-3.5 h-3.5" />
                                            {{ $mov->etiqueta_tipo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal max-w-[200px]">
                                        <div class="text-sm text-white font-medium leading-tight">
                                            {{ $mov->departamentoOrigen?->nombre ?? '—' }}
                                        </div>
                                        @if($mov->areaOrigen)
                                        <div class="text-[10px] text-gray-500 mt-1">{{ $mov->areaOrigen->nombre }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal max-w-[200px]">
                                        <div class="text-sm text-white font-medium leading-tight">
                                            {{ $mov->departamentoDestino?->nombre ?? '—' }}
                                        </div>
                                        @if($mov->areaDestino)
                                        <div class="text-[10px] text-gray-500 mt-1">{{ $mov->areaDestino->nombre }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="h-7 w-7 rounded-full bg-linear-to-tr from-brand-purple to-brand-lila flex items-center justify-center text-white font-bold text-[10px] shadow-lg">
                                                {{ substr($mov->user?->name ?? '?', 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium text-white">{{ $mov->user?->name ?? 'Sistema' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                        $bienTipo = str_contains($mov->bien_type, 'BienExterno') ? 'externo' : 'dtic';
                                        @endphp
                                        @if($mov->bien_id > 0)
                                        <a href="{{ route('movimientos.por-bien', [$bienTipo, $mov->bien_id]) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-brand-purple/10 text-brand-lila hover:bg-brand-purple/25 hover:text-white hover:shadow-lg hover:shadow-brand-purple/20 transition-all duration-300 group"
                                            title="Ver historial completo del bien {{ $mov->numero_bien }}">
                                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                        @else
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-500/10 text-gray-600 cursor-not-allowed" title="Bien sin referencia válida">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                @if(request()->anyFilled(['buscar', 'tipo_movimiento', 'departamento_origen_id', 'departamento_destino_id', 'area_origen_id', 'area_destino_id', 'fecha_desde', 'fecha_hasta']))
                                                No se encontraron movimientos con los filtros aplicados.
                                                @else
                                                No hay movimientos registrados aún.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $movimientos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>