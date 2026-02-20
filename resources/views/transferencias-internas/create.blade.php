<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-plus-circle" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Nueva Transferencia Interna') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Operaciones</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="transferenciaForm" method="POST" action="{{ route('transferencias-internas.store') }}" @submit="limpiarBorrador()" class="space-y-8" x-data="{
                modoTransferencia: 'individual', // 'individual' o 'multiple'
                openOrigen: false,
                openBien: false,
                openGlobal: false,
                globalSearch: '',
                bienSearch: '',
                tipoBien: '',
                globalResults: [],
                bienResults: [],
                debounceTimer: null,
                bienDebounceTimer: null,
                loading: false,
                bienLoading: false,
                bienesSeleccionados: @js(old('bienes', [])),
                destinoSeleccionado: @js(old('destino_id')),
                errorModal: false,
                mensajeError: '',

                init() {
                    let guardado = localStorage.getItem('transferenciaDraft');
                    // Solo restaurar si es una carga limpia (sin errores de validación)
                    if (guardado && {{ old('_token') ? 'false' : 'true' }}) {
                        try {
                            let data = JSON.parse(guardado);
                            if (data.bienesSeleccionados && data.bienesSeleccionados.length > 0) {
                                this.bienesSeleccionados = data.bienesSeleccionados;
                            }
                            if (data.modoTransferencia) {
                                this.modoTransferencia = data.modoTransferencia;
                            }
                            
                            // Restaurar selecciones mediante eventos globales tras un pequeño delay para asegurar que los componentes hijos se montaron
                            setTimeout(() => {
                                if (data.procedencia_id) this.$dispatch('set-selected-procedencia-id', data.procedencia_id);
                                if (data.destino_id) this.$dispatch('set-selected-destino-id', data.destino_id);
                                if (data.area_id) this.$dispatch('set-selected-area-id', data.area_id);
                                if (data.estatus_acta_id) this.$dispatch('set-selected-estatus-acta-id', data.estatus_acta_id);
                            }, 250);
                        } catch (e) {}
                    }

                    // Autoguardar cada 3 segundos
                    setInterval(() => {
                        this.guardarBorrador();
                    }, 3000);
                },

                guardarBorrador() {
                    let form = document.getElementById('transferenciaForm');
                    if (!form) return;
                    let formData = new FormData(form);
                    let data = {
                        bienesSeleccionados: this.bienesSeleccionados,
                        modoTransferencia: this.modoTransferencia,
                        procedencia_id: formData.get('procedencia_id') || '',
                        destino_id: formData.get('destino_id') || '',
                        area_id: formData.get('area_id') || '',
                        estatus_acta_id: formData.get('estatus_acta_id') || ''
                    };
                    localStorage.setItem('transferenciaDraft', JSON.stringify(data));
                },

                limpiarBorrador() {
                    localStorage.removeItem('transferenciaDraft');
                },

                get selectedOrigenLabel() {
                    if (this.tipoBien === 'dtic') return 'Bienes DTIC (Internos)';
                    if (this.tipoBien === 'externo') return 'Bienes Externos';
                    return 'Seleccione origen...';
                },

                buscarGlobal() {
                    clearTimeout(this.debounceTimer);
                    if (this.globalSearch.length < 2) { this.globalResults = []; return; }
                    this.loading = true;
                    this.debounceTimer = setTimeout(() => {
                        fetch(`/api/bienes/buscar?q=${encodeURIComponent(this.globalSearch)}`)
                            .then(r => r.json())
                            .then(data => { this.globalResults = data; this.loading = false; })
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

                selectOrigen(tipo) {
                    this.tipoBien = tipo;
                    this.bienSearch = '';
                    this.bienResults = [];
                    this.openOrigen = false;
                },

                selectFromGlobal(bien) {
                    this.globalSearch = '';
                    this.openGlobal = false;
                    this.globalResults = [];
                    this.agregarBien(bien);
                },

                selectBien(bien) {
                    this.openBien = false;
                    this.bienSearch = '';
                    this.agregarBien(bien);
                },

                agregarBien(bien) {
                    // Evitar duplicados
                    if (this.bienesSeleccionados.find(b => b.id == bien.id && b.tipo == bien.tipo)) {
                        this.mensajeError = 'Este bien ya ha sido agregado a la lista.';
                        this.errorModal = true;
                        return;
                    }

                    // Validar Procedencia en Transferencia Múltiple
                    if (this.bienesSeleccionados.length > 0) {
                        const primerBien = this.bienesSeleccionados[0];
                        // Para bienes DTIC su orgien conceptual es DTIC, para foraneos es departamento_id
                        const origenPrimerBien = primerBien.tipo === 'dtic' ? 'DTIC' : primerBien.departamento_id;
                        const origenNuevoBien = bien.tipo === 'dtic' ? 'DTIC' : bien.departamento_id;

                        if (origenPrimerBien !== origenNuevoBien) {
                            this.mensajeError = 'Todos los bienes de la transferencia múltiple deben pertenecer al mismo departamento de origen.';
                            this.errorModal = true;
                            return;
                        }
                    }

                    // En modo individual, reemplazamos el array entero (siempre habrá un único bien)
                    if (this.modoTransferencia === 'individual') {
                        this.bienesSeleccionados = [];
                    }
                    
                    this.bienesSeleccionados.push({
                        id: bien.id,
                        tipo: bien.tipo,
                        numero_bien: bien.numero_bien,
                        descripcion: bien.equipo,
                        serial: bien.serial || '',
                        departamento_id: bien.departamento_id
                    });

                    // Si es el primer bien, preseleccionar origen
                    if (this.bienesSeleccionados.length === 1) {
                        this.preseleccionarProcedencia(bien);
                    }
                },
                
                preseleccionarProcedencia(bien) {
                    this.$nextTick(() => {
                        let idToSelect = bien.tipo === 'dtic' ? 'DTIC' : bien.departamento_id;
                        this.$dispatch('set-selected-procedencia-id', idToSelect);
                        // También forzar el value en el hidden select
                        let selectEl = document.querySelector('select[name=procedencia_id]');
                        if (selectEl) {
                            selectEl.value = idToSelect;
                            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                },

                removerBien(index) {
                    this.bienesSeleccionados.splice(index, 1);
                },
                
                agregarManual() {
                    // En modo individual, si ya hay un bien, no permitir añadir más manualmente
                    if (this.modoTransferencia === 'individual' && this.bienesSeleccionados.length > 0) {
                        this.mensajeError = 'En modo de transferencia individual, solo se puede añadir un bien a la vez.';
                        this.errorModal = true;
                        return;
                    }

                    this.bienesSeleccionados.push({
                        id: '',
                        tipo: 'dtic',
                        numero_bien: '',
                        descripcion: '',
                        serial: '',
                        departamento_id: null
                    });
                }
            }">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna Izquierda -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- Selector de Modo de Transferencia -->
                        <div class="bg-dark-850/40 backdrop-blur-xl p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 relative flex justify-center items-center w-fit mx-auto lg:mx-0">
                            <div class="inline-flex rounded-xl bg-[#131313] p-1 border border-white/5 shadow-inner">
                                <button
                                    type="button"
                                    @click="modoTransferencia = 'individual'; bienesSeleccionados = bienesSeleccionados.slice(0, 1);"
                                    class="relative flex items-center justify-center gap-2 px-8 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-all duration-300 pointer-events-auto"
                                    :class="modoTransferencia === 'individual' ? 'bg-white dark:bg-white text-brand-purple shadow-sm' : 'text-gray-500 hover:text-gray-400 dark:text-gray-500 dark:hover:text-gray-300'">
                                    <x-mary-icon name="o-document" class="w-4 h-4" />
                                    Individual
                                </button>
                                <button
                                    type="button"
                                    @click="modoTransferencia = 'multiple'"
                                    class="relative flex items-center justify-center gap-2 px-8 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-all duration-300 pointer-events-auto"
                                    :class="modoTransferencia === 'multiple' ? 'bg-white dark:bg-white text-brand-purple shadow-sm' : 'text-gray-500 hover:text-gray-400 dark:text-gray-500 dark:hover:text-gray-300'">
                                    <x-mary-icon name="o-document-duplicate" class="w-4 h-4" />
                                    Múltiple
                                </button>
                            </div>
                        </div>

                        <!-- Importar Bien -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-40'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-arrow-down-tray" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Importar Bien</h3>
                            </div>

                            <div class="space-y-6">

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
                                        class="absolute z-60 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-xl overflow-hidden"
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
                                                            <span
                                                                class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-tighter"
                                                                :class="b.tipo === 'dtic' ? 'bg-brand-purple/10 text-brand-purple border border-brand-purple/20' : 'bg-blue-500/10 text-blue-500 border border-blue-500/20'"
                                                                x-text="b.tipo === 'dtic' ? 'DTIC' : 'Externo'"></span>
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
                                    <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">O buscar por origen específico</span>
                                    <div class="flex-1 h-px bg-linear-to-r from-transparent via-gray-200 dark:via-white/10 to-transparent"></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Origen del Bien -->
                                    <div class="space-y-2" @click.away="openOrigen = false">
                                        <label class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1 mb-1 block">Origen del Bien</label>
                                        <div class="relative group">
                                            <button
                                                type="button"
                                                @click="openOrigen = !openOrigen"
                                                class="relative w-full flex items-center pl-11 pr-12 py-4 h-14 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-left text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-sm dark:shadow-none hover:bg-gray-50 dark:hover:bg-[#222] cursor-pointer"
                                                :class="{'ring-2 ring-brand-purple/20 bg-gray-50 dark:bg-[#222]': openOrigen}">
                                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                    <x-mary-icon name="o-building-library" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-brand-purple transition-colors duration-300" x-bind:class="{'text-brand-purple': openOrigen}" />
                                                </div>
                                                <span class="block truncate font-medium text-sm" :class="{'text-gray-400 dark:text-gray-500': !tipoBien, 'text-gray-900 dark:text-white': tipoBien}" x-text="selectedOrigenLabel"></span>
                                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none transition-transform duration-300" :class="{'rotate-180': openOrigen}">
                                                    <x-mary-icon name="o-chevron-down" class="w-4 h-4 text-gray-400" />
                                                </span>
                                            </button>

                                            <div
                                                x-show="openOrigen"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                                class="absolute z-50 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-xl overflow-hidden"
                                                style="display: none;">
                                                <div class="py-2">
                                                    <button type="button" @click="selectOrigen('dtic')" class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 transition-all duration-200 group relative">
                                                        <div x-show="tipoBien === 'dtic'" class="absolute left-0 w-1 h-6 bg-brand-purple rounded-r-full"></div>
                                                        <span class="ml-2 font-medium" :class="{'text-brand-purple font-bold': tipoBien === 'dtic'}">Bienes DTIC (Internos)</span>
                                                        <x-mary-icon x-show="tipoBien === 'dtic'" name="o-check" class="ml-auto w-4 h-4 text-brand-purple" />
                                                    </button>
                                                    <button type="button" @click="selectOrigen('externo')" class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 transition-all duration-200 group relative">
                                                        <div x-show="tipoBien === 'externo'" class="absolute left-0 w-1 h-6 bg-brand-purple rounded-r-full"></div>
                                                        <span class="ml-2 font-medium" :class="{'text-brand-purple font-bold': tipoBien === 'externo'}">Bienes Externos</span>
                                                        <x-mary-icon x-show="tipoBien === 'externo'" name="o-check" class="ml-auto w-4 h-4 text-brand-purple" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                                                class="absolute z-50 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-xl overflow-hidden"
                                                style="display: none;">
                                                <div class="max-h-60 overflow-y-auto py-2 custom-scrollbar">
                                                    <template x-for="b in bienResults" :key="b.tipo + '-' + b.id">
                                                        <button
                                                            type="button"
                                                            @click="selectBien(b)"
                                                            class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 transition-all duration-200 group relative">
                                                            <div class="flex flex-col items-start ml-2">
                                                                <span class="font-bold tracking-wider" x-text="b.numero_bien"></span>
                                                                <span class="text-[10px] text-gray-500 uppercase font-medium" x-text="`${b.equipo}${b.marca ? ' - ' + b.marca : ''}${b.modelo ? ' - ' + b.modelo : ''}`"></span>
                                                                <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5 block" x-show="b.serial" x-text="'SN: ' + b.serial"></span>
                                                            </div>
                                                            <x-mary-icon name="o-plus" class="ml-auto w-4 h-4 text-gray-400 group-hover:text-brand-purple" />
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
                                            <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em] mb-0.5">Transferencia Múltiple</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Puedes añadir <span class="text-gray-900 dark:text-white font-bold tracking-tight uppercase text-[9px]">varios bienes</span> a la lista para transferirlos todos juntos al mismo departamento de destino.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Error Genérico -->
                        <div x-show="errorModal" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <!-- Background backdrop -->
                            <div x-show="errorModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                    <!-- Panel modal -->
                                    <div x-show="errorModal" @click.away="errorModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-4xl bg-white dark:bg-[#1a1a1a] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-white/10">
                                        <div class="bg-white dark:bg-[#1a1a1a] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20 sm:mx-0 sm:h-10 sm:w-10">
                                                    <x-mary-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600 dark:text-red-400" />
                                                </div>
                                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                    <h3 class="text-lg font-black leading-6 text-gray-900 dark:text-white" id="modal-title">Operación denegada</h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="mensajeError"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-[#222] px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                            <button type="button" @click="errorModal = false" class="inline-flex w-full justify-center rounded-xl bg-brand-purple px-4 py-3 text-sm font-bold text-white shadow-sm hover:brightness-110 sm:ml-3 sm:w-auto uppercase tracking-widest transition-all">Entendido</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Datos de la Transferencia / Lista -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500 hover:shadow-brand-purple/5">
                            <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                            <div class="flex items-center justify-between mb-8 relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                        <x-mary-icon name="o-list-bullet" class="w-6 h-6 text-brand-lila" />
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest" x-text="modoTransferencia === 'multiple' ? 'Bienes a Transferir' : 'Datos del Bien'"></h3>
                                </div>
                                <button x-show="modoTransferencia === 'multiple' || (modoTransferencia === 'individual' && bienesSeleccionados.length === 0)" type="button" @click="agregarManual()" class="px-4 py-2 bg-brand-purple/10 text-brand-purple rounded-xl text-xs font-bold uppercase hover:bg-brand-purple hover:text-white transition-colors flex items-center gap-2 cursor-pointer shadow-sm">
                                    <x-mary-icon name="o-plus" class="w-4 h-4 border-2 border-current rounded-full" />
                                    <span>Añadir Manual</span>
                                </button>
                            </div>

                            <!-- Empty state -->
                            <div x-show="bienesSeleccionados.length === 0" class="text-center py-10 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-2xl relative z-10">
                                <x-mary-icon name="o-inbox" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="text-sm text-gray-500 font-medium">Búsque y seleccione bienes arriba o añada manualmente.</p>
                                @error('bienes')
                                <p class="text-red-500 text-xs mt-3 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lista -->
                            <div class="space-y-4 relative z-10">
                                <template x-for="(bien, index) in bienesSeleccionados" :key="index">
                                    <div class="p-5 border border-gray-100 dark:border-white/10 rounded-2xl bg-gray-50 dark:bg-[#1a1a1a] relative group transition-all duration-300 hover:shadow-md hover:border-brand-purple/30">
                                        <button x-show="modoTransferencia === 'multiple' || (modoTransferencia === 'individual' && bienesSeleccionados.length > 0)" type="button" @click="removerBien(index)" class="absolute -top-3 -right-3 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transform transition-all scale-100 md:scale-0 md:group-hover:scale-100 cursor-pointer z-60">
                                            <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                        </button>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <!-- Hidden Inputs para Laravel -->
                                            <input type="hidden" :name="'bienes['+index+'][id]'" x-model="bien.id">
                                            <input type="hidden" :name="'bienes['+index+'][tipo]'" x-model="bien.tipo">

                                            <!-- Inputs Visibles Form -->
                                            <div>
                                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">N° Bien <span class="text-red-500">*</span></label>
                                                <input type="text" x-model="bien.numero_bien" :name="'bienes['+index+'][numero_bien]'" required class="w-full h-11 bg-white dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 outline-none transition-all shadow-sm dark:shadow-none placeholder-gray-400" placeholder="Ej: 12345">
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">Descripción <span class="text-red-500">*</span></label>
                                                <input type="text" x-model="bien.descripcion" :name="'bienes['+index+'][descripcion]'" required class="w-full h-11 bg-white dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 outline-none transition-all shadow-sm dark:shadow-none placeholder-gray-400" placeholder="Ej: Monitor">
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">Serial</label>
                                                <input type="text" x-model="bien.serial" :name="'bienes['+index+'][serial]'" class="w-full h-11 bg-white dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 outline-none transition-all shadow-sm dark:shadow-none placeholder-gray-400" placeholder="Ej: S/N">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-8 border-t border-gray-100 dark:border-white/10 pt-8 relative z-10" x-show="bienesSeleccionados.length > 0">
                                <div class="w-full sm:w-1/2">
                                    <x-date-input-premium
                                        name="fecha"
                                        label="Fecha de Transferencia"
                                        required />
                                </div>
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

                            <div class="space-y-6">
                                <x-select-premium
                                    name="procedencia_id"
                                    label="Procedencia"
                                    placeholder="Depto. de origen"
                                    required
                                    icon="o-building-office-2"
                                    :options="array_merge([['value' => 'DTIC', 'label' => 'DTIC']], $departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray())"
                                    :value="old('procedencia_id')" />

                                <div class="space-y-6">
                                    <x-select-premium
                                        name="destino_id"
                                        label="Destino"
                                        placeholder="Depto. de destino"
                                        required
                                        icon="o-building-office-2"
                                        :options="array_merge([['value' => 'DTIC', 'label' => 'DTIC']], $departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray())"
                                        :value="old('destino_id')"
                                        @option-selected="destinoSeleccionado = $event.detail" />

                                    <div x-show="destinoSeleccionado === 'DTIC'" x-transition>
                                        <x-select-premium
                                            name="area_id"
                                            label="Ubicación en DTIC (Área)"
                                            placeholder="Seleccione Área de destino"
                                            icon="o-map-pin"
                                            :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                            :value="old('area_id')"
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
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Estado</h3>
                            </div>

                            <div class="space-y-6">
                                <x-select-premium
                                    name="estatus_acta_id"
                                    label="Estatus"
                                    placeholder="Seleccione estatus"
                                    required
                                    icon="o-clock"
                                    :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                    :value="old('estatus_acta_id')" />

                                <x-date-input-premium
                                    name="fecha_firma"
                                    label="Fecha de Firma"
                                    icon="o-pencil-square" />
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-check" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" />
                                {{ __('Registrar Transferencia') }}
                            </button>
                            <a href="{{ route('transferencias-internas.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar y Volver') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>