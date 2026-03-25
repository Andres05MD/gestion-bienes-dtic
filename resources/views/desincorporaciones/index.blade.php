<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Desincorporaciones') }}
            </h2>
            @can('crear desincorporaciones')
            <a href="{{ route('desincorporaciones.create') }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Nueva Desincorporación') }}
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 shadow-2xl sm:rounded-2xl border border-dark-800 relative z-10">
                <div class="p-6">

                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ route('desincorporaciones.index') }}" class="mb-8 space-y-4">
                        <div class="flex flex-col xl:flex-row gap-4">
                            <!-- Búsqueda -->
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por N° bien, descripción, serial, informe..." class="block w-full pl-11 pr-4 h-14 bg-dark-900 border-none rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/20 transition-all text-sm" />
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 xl:flex-none inline-flex items-center justify-center px-6 py-4 bg-brand-purple/20 text-brand-lila rounded-2xl hover:bg-brand-purple/30 transition-all font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-purple/5">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Buscar
                                </button>
                                @if(request()->anyFilled(['buscar', 'estatus_acta_id', 'procedencia_id', 'fecha_desde', 'fecha_hasta']))
                                <a href="{{ route('desincorporaciones.index') }}" class="flex-1 xl:flex-none inline-flex items-center justify-center px-6 py-4 bg-rose-500/10 text-rose-400 rounded-2xl hover:bg-rose-500/20 transition-all font-bold text-xs uppercase tracking-widest shadow-lg shadow-rose-500/5" title="Limpiar Filtros">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Limpiar
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Filtros Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <x-select-premium
                                name="estatus_acta_id"
                                placeholder="Estatus"
                                icon="o-clock"
                                :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                :value="request('estatus_acta_id')" />

                            <x-select-premium
                                name="procedencia_id"
                                placeholder="Procedencia"
                                icon="o-building-office-2"
                                :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                :value="request('procedencia_id')" />

                            <x-date-input-premium
                                name="fecha_desde"
                                placeholder="Desde"
                                :value="request('fecha_desde')" />

                            <x-date-input-premium
                                name="fecha_hasta"
                                placeholder="Hasta"
                                :value="request('fecha_hasta')" />
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest min-w-[200px]">N° Bien / Equipo</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Marca / Serial</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Procedencia</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Fecha</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">N° Informe</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Estatus</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest border-r-0">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($desincorporacionesAgrupadas as $codigoActa => $grupo)
                                @php
                                // Meta-datos comunes a toda el acta
                                $primera = $grupo->first();
                                $cantidad = $grupo->count();
                                @endphp
                                <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                    <!-- N° Bien / Equipo Combinados -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cantidad > 1)
                                        <div class="flex flex-col gap-4 py-2">
                                            @foreach($grupo as $d)
                                            <div>
                                                <span class="text-base font-bold text-white block">{{ $d->numero_bien }}</span>
                                                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest block truncate max-w-[200px]" title="{{ $d->descripcion }}">{{ $d->descripcion }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        <div>
                                            <span class="text-base font-bold text-white block">{{ $primera->numero_bien }}</span>
                                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest block truncate max-w-[200px]" title="{{ $primera->descripcion }}">{{ $primera->descripcion }}</span>
                                        </div>
                                        @endif
                                    </td>
                                    <!-- Marca / Serial Combinados -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cantidad > 1)
                                        <div class="flex flex-col gap-4 py-2">
                                            @foreach($grupo as $d)
                                            <div>
                                                <span class="text-sm font-bold text-gray-300 block uppercase tracking-widest">{{ $d->bien?->marca ?? $d->bienExterno?->marca ?? 'N/A' }}</span>
                                                <span class="text-xs text-gray-500 font-mono block mt-0.5">{{ $d->serial ?? 'S/N' }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        <div>
                                            <span class="text-sm font-bold text-gray-300 block uppercase tracking-widest">{{ $primera->bien?->marca ?? $primera->bienExterno?->marca ?? 'N/A' }}</span>
                                            <span class="text-xs text-gray-500 font-mono block mt-0.5">{{ $primera->serial ?? 'S/N' }}</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal min-w-[200px] text-base text-dark-text leading-tight">
                                        {!! str_replace(' - ', '<br>', e($primera->procedencia?->nombre ?? 'N/A')) !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-dark-text font-medium">{{ $primera->fecha->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base">
                                        @php
                                        $todosLosInformes = $grupo->pluck('numero_informe')
                                        ->filter()
                                        ->flatMap(fn($i) => array_map('trim', explode(',', $i)))
                                        ->unique();
                                        @endphp
                                        @if($todosLosInformes->isNotEmpty())
                                        <div class="flex flex-col gap-2 items-start">
                                            @foreach($todosLosInformes as $informe)
                                            <code class="text-brand-lila bg-brand-lila/5 px-2.5 py-1 rounded-md font-mono text-sm border border-brand-lila/10 whitespace-nowrap">{{ trim($informe) }}</code>
                                            @endforeach
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal min-w-[160px] align-middle">
                                        @php
                                            $todosTienenMismoEstatus = $cantidad > 1 ? $grupo->map(function($d) use ($primera) {
                                                return $d->estatus_acta_individual_id ?: ($primera->estatus_acta_id ?? 0);
                                            })->unique()->count() === 1 : true;
                                        @endphp
                                        
                                        @if($cantidad > 1 && !$todosTienenMismoEstatus)
                                        <div class="flex flex-col gap-4 py-2">
                                            @foreach($grupo as $d)
                                            <div class="flex items-center min-h-[40px]">
                                                @php
                                                    $estatusActivo = $d->estatus_acta_individual_id ? $d->estatusActaIndividual : $primera->estatusActa;
                                                    $estatusNombre = $estatusActivo?->nombre ?? 'N/A';
                                                    $estatusColor = $estatusActivo?->color ?? '#6b7280';
                                                    $esIndividual = $d->estatus_acta_individual_id ? true : false;
                                                    $nombreEstatusFormateado = str_replace(' - ', '<br>', str_replace(' falta ', '<br>falta ', e($estatusNombre)));
                                                @endphp
                                                <span class="px-2 py-1 relative inline-flex items-center justify-center text-center text-[10px] leading-tight font-black rounded-lg shadow-sm uppercase tracking-widest whitespace-normal w-full max-w-[140px] min-h-[36px]"
                                                    @style([
                                                        "background-color: {$estatusColor}15",
                                                        "color: {$estatusColor}",
                                                        "border: 1px ".($esIndividual ? 'dashed' : 'solid')." {$estatusColor}40"
                                                    ])
                                                    title="{{ $esIndividual ? 'Acta individual' : 'Acta grupal heredada' }}">
                                                    {!! $nombreEstatusFormateado !!}
                                                </span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        @php
                                            $estatusColor = $primera->estatus_acta_individual_id ? ($primera->estatusActaIndividual?->color ?? '#6b7280') : ($primera->estatusActa?->color ?? '#6b7280');
                                            $nombreEstatus = str_replace(' - ', '<br>', str_replace(' falta ', '<br>falta ', e($primera->estatus_acta_individual_id ? ($primera->estatusActaIndividual?->nombre ?? 'N/A') : ($primera->estatusActa?->nombre ?? 'N/A'))));
                                        @endphp
                                        <span class="px-3 py-1.5 inline-block text-center text-xs leading-5 font-black rounded-lg shadow-sm uppercase tracking-widest whitespace-normal"
                                            @style([ 
                                                "background-color: {$estatusColor}20", 
                                                "color: {$estatusColor}", 
                                                "border: 1px ".($primera->estatus_acta_individual_id ? 'dashed' : 'solid')." {$estatusColor}50" 
                                            ])>
                                            {!! $nombreEstatus !!}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold align-middle" x-data="{ openModal: {{ session('open_acta') === $codigoActa ? 'true' : 'false' }} }">
                                        <div class="flex items-center gap-3">
                                            @if($cantidad > 1)
                                            @can('editar desincorporaciones')
                                            <button type="button" @click="openModal = true" class="text-brand-purple hover:text-brand-lila transition" title="Gestionar Actas Individuales">
                                                <x-mary-icon name="o-clipboard-document-check" class="w-5 h-5" />
                                            </button>
                                            @endcan
                                            @endif

                                            @can('ver desincorporaciones')
                                            <a href="{{ route('desincorporaciones.show', $primera) }}" class="text-sky-400 hover:text-sky-300 transition" title="Ver detalle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @endcan
                                            @can('editar desincorporaciones')
                                            <a href="{{ route('desincorporaciones.edit', $primera) }}" class="text-amber-400 hover:text-amber-300 transition" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            @endcan
                                            @can('eliminar desincorporaciones')
                                            <button type="button"
                                                @click="window.dispatchEvent(new CustomEvent('open-deletion-modal', { detail: { action: '{{ route('desincorporaciones.destroy', $primera) }}' } }))"
                                                class="text-rose-500 hover:text-rose-400 transition transform active:scale-90"
                                                title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                        </div>

                                        {{-- Modal Actas Individuales --}}
                                        @if($cantidad > 1)
                                        @can('editar desincorporaciones')
                                        <div x-show="openModal" class="fixed inset-0 z-100 flex items-center justify-center bg-black/60 backdrop-blur-sm whitespace-normal" x-transition x-cloak style="display: none;">
                                            <div @click.away="openModal = false" class="bg-white dark:bg-dark-900 border border-gray-100 dark:border-white/10 rounded-3xl shadow-2xl w-full max-w-4xl flex flex-col mx-4 text-left relative z-50">
                                                <!-- Header -->
                                                <div class="px-6 py-5 border-b border-gray-100 dark:border-white/5 flex justify-between items-center bg-gray-50 dark:bg-dark-850">
                                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                                                        <x-mary-icon name="o-clipboard-document-list" class="w-5 h-5 text-brand-purple" />
                                                        Actas Individuales <span class="text-brand-purple bg-brand-purple/10 px-2 py-0.5 rounded-lg">{{ $primera->codigo_acta }}</span>
                                                    </h3>
                                                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors bg-white/5 hover:bg-white/10 p-1.5 rounded-xl">
                                                        <x-mary-icon name="o-x-mark" class="w-5 h-5" />
                                                    </button>
                                                </div>
                                                <!-- Body -->
                                                <div class="p-6 pb-20 bg-white dark:bg-dark-900 space-y-4">
                                                    @foreach($grupo as $bien)
                                                    <div class="bg-gray-50 dark:bg-[#1a1a1a] p-5 rounded-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-300 hover:border-brand-purple/20">
                                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between mb-4 gap-2">
                                                            <div>
                                                                <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em] mb-1">Bien N° {{ $bien->numero_bien }}</p>
                                                                <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $bien->descripcion }}</p>
                                                            </div>
                                                            <div class="inline-flex items-center gap-1.5 bg-white/5 px-2.5 py-1.5 rounded-xl border border-white/5">
                                                                <x-mary-icon name="o-qr-code" class="w-3.5 h-3.5 text-gray-500" />
                                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">SN: <span class="text-gray-200 ml-0.5">{{ $bien->serial ?: 'N/A' }}</span></span>
                                                            </div>
                                                        </div>
                                                        <form method="POST" action="{{ route('desincorporaciones.estatus-individual', $bien) }}" class="flex items-start gap-3">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="flex-1 min-w-0">
                                                                <x-select-premium 
                                                                    name="estatus_acta_individual_id" 
                                                                    :options="\App\Models\EstatusActa::all()->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                                                    :value="$bien->estatus_acta_individual_id"
                                                                    placeholder="— Heredar Grupal —"
                                                                    icon="o-clipboard-document-check"
                                                                    :searchable="false"
                                                                />
                                                            </div>
                                                            <button type="submit" class="px-5 py-3 h-12 bg-linear-to-r from-brand-lila to-brand-purple text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-300 shadow-lg shadow-brand-purple/20 shrink-0">
                                                                Guardar
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endcan
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                @if(request()->anyFilled(['buscar', 'estatus', 'procedencia_id']))
                                                No se encontraron desincorporaciones con los filtros aplicados.
                                                @else
                                                No hay desincorporaciones registradas.
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
                        {{ $desincorporacionesPaginadas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-deletion
        title="¿Eliminar Desincorporación?"
        message="¿Estás seguro de que deseas eliminar esta desincorporación? Esta acción es irreversible." />


</x-app-layout>