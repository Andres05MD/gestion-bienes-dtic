<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="relative w-14 h-14 group">
                    <div class="absolute inset-0 bg-brand-purple/40 rounded-xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative w-full h-full bg-dark-850 border border-white/5 rounded-xl flex items-center justify-center shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-linear-to-br from-brand-purple/10 to-transparent"></div>
                        <x-mary-icon name="o-cube-transparent" class="w-7 h-7 text-brand-lila group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500" />
                    </div>
                </div>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-none tracking-tight">
                        Detalles del Bien
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-dark-850/50 p-2 rounded-2xl border border-white/5 backdrop-blur-md">
                @can('editar bienes')
                <a href="{{ route('bienes.edit', $bien) }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-white text-brand-purple rounded-xl font-black text-[10px] uppercase tracking-widest hover:brightness-90 active:scale-95 transition-all shadow-lg">
                    <x-mary-icon name="o-pencil-square" class="w-4 h-4 mr-2" />
                    {{ __('Editar Registro') }}
                </a>
                @endcan
                <a href="{{ route('bienes.index') }}" class="px-6 py-3 text-[10px] font-black text-gray-400 hover:text-white uppercase tracking-widest transition-all">
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
                    <!-- Tarjeta de Nombre del Equipo -->
                    <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group text-center">
                        <div class="absolute inset-0 bg-linear-to-br from-brand-purple/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        <div class="relative z-10">
                            <p class="text-[9px] font-black text-brand-lila uppercase tracking-[0.3em] mb-3">Nombre del Equipo</p>
                            <h2 class="text-3xl font-black text-white tracking-tight leading-none">{{ $bien->equipo }}</h2>
                        </div>
                    </div>

                    <!-- Tarjeta de Identificación Principal -->
                    <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl group">

                        <div class="px-8 py-10 relative z-10 text-center">
                            <div class="w-24 h-24 mx-auto bg-dark-900 rounded-3xl border-4 border-dark-850 flex items-center justify-center shadow-2xl group-hover:scale-105 transition-transform duration-500">
                                <x-mary-icon name="o-device-tablet" class="w-12 h-12 text-brand-lila" />
                            </div>

                            <div class="mt-6">
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Número</p>
                                <h1 class="text-5xl font-black text-white tracking-tighter">{{ $bien->numero_bien }}</h1>
                            </div>

                            <div class="mt-10 pt-10 border-t border-white/5 text-center">
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-2">Categoría</p>
                                <p class="text-sm font-black text-brand-lila leading-tight">{{ $bien->categoria?->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Estado y Ubicación -->
                    <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 p-8 shadow-2xl space-y-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-linear-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        @if($bien->estado)
                        <div class="relative z-10 text-center">
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Estado Operativo</p>
                            <div class="inline-flex px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest {{ $bien->estado->badgeClasses() }} shadow-lg">
                                {{ $bien->estado->nombre }}
                            </div>
                        </div>
                        <div class="border-t border-white/5 pt-6 relative z-10 text-center">
                            @else
                            <div class="relative z-10 text-center">
                                @endif
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-3">Ubicación Actual</p>
                                <div class="flex justify-center items-center gap-2">
                                    <x-mary-icon name="o-map-pin" class="w-5 h-5 text-gray-400" />
                                    <p class="text-base font-black text-white leading-tight">{{ $bien->area?->nombre ?? 'DTIC' }}</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Columna Derecha: Especificaciones Técnicas -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- Panel de Especificaciones -->
                        <div class="bg-dark-850 rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl">
                            <div class="px-10 py-8 border-b border-white/5 bg-white/1 flex items-center justify-between">
                                <h3 class="text-sm font-black text-white uppercase tracking-[0.25em] flex items-center gap-3">
                                    <x-mary-icon name="o-adjustments-horizontal" class="w-6 h-6 text-brand-purple" />
                                    Especificaciones Técnicas
                                </h3>
                                <div class="flex gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-rose-500/20"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500/20"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/20"></div>
                                </div>
                            </div>

                            <div class="p-12">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-16 gap-x-12">
                                    <!-- Marca -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Marca</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $bien->marca ?? 'SIN MARCA' }}</p>
                                    </div>

                                    <!-- Modelo -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Modelo</p>
                                        <p class="text-2xl font-black text-white tracking-tight">{{ $bien->modelo ?? 'SIN MODELO' }}</p>
                                    </div>

                                    <!-- Serial -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Serial</p>
                                        <p class="text-2xl font-mono font-bold text-brand-lila tracking-wider">{{ $bien->serial ?? 'SIN SERIAL' }}</p>
                                    </div>

                                    <!-- Color -->
                                    <div class="group">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.25em] mb-2 group-hover:text-brand-lila transition-colors">Color</p>
                                        <div class="flex items-center gap-4 mt-1">
                                            <div @class([ 'w-5 h-5 rounded-full border border-white/20 shadow-sm' , 'bg-[#111]'=> strtolower($bien->color) == 'negro',
                                                'bg-[#fff]' => strtolower($bien->color) == 'blanco',
                                                'bg-[#444]' => strtolower($bien->color) != 'negro' && strtolower($bien->color) != 'blanco'
                                                ])></div>
                                            <p class="text-2xl font-black text-white tracking-tight">{{ $bien->color ?? 'ESTÁNDAR' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($bien->observaciones)
                                <div class="mt-16 pt-10 border-t border-white/5">
                                    <div class="bg-dark-900 rounded-3xl p-8 border border-white/5 relative group overflow-hidden">
                                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                                            <x-mary-icon name="o-document-text" class="w-24 h-24" />
                                        </div>
                                        <h4 class="text-[10px] font-black text-brand-lila uppercase tracking-[0.3em] mb-4">Notas y Observaciones</h4>
                                        <p class="text-gray-300 font-medium leading-relaxed italic pr-12">
                                            "{{ $bien->observaciones }}"
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
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Responsable</p>
                                    <p class="text-lg font-black text-white">{{ $bien->user?->name ?? 'Sistema' }}</p>
                                </div>
                            </div>

                            <div class="bg-dark-850/50 backdrop-blur-xl p-8 rounded-4xl border border-white/5 flex items-center gap-5 shadow-xl group hover:border-white/10 transition-colors">
                                <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:bg-brand-purple/10 transition-colors">
                                    <x-mary-icon name="o-calendar" class="w-7 h-7 text-gray-400 group-hover:text-brand-lila transition-colors" />
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Fecha de Registro</p>
                                    <p class="text-lg font-black text-white">{{ $bien->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Acción Inferior para móviles -->
                        <div class="lg:hidden">
                            @can('editar bienes')
                            <a href="{{ route('bienes.edit', $bien) }}" class="w-full inline-flex items-center justify-center px-8 py-5 bg-white text-brand-purple rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
                                <x-mary-icon name="o-pencil-square" class="w-5 h-5 mr-3" />
                                Editar Bien
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>