<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-pencil-square" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Editar Registro de Mantenimiento') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">
                    Bien: <span class="text-brand-lila">#{{ $mantenimiento->numero_bien }}</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('mantenimientos.update', $mantenimiento) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Importar Bien -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-40'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-arrow-down-tray" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Importar Bien</h3>
                            </div>

                            <div x-data="{
                                openBien: false,
                                openGlobal: false,
                                globalSearch: '',
                                bienSearch: '',
                                tipoBien: 'externo',
                                bienId: '{{ $mantenimiento->bien_externo_id ?? '' }}',
                                globalResults: [],
                                bienResults: [],
                                debounceTimer: null,
                                bienDebounceTimer: null,
                                loading: false,
                                bienLoading: false,

                                get selectedBienLabel() {
                                    if (!this.bienId) return 'Buscar un bien...';
                                    let bien = this.bienResults.find(b => b.id == this.bienId && b.tipo === 'externo');
                                    return bien ? bien.numero_bien + ' - ' + bien.equipo : 'Buscar un bien...';
                                },

                                buscarGlobal() {
                                    clearTimeout(this.debounceTimer);
                                    if (this.globalSearch.length < 2) { this.globalResults = []; return; }
                                    this.loading = true;
                                    this.debounceTimer = setTimeout(() => {
                                        fetch(`/api/bienes/buscar?q=${encodeURIComponent(this.globalSearch)}`)
                                            .then(r => r.json())
                                            .then(data => { 
                                                this.globalResults = data.filter(b => b.tipo === 'externo'); 
                                                this.loading = false; 
                                            })
                                            .catch(() => { this.loading = false; });
                                    }, 300);
                                },

                                buscarPorTipo() {
                                    clearTimeout(this.bienDebounceTimer);
                                    if (this.bienSearch.length < 2) { this.bienResults = []; return; }
                                    this.bienLoading = true;
                                    this.bienDebounceTimer = setTimeout(() => {
                                        fetch(`/api/bienes/buscar?q=${encodeURIComponent(this.bienSearch)}`)
                                            .then(r => r.json())
                                            .then(data => {
                                                this.bienResults = this.tipoBien ? data.filter(b => b.tipo === this.tipoBien) : data;
                                                this.bienLoading = false;
                                            })
                                            .catch(() => { this.bienLoading = false; });
                                    }, 300);
                                },

                                selectFromGlobal(bien) {
                                    this.bienId = bien.id;
                                    this.globalSearch = '';
                                    this.openGlobal = false;
                                    this.globalResults = [];
                                    this.actualizarCampos(bien);
                                },

                                selectBien(bien) {
                                    this.bienId = bien.id;
                                    this.openBien = false;
                                    this.bienSearch = bien.numero_bien + ' - ' + bien.equipo;
                                    this.actualizarCampos(bien);
                                },

                                actualizarCampos(bien) {
                                    document.getElementById('numero_bien').value = bien.numero_bien;
                                    document.getElementById('descripcion').value = bien.equipo;
                                    document.getElementById('serial').value = bien.serial || '';
                                    
                                    document.getElementById('bien_externo_id').value = bien.id;
                                    document.getElementById('bien_id').value = '';
                                    
                                    if (bien.departamento_id) {
                                        window.dispatchEvent(new CustomEvent('set-selected-procedencia-id', { detail: bien.departamento_id }));
                                    }
                                },

                                limpiarCampos() {
                                    document.getElementById('numero_bien').value = '';
                                    document.getElementById('descripcion').value = '';
                                    document.getElementById('serial').value = '';
                                    document.getElementById('bien_id').value = '';
                                    document.getElementById('bien_externo_id').value = '';
                                }
                            }" class="space-y-6">

                                <!-- Barra de búsqueda GLOBAL -->
                                <div class="relative" @click.away="openGlobal = false">
                                    <label class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1 mb-2 block">Búsqueda Rápida de Bienes (Ambas Tablas)</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <x-mary-icon name="o-magnifying-glass" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                                        </div>
                                        <input
                                            type="text"
                                            x-model="globalSearch"
                                            @input="buscarGlobal()"
                                            @focus="openGlobal = true"
                                            class="w-full pl-5 pr-20 py-4 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-purple/20 placeholder-gray-400 dark:placeholder-gray-600 transition-all duration-300 shadow-sm dark:shadow-none hover:bg-gray-50 dark:hover:bg-[#222]"
                                            placeholder="Escriba N° de bien, equipo, serial, marca o modelo para buscar...">
                                        <button
                                            x-show="globalSearch"
                                            @click="globalSearch = ''; openGlobal = false"
                                            class="absolute inset-y-0 right-10 flex items-center group">
                                            <x-mary-icon name="o-x-mark" class="w-5 h-5 text-gray-400 group-hover:text-red-500 transition-colors" />
                                        </button>
                                    </div>

                                    <!-- Resultados de Búsqueda Global -->
                                    <div
                                        x-show="openGlobal && globalResults.length > 0"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                        class="absolute z-60 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-xl overflow-hidden"
                                        style="display: none;">
                                        <div class="max-h-72 overflow-y-auto py-2 custom-scrollbar">
                                            <template x-for="b in globalResults" :key="b.tipo + '-' + b.id">
                                                <button
                                                    type="button"
                                                    @click="selectFromGlobal(b)"
                                                    class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 transition-all duration-200 group relative border-b border-gray-100 dark:border-white/5 last:border-0">
                                                    <div class="flex flex-col items-start ml-2 flex-1">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="font-bold tracking-wider text-gray-900 dark:text-white" x-text="b.numero_bien"></span>
                                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-tighter bg-blue-500/10 text-blue-500 border border-blue-500/20">Externo</span>
                                                        </div>
                                                        <span class="text-[11px] text-gray-500 uppercase font-medium line-clamp-1" x-text="`${b.equipo}${b.marca ? ' - ' + b.marca : ''}${b.modelo ? ' - ' + b.modelo : ''}`"></span>
                                                        <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5 block" x-show="b.serial" x-text="'SN: ' + b.serial"></span>
                                                    </div>
                                                    <x-mary-icon name="o-arrow-right" class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-brand-purple group-hover:translate-x-1 transition-all" />
                                                </button>
                                            </template>
                                            <div x-show="globalResults.length === 0 && loading" class="px-4 py-8 text-center text-gray-500">
                                                <div class="w-6 h-6 border-2 border-brand-purple/30 border-t-brand-purple rounded-full animate-spin mx-auto mb-2"></div>
                                                <p class="text-[10px] uppercase tracking-widest font-bold">Buscando...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative flex items-center gap-4 py-2">
                                    <div class="flex-1 h-px bg-linear-to-r from-transparent via-gray-200 dark:via-white/10 to-transparent"></div>
                                    <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">O seleccionar manualmente</span>
                                    <div class="flex-1 h-px bg-linear-to-r from-transparent via-gray-200 dark:via-white/10 to-transparent"></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Seleccionar Bien -->
                                    <div class="space-y-2" @click.away="openBien = false">
                                        <label class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1 mb-1 block">Buscar Bien</label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <x-mary-icon name="o-magnifying-glass" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                                            </div>
                                            <input
                                                type="text"
                                                x-model="bienSearch"
                                                @input="buscarPorTipo()"
                                                @focus="openBien = true"
                                                :disabled="!tipoBien"
                                                class="w-full pl-11 pr-12 py-4 h-14 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-purple/20 placeholder-gray-400 dark:placeholder-gray-600 transition-all duration-300 shadow-sm dark:shadow-none hover:bg-gray-50 dark:hover:bg-[#222] disabled:opacity-50 disabled:cursor-not-allowed"
                                                placeholder="Escriba para buscar...">

                                            <div
                                                x-show="openBien && bienResults.length > 0"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                                class="absolute z-50 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-xl overflow-hidden"
                                                style="display: none;">
                                                <div class="max-h-60 overflow-y-auto py-2 custom-scrollbar">
                                                    <template x-for="b in bienResults" :key="b.tipo + '-' + b.id">
                                                        <button
                                                            type="button"
                                                            @click="selectBien(b)"
                                                            class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 transition-all duration-200 group relative"
                                                            :class="{'bg-brand-purple/10 text-brand-purple font-bold': bienId == b.id, 'text-gray-700 dark:text-gray-300': bienId != b.id}">
                                                            <div x-show="bienId == b.id" class="absolute left-0 w-1 h-6 bg-brand-purple rounded-r-full"></div>
                                                            <div class="flex flex-col items-start ml-2">
                                                                <span class="font-bold tracking-wider" :class="{'text-brand-purple': bienId == b.id}" x-text="b.numero_bien"></span>
                                                                <span class="text-[10px] text-gray-500 uppercase font-medium" x-text="`${b.equipo}${b.marca ? ' - ' + b.marca : ''}${b.modelo ? ' - ' + b.modelo : ''}`"></span>
                                                                <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5 block" x-show="b.serial" x-text="'SN: ' + b.serial"></span>
                                                            </div>
                                                            <x-mary-icon x-show="bienId == b.id" name="o-check" class="ml-auto w-4 h-4 text-brand-purple" />
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative overflow-hidden rounded-2xl bg-brand-purple/5 border border-brand-purple/10 p-4 transition-all hover:bg-brand-purple/10 group shadow-sm dark:shadow-none">
                                    <div class="absolute top-0 right-0 -mr-4 -mt-4 w-16 h-16 bg-brand-purple/10 rounded-full blur-xl group-hover:bg-brand-purple/20 transition-all"></div>
                                    <div class="flex items-center gap-4 relative z-10">
                                        <div class="w-10 h-10 rounded-xl bg-brand-purple/20 flex items-center justify-center text-brand-lila shadow-lg shadow-brand-purple/10">
                                            <x-mary-icon name="o-sparkles" class="w-5 h-5" />
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em] mb-0.5">Asistente de Carga</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Los campos <span class="text-gray-900 dark:text-white font-bold tracking-tight uppercase text-[9px]">N° Bien, Descripción y Serial</span> se autocompletarán automáticamente al seleccionar un bien de la lista.</p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="bien_id" id="bien_id" value="{{ old('bien_id', $mantenimiento->bien_id) }}">
                                <input type="hidden" name="bien_externo_id" id="bien_externo_id" value="{{ old('bien_externo_id', $mantenimiento->bien_externo_id) }}">
                            </div>
                        </div>

                        <!-- Datos del Bien -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-information-circle" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Datos del Bien</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-input-premium name="numero_bien" label="Número de Bien" :value="$mantenimiento->numero_bien" required icon="o-hashtag" id="numero_bien" />
                                <x-input-premium name="descripcion" label="Descripción" :value="$mantenimiento->descripcion" required icon="o-document-text" id="descripcion" />
                                <x-input-premium name="serial" label="Serial" :value="$mantenimiento->serial" icon="o-qr-code" id="serial" />
                                <x-date-input-premium name="fecha" label="Fecha de Transferencia" :value="$mantenimiento->fecha->format('Y-m-d')" required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8 relative z-10">
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-40'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-arrows-right-left" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Ubicación</h3>
                            </div>

                            <div class="space-y-6" x-data="{
                                procedenciaSeleccionada: @js(old('procedencia_id', $mantenimiento->procedencia_id ?? $dticId)),
                                destino: @js(old('destino_id', $mantenimiento->destino_id ?? $dticId))
                            }" @set-selected-procedencia-id.window="procedenciaSeleccionada = $event.detail">
                                <div class="space-y-6">
                                    <x-select-premium
                                        name="procedencia_id"
                                        label="Procedencia"
                                        placeholder="Depto. de origen"
                                        required
                                        icon="o-building-office-2"
                                        :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                        :value="old('procedencia_id', $mantenimiento->procedencia_id ?? $dticId)"
                                        @option-selected="procedenciaSeleccionada = $event.detail" />

                                    <div x-show="procedenciaSeleccionada == {{ $dticId }}" x-transition>
                                        <x-select-premium
                                            name="area_procedencia_id"
                                            label="Ubicación en DTIC (Área Origen)"
                                            placeholder="Seleccione Área de origen"
                                            icon="o-map-pin"
                                            :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                            :value="old('area_procedencia_id', $mantenimiento->bien?->area_id)"
                                            :required="false" />
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <x-select-premium
                                        name="destino_id"
                                        label="Destino"
                                        placeholder="Depto. de destino"
                                        required
                                        icon="o-building-office-2"
                                        :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                        :value="old('destino_id', $mantenimiento->destino_id ?? $dticId)"
                                        @option-selected="destino = $event.detail" />

                                    <div x-show="destino == {{ $dticId }}" x-transition>
                                        <x-select-premium
                                            name="area_id"
                                            label="Ubicación en DTIC (Área)"
                                            placeholder="Seleccione Área de destino"
                                            icon="o-map-pin"
                                            :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                            :value="old('area_id', $mantenimiento->area_id)"
                                            :required="false" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-shield-check" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Estado y Acta</h3>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">N° Orden Acta (Opcional)</label>
                                    <input type="text" name="n_orden_acta" value="{{ old('n_orden_acta', $mantenimiento->n_orden_acta) }}" class="w-full h-11 bg-white dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 transition-all placeholder-gray-400" placeholder="Ej: 0001" maxlength="4" pattern="\d{4}" title="Debe ser un número de 4 dígitos (Ej. 0001)">
                                </div>

                                <x-select-premium name="estatus_acta_id" label="Estatus" placeholder="Seleccione estatus" required icon="o-clock" :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()" :value="old('estatus_acta_id', $mantenimiento->estatus_acta_id)" />

                                <x-date-input-premium name="fecha_acta" label="Fecha Redacción Acta (Opcional)" :value="$mantenimiento->fecha_acta?->format('Y-m-d')" icon="o-calendar" />
                                <x-date-input-premium name="fecha_firma" label="Fecha de Firma (Opcional)" :value="$mantenimiento->fecha_firma?->format('Y-m-d')" icon="o-pencil-square" />
                            </div>
                        </div>

                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-arrow-path" class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" />
                                {{ __('Actualizar Mantenimiento') }}
                            </button>
                            <a href="{{ route('mantenimientos.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar Cambios') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>