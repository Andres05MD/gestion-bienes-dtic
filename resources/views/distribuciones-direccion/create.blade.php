<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-plus-circle" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Nueva Distribución de Dirección') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Operaciones</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('distribuciones-direccion.store') }}" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Datos del Bien a Crear (Nuevo Bien Externo) -->
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-document-plus" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Datos del Nuevo Bien Externo</h3>
                            </div>

                            <div class="relative overflow-hidden rounded-2xl bg-brand-purple/5 border border-brand-purple/10 p-4 transition-all hover:bg-brand-purple/10 shadow-sm dark:shadow-none mb-8">
                                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-16 h-16 bg-brand-purple/10 rounded-full blur-xl transition-all"></div>
                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="w-10 h-10 rounded-xl bg-brand-purple/20 flex items-center justify-center text-brand-lila shadow-lg shadow-brand-purple/10">
                                        <x-mary-icon name="o-information-circle" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em] mb-0.5">Atención</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Al registrar una distribución de dirección, se está dando de <span class="text-gray-900 dark:text-white font-bold tracking-tight uppercase text-[9px]">ALTA DIRECTA</span> un bien en el inventario externo y en paralelo distribuyéndolo a un departamento.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                                <div class="space-y-6">
                                    <x-input-premium name="numero_bien" label="Número de Bien" placeholder="Ej: 12345" required icon="o-hashtag" :value="old('numero_bien', 'S/N')" />
                                    <x-input-premium name="descripcion" label="Descripción / Equipo" placeholder="Ej: Monitor LED" required icon="o-computer-desktop" :value="old('descripcion')" />
                                    <x-select-premium
                                        name="categoria_bien_id"
                                        label="Categoría"
                                        placeholder="Seleccione categoría"
                                        required
                                        icon="o-tag"
                                        :options="\App\Models\CategoriaBien::orderBy('nombre')->get()->map(fn($c) => ['value' => $c->id, 'label' => $c->nombre])->toArray()"
                                        :value="old('categoria_bien_id')" />
                                    <x-select-premium
                                        name="estado_id"
                                        label="Estado del Bien"
                                        placeholder="Seleccione estado"
                                        required
                                        icon="o-check-badge"
                                        :options="\App\Models\Estado::orderBy('nombre')->get()->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                        :value="old('estado_id')" />
                                </div>

                                <div class="space-y-6">
                                    <x-input-premium name="marca" label="Marca" placeholder="Ej: HP" icon="o-bookmark" :value="old('marca')" />
                                    <x-input-premium name="modelo" label="Modelo" placeholder="Ej: ProBook 450" icon="o-cube" :value="old('modelo')" />
                                    <x-input-premium name="serial" label="Serial" placeholder="Ej: S/N-123" icon="o-qr-code" :value="old('serial', 'S/N')" />
                                    <x-input-premium name="color" label="Color" placeholder="Ej: Negro" icon="o-swatch" :value="old('color')" />
                                </div>
                            </div>

                            <div class="mt-8 border-t border-gray-100 dark:border-white/10 pt-8 relative">
                                <div class="w-full sm:w-1/2">
                                    <x-date-input-premium name="fecha" label="Fecha de Distribución" required :value="old('fecha', now()->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div x-data="{ procedenciaSeleccionada: '{{ old('procedencia_id') }}' }" @set-selected-procedencia-id.window="procedenciaSeleccionada = $event.detail" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative z-20">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-building-office-2" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Ubicación</h3>
                            </div>
                            <div class="space-y-6">
                                <x-select-premium
                                    name="procedencia_id"
                                    label="Destino"
                                    placeholder="Depto. de destino"
                                    required
                                    icon="o-building-office-2"
                                    :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                    :value="old('procedencia_id')"
                                    @option-selected="procedenciaSeleccionada = $event.detail" />

                                <div x-show="procedenciaSeleccionada == {{ $dticId }}" x-transition>
                                    <x-select-premium
                                        name="area_id"
                                        label="Ubicación en DTIC (Área Destino)"
                                        placeholder="Seleccione Área de destino"
                                        icon="o-map-pin"
                                        :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                        :value="old('area_id')"
                                        :required="false" />
                                </div>
                            </div>
                        </div>

                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-check" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" />
                                {{ __('Registrar Distribución') }}
                            </button>
                            <a href="{{ route('distribuciones-direccion.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar y Volver') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>