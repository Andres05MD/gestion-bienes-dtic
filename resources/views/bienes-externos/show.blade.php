<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Detalle del Bien Externo') }} <span class="text-brand-lila">#{{ $bienExterno->numero_bien }}</span>
            </h2>
            <div class="flex items-center gap-3">
                @can('editar bienes externos')
                    <a href="{{ route('bienes-externos.edit', $bienExterno) }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        {{ __('Editar') }}
                    </a>
                @endcan
                <a href="{{ route('bienes-externos.index') }}" class="text-sm font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest text-[10px]">
                    {{ __('← Volver') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-900/50 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-white/5 relative backdrop-blur-sm">
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 bg-brand-purple/20 rounded-full blur-[100px] pointer-events-none opacity-50"></div>
                <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-96 h-96 bg-brand-lila/10 rounded-full blur-[100px] pointer-events-none opacity-30"></div>

                <div class="p-8 lg:p-12 relative z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                        
                        <!-- Columna Principal: Detalles del Equipo -->
                        <div class="lg:col-span-8 space-y-8">
                            <!-- Tarjeta Principal -->
                            <div class="relative group">
                                <div class="absolute -inset-0.5 bg-linear-to-r from-brand-lila/30 to-brand-purple/30 rounded-3xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                                <div class="relative bg-dark-850 p-8 lg:p-10 rounded-3xl border border-white/5 shadow-2xl">
                                    <div class="flex items-center justify-between mb-10">
                                        <h3 class="text-sm font-black text-white uppercase tracking-[0.25em] flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-brand-purple/20 flex items-center justify-center text-brand-lila shadow-lg shadow-brand-purple/10">
                                                <x-mary-icon name="o-cpu-chip" class="w-5 h-5" />
                                            </div>
                                            Detalles del Equipo
                                        </h3>
                                        @if($bienExterno->estado)
                                            <span class="px-4 py-1.5 inline-flex text-[10px] bg-dark-800 border border-white/5 rounded-full uppercase font-bold tracking-widest {{ $bienExterno->estado->badgeClasses() }}">
                                                {{ $bienExterno->estado->nombre }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="space-y-10">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-3">Equipo</p>
                                            <p class="text-3xl lg:text-4xl font-black text-white leading-tight tracking-tight">{{ $bienExterno->equipo }}</p>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-8 pt-8 border-t border-white/5">
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Marca</p>
                                                <p class="text-base font-bold text-gray-200">{{ $bienExterno->marca ?? '—' }}</p>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Modelo</p>
                                                <p class="text-base font-bold text-gray-200">{{ $bienExterno->modelo ?? '—' }}</p>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Serial / S/N</p>
                                                <code class="text-sm font-mono text-brand-lila bg-brand-lila/5 px-3 py-1 rounded-lg border border-brand-lila/10 inline-block">{{ $bienExterno->serial ?? '—' }}</code>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Color</p>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-3 h-3 rounded-full bg-gray-700 border border-gray-600"></span>
                                                    <p class="text-base font-bold text-gray-200">{{ $bienExterno->color ?? '—' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            @if($bienExterno->observaciones)
                                <div class="bg-dark-850 p-8 rounded-3xl border border-white/5 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-6 opacity-[0.03] pointer-events-none">
                                        <x-mary-icon name="o-document-text" class="w-32 h-32" />
                                    </div>
                                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                        <x-mary-icon name="o-bars-3-bottom-left" class="w-4 h-4 text-brand-purple" />
                                        Notas Adicionales
                                    </h3>
                                    <p class="text-base text-gray-300 leading-relaxed font-medium pl-4 border-l-2 border-brand-purple/30">
                                        {{ $bienExterno->observaciones }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Columna Lateral -->
                        <div class="lg:col-span-4 space-y-6">
                            <!-- Identificación -->
                            <div class="bg-dark-850 p-8 rounded-3xl border border-white/5 shadow-xl relative overflow-hidden group hover:border-brand-lila/20 transition-colors">
                                <div class="absolute inset-0 bg-linear-to-b from-brand-purple/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8 relative z-10">Identificación</h3>
                                
                                <div class="space-y-8 relative z-10">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2">Categoría</p>
                                        <div class="inline-flex items-center px-4 py-2 rounded-xl bg-dark-800 border border-white/5 text-sm font-bold text-white shadow-inner">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-lila mr-2 shadow-[0_0_8px_currentColor]"></span>
                                            {{ $bienExterno->categoria?->nombre ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-1">Número de Bien</p>
                                        <p class="text-5xl font-black text-transparent bg-clip-text bg-linear-to-r from-brand-lila to-white tracking-tighter drop-shadow-lg">
                                            {{ $bienExterno->numero_bien }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="bg-dark-850 p-8 rounded-3xl border border-white/5 shadow-xl">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Ubicación Actual</h3>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-dark-800 flex items-center justify-center border border-white/5 shrink-0">
                                        <x-mary-icon name="o-building-office-2" class="w-5 h-5 text-gray-300" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-1">Departamento / Servicio</p>
                                        <p class="text-lg font-bold text-white">{{ $bienExterno->departamento?->nombre ?? 'No asignado' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadatos -->
                            <div class="px-6 py-6 rounded-3xl border border-dashed border-white/10 bg-white/2">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Creado</span>
                                        <span class="text-xs font-mono text-gray-300">{{ $bienExterno->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="h-px bg-white/5"></div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Editor</span>
                                        <span class="text-xs font-bold text-brand-lila">{{ $bienExterno->user?->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
