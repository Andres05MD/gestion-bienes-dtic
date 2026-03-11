<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="relative w-14 h-14 group">
                    <div class="absolute inset-0 bg-brand-purple/40 rounded-xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative w-full h-full bg-dark-850 border border-white/5 rounded-xl flex items-center justify-center shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-linear-to-br from-brand-purple/10 to-transparent"></div>
                        <x-mary-icon name="o-trash" class="w-7 h-7 text-brand-lila group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500" />
                    </div>
                </div>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-none tracking-tight">
                        Detalles de Desincorporación
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-dark-850/50 p-2 rounded-2xl border border-white/5 backdrop-blur-md">
                @can('editar desincorporaciones')
                <a href="{{ route('desincorporaciones.edit', $desincorporacion) }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-white text-brand-purple rounded-xl font-black text-[10px] uppercase tracking-widest hover:brightness-90 active:scale-95 transition-all shadow-lg">
                    <x-mary-icon name="o-pencil-square" class="w-4 h-4 mr-2" />
                    {{ __('Editar Registro') }}
                </a>
                @endcan
                <a href="{{ route('desincorporaciones.index') }}" class="px-6 py-3 text-[10px] font-black text-gray-400 hover:text-white uppercase tracking-widest transition-all">
                    {{ __('Cerrar') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden">
        <!-- Background Orbs -->
        <div class="absolute top-0 right-0 -mr-40 -mt-40 w-[500px] h-[500px] bg-brand-purple/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-40 -mb-40 w-[500px] h-[500px] bg-brand-lila/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Columna Izquierda: Identidad y Visual -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Tarjeta de Identificación Principal -->
                    <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl group">
                        <div class="px-8 py-10 relative z-10 text-center">
                            <div class="w-24 h-24 mx-auto bg-dark-900 rounded-3xl border-4 border-dark-850 flex items-center justify-center shadow-2xl group-hover:scale-105 transition-transform duration-500">
                                <x-mary-icon name="o-trash" class="w-12 h-12 text-brand-lila" />
                            </div>

                            <div class="mt-6">
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Número de Desinc.</p>
                                <h1 class="text-4xl font-black text-white tracking-tighter">
                                    @if($bienesGrupo->count() > 1)
                                    Grupo ({{ $bienesGrupo->count() }} ítems)
                                    @else
                                    {{ $desincorporacion->numero_bien }}
                                    @endif
                                </h1>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Estatus y Procedencia -->
                    <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 p-8 shadow-2xl space-y-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-linear-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        @if($desincorporacion->estatusActa)
                        <div class="relative z-10 text-center">
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Estatus del Acta</p>
                            @php
                            $estatusColor = $desincorporacion->estatusActa->color ?? '#6b7280';
                            @endphp
                            <div class="inline-flex px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg"
                                @style([ "background-color: {$estatusColor}20" , "color: {$estatusColor}" , "border: 1px solid {$estatusColor}50"
                                ])>
                                {!! str_replace(' falta ', '<br>falta ', e($desincorporacion->estatusActa->nombre)) !!}
                            </div>
                        </div>
                        <div class="border-t border-white/5 pt-6 relative z-10 text-center">
                            @else
                            <div class="relative z-10 text-center">
                                @endif
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Procedencia</p>
                                <div class="flex justify-center items-center gap-2">
                                    <x-mary-icon name="o-building-office" class="w-5 h-5 text-gray-400" />
                                    <p class="text-base font-black text-white leading-tight">{{ $desincorporacion->procedencia?->nombre ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Detalles -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- Panel de Especificaciones -->
                        <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl">
                            <div class="px-10 py-8 border-b border-white/5 bg-white/1 flex items-center justify-between">
                                <h3 class="text-sm font-black text-white uppercase tracking-[0.25em] flex items-center gap-3">
                                    <x-mary-icon name="o-document-text" class="w-6 h-6 text-brand-purple" />
                                    Detalles de la Desincorporación
                                </h3>
                                <div class="flex gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-rose-500/20"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500/20"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/20"></div>
                                </div>
                            </div>

                            <div class="p-12">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-16 gap-x-12">
                                    <!-- Fecha -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Fecha</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $desincorporacion->fecha->format('d/m/Y') }}</p>
                                    </div>

                                    <!-- N Informe -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">N° Informe(s)</p>
                                        <p class="text-xl font-mono font-bold text-brand-lila tracking-wider">
                                            @php
                                            $todosLosInformesShow = $bienesGrupo->pluck('numero_informe')
                                            ->filter()
                                            ->flatMap(fn($i) => array_map('trim', explode(',', $i)))
                                            ->unique()
                                            ->implode(', ');
                                            @endphp
                                            {{ $todosLosInformesShow ?: 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Bienes Involucrados -->
                                <div class="mt-16 pt-10 border-t border-white/5">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-6">Bienes involucrados</p>
                                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                        @foreach($bienesGrupo as $bg)
                                        <div class="bg-dark-900/50 hover:bg-dark-900 rounded-[2.5rem] p-8 border border-white/5 relative group transition-all duration-300">
                                            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:scale-110 group-hover:opacity-20 transition-all duration-500 pointer-events-none">
                                                <x-mary-icon name="o-cube" class="w-16 h-16 text-brand-purple" />
                                            </div>
                                            <div class="relative z-10">
                                                <div class="flex items-start justify-between mb-3">
                                                    <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em]">{{ $bg->numero_bien }}</p>
                                                    {{-- Badge de estatus efectivo (individual o heredado) --}}
                                                    @php
                                                    $estatusEfectivo = $bg->estatus_acta_individual_id ? $bg->estatusActaIndividual : $bg->estatusActa;
                                                    $esIndividual = $bg->estatus_acta_individual_id !== null;
                                                    $colorEfectivo = $estatusEfectivo?->color ?? '#6b7280';
                                                    @endphp
                                                    <span class="px-2 py-1 text-[8px] leading-3 font-bold rounded-lg uppercase tracking-wider whitespace-nowrap"
                                                        @style([ "background-color: {$colorEfectivo}20" , "color: {$colorEfectivo}" , "border: 1px " . ($esIndividual ? 'solid' : 'dashed') . " {$colorEfectivo}40" ])
                                                        title="{{ $esIndividual ? 'Acta Individual' : 'Hereda Acta Grupal' }}">
                                                        {{ $esIndividual ? '● ' : '○ ' }}{{ $estatusEfectivo?->nombre ?? 'N/A' }}
                                                    </span>
                                                </div>
                                                <p class="text-lg font-black text-white leading-tight tracking-tight mb-4 pr-12">{{ $bg->descripcion }}</p>

                                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
                                                    <div class="inline-flex items-center gap-2 bg-white/5 px-4 py-2.5 rounded-2xl border border-white/5 shadow-sm">
                                                        <x-mary-icon name="o-qr-code" class="w-4 h-4 text-gray-500" />
                                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">SN: <span class="text-gray-200 ml-1">{{ $bg->serial ?: 'N/A' }}</span></span>
                                                    </div>
                                                    @if($bg->numero_informe)
                                                    @php
                                                    $informesDelBien = array_map('trim', explode(',', $bg->numero_informe));
                                                    $informeMostrar = count($informesDelBien) > 1 ? ($informesDelBien[$loop->index] ?? $informesDelBien[0]) : $bg->numero_informe;
                                                    @endphp
                                                    <div class="inline-flex items-center gap-2 bg-brand-purple/10 px-4 py-2.5 rounded-2xl border border-brand-purple/20 shadow-sm">
                                                        <x-mary-icon name="o-document-text" class="w-4 h-4 text-brand-lila" />
                                                        <span class="text-[10px] font-bold text-brand-lila uppercase tracking-wider">Informe: <span class="text-white ml-1 font-mono">{{ $informeMostrar }}</span></span>
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Formulario inline para cambiar estatus individual --}}
                                                @if($bienesGrupo->count() > 1)
                                                @can('editar desincorporaciones')
                                                <div class="pt-4 border-t border-white/5" x-data="{ abierto: false }">
                                                    <button type="button" @click="abierto = !abierto" class="text-[9px] font-bold text-gray-500 hover:text-brand-lila uppercase tracking-widest transition-colors flex items-center gap-1 cursor-pointer">
                                                        <x-mary-icon name="o-pencil" class="w-3 h-3" />
                                                        <span x-text="abierto ? 'Cerrar' : 'Cambiar Acta Individual'"></span>
                                                    </button>
                                                    <div x-show="abierto" x-transition class="mt-3">
                                                        <form method="POST" action="{{ route('desincorporaciones.estatus-individual', $bg) }}" class="flex items-start gap-3">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="flex-1">
                                                                <x-select-premium 
                                                                    name="estatus_acta_individual_id" 
                                                                    :options="\App\Models\EstatusActa::all()->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                                                    :value="$bg->estatus_acta_individual_id"
                                                                    placeholder="— Heredar Grupal —"
                                                                    icon="o-clipboard-document-check"
                                                                    :searchable="false"
                                                                />
                                                            </div>
                                                            <button type="submit" class="px-5 py-3 h-12 bg-linear-to-r from-brand-lila to-brand-purple text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-300 cursor-pointer whitespace-nowrap shadow-lg shadow-brand-purple/20">
                                                                Guardar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @endcan
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if($desincorporacion->observaciones)
                                <div class="mt-16 pt-10 border-t border-white/5">
                                    <div class="bg-dark-900 rounded-3xl p-8 border border-white/5 relative group overflow-hidden">
                                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                                            <x-mary-icon name="o-document-text" class="w-24 h-24" />
                                        </div>
                                        <h4 class="text-[10px] font-black text-brand-lila uppercase tracking-[0.3em] mb-4">Notas y Observaciones</h4>
                                        <p class="text-gray-300 font-medium leading-relaxed italic pr-12">
                                            "{{ $desincorporacion->observaciones }}"
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Metadatos de Sistema -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-dark-850/50 backdrop-blur-xl p-8 rounded-4xl border border-white/5 flex items-center gap-5 shadow-xl group hover:border-white/10 transition-colors">
                                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:bg-brand-purple/10 transition-colors">
                                    <x-mary-icon name="o-user" class="w-7 h-7 text-gray-400 group-hover:text-brand-lila transition-colors" />
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Registrado por</p>
                                    <p class="text-lg font-black text-white">{{ $desincorporacion->user?->name ?? 'Sistema' }}</p>
                                </div>
                            </div>

                            <div class="bg-dark-850/50 backdrop-blur-xl p-8 rounded-4xl border border-white/5 flex items-center gap-5 shadow-xl group hover:border-white/10 transition-colors">
                                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:bg-brand-purple/10 transition-colors">
                                    <x-mary-icon name="o-calendar" class="w-7 h-7 text-gray-400 group-hover:text-brand-lila transition-colors" />
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Fecha de Registro</p>
                                    <p class="text-lg font-black text-white">{{ $desincorporacion->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Acción Inferior para móviles -->
                        <div class="lg:hidden">
                            @can('editar desincorporaciones')
                            <a href="{{ route('desincorporaciones.edit', $desincorporacion) }}" class="w-full inline-flex items-center justify-center px-8 py-5 bg-white text-brand-purple rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
                                <x-mary-icon name="o-pencil-square" class="w-5 h-5 mr-3" />
                                Editar Registro
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>