<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-plus-circle" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Ingresar Bien a Mantenimiento') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Operaciones</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="mantenimientoForm" method="POST" action="{{ route('mantenimientos.store') }}" @submit="if(bienesSeleccionados.length === 0) { mensajeError = 'Debe agregar al menos un bien a la lista para procesar el mantenimiento.'; errorModal = true; $event.preventDefault(); return; } limpiarBorrador()" class="space-y-8" x-data="{
                modoTransferencia: 'individual', 
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
                procedenciaSeleccionada: @js(old('procedencia_id')),
                errorModal: false,
                mensajeError: '',

                init() {
                    let guardado = localStorage.getItem('mantenimientoDraft');
                    if (guardado && {{ old('_token') ? 'false' : 'true' }}) {
                        try {
                            let data = JSON.parse(guardado);
                            if (data.bienesSeleccionados && data.bienesSeleccionados.length > 0) {
                                this.bienesSeleccionados = data.bienesSeleccionados;
                            }
                            if (data.modoTransferencia) {
                                this.modoTransferencia = data.modoTransferencia;
                            }
                            
                            setTimeout(() => {
                                if (data.procedencia_id) this.$dispatch('set-selected-procedencia-id', data.procedencia_id);
                                if (data.procedencia_id) this.procedenciaSeleccionada = data.procedencia_id;
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
                    let form = document.getElementById('mantenimientoForm');
                    if (!form) return;
                    let formData = new FormData(form);
                    let data = {
                        bienesSeleccionados: this.bienesSeleccionados,
                        modoTransferencia: this.modoTransferencia,
                        procedencia_id: formData.get('procedencia_id') || '',
                        estatus_acta_id: formData.get('estatus_acta_id') || ''
                    };
                    localStorage.setItem('mantenimientoDraft', JSON.stringify(data));
                },

                limpiarBorrador() {
                    localStorage.removeItem('mantenimientoDraft');
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
                            .then(data => { 
                                // Solo se permiten bienes externos para entrar a mantenimiento
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
                    if (this.bienesSeleccionados.find(b => b.id == bien.id && b.tipo == bien.tipo)) {
                        this.mensajeError = 'Este bien ya ha sido agregado a la lista.';
                        this.errorModal = true;
                        return;
                    }

                    if (this.bienesSeleccionados.length > 0) {
                        const primerBien = this.bienesSeleccionados[0];
                        const origenPrimerBien = primerBien.tipo === 'dtic' ? 'DTIC' : primerBien.departamento_id;
                        const origenNuevoBien = bien.tipo === 'dtic' ? 'DTIC' : bien.departamento_id;

                        if (origenPrimerBien !== origenNuevoBien) {
                            this.mensajeError = 'Todos los bienes deben pertenecer al mismo departamento de origen.';
                            this.errorModal = true;
                            return;
                        }
                    }

                    if (this.modoTransferencia === 'individual') {
                        this.bienesSeleccionados = [];
                    }
                    
                    this.bienesSeleccionados.push({
                        id: bien.id,
                        tipo: bien.tipo,
                        numero_bien: bien.numero_bien,
                        descripcion: bien.equipo,
                        serial: bien.serial || '',
                        marca: bien.marca || '',
                        modelo: bien.modelo || '',
                        color: bien.color || '',
                        categoria: bien.categoria || '',
                        estado_nombre: bien.estado_nombre || '',
                        departamento_id: bien.departamento_id
                    });

                    if (this.bienesSeleccionados.length === 1) {
                        this.preseleccionarProcedencia(bien);
                    }
                },
                
                preseleccionarProcedencia(bien) {
                    this.$nextTick(() => {
                        let idToSelect = bien.tipo === 'dtic' ? {{ $dticId }} : bien.departamento_id;
                        this.procedenciaSeleccionada = idToSelect;
                        // Disparar evento a nivel global (window)
                        window.dispatchEvent(new CustomEvent('set-selected-procedencia-id', { detail: idToSelect }));
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
                    if (this.modoTransferencia === 'individual' && this.bienesSeleccionados.length > 0) {
                        this.mensajeError = 'En modo individual, solo se puede añadir un bien a la vez.';
                        this.errorModal = true;
                        return;
                    }

                    this.bienesSeleccionados.push({
                        id: '',
                        tipo: 'externo',
                        numero_bien: '',
                        descripcion: '',
                        serial: '',
                        marca: '',
                        modelo: '',
                        color: '',
                        categoria: '',
                        estado_nombre: '',
                        departamento_id: null
                    });
                }
            }">
                @csrf
                <!-- Input hidden indicando que esto es una entrada (por defecto el controlador asume entrada salvo 'devolviendo' true) -->

                <!-- Inputs Hiddens forzados de destino para Mantenimiento -->
                <input type="hidden" name="destino_id" value="{{ $dticId }}">
                @if($areaMantenimiento)
                <input type="hidden" name="area_id" value="{{ $areaMantenimiento->id }}">
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        @if($errors->any())
                        <div class="p-5 bg-red-500/10 backdrop-blur-md border border-red-500/30 rounded-2xl flex items-start gap-4 shadow-lg">
                            <x-mary-icon name="o-exclamation-triangle" class="w-8 h-8 text-red-500 shrink-0" />
                            <div>
                                <h4 class="text-red-500 font-black tracking-widest uppercase text-sm mb-2">Por favor corrige los siguientes errores:</h4>
                                <ul class="list-disc list-inside text-xs text-red-400 font-medium space-y-1">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

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
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500" :class="activeCard ? 'z-50' : 'z-10'">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-arrow-down-tray" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Importar Bien</h3>
                            </div>

                            <div class="space-y-6">

                                <!-- Barra de búsqueda GLOBAL y ÚNICA -->
                                <div class="relative" @click.away="openGlobal = false">
                                    <label class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1 mb-2 block">Búsqueda Rápida de Bienes Externos</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <x-mary-icon name="o-magnifying-glass" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                                        </div>
                                        <input
                                            type="text"
                                            x-model="globalSearch"
                                            @input="buscarGlobal()"
                                            @focus="openGlobal = true"
                                            class="w-full pl-11 pr-20 py-4 h-14 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-purple/20 placeholder-gray-400 dark:placeholder-gray-600 transition-all duration-300 shadow-sm dark:shadow-none hover:bg-gray-50 dark:hover:bg-[#222]"
                                            placeholder="Escriba N° de bien, equipo, serial, marca o modelo para buscar...">
                                        <button
                                            x-show="globalSearch"
                                            @click="globalSearch = ''; openGlobal = false"
                                            class="absolute inset-y-0 right-10 flex items-center group">
                                            <x-mary-icon name="o-x-mark" class="w-5 h-5 text-gray-400 group-hover:text-red-500 transition-colors" />
                                        </button>
                                    </div>

                                    <!-- Resultados de Búsqueda -->
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
                                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-tighter bg-blue-500/10 text-blue-500 border border-blue-500/20">Externo</span>
                                                        </div>
                                                        <span class="text-[11px] text-gray-500 uppercase font-medium line-clamp-1" x-text="`${b.equipo}${b.marca ? ' - ' + b.marca : ''}${b.modelo ? ' - ' + b.modelo : ''}`"></span>
                                                        <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5 block" x-show="b.serial" x-text="'SN: ' + b.serial"></span>
                                                    </div>
                                                    <x-mary-icon name="o-arrow-right" class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-brand-purple group-hover:translate-x-1 transition-all" />
                                                </button>
                                            </template>
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
                                            <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em] mb-0.5">Mantenimiento de Bienes</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Solo se permiten <span class="text-gray-900 dark:text-white font-bold tracking-tight uppercase text-[9px]">bienes externos</span> para el ingreso a mantenimiento.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Error Genérico -->
                        <div x-show="errorModal" x-cloak class="relative z-50">
                            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                    <div @click.away="errorModal = false" class="relative transform overflow-hidden rounded-4xl bg-white dark:bg-[#1a1a1a] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-white/10">
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
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500 hover:shadow-brand-purple/5" :class="activeCard ? 'z-50' : 'z-10'">
                            <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                            <div class="flex items-center justify-between mb-8 relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                        <x-mary-icon name="o-list-bullet" class="w-6 h-6 text-brand-lila" />
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest" x-text="modoTransferencia === 'multiple' ? 'Bienes a Ingresar' : 'Datos del Bien'"></h3>
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
                                            <input type="hidden" :name="'bienes['+index+'][id]'" x-model="bien.id">
                                            <input type="hidden" :name="'bienes['+index+'][tipo]'" x-model="bien.tipo">

                                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">N° Bien <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="bien.numero_bien" :name="'bienes['+index+'][numero_bien]'" :readonly="bien.id !== '' && modoTransferencia === 'individual'" :class="bien.id !== '' && modoTransferencia === 'individual' ? 'bg-gray-100 dark:bg-[#2a2a2a] text-gray-500 dark:text-gray-400' : 'bg-white dark:bg-[#222] text-gray-900 dark:text-white'" required class="w-full h-11 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-purple/20 outline-none transition-all shadow-sm dark:shadow-none placeholder-gray-400" placeholder="Ej: 12345">
                                                </div>
                                                <div>
                                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">Equipo <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="bien.descripcion" :name="'bienes['+index+'][descripcion]'" :readonly="bien.id !== '' && modoTransferencia === 'individual'" :class="bien.id !== '' && modoTransferencia === 'individual' ? 'bg-gray-100 dark:bg-[#2a2a2a] text-gray-500 dark:text-gray-400' : 'bg-white dark:bg-[#222] text-gray-900 dark:text-white'" required class="w-full h-11 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-purple/20 outline-none transition-all shadow-sm dark:shadow-none placeholder-gray-400" placeholder="Ej: Monitor">
                                                </div>
                                                <div>
                                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">Serial</label>
                                                    <input type="text" x-model="bien.serial" :name="'bienes['+index+'][serial]'" :readonly="bien.id !== '' && modoTransferencia === 'individual'" :class="bien.id !== '' && modoTransferencia === 'individual' ? 'bg-gray-100 dark:bg-[#2a2a2a] text-gray-500 dark:text-gray-400' : 'bg-white dark:bg-[#222] text-gray-900 dark:text-white'" class="w-full h-11 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-purple/20 outline-none transition-all shadow-sm dark:shadow-none placeholder-gray-400" placeholder="Ej: S/N">
                                                </div>
                                            </div>

                                            <!-- Campos adicionales solo visibles si fueron cargados de un bien existente y está en modo individual para visualización rápida -->
                                            <div x-show="modoTransferencia === 'individual' && bien.id !== ''" class="md:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4 mt-2 p-3 bg-brand-purple/5 border border-brand-purple/10 rounded-xl">
                                                <div>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Marca</p>
                                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="bien.marca || 'S/M'"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Modelo</p>
                                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="bien.modelo || 'S/M'"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Color</p>
                                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="bien.color || 'N/A'"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Estado / Cat.</p>
                                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                        <span x-text="bien.estado_nombre || 'N/A'"></span>
                                                        <span class="text-gray-400">|</span>
                                                        <span x-text="bien.categoria || 'N/A'"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-8 border-t border-gray-100 dark:border-white/10 pt-8 relative z-10" x-show="bienesSeleccionados.length > 0">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-date-input-premium
                                        name="fecha"
                                        label="Fecha de Entrada a DTIC"
                                        required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8 relative z-10">
                        <!-- Destino (Pre-cargado a Soporte Técnico) -->
                        <div class="bg-linear-to-br from-brand-purple/10 to-transparent backdrop-blur-xl p-8 rounded-[2.5rem] shadow-sm border border-brand-purple/20 relative">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-brand-purple/20 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-wrench" class="w-6 h-6 text-brand-purple" />
                                </div>
                                <h3 class="text-xl font-black text-brand-purple dark:text-brand-lila uppercase tracking-widest">Destino Fijo</h3>
                            </div>
                            <div class="pl-14">
                                <p class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">DTIC</p>
                                <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $areaMantenimiento?->nombre ?? 'Soporte Técnico - Mantenimiento' }}</p>
                            </div>
                        </div>

                        <!-- Origen Real -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300" :class="activeCard ? 'z-50' : 'z-10'">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-arrow-left-end-on-rectangle" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Procedencia</h3>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-6">
                                    <x-select-premium
                                        name="procedencia_id"
                                        label="Dpto. de Procedencia"
                                        placeholder="Depto. que solicita"
                                        required
                                        icon="o-building-office-2"
                                        :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                        :value="old('procedencia_id')"
                                        @option-selected="procedenciaSeleccionada = $event.detail" />
                                </div>
                            </div>
                        </div>

                        <!-- Estado y Acta -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300" :class="activeCard ? 'z-50' : 'z-10'">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-clipboard-document-check" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Estado y Acta</h3>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1 block">N° Orden Acta (Opcional)</label>
                                    <input type="text" name="n_orden_acta" value="{{ old('n_orden_acta') }}" class="w-full h-11 bg-white dark:bg-[#222] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 transition-all placeholder-gray-400" placeholder="Ej: 0001" maxlength="4" pattern="\d{4}" title="Debe ser un número de 4 dígitos (Ej. 0001)">
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
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-check" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" />
                                {{ __('Registrar Mantenimiento') }}
                            </button>
                            <a href="{{ route('mantenimientos.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar y Volver') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>