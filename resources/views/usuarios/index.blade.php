<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                {{ __('Gestión de Usuarios') }}
            </h2>
            @can('gestionar usuarios')
                <a href="{{ route('usuarios.create') }}" class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-brand-purple/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Nuevo Usuario') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 overflow-hidden shadow-2xl sm:rounded-2xl border border-dark-800">
                <div class="p-6">
                    


                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ route('usuarios.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Búsqueda -->
                        <div class="md:col-span-2 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o email..." class="block w-full pl-11 pr-4 py-3 bg-dark-900 border-none rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/20 transition-all text-sm" />
                        </div>

                        <!-- Filtro Rol -->
                        <div class="flex gap-2 md:col-span-2">
                            <select name="rol" class="flex-1 bg-dark-900 border-none rounded-xl text-white py-3 px-4 text-sm focus:ring-2 focus:ring-brand-purple/20 appearance-none cursor-pointer">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('rol') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-3 bg-brand-purple/20 text-brand-lila rounded-xl hover:bg-brand-purple/30 transition-colors font-bold text-xs uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            </button>
                            @if(request()->anyFilled(['buscar', 'rol']))
                                <a href="{{ route('usuarios.index') }}" class="px-4 py-3 bg-rose-500/10 text-rose-400 rounded-xl hover:bg-rose-500/20 transition-colors font-bold text-xs flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Nombre</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Email</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Rol</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Fecha Registro</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest border-r-0">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-white">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-linear-to-tr from-brand-purple to-brand-lila flex items-center justify-center text-white font-bold text-xs mr-3">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($user->roles as $role)
                                                <span class="px-3 py-1 inline-flex text-[10px] leading-4 font-black rounded-lg bg-brand-purple/10 text-brand-lila border border-brand-purple/20 shadow-sm uppercase">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-3">
                                            <a href="{{ route('usuarios.edit', $user) }}" class="text-amber-400 hover:text-amber-300 transition" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            
                                            @can('gestionar usuarios')
                                                <button type="button" 
                                                        @click="$dispatch('open-reset-modal', { action: '{{ route('usuarios.reset-password', $user) }}', name: '{{ $user->name }}' })"
                                                        class="text-brand-lila hover:text-brand-purple transition transform active:scale-90" 
                                                        title="Resetear Contraseña">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                </button>

                                                <button type="button" 
                                                        @click="window.dispatchEvent(new CustomEvent('open-deletion-modal', { detail: { action: '{{ route('usuarios.destroy', $user) }}' } }))" 
                                                        class="text-rose-500 hover:text-rose-400 transition transform active:scale-90" 
                                                        title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                    @if(request()->anyFilled(['buscar', 'rol']))
                                                        No se encontraron usuarios con esos filtros.
                                                    @else
                                                        No hay usuarios registrados.
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
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deletion Confirmation Modal -->
    <x-confirm-deletion 
        title="¿Eliminar Usuario?" 
        message="¿Estás seguro de que deseas eliminar este usuario? Perderá el acceso al sistema de forma inmediata."
    />

    <!-- Reset Password Modal -->
    <div x-data="{ open: false, action: '', name: '' }"
         x-on:open-reset-modal.window="open = true; action = $event.detail.action; name = $event.detail.name"
         x-on:close-modal.window="if ($event.detail === 'reset-password-modal') open = false"
         class="relative z-50"
         style="display: none;"
         x-show="open">
        
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" 
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="open = false"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-dark-850 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-dark-800">
                    
                    <form :action="action" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-3 bg-brand-purple/10 rounded-full">
                                <svg class="w-6 h-6 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                                    Resetear Contraseña
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Usuario: <span class="font-bold text-brand-lila" x-text="name"></span>
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nueva Contraseña</label>
                                <input type="password" name="password" id="password" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-dark-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-purple/20 focus:border-brand-purple transition-all text-gray-900 dark:text-white" placeholder="Mínimo 8 caracteres">
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-dark-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-purple/20 focus:border-brand-purple transition-all text-gray-900 dark:text-white" placeholder="Repite la contraseña">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" @click="open = false" class="px-5 py-2.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-xs uppercase tracking-widest transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-linear-to-r from-brand-purple to-brand-lila text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:brightness-110 shadow-lg shadow-brand-purple/20 transition-all active:scale-95">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
