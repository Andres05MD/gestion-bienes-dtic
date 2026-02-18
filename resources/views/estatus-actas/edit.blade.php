<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-pencil-square" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Editar Estatus de Acta') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Configuración de Extras</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('estatus-actas.update', $estatusActa) }}" class="space-y-8">
                @csrf
                @method('PATCH')

                <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                    <!-- Inner Shine -->
                    <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                    
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                            <x-mary-icon name="o-tag" class="w-6 h-6 text-brand-lila" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Detalles del Estatus</h3>
                    </div>

                    <div class="space-y-6">
                        <x-input-premium
                            id="nombre"
                            type="text"
                            label="Nombre del Estatus"
                            name="nombre"
                            :value="old('nombre', $estatusActa->nombre)"
                            required
                            autofocus
                            icon="o-tag"
                            placeholder="Ej: Actas Listas, Pendiente, Acta Firmada falta Copia"
                        />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />

                        <x-input-premium
                            id="color"
                            type="color"
                            label="Color identificador"
                            name="color"
                            :value="old('color', $estatusActa->color ?? '#a855f7')"
                            icon="o-swatch"
                            class="h-16"
                        />
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-white/5 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('estatus-actas.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300 order-2 sm:order-1">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" class="flex-1 w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group order-1 sm:order-2">
                            <x-mary-icon name="o-check" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" />
                            {{ __('Actualizar Estatus') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
