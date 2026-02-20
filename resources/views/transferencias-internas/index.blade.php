<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Transferencias Internas') }}
            </h2>
            @can('crear transferencias')
                <a href="{{ route('transferencias-internas.create') }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Nueva Transferencia') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 shadow-2xl sm:rounded-2xl border border-dark-800 relative z-10">
                <div class="p-6">

                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ route('transferencias-internas.index') }}" class="mb-8 space-y-4">
                        <div class="flex flex-col xl:flex-row gap-4">
                            <!-- Búsqueda -->
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por N° bien, descripción, serial..." class="block w-full pl-11 pr-4 h-14 bg-dark-900 border-none rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/20 transition-all text-sm" />
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 xl:flex-none inline-flex items-center justify-center px-6 py-4 bg-brand-purple/20 text-brand-lila rounded-2xl hover:bg-brand-purple/30 transition-all font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-purple/5">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    Buscar
                                </button>
                                @if(request()->anyFilled(['buscar', 'estatus_acta_id', 'procedencia_id', 'destino_id', 'fecha_desde', 'fecha_hasta']))
                                    <a href="{{ route('transferencias-internas.index') }}" class="flex-1 xl:flex-none inline-flex items-center justify-center px-6 py-4 bg-rose-500/10 text-rose-400 rounded-2xl hover:bg-rose-500/20 transition-all font-bold text-xs uppercase tracking-widest shadow-lg shadow-rose-500/5" title="Limpiar Filtros">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Limpiar
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Filtros Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                            <x-select-premium
                                name="estatus_acta_id"
                                placeholder="Estatus"
                                icon="o-clock"
                                :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                :value="request('estatus_acta_id')"
                            />

                            <x-select-premium
                                name="procedencia_id"
                                placeholder="Procedencia"
                                icon="o-building-office-2"
                                :options="array_merge([['value' => 'dtic', 'label' => 'DTIC']], $departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray())"
                                :value="request('procedencia_id')"
                            />

                            <x-select-premium
                                name="destino_id"
                                placeholder="Destino"
                                icon="o-building-office-2"
                                :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                :value="request('destino_id')"
                            />

                            <x-date-input-premium 
                                name="fecha_desde" 
                                placeholder="Desde" 
                                :value="request('fecha_desde')" 
                            />

                            <x-date-input-premium 
                                name="fecha_hasta" 
                                placeholder="Hasta" 
                                :value="request('fecha_hasta')" 
                            />
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">N° Bien</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Descripción</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Serial</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Procedencia</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Destino</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Fecha</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Estatus</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Fecha Firma</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest border-r-0">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($transferenciasAgrupadas as $codigoActa => $grupo)
                                    @php
                                        // Meta-datos comunes a toda el acta
                                        $primera = $grupo->first();
                                        $cantidad = $grupo->count();
                                    @endphp
                                    <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                        <!-- N° Bien Agrupado -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-white">
                                            @if($cantidad > 1)
                                                <div class="flex flex-col gap-1 mt-6">
                                                    @foreach($grupo as $t)
                                                        <span class="text-gray-300">{{ $t->numero_bien }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $primera->numero_bien }}
                                            @endif
                                        </td>
                                        
                                        <!-- Descripción Agrupada -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                             @if($cantidad > 1)
                                                <div class="flex flex-col gap-1 mt-6">
                                                    @foreach($grupo as $t)
                                                        <span class="text-gray-300 truncate max-w-[220px]" title="{{ $t->descripcion }}">{{ $t->descripcion }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $primera->descripcion }}
                                            @endif
                                        </td>
                                        
                                        <!-- Serial Agrupado -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">
                                            @if($cantidad > 1)
                                                <div class="flex flex-col gap-1 mt-6">
                                                    @foreach($grupo as $t)
                                                        <span class="text-gray-400 font-mono text-xs">{{ $t->serial ?? '—' }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                {{ $primera->serial ?? '—' }}
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-normal text-sm text-dark-text min-w-[200px]">{{ $primera->procedencia?->nombre ?? 'DTIC' }}</td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-dark-text min-w-[200px]">{{ $primera->destino?->nombre ?? 'DTIC' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">{{ $primera->fecha->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-[10px] leading-4 font-black rounded-lg bg-gray-100 dark:bg-white/10 text-gray-800 dark:text-gray-200 shadow-sm uppercase">
                                                {{ $primera->estatusActa?->nombre ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">{{ $primera->fecha_firma?->format('d/m/Y') ?? '—' }}</td>
                                        
                                        <!-- Acciones (El enlace asume que abriremos la primera, o idealmente el grupo. En este punto el view sigue igual) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-3">
                                            @can('ver transferencias')
                                                <a href="{{ route('transferencias-internas.show', $primera) }}" class="text-sky-400 hover:text-sky-300 transition" title="Ver detalle del Acta">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                            @endcan
                                            @can('editar transferencias')
                                                <a href="{{ route('transferencias-internas.edit', $primera) }}" class="text-amber-400 hover:text-amber-300 transition" title="Editar Estatus del Acta">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                            @endcan
                                            @can('eliminar transferencias')
                                                <button type="button"
                                                        @click="window.dispatchEvent(new CustomEvent('open-deletion-modal', { detail: { action: '{{ route('transferencias-internas.destroy', $primera) }}' } }))"
                                                        class="text-rose-500 hover:text-rose-400 transition transform active:scale-90"
                                                        title="Eliminar Registro (Si un acta tiene múltiples, eliminar una por aquí borrará solo esa individual si el DestroyController no se ajusta, a reverenciar)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                                <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                    @if(request()->anyFilled(['buscar', 'estatus', 'procedencia_id', 'destino_id']))
                                                        No se encontraron transferencias con los filtros aplicados.
                                                    @else
                                                        No hay transferencias internas registradas.
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
                        {{ $transferenciasPaginadas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-deletion
        title="¿Eliminar Registro?"
        message="¿Estás seguro de que deseas eliminar este registro de la transferencia interna?"
    />


</x-app-layout>
