<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-arrow-path" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Devolver Bien de Mantenimiento') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Operaciones de Salida</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="mantenimientoForm" method="POST" action="{{ route('mantenimientos.store') }}" class="space-y-8" x-data="{
                destinoSeleccionado: @js(old('destino_id', $destinoOriginalId))
            }">
                @csrf
                <!-- Input hidden indicando que esto es una devolución (salida) -->
                <input type="hidden" name="devolviendo" value="1">

                <!-- Inputs Hiddens forzados de procedencia para Devolución -->
                <input type="hidden" name="procedencia_id" value="{{ $dticId }}">
                @if($areaMantenimiento)
                <input type="hidden" name="area_procedencia_id" value="{{ $areaMantenimiento->id }}">
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna Izquierda -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Datos del Bien a Devolver -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500 hover:shadow-brand-purple/5">
                            <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                            <div class="flex items-center gap-3 mb-8 relative z-10">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-cube" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Bien Seleccionado</h3>
                            </div>

                            <!-- Lista (Un solo elemento en devolución) -->
                            <div class="space-y-4 relative z-10">
                                <div class="p-5 border border-gray-100 dark:border-white/10 rounded-2xl bg-gray-50 dark:bg-[#1a1a1a] relative group transition-all duration-300">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Enviamos los datos del bien dentro del array que espera StoreMantenimientoRequest -->
                                        <input type="hidden" name="bienes[0][id]" value="{{ $mantenimiento->bien_id ?? $mantenimiento->bien_externo_id }}">
                                        <input type="hidden" name="bienes[0][tipo]" value="{{ $mantenimiento->bien_id ? 'dtic' : 'externo' }}">
                                        <input type="hidden" name="bienes[0][numero_bien]" value="{{ $mantenimiento->numero_bien }}">
                                        <input type="hidden" name="bienes[0][descripcion]" value="{{ $mantenimiento->descripcion }}">
                                        <input type="hidden" name="bienes[0][serial]" value="{{ $mantenimiento->serial }}">

                                        <div>
                                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">N° Bien</label>
                                            <div class="w-full h-11 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-400 opacity-80 cursor-not-allowed">
                                                {{ $mantenimiento->numero_bien }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">Descripción</label>
                                            <div class="w-full h-11 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-400 opacity-80 cursor-not-allowed truncate" title="{{ $mantenimiento->descripcion }}">
                                                {{ $mantenimiento->descripcion }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">Serial</label>
                                            <div class="w-full h-11 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-400 opacity-80 cursor-not-allowed">
                                                {{ $mantenimiento->serial ?? 'S/N' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 border-t border-gray-100 dark:border-white/10 pt-8 relative z-10">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-date-input-premium
                                        name="fecha"
                                        label="Fecha de Devolución"
                                        required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8 relative z-10">
                        <!-- Origen Fijo: DTIC -->
                        <div class="bg-linear-to-br from-brand-purple/10 to-transparent backdrop-blur-xl p-8 rounded-[2.5rem] shadow-sm border border-brand-purple/20 relative">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-brand-purple/20 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-wrench" class="w-6 h-6 text-brand-purple" />
                                </div>
                                <h3 class="text-xl font-black text-brand-purple dark:text-brand-lila uppercase tracking-widest">Sale de</h3>
                            </div>
                            <div class="pl-14">
                                <p class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">DTIC</p>
                                <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $areaMantenimiento?->nombre ?? 'Soporte Técnico - Mantenimiento' }}</p>
                            </div>
                        </div>

                        <!-- Destino (El destino original por defecto) -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-40'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-arrow-right-end-on-rectangle" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Destino</h3>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-6">
                                    <x-select-premium
                                        name="destino_id"
                                        label="Dpto. de Destino"
                                        placeholder="Depto. de destino"
                                        required
                                        icon="o-building-office-2"
                                        :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                        :value="old('destino_id', $destinoOriginalId)"
                                        @option-selected="destinoSeleccionado = $event.detail" />
                                </div>

                                <div x-show="destinoSeleccionado == {{ $dticId }}" x-transition style="display: none;">
                                    <x-select-premium
                                        name="area_id"
                                        label="Ubicación en DTIC (Área)"
                                        placeholder="Seleccione Área de destino"
                                        icon="o-map-pin"
                                        :options="\App\Models\Area::all()->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                        :value="old('area_id')"
                                        :required="false" />
                                </div>
                            </div>
                        </div>

                        <!-- Estado y Acta -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-clipboard-document-check" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Estado y Acta de Salida</h3>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">N° Orden Acta (Opcional)</label>
                                    <input type="text" name="n_orden_acta" value="{{ old('n_orden_acta') }}" class="w-full h-11 bg-white dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 transition-all placeholder-gray-400" placeholder="Ej: 0002" maxlength="4" pattern="\d{4}" title="Debe ser un número de 4 dígitos (Ej. 0002)">
                                </div>

                                <x-select-premium
                                    name="estatus_acta_id"
                                    label="Estatus del Acta"
                                    placeholder="Seleccione estatus"
                                    required
                                    icon="o-clock"
                                    :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                    :value="old('estatus_acta_id')" />

                                <x-date-input-premium
                                    name="fecha_acta"
                                    label="Fecha Redacción Acta (Opcional)"
                                    icon="o-calendar" />

                                <x-date-input-premium
                                    name="fecha_firma"
                                    label="Fecha de Firma (Opcional)"
                                    icon="o-pencil-square" />
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-emerald-500 to-teal-500 border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_40px_rgba(16,185,129,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-check" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" />
                                {{ __('Registrar Devolución') }}
                            </button>
                            <a href="{{ route('mantenimientos.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>