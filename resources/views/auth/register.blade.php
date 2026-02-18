<x-guest-layout>
    <div class="relative p-10 lg:p-14 overflow-hidden">
        <!-- Floating Glows -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-40 h-40 bg-brand-purple/20 blur-[80px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-40 h-40 bg-brand-lila/20 blur-[80px] rounded-full pointer-events-none"></div>

        <div class="relative z-10">
            <!-- Header section con Flowbite Typography -->
            <div class="text-center mb-12 space-y-4">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-linear-to-br from-brand-purple/20 to-brand-lila/20 border border-white/10 shadow-2xl backdrop-blur-sm mb-4">
                    <x-mary-icon name="o-user-plus" class="w-10 h-10 text-white" />
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight uppercase">
                    Únete <span class="text-transparent bg-clip-text bg-linear-to-r from-brand-purple to-brand-lila">Hoy</span>
                </h1>
                <p class="text-gray-500 text-[11px] font-bold tracking-[0.4em] uppercase opacity-60 font-[Outfit]">Hospital Management System</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <!-- Nombre Completo -->
                <div class="space-y-2">
                    <label for="name" class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em] ml-1">
                        Nombre Completo <span class="text-brand-purple">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-mary-icon name="o-user" class="w-5 h-5 text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                        </div>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name') }}" 
                            placeholder="Tu nombre completo" 
                            required 
                            autofocus 
                            autocomplete="name"
                            class="block w-full pl-11 pr-4 py-4 h-14 bg-[#1a1a1a] border-none rounded-2xl text-white placeholder-gray-500 focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-none appearance-none"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Correo Electrónico -->
                <div class="space-y-2">
                    <label for="email" class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em] ml-1">
                        Correo Electrónico <span class="text-brand-purple">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-mary-icon name="o-envelope" class="w-5 h-5 text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            placeholder="nombre@hospital.com" 
                            required 
                            autocomplete="username"
                            class="block w-full pl-11 pr-4 py-4 h-14 bg-[#1a1a1a] border-none rounded-2xl text-white placeholder-gray-500 focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-none appearance-none"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Contraseña -->
                <div class="space-y-2" x-data="{ show: false }">
                    <label for="password" class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em] ml-1">
                        Contraseña <span class="text-brand-purple">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-mary-icon name="o-lock-closed" class="w-5 h-5 text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                        </div>
                        <input 
                            :type="show ? 'text' : 'password'" 
                            name="password" 
                            id="password" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="new-password"
                            class="block w-full pl-11 pr-12 py-4 h-14 bg-[#1a1a1a] border-none rounded-2xl text-white placeholder-gray-500 focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-none appearance-none"
                        />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-gray-500 hover:text-white transition-colors focus:outline-none">
                            <x-mary-icon x-show="!show" name="o-eye" class="w-5 h-5" />
                            <x-mary-icon x-show="show" name="o-eye-slash" class="w-5 h-5" />
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirmar Contraseña -->
                <div class="space-y-2" x-data="{ show: false }">
                    <label for="password_confirmation" class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em] ml-1">
                        Confirmar Contraseña <span class="text-brand-purple">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-mary-icon name="o-lock-closed" class="w-5 h-5 text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
                        </div>
                        <input 
                            :type="show ? 'text' : 'password'" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="new-password"
                            class="block w-full pl-11 pr-12 py-4 h-14 bg-[#1a1a1a] border-none rounded-2xl text-white placeholder-gray-500 focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-none appearance-none"
                        />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-gray-500 hover:text-white transition-colors focus:outline-none">
                            <x-mary-icon x-show="!show" name="o-eye" class="w-5 h-5" />
                            <x-mary-icon x-show="show" name="o-eye-slash" class="w-5 h-5" />
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Flux UI: Submit Button -->
                <div class="pt-6">
                    <flux:button type="submit" variant="primary" class="relative w-full h-16 rounded-2xl bg-linear-to-r from-brand-purple to-indigo-600 text-white border-none font-black text-xs tracking-[0.3em] uppercase transition-all duration-500 hover:shadow-[0_0_40px_rgba(168,85,247,0.4)] hover:scale-[1.01]">
                        CREAR CUENTA
                    </flux:button>
                </div>
            </form>

            <!-- Link de Iniciar Sesión con Estilo Preline -->
            <div class="mt-10 pt-8 border-t border-white/5 text-center">
                <p class="text-xs font-bold text-gray-500 tracking-[0.2em] uppercase mb-3">¿Ya eres parte del equipo?</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-x-2 text-sm text-brand-lila hover:text-white font-black tracking-widest uppercase transition-all">
                    Iniciar Sesión
                    <x-mary-icon name="o-arrow-right-on-rectangle" class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
