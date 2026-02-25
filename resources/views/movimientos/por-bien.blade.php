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
                        @php
                        $esExterno = $bien instanceof \App\Models\BienExterno;
                        $tuvoOrigenDtic = $todosMovimientos->contains(fn($m) => $m->departamentoOrigen?->nombre === 'DTIC');
                        $mostrarProcedencia = $esExterno && ($bien->departamentoOrigen?->nombre === 'DTIC' || $tuvoOrigenDtic);
                        $nombreProcedencia = $bien->departamentoOrigen?->nombre ?? 'DTIC';
                        @endphp
                        @if($mostrarProcedencia)
                        <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase">
                            <x-mary-icon name="o-building-office" class="w-3 h-3" />
                            Procedencia: {{ $nombreProcedencia }}
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
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-sm font-bold text-white">{{ $bien->numero_bien }}</p>
                            @if($bien instanceof \App\Models\Bien || $todosMovimientos->contains(fn($m) => $m->departamentoOrigen?->nombre === 'DTIC'))
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[8px] font-black rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 uppercase tracking-tighter" title="Asignado desde DTIC">
                                Asg Dtic
                            </span>
                            @endif
                        </div>
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
            <div class="relative py-4">
                {{-- Línea vertical perfectamente alineada --}}
                <div class="absolute left-[38px] md:left-[42px] top-6 bottom-6 w-0.5 bg-gradient-to-b from-brand-purple/70 via-brand-lila/40 to-transparent shadow-[0_0_10px_rgba(152,87,211,0.5)]"></div>

                <div class="space-y-10">
                    @forelse ($todosMovimientos as $index => $mov)
                    @php
                    $isFirst = $index === 0;
                    $tipoColor = match($mov->tipo_movimiento) {
                    'transferencia' => 'from-blue-500 to-blue-600 shadow-blue-500/30',
                    'desincorporacion' => 'from-rose-500 to-rose-600 shadow-rose-500/30',
                    'distribucion' => 'from-emerald-500 to-emerald-600 shadow-emerald-500/30',
                    'mantenimiento' => 'from-amber-500 to-amber-600 shadow-amber-500/30',
                    'mantenimiento_devolucion' => 'from-teal-500 to-teal-600 shadow-teal-500/30',
                    'registro' => 'from-brand-purple to-brand-lila shadow-brand-purple/30',
                    default => 'from-gray-500 to-gray-600 shadow-gray-500/30',
                    };
                    $tipoBg = match($mov->tipo_movimiento) {
                    'transferencia' => 'bg-blue-500/5 border-blue-500/30 hover:shadow-blue-500/20',
                    'desincorporacion' => 'bg-rose-500/5 border-rose-500/30 hover:shadow-rose-500/20',
                    'distribucion' => 'bg-emerald-500/5 border-emerald-500/30 hover:shadow-emerald-500/20',
                    'mantenimiento' => 'bg-amber-500/5 border-amber-500/30 hover:shadow-amber-500/20',
                    'mantenimiento_devolucion' => 'bg-teal-500/5 border-teal-500/30 hover:shadow-teal-500/20',
                    'registro' => 'bg-brand-purple/5 border-brand-purple/30 hover:shadow-brand-purple/20',
                    default => 'bg-gray-500/5 border-gray-500/30 hover:shadow-gray-500/20',
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
                    <div class="relative flex items-start gap-6 md:gap-8">
                        {{-- Punto en la línea de tiempo --}}
                        <div class="relative z-10 flex-shrink-0 w-12 md:w-14 h-12 md:h-14 rounded-full bg-dark-900 border-[3px] border-dark-800 flex items-center justify-center shadow-xl {{ $isFirst ? 'ring-4 ring-brand-purple/20' : '' }}">
                            <div class="w-8 md:w-10 h-8 md:h-10 rounded-full bg-gradient-to-tr {{ $tipoColor }} flex items-center justify-center shadow-lg">
                                <x-mary-icon name="{{ $tipoIcon }}" class="w-4 md:w-5 h-4 md:h-5 text-white" />
                            </div>
                        </div>

                        {{-- Contenido del Historial --}}
                        <div class="flex-1 mt-1 md:mt-2">
                            <div class="relative w-full {{ $tipoBg }} border rounded-2xl p-5 md:p-6 shadow-lg backdrop-blur-sm transition-all duration-300">
                                {{-- Triangulito decorativo que apunta a la linea de tiempo --}}
                                <div class="absolute w-4 h-4 rotate-45 border-l border-b bg-dark-900 -left-[9px] top-6 {{ str_replace(['bg-', '/5'], ['border-', '/30'], $tipoBg) }} hidden md:block"></div>

                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-5">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-black text-white uppercase tracking-widest">{{ $mov->etiqueta_tipo }}</span>
                                            @if($isFirst)
                                            <span class="px-2 py-0.5 rounded-full bg-brand-purple/20 text-brand-lila text-[9px] font-bold uppercase tracking-wider animate-pulse">Último</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <x-mary-icon name="o-calendar" class="w-3.5 h-3.5 text-gray-500" />
                                            <p class="text-[11px] text-gray-400 font-medium">{{ $mov->fecha->format('d/m/Y') }} <span class="text-gray-600 mx-1">•</span> {{ ucfirst($mov->created_at->diffForHumans()) }}</p>
                                        </div>
                                    </div>
                                    @if($mov->user)
                                    <div class="flex items-center gap-2.5 bg-dark-800/50 px-3 py-1.5 rounded-xl border border-dark-700/50">
                                        <div class="h-6 w-6 rounded-full bg-gradient-to-tr from-brand-purple to-brand-lila flex items-center justify-center text-white font-bold text-[10px] shadow-sm">
                                            {{ substr($mov->user->name, 0, 1) }}
                                        </div>
                                        <span class="text-[11px] text-gray-300 font-medium">{{ $mov->user->name }}</span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Flujo Origen → Destino --}}
                                @if($mov->departamentoOrigen || $mov->departamentoDestino)
                                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4 p-4 rounded-xl bg-dark-900/40 border border-dark-800/60">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <x-mary-icon name="o-arrow-up-right" class="w-3 h-3 text-gray-500" />
                                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Origen</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-200 leading-tight">{{ $mov->departamentoOrigen?->nombre ?? '—' }}</p>
                                            @if($mov->departamentoOrigen?->nombre === 'DTIC')
                                            <span class="inline-flex items-center px-1.5 py-0.5 text-[8px] font-black rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 uppercase tracking-tighter" title="Asignado desde DTIC">
                                                Asg Dtic
                                            </span>
                                            @endif
                                        </div>
                                        @if($mov->areaOrigen)
                                        <p class="text-[11px] text-brand-lila font-medium mt-1 leading-tight flex items-center gap-1">
                                            <x-mary-icon name="o-hashtag" class="w-3 h-3" />
                                            {{ $mov->areaOrigen->nombre }}
                                        </p>
                                        @endif
                                    </div>

                                    <div class="hidden md:flex items-center justify-center w-8 h-8 rounded-full bg-dark-800 border border-dark-700 mx-2">
                                        <x-mary-icon name="o-chevron-right" class="w-4 h-4 text-gray-400" />
                                    </div>

                                    <x-mary-icon name="o-arrow-down" class="w-5 h-5 text-gray-500 my-1 md:hidden" />

                                    <div class="flex-1">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <x-mary-icon name="o-map-pin" class="w-3 h-3 text-gray-500" />
                                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Destino</p>
                                        </div>
                                        <p class="text-sm font-bold text-gray-200 leading-tight">{{ $mov->departamentoDestino?->nombre ?? '—' }}</p>
                                        @if($mov->areaDestino)
                                        <p class="text-[11px] text-brand-lila font-medium mt-1 leading-tight flex items-center gap-1">
                                            <x-mary-icon name="o-hashtag" class="w-3 h-3" />
                                            {{ $mov->areaDestino->nombre }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($mov->descripcion)
                                <div class="mt-4 pt-4 border-t border-dark-800/80">
                                    <p class="text-[13px] leading-relaxed text-gray-300 flex items-start gap-2">
                                        <x-mary-icon name="o-chat-bubble-bottom-center-text" class="w-4 h-4 text-gray-500 mt-0.5 shrink-0" />
                                        {{ $mov->descripcion }}
                                    </p>
                                </div>
                                @endif
                            </div>
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