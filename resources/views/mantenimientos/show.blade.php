<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Detalle de ' . ucfirst($mantenimiento->tipo_movimiento)) }} <span class="text-brand-lila">#{{ $mantenimiento->numero_bien }}</span>
            </h2>
            <div class="flex items-center gap-3">
                @can('editar transferencias')
                <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ __('Editar') }}
                </a>
                @endcan
                <a href="{{ route('mantenimientos.index') }}" class="text-sm font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest text-[10px]">
                    {{ __('← Volver') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-900/50 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-white/5 relative backdrop-blur-sm">
                <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 bg-brand-purple/20 rounded-full blur-[100px] pointer-events-none opacity-50"></div>
                <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-96 h-96 bg-brand-lila/10 rounded-full blur-[100px] pointer-events-none opacity-30"></div>

                <div class="p-8 lg:p-12 relative z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                        <div class="lg:col-span-8 space-y-8">
                            <div class="relative group">
                                <div class="absolute -inset-0.5 bg-linear-to-r from-brand-lila/30 to-brand-purple/30 rounded-3xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                                <div class="relative bg-dark-850 p-8 lg:p-10 rounded-3xl border border-white/5 shadow-2xl">
                                    <div class="flex items-center justify-between mb-10">
                                        <h3 class="text-sm font-black text-white uppercase tracking-[0.25em] flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-brand-purple/20 flex items-center justify-center text-brand-lila shadow-lg shadow-brand-purple/10">
                                                <x-mary-icon name="o-information-circle" class="w-5 h-5" />
                                            </div>
                                            Datos del Bien
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase tracking-widest {{ $mantenimiento->tipo_movimiento === 'entrada' ? 'bg-brand-lila/20 text-brand-lila' : 'bg-emerald-500/20 text-emerald-400' }}">
                                                {{ $mantenimiento->tipo_movimiento }}
                                            </span>
                                            <span class="px-4 py-1.5 inline-flex text-[10px] bg-dark-800 border border-white/5 rounded-full uppercase font-bold tracking-widest text-white">
                                                {{ $mantenimiento->estatusActa?->nombre ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-10">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-3">Descripción</p>
                                            <p class="text-3xl lg:text-4xl font-black text-white leading-tight tracking-tight">{{ $mantenimiento->descripcion }}</p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-8 pt-8 border-t border-white/5">
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">N° Bien</p>
                                                <code class="text-sm font-mono text-brand-lila bg-brand-lila/5 px-3 py-1 rounded-lg border border-brand-lila/10 inline-block">{{ $mantenimiento->numero_bien }}</code>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Serial</p>
                                                <p class="text-base font-bold text-gray-200">{{ $mantenimiento->serial ?? '—' }}</p>
                                            </div>
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Movimiento Registrado el</p>
                                                <p class="text-base font-bold text-gray-200">{{ $mantenimiento->fecha->format('d/m/Y') }}</p>
                                            </div>
                                            @if($mantenimiento->n_orden_acta)
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">N° de Acta</p>
                                                <p class="text-base font-bold text-gray-200">{{ $mantenimiento->n_orden_acta }}</p>
                                            </div>
                                            @endif
                                            @if($mantenimiento->fecha_acta)
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Fecha de Acta</p>
                                                <p class="text-base font-bold text-gray-200">{{ $mantenimiento->fecha_acta->format('d/m/Y') }}</p>
                                            </div>
                                            @endif
                                            <div class="space-y-2">
                                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">Fecha de Firma</p>
                                                <p class="text-base font-bold text-gray-200">{{ $mantenimiento->fecha_firma?->format('d/m/Y') ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-4 space-y-6">
                            <div class="bg-dark-850 p-8 rounded-3xl border border-white/5 shadow-xl">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Ubicación del Movimiento</h3>
                                <div class="space-y-6">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20 shrink-0">
                                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-1">Procedencia</p>
                                            <p class="text-lg font-bold text-white">{{ $mantenimiento->procedencia?->nombre ?? 'DTIC' }}</p>
                                            @if($mantenimiento->area_procedencia_id)
                                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Área: {{ \App\Models\Area::find($mantenimiento->area_procedencia_id)?->nombre ?? '' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20 shrink-0">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-1">Destino</p>
                                            <p class="text-lg font-bold text-white">{{ $mantenimiento->destino?->nombre ?? 'DTIC' }}</p>
                                            @if($mantenimiento->area_id)
                                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Área: {{ \App\Models\Area::find($mantenimiento->area_id)?->nombre ?? '' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-6 rounded-3xl border border-dashed border-white/10 bg-white/2">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Creado</span>
                                        <span class="text-xs font-mono text-gray-300">{{ $mantenimiento->created_at->format('d/m/Y h:i A') }}</span>
                                    </div>
                                    <div class="h-px bg-white/5"></div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Registrado por</span>
                                        <span class="text-xs font-bold text-brand-lila">{{ $mantenimiento->user?->name }}</span>
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