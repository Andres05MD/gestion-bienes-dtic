<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-brand-purple/20 rounded-2xl">
                <x-mary-icon name="o-pencil-square" class="w-8 h-8 text-brand-lila" />
            </div>
            <div>
                <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                    {{ __('Editar Usuario') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">
                    Cuenta: <span class="text-brand-lila">{{ $usuario->email }}</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-brand-purple/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-96 h-96 bg-brand-lila/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Card Principal: Datos de Perfil -->
                <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative group transition-all duration-500">
                    <!-- Inner Shine -->
                    <div class="absolute inset-0 rounded-[2.5rem] border border-white/5 pointer-events-none"></div>
                    
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                            <x-mary-icon name="o-identification" class="w-6 h-6 text-brand-lila" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Perfil de Usuario</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input-premium
                            name="name"
                            label="Nombre Completo"
                            :value="old('name', $usuario->name)"
                            required
                            icon="o-user"
                        />

                        <div class="space-y-2">
                             <x-input-premium
                                name="email"
                                type="email"
                                label="Correo Electrónico"
                                :value="old('email', $usuario->email)"
                                required
                                icon="o-envelope"
                            />
                        </div>
                    </div>
                </div>

                <!-- Card Mixta: Rol y Seguridad -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                <x-mary-icon name="o-shield-check" class="w-6 h-6 text-brand-lila" />
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Rol</h3>
                        </div>

                        <div class="space-y-4">
                            @php
                                $roleOptions = $roles->map(fn($role) => [
                                    'value' => $role->name,
                                    'label' => ucfirst($role->name)
                                ])->toArray();
                            @endphp

                            <x-select-premium
                                name="role"
                                label="Rol en Sistema"
                                :options="$roleOptions"
                                :value="old('role', $userRole)"
                                required
                                icon="o-shield-check"
                            />
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-white dark:bg-dark-850/40 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 relative">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-brand-purple/10 rounded-xl flex items-center justify-center">
                                <x-mary-icon name="o-key" class="w-6 h-6 text-brand-lila" />
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Nueva Contraseña</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-input-premium
                                name="password"
                                type="password"
                                label="Contraseña"
                                placeholder="••••••••"
                                icon="o-lock-closed"
                            />

                            <x-input-premium
                                name="password_confirmation"
                                type="password"
                                label="Validar"
                                placeholder="••••••••"
                                icon="o-check-badge"
                            />
                        </div>
                        <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-[0.05em] mt-4 ml-1 italic">* Dejar en blanco si no desea cambiar la contraseña actual.</p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-between gap-6 pt-4">
                    <a href="{{ route('usuarios.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-transparent border border-gray-200 dark:border-white/10 rounded-2xl font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-300">
                        {{ __('Cancelar Cambios') }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-12 py-5 bg-linear-to-r from-brand-lila to-brand-purple border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-[0.2em] hover:brightness-110 active:scale-95 transition-all duration-300 shadow-[0_10px_30px_rgba(168,85,247,0.3)] hover:shadow-[0_15px_40px_rgba(168,85,247,0.5)] cursor-pointer group">
                        <x-mary-icon name="o-arrow-path" class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" />
                        {{ __('Guardar Cambios') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
