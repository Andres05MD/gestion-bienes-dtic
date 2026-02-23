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
                    <form method="GET" action="{{ route('movimientos.index') }}" class="mb-8 flex flex-col xl:flex-row gap-4">
                        <!-- Búsqueda -->
                        <div class="xl:w-1/3 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por número de bien, descripción..." class="block w-full pl-11 pr-4 h-12 bg-dark-900 border-none rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/20 transition-all text-sm" />
                        </div>

                        <!-- Filtros -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                            <x-select-premium
                                name="tipo_movimiento"
                                placeholder="Tipo de Movimiento"
                                icon="o-funnel"
                                :options="collect($tiposMovimiento)->map(fn($label, $value) => ['value' => $value, 'label' => $label])->values()->toArray()"
                                :value="request('tipo_movimiento')" />

                            <x-select-premium
                                name="departamento_id"
                                placeholder="Departamento"
                                icon="o-building-office"
                                :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                :value="request('departamento_id')" />

                            <x-date-input-premium
                                name="fecha_desde"
                                placeholder="Desde"
                                icon="o-calendar"
                                :value="request('fecha_desde')" />

                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <x-date-input-premium
                                        name="fecha_hasta"
                                        placeholder="Hasta"
                                        icon="o-calendar"
                                        :value="request('fecha_hasta')" />
                                </div>
                                <button type="submit" class="mb-2 shrink-0 p-4 bg-brand-purple/20 text-brand-lila rounded-2xl hover:bg-brand-purple/30 transition-colors shadow-lg shadow-brand-purple/5 h-14 flex items-center justify-center" title="Buscar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                                @if(request()->anyFilled(['buscar', 'tipo_movimiento', 'departamento_id', 'fecha_desde', 'fecha_hasta']))
                                <a href="{{ route('movimientos.index') }}" class="mb-2 shrink-0 p-4 bg-rose-500/10 text-rose-400 rounded-2xl hover:bg-rose-500/20 transition-colors shadow-lg shadow-rose-500/5 flex items-center h-14 justify-center" title="Limpiar Filtros">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Fecha</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Bien</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Tipo</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Origen</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Destino</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Usuario</th>
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
                                        <div class="text-sm font-bold text-white">{{ $mov->numero_bien }}</div>
                                        <div class="text-[10px] text-gray-500 font-medium max-w-[200px] truncate">{{ $mov->descripcion }}</div>
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                @if(request()->anyFilled(['buscar', 'tipo_movimiento', 'departamento_id', 'fecha_desde', 'fecha_hasta']))
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