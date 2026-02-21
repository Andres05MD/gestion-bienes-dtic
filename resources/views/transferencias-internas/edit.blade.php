<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-pencil-square" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Editar Transferencia') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">
                    @if($bienesGrupo->count() > 1)
                    Grupo de Bienes: <span class="text-brand-lila">{{ $bienesGrupo->count() }} ítems</span>
                    @else
                    Bien: <span class="text-brand-lila">#{{ $transferencia->numero_bien }}</span>
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('transferencias-internas.update', $transferencia) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Bienes a Transferir -->
                        <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-list-bullet" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Bienes a Transferir</h3>
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
                                procedenciaSeleccionada: @js(old('procedencia_id', $transferencia->procedencia_id ?? $dticId)),
                                destino: @js(old('destino_id', $transferencia->destino_id ?? $dticId))
                            }" @set-selected-procedencia-id.window="procedenciaSeleccionada = $event.detail">
                                <div class="space-y-6">
                                    <x-select-premium
                                        name="procedencia_id"
                                        label="Procedencia"
                                        placeholder="Depto. de origen"
                                        required
                                        icon="o-building-office-2"
                                        :options="$departamentos->map(fn($d) => ['value' => $d->id, 'label' => $d->nombre])->toArray()"
                                        :value="old('procedencia_id', $transferencia->procedencia_id ?? $dticId)"
                                        @option-selected="procedenciaSeleccionada = $event.detail" />

                                    <div x-show="procedenciaSeleccionada == {{ $dticId }}" x-transition>
                                        <x-select-premium
                                            name="area_procedencia_id"
                                            label="Ubicación en DTIC (Área Origen)"
                                            placeholder="Seleccione Área de origen"
                                            icon="o-map-pin"
                                            :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                            :value="old('area_procedencia_id', $transferencia->bien?->area_id)"
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
                                        :value="old('destino_id', $transferencia->destino_id ?? $dticId)"
                                        @option-selected="destino = $event.detail" />

                                    <div x-show="destino == {{ $dticId }}" x-transition>
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
                        </div>

                        <div x-data="{ activeCard: false }" @click="activeCard = true" @click.outside="activeCard = false" :class="activeCard ? 'z-50' : 'z-30'" class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative transition-all duration-300">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                    <x-mary-icon name="o-shield-check" class="w-6 h-6 text-brand-lila" />
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Datos del Acta</h3>
                            </div>

                            <div class="space-y-6">
                                <x-date-input-premium name="fecha" label="Fecha de Transferencia" :value="$transferencia->fecha->format('Y-m-d')" required />
                                <x-select-premium name="estatus_acta_id" label="Estatus" placeholder="Seleccione estatus" required icon="o-clock" :options="$estatuses->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()" :value="old('estatus_acta_id', $transferencia->estatus_acta_id)" />
                                <x-date-input-premium name="fecha_firma" label="Fecha de Firma" :value="$transferencia->fecha_firma?->format('Y-m-d')" icon="o-pencil-square" />
                            </div>
                        </div>

                        <div class="p-2 space-y-4">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                                <x-mary-icon name="o-arrow-path" class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" />
                                {{ __('Actualizar Transferencia') }}
                            </button>
                            <a href="{{ route('transferencias-internas.index') }}" class="w-full inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                                {{ __('Descartar Cambios') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>