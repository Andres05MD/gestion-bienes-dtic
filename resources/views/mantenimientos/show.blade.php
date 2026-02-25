<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="relative w-14 h-14 group">
                    <div class="absolute inset-0 bg-brand-purple/40 rounded-xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative w-full h-full bg-dark-850 border border-white/5 rounded-xl flex items-center justify-center shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-linear-to-br from-brand-purple/10 to-transparent"></div>
                        <x-mary-icon name="o-information-circle" class="w-7 h-7 text-brand-lila group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500" />
                    </div>
                </div>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-none tracking-tight">
                        Detalle de {{ ucfirst($mantenimiento->tipo_movimiento) }}
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-dark-850/50 p-2 rounded-2xl border border-white/5 backdrop-blur-md">
                @can('editar transferencias')
                <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-white text-brand-purple rounded-xl font-black text-[10px] uppercase tracking-widest hover:brightness-90 active:scale-95 transition-all shadow-lg">
                    <x-mary-icon name="o-pencil-square" class="w-4 h-4 mr-2" />
                    {{ __('Editar Registro') }}
                </a>
                @endcan
                <a href="{{ route('mantenimientos.index') }}" class="px-6 py-3 text-[10px] font-black text-gray-400 hover:text-white uppercase tracking-widest transition-all">
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
                                <x-mary-icon name="o-information-circle" class="w-12 h-12 text-brand-lila" />
                            </div>

                            <div class="mt-6 flex flex-col items-center">
                                <span class="px-3 py-1 mb-2 text-[9px] font-black rounded-full uppercase tracking-widest {{ $mantenimiento->tipo_movimiento === 'entrada' ? 'bg-brand-lila/20 text-brand-lila' : 'bg-emerald-500/20 text-emerald-400' }}">
                                    {{ $mantenimiento->tipo_movimiento }}
                                </span>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Número de Bien</p>
                                <h1 class="text-5xl font-black text-white tracking-tighter">
                                    {{ $mantenimiento->numero_bien }}
                                </h1>
                            </div>

                            <div class="mt-10 pt-10 border-t border-white/5 text-center">
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-2">Estatus Acta</p>
                                <p class="text-sm font-black text-brand-lila leading-tight">{{ $mantenimiento->estatusActa?->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Estatus, Procedencia y Destino -->
                    <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 p-8 shadow-2xl space-y-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-linear-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        @if($mantenimiento->estatusActa)
                        <div class="relative z-10 text-center">
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Estatus del Acta</p>
                            @php
                            $estatusColor = $mantenimiento->estatusActa->color ?? '#6b7280';
                            @endphp
                            <div class="inline-flex px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg"
                                @style([ "background-color: {$estatusColor}20" , "color: {$estatusColor}" , "border: 1px solid {$estatusColor}50"
                                ])>
                                {!! str_replace(' falta ', '<br>falta ', e($mantenimiento->estatusActa->nombre)) !!}
                            </div>
                        </div>
                        <div class="border-t border-white/5 pt-6 relative z-10 text-center">
                            @else
                            <div class="relative z-10 text-center">
                                @endif
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Procedencia</p>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="flex justify-center items-center gap-2">
                                        <x-mary-icon name="o-arrow-up-right" class="w-5 h-5 text-gray-400" />
                                        <p class="text-base font-black text-white leading-tight">{{ $mantenimiento->procedencia?->nombre ?? 'DTIC' }}</p>
                                    </div>
                                    @if($mantenimiento->area_procedencia_id)
                                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Área: {{ \App\Models\Area::find($mantenimiento->area_procedencia_id)?->nombre ?? '' }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="border-t border-white/5 pt-6 relative z-10 text-center">
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Destino</p>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="flex justify-center items-center gap-2">
                                        <x-mary-icon name="o-map-pin" class="w-5 h-5 text-gray-400" />
                                        <p class="text-base font-black text-white leading-tight">{{ $mantenimiento->destino?->nombre ?? 'DTIC' }}</p>
                                    </div>
                                    @if($mantenimiento->area_id)
                                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Área: {{ \App\Models\Area::find($mantenimiento->area_id)?->nombre ?? '' }}</p>
                                    @endif
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
                                    Detalles del Movimiento
                                </h3>
                                <div class="flex gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-rose-500/20"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500/20"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/20"></div>
                                </div>
                            </div>

                            <div class="p-12">
                                <div class="mb-12">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-3">Descripción</p>
                                    <p class="text-3xl lg:text-4xl font-black text-white leading-tight tracking-tight">{{ $mantenimiento->descripcion }}</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-16 gap-x-12 border-t border-white/5 pt-12">
                                    <!-- Serial -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Serial</p>
                                        <p class="text-2xl font-mono font-bold text-brand-lila tracking-wider">{{ $mantenimiento->serial ?? '—' }}</p>
                                    </div>

                                    <!-- Fecha Registro Movimiento -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Fecha de Movimiento</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $mantenimiento->fecha->format('d/m/Y') }}</p>
                                    </div>

                                    @if($mantenimiento->n_orden_acta)
                                    <!-- N Orden Acta -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">N° de Acta</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $mantenimiento->n_orden_acta }}</p>
                                    </div>
                                    @endif

                                    @if($mantenimiento->fecha_acta)
                                    <!-- Fecha Acta -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Fecha de Acta</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $mantenimiento->fecha_acta->format('d/m/Y') }}</p>
                                    </div>
                                    @endif

                                    <!-- Fecha Firma -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Fecha de Firma</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $mantenimiento->fecha_firma?->format('d/m/Y') ?? '—' }}</p>
                                    </div>
                                </div>
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
                                    <p class="text-lg font-black text-white">{{ $mantenimiento->user?->name ?? 'Sistema' }}</p>
                                </div>
                            </div>

                            <div class="bg-dark-850/50 backdrop-blur-xl p-8 rounded-4xl border border-white/5 flex items-center gap-5 shadow-xl group hover:border-white/10 transition-colors">
                                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:bg-brand-purple/10 transition-colors">
                                    <x-mary-icon name="o-calendar" class="w-7 h-7 text-gray-400 group-hover:text-brand-lila transition-colors" />
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Fecha de Registro</p>
                                    <p class="text-lg font-black text-white">{{ $mantenimiento->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Acción Inferior para móviles -->
                        <div class="lg:hidden">
                            @can('editar transferencias')
                            <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="w-full inline-flex items-center justify-center px-8 py-5 bg-white text-brand-purple rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
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