<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-pencil-square" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Editar Desincorporación') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">
                    @if($bienesGrupo->count() > 1)
                    Grupo de Bienes: <span class="text-brand-lila">{{ $bienesGrupo->count() }} ítems</span>
                    @else
                    Bien: <span class="text-brand-lila">#{{ $desincorporacion->numero_bien }}</span>
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('desincorporaciones.update', $desincorporacion) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Bienes a Desincorporar -->
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-list-bullet" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Bienes a Desincorporar</h3>
                            </div>

                            <div class="relative overflow-hidden rounded-2xl bg-brand-purple/5 border border-brand-purple/10 p-4 transition-all hover:bg-brand-purple/10 group shadow-sm dark:shadow-none mb-6">
                                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-16 h-16 bg-brand-purple/10 rounded-full blur-xl group-hover:bg-brand-purple/20 transition-all"></div>
                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="w-10 h-10 rounded-xl bg-brand-purple/20 flex items-center justify-center text-brand-lila shadow-lg shadow-brand-purple/10">
                                        <x-mary-icon name="o-information-circle" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-brand-lila uppercase tracking-[0.2em] mb-0.5">Nota</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">La lista de bienes no puede ser modificada tras su creación. Solo puede actualizar los datos compartidos del acta.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach($bienesGrupo as $bien)
                                <div class="p-5 border border-gray-100 dark:border-white/10 rounded-2xl bg-gray-50 dark:bg-[#1a1a1a] relative group transition-all duration-300">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">N° Bien</p>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $bien->numero_bien }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Descripción</p>
                                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $bien->descripcion }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Serial</p>
                                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $bien->serial ?: 'S/N' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-chat-bubble-bottom-center-text" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Observaciones</h3>
                            </div>
                            <textarea name="observaciones" rows="4" class="block w-full px-5 py-4 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:ring-2 focus:ring-brand-purple/20 transition-all resize-none" placeholder="Observaciones adicionales...">{{ old('observaciones', $desincorporacion->observaciones) }}</textarea>
                            <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Ubicación -->
                        <div x-data="{ 
                            activeCard: false, 
                            procedenciaSeleccionada: '{{ old('procedencia_id', $desincorporacion->procedencia_id ?? $dticId) }}', 
                            destinoSeleccionado: '{{ old('destino_id', $desincorporacion->destino_id ?? $dticId) }}',
                            informes: @js(old('numero_informe', $desincorporacion->numero_informe ? explode(', ', $desincorporacion->numero_informe) : [''])),
                            
                            agregarInforme() {
                                this.informes.push('');
                            },

                            removerInforme(index) {
                                if (this.informes.length > 1) {
                                    this.informes.splice(index, 1);
                                } else {
                                    this.informes[0] = '';
                                }
                            }
                        }" @set-selected-procedencia-id.window="procedenciaSeleccionada = $event.detail" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-40'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-building-office-2" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Ubicación</h3>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-6">
                                    <x-select-premium name="procedencia_id" label="Procedencia" placeholder="Depto. de origen" required icon="o-building-office-2" :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()" :value="old('procedencia_id', $desincorporacion->procedencia_id ?? $dticId)" @option-selected="procedenciaSeleccionada = $event.detail" />
                                    <!-- Áreas omitidas intencionadamente debido a que en edición con múltiples orígenes DTIC puede variar, 
                                         pero dejaremos la selección de área DTIC única si es necesario. DesincorporacionController@update guarda lo que haya. -->
                                </div>

                                <div class="space-y-6">
                                    <x-select-premium name="destino_id" label="Destino" placeholder="Depto. de destino" required icon="o-map-pin" :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()" :value="old('destino_id', $desincorporacion->destino_id ?? $dticId)" @option-selected="destinoSeleccionado = $event.detail" />
                                </div>
                            </div>
                        </div>

                        <!-- Estado y Datos -->
                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-shield-check" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Datos del Acta</h3>
                            </div>
                            <div class="space-y-6">
                                <x-date-input-premium name="fecha" label="Fecha" :value="$desincorporacion->fecha->format('Y-m-d')" required />
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between ml-1">
                                        <label class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em]">N° Informe(s)</label>
                                        <button type="button" @click="agregarInforme()" class="text-[10px] font-bold text-brand-purple hover:text-brand-lila uppercase tracking-widest transition-colors flex items-center gap-1 cursor-pointer">
                                            <x-mary-icon name="o-plus" class="w-3 h-3 border border-current rounded-full" />
                                            Añadir
                                        </button>
                                    </div>

                                    <template x-for="(informe, idx) in informes" :key="idx">
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <x-mary-icon name="o-document-text" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                                            </div>
                                            <input
                                                type="text"
                                                name="numero_informe[]"
                                                x-model="informes[idx]"
                                                placeholder="00-00-00"
                                                class="block w-full pl-11 pr-12 py-4 h-14 bg-white dark:bg-[#1a1a1a] border-gray-200 dark:border-none rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-gray-50 dark:focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-sm dark:shadow-none appearance-none" />
                                            <button
                                                type="button"
                                                @click="removerInforme(idx)"
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 transition-colors"
                                                x-show="informes.length > 1 || (informes.length === 1 && informes[0] !== '')">
                                                <x-mary-icon name="o-trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <x-select-premium name="estatus_acta_id" label="Estatus" placeholder="Seleccione estatus" required icon="o-clock" :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()" :value="old('estatus_acta_id', $desincorporacion->estatus_acta_id)" />
                            </div>
                        </div>

                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] cursor-pointer group">
                                <x-mary-icon name="o-arrow-path" class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" />
                                {{ __('Actualizar Desincorporación') }}
                            </button>
                            <a href="{{ route('desincorporaciones.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar Cambios') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>