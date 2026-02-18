<x-guest-layout>
    <div class="relative z-10">
        
        <!-- Header -->
        <div class="mb-10 fade-up fade-up-delay-1">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-1.5 h-10 rounded-full bg-gradient-to-b from-brand-purple to-brand-lila"></div>
                <div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">
                        Bienvenido
                    </h2>
                    <p class="text-gray-500 text-base font-medium">Ingresa tus credenciales para continuar</p>
                </div>
            </div>
        </div>

        <!-- Status Alert -->
        @if (session('status'))
            <div class="flex items-center gap-3 p-5 mb-8 rounded-xl bg-brand-purple/10 border border-brand-purple/20 text-brand-neon fade-up" role="alert">
                <svg class="w-6 h-6 shrink-0 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-base font-medium">{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Correo Electrónico -->
            <div class="space-y-3 fade-up fade-up-delay-2">
                <label for="email" class="text-xs font-bold text-gray-400 uppercase tracking-[0.15em] flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-purple/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Correo Electrónico
                </label>
                <div class="relative group">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        placeholder="usuario@hospital.gob.ve" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="input-glow block w-full px-6 py-5 bg-white/[0.03] border border-white/[0.08] rounded-2xl text-white placeholder-gray-600 focus:bg-white/[0.05] focus:border-brand-purple/40 transition-all duration-300 text-base outline-none"
                    />
                    <div class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-brand-purple/0 to-transparent group-focus-within:via-brand-purple/40 transition-all duration-500"></div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Contraseña -->
            <div class="space-y-3 fade-up fade-up-delay-3" x-data="{ show: false }">
                <label for="password" class="text-xs font-bold text-gray-400 uppercase tracking-[0.15em] flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-purple/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Contraseña
                </label>
                <div class="relative group">
                    <input 
                        :type="show ? 'text' : 'password'" 
                        name="password" 
                        id="password" 
                        placeholder="••••••••••" 
                        required 
                        autocomplete="current-password"
                        class="input-glow block w-full px-6 py-5 pr-14 bg-white/[0.03] border border-white/[0.08] rounded-2xl text-white placeholder-gray-600 focus:bg-white/[0.05] focus:border-brand-purple/40 transition-all duration-300 text-base outline-none"
                    />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-5 flex items-center cursor-pointer text-gray-600 hover:text-brand-purple transition-colors duration-300 focus:outline-none">
                        <svg x-show="!show" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                    <div class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-brand-purple/0 to-transparent group-focus-within:via-brand-purple/40 transition-all duration-500"></div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Botón de Login -->
            <div class="pt-4 fade-up fade-up-delay-4">
                <button 
                    type="submit" 
                    class="btn-shimmer w-full relative bg-gradient-to-r from-brand-purple via-brand-lila to-brand-purple text-white font-bold py-5 rounded-2xl shadow-lg shadow-brand-purple/20 hover:shadow-brand-purple/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 uppercase tracking-[0.2em] text-sm cursor-pointer"
                >
                    <span class="relative z-10 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Acceder al Sistema
                    </span>
                </button>
            </div>

        </form>

        <!-- Security Badge -->
        <div class="mt-10 fade-up fade-up-delay-5">
            <div class="flex items-center justify-center gap-2 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span class="text-xs uppercase tracking-[0.15em] font-semibold">Conexión segura y encriptada</span>
            </div>
        </div>

    </div>
</x-guest-layout>
