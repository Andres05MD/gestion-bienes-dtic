<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Bienes DTIC') }}
            </h2>
            <div class="flex gap-2" x-data="{}">
                @can('crear bienes')
                <button @click="$dispatch('open-modal', 'import-bienes')" class="inline-flex items-center px-5 py-2.5 bg-dark-800 border border-dark-700 rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-dark-700 active:scale-95 transition-all duration-150 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    {{ __('Importar') }}
                </button>
                <a href="{{ route('bienes.create') }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Nuevo Bien DTIC') }}
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 overflow-hidden shadow-2xl sm:rounded-2xl border border-dark-800">
                <div class="p-6">



                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ route('bienes.index') }}" class="mb-8 flex flex-col xl:flex-row gap-4">
                        <!-- Búsqueda -->
                        <div class="xl:w-1/3 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por equipo, número, marca, serial..." class="block w-full pl-11 pr-4 h-12 bg-dark-900 border-none rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/20 transition-all text-sm" />
                        </div>

                        <!-- Otros Filtros -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Filtro Categoría -->
                            <x-select-premium
                                name="categoria_bien_id"
                                placeholder="Categoría"
                                icon="o-bookmark"
                                :options="$categorias->map(fn($c) => ['value' => $c->id, 'label' => $c->nombre])->toArray()"
                                :value="request('categoria_bien_id')" />

                            <!-- Filtro Estado -->
                            <x-select-premium
                                name="estado_id"
                                placeholder="Estado"
                                icon="o-shield-check"
                                :options="$estados->map(fn($e) => ['value' => $e->id, 'label' => $e->nombre])->toArray()"
                                :value="request('estado_id')" />

                            <!-- Filtro Área + Botones -->
                            <div class="flex gap-2 min-w-0">
                                <div class="flex-1 min-w-0">
                                    <x-select-premium
                                        name="area_id"
                                        placeholder="Área"
                                        icon="o-building-office-2"
                                        :options="$areas->map(fn($a) => ['value' => $a->id, 'label' => $a->nombre])->toArray()"
                                        :value="request('area_id')"
                                        class="w-full" />
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <button type="submit" class="p-4 bg-brand-purple/20 text-brand-lila rounded-2xl hover:bg-brand-purple/30 transition-colors shadow-lg shadow-brand-purple/5" title="Buscar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>
                                    @if(request()->anyFilled(['buscar', 'estado_id', 'categoria_bien_id', 'area_id']))
                                    <a href="{{ route('bienes.index') }}" class="p-4 bg-rose-500/10 text-rose-400 rounded-2xl hover:bg-rose-500/20 transition-colors shadow-lg shadow-rose-500/5 flex items-center" title="Limpiar Filtros">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Número Bien</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Equipo / Serial</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Marca/Modelo</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Ubicación</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Estado</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest border-r-0">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($bienes as $bien)
                                <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-white">
                                        <div class="font-bold">{{ $bien->numero_visible }}</div>
                                        <div class="text-[10px] font-medium text-dark-text uppercase">{{ $bien->categoria?->nombre ?? 'PENDIENTE POR CATEGORIA' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <div class="font-bold text-base">{{ $bien->equipo }}</div>
                                        <div class="text-[10px] font-medium text-dark-text uppercase">{{ $bien->serial ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">
                                        <span class="font-medium text-white/90">{{ $bien->marca }}</span> <span class="text-xs opacity-50 ml-1">{{ $bien->modelo }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal text-sm text-dark-text min-w-[200px]">
                                        {{ $bien->area?->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-4 font-black rounded-lg {{ $bien->estado?->badgeClasses() ?? 'bg-gray-500/10 text-gray-400' }} shadow-sm uppercase">
                                            {{ $bien->estado?->nombre ?? 'Sin Estado' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-3">
                                        @can('ver bienes')
                                        <a href="{{ route('bienes.show', $bien) }}" class="text-sky-400 hover:text-sky-300 transition" title="Ver detalle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        @endcan
                                        @can('editar bienes')
                                        <a href="{{ route('bienes.edit', $bien) }}" class="text-amber-400 hover:text-amber-300 transition" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @endcan
                                        @can('eliminar bienes')
                                        <button type="button"
                                            @click="window.dispatchEvent(new CustomEvent('open-deletion-modal', { detail: { action: '{{ route('bienes.destroy', $bien) }}' } }))"
                                            class="text-rose-500 hover:text-rose-400 transition transform active:scale-90"
                                            title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                @if(request()->anyFilled(['buscar', 'estado_id', 'categoria_bien_id', 'area_id']))
                                                No se encontraron bienes con los filtros aplicados.
                                                @else
                                                No hay bienes registrados.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $bienes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deletion Confirmation Modal -->
    <x-confirm-deletion
        title="¿Eliminar Bien?"
        message="¿Estás seguro de que deseas eliminar este bien? Esta acción es irreversible." />

    <!-- Success Modal -->

    <!-- Import Modal -->
    <x-modal name="import-bienes" maxWidth="lg">
        <div class="p-8 bg-dark-850 relative overflow-hidden">
            <!-- Decoration Glow -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-brand-purple/10 rounded-full blur-3xl pointer-events-none"></div>

            <form action="{{ route('bienes.import-preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-white leading-tight uppercase tracking-tight">Importar Bienes</h3>
                            <div class="h-1 w-12 bg-brand-purple rounded-full mt-2"></div>
                        </div>
                        <button type="button" @click="$dispatch('close-modal', 'import-bienes')" class="p-2 text-gray-500 hover:text-white hover:bg-white/5 rounded-xl transition-all">
                            <x-mary-icon name="o-x-mark" class="w-6 h-6" />
                        </button>
                    </div>

                    <div class="space-y-6">
                        <p class="text-gray-400 font-medium">Selecciona tu archivo de inventario (ODS, XLS, XLSX). Procesaremos categorías y áreas automáticamente.</p>

                        <div class="relative group">
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-white/10 rounded-3xl cursor-pointer bg-white/[0.02] hover:bg-brand-purple/[0.03] hover:border-brand-purple/50 transition-all duration-500">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="p-4 bg-brand-purple/10 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-500">
                                        <x-mary-icon name="o-cloud-arrow-up" class="w-8 h-8 text-brand-lila" />
                                    </div>
                                    <p class="mb-2 text-sm text-gray-300"><span class="font-bold text-white">Haz clic</span> o arrastra el archivo</p>
                                    <span id="file-name-import" class="text-[10px] text-brand-neon font-black uppercase tracking-[0.2em]">Esperando archivo...</span>
                                </div>
                                <input type="file" name="archivo" id="archivo" accept=".ods,.xls,.xlsx" class="hidden" @change="document.getElementById('file-name-import').innerText = $event.target.files[0].name" required />
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 flex flex-col sm:flex-row-reverse gap-3">
                        <x-button-premium type="submit" text="Procesar Archivo" icon="o-rocket-launch" color="purple" class="w-full sm:w-auto" />
                        <button type="button" @click="$dispatch('close-modal', 'import-bienes')" class="flex-1 inline-flex justify-center items-center px-6 py-4 bg-dark-800 border border-transparent rounded-xl font-bold text-xs text-gray-400 uppercase tracking-widest hover:bg-dark-700 hover:text-white active:scale-95 transition-all duration-150">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>