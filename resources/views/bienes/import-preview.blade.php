<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-4xl text-white leading-tight tracking-tighter drop-shadow-2xl">
                    {{ __('Vista Previa') }} <span class="text-brand-purple italic">de Importación</span>
                </h2>
                <p class="text-gray-400 text-sm mt-1 font-medium select-none">Panel de revisión y validación de activos masivos</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('bienes.index') }}" class="group inline-flex items-center px-6 py-3 bg-dark-800/50 hover:bg-dark-700 border border-dark-700/50 rounded-2xl font-bold text-xs text-gray-400 hover:text-white uppercase tracking-widest transition-all duration-300 backdrop-blur-md active:scale-95">
                    <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Cancelar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="importPreview(@js($previewData))">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Estadísticas y Acciones Rápidas -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <div class="lg:col-span-3 bg-dark-850/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-brand-purple/10 blur-[80px] rounded-full group-hover:bg-brand-purple/20 transition-all duration-700"></div>
                    
                    <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                        <div class="max-w-xl">
                            <h3 class="text-2xl font-black text-white mb-2 tracking-tight">Registro de Bienes <span class="text-xs px-2 py-1 bg-brand-purple/20 text-brand-lila rounded-lg ml-2 border border-brand-purple/30 uppercase tracking-widest font-black">Beta</span></h3>
                            <p class="text-gray-400 leading-relaxed font-medium">
                                @if($isTruncated)
                                    Se han detectado <span class="text-white font-bold">{{ $totalRows }}</span> registros. Mostrando los primeros <span class="text-brand-purple font-bold">{{ $maxPreviewRows }}</span> para optimización.
                                @else
                                    Hemos procesado exitosamente <span class="text-emerald-400 font-bold">{{ $totalRows }}</span> registros listos para su incorporación.
                                @endif
                                Revisa la información relevante y confirma la validez de cada activo.
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap gap-3 w-full md:w-auto">
                            <button type="button" @click="toggleAll" 
                                    class="flex-1 md:flex-none inline-flex items-center justify-center px-6 py-4 bg-dark-800/80 hover:bg-dark-700 border border-dark-700/50 rounded-2xl font-black text-xs text-white uppercase tracking-widest transition-all shadow-lg active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                Alternar Todo
                            </button>
                            <div class="flex-1 md:flex-none">
                                <x-button-premium type="button" @click="submitForm" text="Procesar Selección" icon="o-cloud-arrow-up" color="purple" class="w-full h-full py-4 rounded-2xl" />
                            </div>
                            <form action="{{ route('bienes.import') }}" method="POST" id="main-import-form" class="hidden">
                                @csrf
                                <input type="hidden" name="bienes_json" id="bienes_json">
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-brand-purple/20 to-brand-lila/5 backdrop-blur-xl border border-brand-purple/20 rounded-[2.5rem] p-8 flex flex-col justify-center items-center text-center shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(139,92,246,0.1),transparent)] opacity-50"></div>
                    <div class="relative">
                        <span class="block text-5xl font-black text-white mb-2 tracking-tighter" x-text="selectedCount">0</span>
                        <span class="text-brand-lila font-black text-xs uppercase tracking-[0.2em] select-none">Bienes Seleccionados</span>
                    </div>
                </div>
            </div>

            <!-- Tabla de Datos -->
            <div class="bg-dark-850/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] shadow-2xl overflow-hidden mb-12">
                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark-800/30">
                                <th class="w-16 px-6 py-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" id="select-all" x-model="allSelected"
                                               class="w-5 h-5 rounded-lg border-dark-600 bg-dark-900 text-brand-purple focus:ring-brand-purple focus:ring-offset-dark-900 transition-all cursor-pointer">
                                    </div>
                                </th>
                                <th class="px-6 py-6 text-xs font-black text-gray-400 uppercase tracking-[0.15em] select-none">Número Bien</th>
                                <th class="px-6 py-6 text-xs font-black text-gray-400 uppercase tracking-[0.15em] select-none">Equipo / Serial</th>
                                <th class="px-6 py-6 text-xs font-black text-gray-400 uppercase tracking-[0.15em] select-none">Marca / Modelo</th>
                                <th class="px-6 py-6 text-xs font-black text-gray-400 uppercase tracking-[0.15em] select-none">Ubicación</th>
                                <th class="px-6 py-6 text-xs font-black text-gray-400 uppercase tracking-[0.15em] select-none">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($previewData as $index => $item)
                                <tr class="group hover:bg-brand-purple/[0.03] transition-all duration-300 {{ $item['ya_existe'] ? 'opacity-60 bg-rose-500/[0.02]' : '' }}">
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" value="{{ $index }}" x-model="selectedIndices"
                                                   class="row-checkbox w-5 h-5 rounded-lg border-dark-600 bg-dark-900 text-brand-purple focus:ring-brand-purple focus:ring-offset-dark-900 transition-all cursor-pointer">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-black text-white tracking-tight">{{ $item['numero_bien'] }}</span>
                                                @if($item['ya_existe'])
                                                    <span class="flex items-center px-2 py-0.5 bg-rose-500/20 text-rose-500 text-[10px] font-black rounded-md border border-rose-500/30 uppercase tracking-widest whitespace-nowrap">
                                                        <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                        Existente
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                                {{ \App\Models\CategoriaBien::find($item['categoria_bien_id'])?->nombre ?? 'PENDIENTE POR CATEGORIA' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-white uppercase">{{ $item['equipo'] }}</span>
                                            <span class="text-[10px] text-gray-500 font-medium uppercase">{{ $item['serial'] ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-white/90">{{ $item['marca'] }}</span>
                                            <span class="text-[10px] text-gray-500 uppercase">{{ $item['modelo'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-400 font-bold uppercase">
                                            @if($item['area_id'])
                                                {{ \App\Models\Area::find($item['area_id'])?->nombre }}
                                            @else
                                                <span class="text-brand-lila/60 italic">{{ $item['area_nombre'] ?: 'N/A' }}</span>
                                                <div class="text-[9px] text-brand-neon font-black lowercase tracking-tighter">Nueva Área</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-4 font-black rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-sm uppercase">
                                            {{ \App\Models\Estado::find($item['estado_id'])?->nombre ?? 'Sin Estado' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer de la Tabla -->
                <div class="px-8 py-8 bg-dark-800/40 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-3 group select-none">
                            <div class="w-4 h-4 bg-emerald-500/20 border border-emerald-500/40 rounded-md ring-4 ring-emerald-500/5 transition-all group-hover:scale-110"></div>
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Registros Válidos</span>
                        </div>
                        <div class="flex items-center gap-3 group select-none">
                            <div class="w-4 h-4 bg-rose-500/20 border border-rose-500/40 rounded-md ring-4 ring-rose-500/5 transition-all group-hover:scale-110"></div>
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Duplicados</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-gray-500 mr-2 uppercase tracking-widest">Acción Final:</span>
                        <x-button-premium type="button" @click="submitForm" text="Confirmar Importación" icon="o-check-circle" color="purple" class="px-10 py-3.5 rounded-2xl shadow-xl hover:shadow-brand-purple/20 transition-all active:scale-95" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('importPreview', (items) => ({
                allSelected: true,
                selectedIndices: [],
                items: items,
                
                init() {
                    // Seleccionar por defecto los que no existen
                    this.selectedIndices = this.items
                        .map((item, index) => item.ya_existe ? null : index.toString())
                        .filter(index => index !== null);
                        
                    this.updateAllSelectedStatus();

                    this.$watch('allSelected', value => {
                        if (value) {
                            this.selectedIndices = this.items.map((_, index) => index.toString());
                        } else if (this.selectedIndices.length === this.items.length) {
                            this.selectedIndices = [];
                        }
                    });

                    this.$watch('selectedIndices', () => {
                        this.updateAllSelectedStatus();
                    });
                },

                updateAllSelectedStatus() {
                    this.allSelected = this.selectedIndices.length === this.items.length;
                },

                get selectedCount() {
                    return this.selectedIndices.length;
                },

                toggleAll() {
                    this.allSelected = !this.allSelected;
                },

                submitForm() {
                    if (this.selectedIndices.length === 0) {
                        alert('Por favor, selecciona al menos un bien para importar.');
                        return;
                    }

                    const selectedData = this.selectedIndices.map(idx => this.items[parseInt(idx)]);
                    document.getElementById('bienes_json').value = JSON.stringify(selectedData);
                    document.getElementById('main-import-form').submit();
                }
            }))
        })
    </script>

    <style>
        @keyframes subtle-glow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .animate-subtle-glow {
            animation: subtle-glow 4s ease-in-out infinite;
        }
    </style>
</x-app-layout>
