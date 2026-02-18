<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Áreas') }}
            </h2>
            @can('crear areas')
            <a href="{{ route('areas.create') }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                {{ __('Nueva Área') }}
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 overflow-hidden shadow-2xl sm:rounded-2xl border border-dark-800">
                <div class="p-6">
                    @if($areas->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="bg-dark-700/50 p-6 rounded-full mb-4">
                                <svg class="w-12 h-12 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">No hay áreas registradas</h3>
                            <p class="text-gray-400 text-sm max-w-sm mb-6">Comienza registrando las áreas de la institución para ubicar los bienes.</p>
                            <a href="{{ route('areas.create') }}" class="text-brand-lila font-bold hover:text-brand-purple transition-colors text-sm">
                                + Crear primera área
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-dark-800/50">
                                        <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Nombre</th>
                                        <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Descripción</th>
                                        <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest border-r-0">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-dark-800">
                                    @foreach ($areas as $area)
                                        <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-white">
                                                {{ $area->nombre }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">
                                                {{ Str::limit($area->descripcion, 50) ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-3">
                                                @can('editar areas')
                                                <a href="{{ route('areas.edit', $area) }}" class="text-amber-400 hover:text-amber-300 transition" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                @endcan

                                                @can('eliminar areas')
                                                <button type="button" 
                                                        @click="window.dispatchEvent(new CustomEvent('open-deletion-modal', { detail: { action: '{{ route('areas.destroy', $area) }}' } }))" 
                                                        class="text-rose-500 hover:text-rose-400 transition transform active:scale-90" 
                                                        title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $areas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Deletion Confirmation Modal -->
    <x-confirm-deletion 
        title="¿Eliminar Área?" 
        message="¿Estás seguro de que deseas eliminar esta área?"
    />

    <!-- Success Modal -->
    @if(session('success'))
        <x-modal name="success-modal" :show="true" focusable>
            <div class="p-8 bg-white dark:bg-dark-850 relative overflow-hidden">
                <!-- Decorative Glow -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-brand-purple/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-brand-lila/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-linear-to-tr from-brand-lila to-brand-purple rounded-full flex items-center justify-center shadow-lg shadow-brand-purple/30 mb-6 animate-bounce">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>

                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">
                        ¡Operación Exitosa!
                    </h3>
                    
                    <p class="text-gray-500 dark:text-gray-300 font-medium mb-8 text-lg">
                        {{ session('success') }}
                    </p>

                    <button x-on:click="$dispatch('close-modal', 'success-modal')" class="w-full inline-flex justify-center items-center px-6 py-4 bg-dark-800 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-dark-700 active:scale-95 transition-all duration-150">
                        Entendido
                    </button>
                </div>
            </div>
        </x-modal>
    @endif
</x-app-layout>
