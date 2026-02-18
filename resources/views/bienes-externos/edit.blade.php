<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-pencil-square" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Editar Bien Externo') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">
                    Activo: <span class="text-brand-lila">#{{ $bienExterno->numero_bien }}</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('bienes-externos.update', $bienExterno) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna Izquierda: Información Principal -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <!-- Inner Shine -->
                            <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                            
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-information-circle" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Detalles Actualizados</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-input-premium
                                    name="equipo"
                                    label="Nombre del Dispositivo"
                                    :value="$bienExterno->equipo"
                                    placeholder="Ej: Monitor"
                                    required
                                    autofocus
                                    icon="o-cpu-chip"
                                />

                                <x-select-premium
                                    name="estado_id"
                                    label="Estado"
                                    placeholder="Seleccione estado"
                                    required
                                    icon="o-shield-check"
                                    :options="$estados->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                    :value="old('estado_id', $bienExterno->estado_id)"
                                />

                                <x-input-premium
                                    name="marca"
                                    label="Marca"
                                    :value="$bienExterno->marca"
                                    placeholder="Ej: Phillips"
                                    icon="o-tag"
                                />

                                <x-input-premium
                                    name="modelo"
                                    label="Modelo"
                                    :value="$bienExterno->modelo"
                                    placeholder="Ej: IntelliVue MX400"
                                    icon="o-cube"
                                />

                                <x-input-premium
                                    name="serial"
                                    label="Número de Serie"
                                    :value="$bienExterno->serial"
                                    placeholder="S/N: USX1234567"
                                    icon="o-qr-code"
                                />

                                <x-input-premium
                                    name="color"
                                    label="Color / Acabado"
                                    :value="$bienExterno->color"
                                    placeholder="Ej: Gris Médico"
                                    icon="o-swatch"
                                />
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-document-text" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Notas Adicionales</h3>
                            </div>

                            <div class="space-y-4">
                                <label for="observaciones" class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1">
                                    Comentarios y Observaciones
                                </label>
                                <div class="relative group">
                                    <textarea 
                                        id="observaciones" 
                                        name="observaciones" 
                                        rows="4" 
                                        placeholder="Ingrese cualquier detalle relevante..."
                                        class="block w-full px-5 py-4 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-gray-50 dark:focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-sm dark:shadow-none min-h-[120px]"
                                    >{{ old('observaciones', $bienExterno->observaciones) }}</textarea>
                                </div>
                                <x-input-error :messages="$errors->get('observaciones')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Clasificación y Ubicación -->
                    <div class="space-y-8">
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative z-20">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-adjustments-horizontal" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Clasificación</h3>
                            </div>

                            <div class="space-y-6">
                                <!-- Categoría del Bien (Nueva) -->
                                <x-select-premium
                                    name="categoria_bien_id"
                                    label="Categoría del Bien"
                                    placeholder="Seleccione una categoría"
                                    icon="o-bookmark"
                                    :options="$categorias->map(fn($c) => ['value' => $c->id, 'label' => $c->nombre])->toArray()"
                                    :value="old('categoria_bien_id', $bienExterno->categoria_bien_id)"
                                />

                                <!-- Número de Bien -->
                                <x-input-premium
                                    name="numero_bien"
                                    label="Número de Patrimonio"
                                    :value="$bienExterno->numero_bien"
                                    placeholder="Ej: DTIC-2024-001"
                                    icon="o-hashtag"
                                />
                            </div>
                        </div>

                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative z-10">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-map-pin" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Localización</h3>
                            </div>

                            <x-select-premium
                                name="departamento_id"
                                label="Departamento / Servicio"
                                placeholder="Seleccione un departamento"
                                required
                                icon="o-building-office-2"
                                :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                :value="old('departamento_id', $bienExterno->departamento_id)"
                            />
                        </div>

                        <!-- Action Buttons -->
                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-arrow-path" class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" />
                                {{ __('Actualizar Información') }}
                            </button>
                            <a href="{{ route('bienes-externos.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar Cambios') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
