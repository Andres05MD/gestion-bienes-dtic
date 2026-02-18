<x-guest-layout>
    <div class="relative z-10">
        
        <!-- Header -->
        <div class="mb-10 fade-up fade-up-delay-1">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-1.5 h-10 rounded-full bg-gradient-to-b from-brand-purple to-brand-lila"></div>
                <div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">
                        Recuperar Acceso
                    </h2>
                    <p class="text-gray-500 text-base font-medium">Restablece tu contraseña de forma segura</p>
                </div>
            </div>
        </div>

        <div class="mb-8 text-base text-gray-400 leading-relaxed fade-up fade-up-delay-2 p-5 rounded-2xl bg-white/[0.03] border border-white/[0.05]">
            {{ __('¿Olvidaste tu contraseña? No hay problema. Simplemente déjanos saber tu dirección de correo electrónico y te enviaremos un enlace para restablecerla.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6 fade-up" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-3 fade-up fade-up-delay-3">
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
                        class="input-glow block w-full px-6 py-5 bg-white/[0.03] border border-white/[0.08] rounded-2xl text-white placeholder-gray-600 focus:bg-white/[0.05] focus:border-brand-purple/40 transition-all duration-300 text-base outline-none"
                    />
                    <div class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-brand-purple/0 to-transparent group-focus-within:via-brand-purple/40 transition-all duration-500"></div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="pt-4 fade-up fade-up-delay-4">
                <button 
                    type="submit" 
                    class="btn-shimmer w-full relative bg-gradient-to-r from-brand-purple via-brand-lila to-brand-purple text-white font-bold py-5 rounded-2xl shadow-lg shadow-brand-purple/20 hover:shadow-brand-purple/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 uppercase tracking-[0.2em] text-sm cursor-pointer"
                >
                    <span class="relative z-10 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Enviar Enlace
                    </span>
                </button>
            </div>
        </form>
        
        <div class="mt-12 pt-8 border-t border-white/[0.08] text-center fade-up fade-up-delay-5">
            <a href="{{ route('login') }}" class="group inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-brand-purple transition-all duration-300 uppercase tracking-widest">
                <span class="transform group-hover:-translate-x-1 transition-transform duration-300">&larr;</span> 
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</x-guest-layout>
