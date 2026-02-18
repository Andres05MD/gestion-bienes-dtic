<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="antialiased bg-gray-50 dark:bg-dark-950 text-gray-900 dark:text-white min-h-screen flex flex-col font-sans selection:bg-brand-purple/30 selection:text-brand-lila">
        
        <!-- Background Elements -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-brand-purple/20 rounded-full blur-[120px] opacity-20 animate-pulse"></div>
            <div class="absolute top-[30%] -right-[10%] w-[40%] h-[40%] bg-brand-lila/10 rounded-full blur-[100px] opacity-20"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-[30%] h-[30%] bg-indigo-500/10 rounded-full blur-[80px] opacity-20"></div>
        </div>

        <!-- Navbar -->
        <nav class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-tr from-brand-purple to-brand-lila p-2 rounded-xl shadow-lg shadow-brand-purple/20">
                    <x-application-logo class="w-6 h-6 text-white" />
                </div>
                <span class="font-bold text-xl tracking-tight hidden sm:block">Hospital Manager</span>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-white/5 border border-white/10 hover:bg-white/10 transition-all backdrop-blur-md">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-brand-lila transition-colors">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-brand-purple hover:bg-brand-lila text-white shadow-lg shadow-brand-purple/25 hover:shadow-brand-lila/40 transition-all transform hover:-translate-y-0.5">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- Use Case / Hero Section -->
        <main class="relative z-10 flex-1 flex flex-col justify-center items-center px-6 text-center max-w-5xl mx-auto mt-10 sm:mt-0">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-purple/10 border border-brand-purple/20 text-brand-lila text-xs font-bold uppercase tracking-widest mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-brand-lila animate-ping"></span>
                Gestión de Activos v1.0
            </div>

            <h1 class="text-5xl sm:text-7xl font-black tracking-tight mb-8 leading-[1.1] animate-fade-in-up delay-100">
                Control Total de <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-lila via-brand-purple to-indigo-500">
                    Infraestructura Hospitalaria
                </span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mb-12 leading-relaxed animate-fade-in-up delay-200">
                Optimiza la administración de bienes, equipos médicos y recursos con nuestra plataforma centralizada. Seguridad, trazabilidad y eficiencia en un solo lugar.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 items-center animate-fade-in-up delay-300">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold bg-white text-gray-900 shadow-xl hover:shadow-2xl hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                        Ir al Dashboard
                        <svg class="w-5 h-5 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold bg-gradient-to-r from-brand-purple to-brand-lila text-white shadow-xl shadow-brand-purple/30 hover:shadow-brand-lila/50 transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl text-base font-bold bg-white/5 border border-white/10 hover:bg-white/10 backdrop-blur-sm transition-all flex items-center justify-center">
                        Crear Cuenta
                    </a>
                @endauth
            </div>

            <!-- Stats Preview -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 w-full max-w-4xl animate-fade-in-up delay-500">
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <div class="text-3xl font-black text-white mb-1">99%</div>
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">Uptime</div>
                </div>
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <div class="text-3xl font-black text-white mb-1">+10k</div>
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">Activos</div>
                </div>
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <div class="text-3xl font-black text-white mb-1">Secure</div>
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">Data</div>
                </div>
                <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <div class="text-3xl font-black text-white mb-1">24/7</div>
                    <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">Support</div>
                </div>
            </div>

        </main>

        <footer class="relative z-10 w-full py-8 text-center text-sm text-gray-500 dark:text-gray-600">
            &copy; {{ date('Y') }} Hospital Manager. Todos los derechos reservados.
        </footer>

    </body>
</html>
