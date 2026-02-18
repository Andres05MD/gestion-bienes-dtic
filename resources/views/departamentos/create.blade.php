<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-plus-circle" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Registrar Nuevo Departamento / Servicio') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Configuración del Sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('departamentos.store') }}" class="space-y-8">
                @csrf

                <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                    <!-- Inner Shine -->
                    <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                    
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                            <x-mary-icon name="o-building-office-2" class="w-6 h-6 text-brand-lila" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Detalles del Departamento</h3>
                    </div>

                    <div class="space-y-6">
                        <x-input-premium
                            id="nombre"
                            type="text"
                            label="Nombre del Departamento / Servicio"
                            name="nombre"
                            :value="old('nombre')"
                            required
                            autofocus
                            icon="o-building-office-2"
                            placeholder="Ej: Mantenimiento"
                        />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />

                        <div>
                            <label for="descripcion" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">
                                Descripción (Opcional)
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-start pt-3 pointer-events-none">
                                    <x-mary-icon name="o-document-text" class="h-5 w-5 text-gray-400 group-focus-within:text-brand-lila transition-colors" />
                                </div>
                                <textarea 
                                    id="descripcion" 
                                    name="descripcion" 
                                    rows="4" 
                                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-dark-900 border-none rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-brand-purple/50 transition-all text-sm resize-none" 
                                    placeholder="Breve descripción de la ubicación o función del departamento..."
                                >{{ old('descripcion') }}</textarea>
                            </div>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-white/5 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('departamentos.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300 order-2 sm:order-1">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" class="flex-1 w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group order-1 sm:order-2">
                            <x-mary-icon name="o-check" class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" />
                            {{ __('Guardar Departamento') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
