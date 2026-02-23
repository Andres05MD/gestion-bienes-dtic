<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('movimientos.index') }}" class="p-2 bg-dark-800 rounded-xl hover:bg-dark-700 transition-colors" title="Volver">
                    <x-mary-icon name="o-arrow-left" class="w-5 h-5 text-gray-400" />
                </a>
                <div class="p-3 bg-brand-purple/20 rounded-2xl">
                    <x-mary-icon name="o-arrow-path" class="w-8 h-8 text-brand-lila" />
                </div>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                        Movimientos de {{ $bien->numero_bien }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">
                        {{ $bien->equipo ?? $bien->descripcion ?? 'Bien' }}
                        @if($bien instanceof \App\Models\BienExterno && $bien->departamentoOrigen)
                        <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase">
                            <x-mary-icon name="o-building-office" class="w-3 h-3" />
                            Procedencia: {{ $bien->departamentoOrigen->nombre }}
                        </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Información del Bien --}}
            <div class="mb-8 bg-dark-850 rounded-2xl border border-dark-800 p-6 shadow-2xl">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">N° Bien</p>
                        <p class="text-sm font-bold text-white mt-1">{{ $bien->numero_bien }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Equipo</p>
                        <p class="text-sm font-bold text-white mt-1">{{ $bien->equipo ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Serial</p>
                        <p class="text-sm font-bold text-white mt-1">{{ $bien->serial ?? 'S/N' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Ubicación Actual</p>
                        <p class="text-sm font-bold text-white mt-1">
                            @if($bien instanceof \App\Models\Bien)
                            {{ $bien->area?->nombre ?? 'DTIC' }}
                            @else
                            {{ $bien->departamento?->nombre ?? '—' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Línea de Tiempo --}}
            <div class="relative">
                {{-- Línea vertical --}}
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-brand-purple/50 via-brand-lila/30 to-transparent"></div>

                <div class="space-y-6">
                    @forelse ($todosMovimientos as $mov)
                    @php
                    $tipoColor = match($mov->tipo_movimiento) {
                    'transferencia' => 'from-blue-500 to-blue-600',
                    'desincorporacion' => 'from-rose-500 to-rose-600',
                    'distribucion' => 'from-emerald-500 to-emerald-600',
                    'mantenimiento' => 'from-amber-500 to-amber-600',
                    'mantenimiento_devolucion' => 'from-teal-500 to-teal-600',
                    'registro' => 'from-brand-purple to-brand-lila',
                    default => 'from-gray-500 to-gray-600',
                    };
                    $tipoBg = match($mov->tipo_movimiento) {
                    'transferencia' => 'bg-blue-500/10 border-blue-500/20',
                    'desincorporacion' => 'bg-rose-500/10 border-rose-500/20',
                    'distribucion' => 'bg-emerald-500/10 border-emerald-500/20',
                    'mantenimiento' => 'bg-amber-500/10 border-amber-500/20',
                    'mantenimiento_devolucion' => 'bg-teal-500/10 border-teal-500/20',
                    'registro' => 'bg-brand-purple/10 border-brand-purple/20',
                    default => 'bg-gray-500/10 border-gray-500/20',
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
                    <div class="relative flex gap-4 pl-2">
                        {{-- Punto en la línea de tiempo --}}
                        <div class="relative z-10 flex-shrink-0 w-9 h-9 rounded-full bg-gradient-to-tr {{ $tipoColor }} flex items-center justify-center shadow-lg">
                            <x-mary-icon name="{{ $tipoIcon }}" class="w-4 h-4 text-white" />
                        </div>

                        {{-- Tarjeta del movimiento --}}
                        <div class="flex-1 {{ $tipoBg }} border rounded-2xl p-5 shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <span class="text-xs font-black text-white uppercase tracking-widest">{{ $mov->etiqueta_tipo }}</span>
                                    <p class="text-[10px] text-gray-500 font-medium mt-0.5">{{ $mov->fecha->format('d/m/Y') }} · {{ $mov->created_at->diffForHumans() }}</p>
                                </div>
                                @if($mov->user)
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-gradient-to-tr from-brand-purple to-brand-lila flex items-center justify-center text-white font-bold text-[9px]">
                                        {{ substr($mov->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $mov->user->name }}</span>
                                </div>
                                @endif
                            </div>

                            {{-- Flujo Origen → Destino --}}
                            @if($mov->departamentoOrigen || $mov->departamentoDestino)
                            <div class="flex items-center gap-3 mb-2">
                                <div class="flex-1 bg-dark-900/50 rounded-xl px-3 py-2">
                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Origen</p>
                                    <p class="text-sm font-bold text-white leading-tight mt-1">{{ $mov->departamentoOrigen?->nombre ?? '—' }}</p>
                                    @if($mov->areaOrigen)
                                    <p class="text-[10px] text-gray-400 mt-1 leading-tight">{{ $mov->areaOrigen->nombre }}</p>
                                    @endif
                                </div>
                                <x-mary-icon name="o-arrow-right" class="w-5 h-5 text-gray-500 flex-shrink-0" />
                                <div class="flex-1 bg-dark-900/50 rounded-xl px-3 py-2">
                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Destino</p>
                                    <p class="text-sm font-bold text-white leading-tight mt-1">{{ $mov->departamentoDestino?->nombre ?? '—' }}</p>
                                    @if($mov->areaDestino)
                                    <p class="text-[10px] text-gray-400 mt-1 leading-tight">{{ $mov->areaDestino->nombre }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($mov->descripcion)
                            <p class="text-xs text-gray-400 mt-2">{{ $mov->descripcion }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-dark-800 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">No hay movimientos registrados para este bien.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>