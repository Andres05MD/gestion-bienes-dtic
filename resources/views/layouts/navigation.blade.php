<div x-data="{ open: false }">
    <!-- Sidebar Desktop -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0a0a0a] border-r border-white/5 hidden md:flex flex-col transition-transform duration-300">
        <!-- Logo -->
        <div class="shrink-0 flex items-center justify-center h-20 border-b border-white/5 bg-[#0a0a0a]/50">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-10 w-auto fill-current text-brand-lila drop-shadow-[0_0_15px_rgba(168,85,247,0.5)]" />
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                {{ __('Dashboard') }}
                @if(isset($totalAlertas) && $totalAlertas > 0)
                <span class="ml-auto bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg shadow-rose-500/40 animate-pulse">
                    {{ $totalAlertas > 99 ? '99+' : $totalAlertas }}
                </span>
                @endif
            </a>


            @can('gestionar usuarios')
            <a href="{{ route('usuarios.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('usuarios.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('usuarios.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ __('Usuarios') }}
            </a>
            @endcan

            <!-- Gestión de Inventario -->
            <div class="px-4 mt-6 mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Inventario</span>
            </div>

            @can('ver bienes')
            <a href="{{ route('bienes.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('bienes.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('bienes.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ __('Bienes DTIC') }}
            </a>
            @endcan

            @can('ver bienes externos')
            <a href="{{ route('bienes-externos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('bienes-externos.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('bienes-externos.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                {{ __('Bienes Externos') }}
            </a>
            @endcan

            <!-- Operaciones -->
            <div class="px-4 mt-6 mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Operaciones</span>
            </div>

            @can('ver transferencias')
            <a href="{{ route('transferencias-internas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('transferencias-internas.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('transferencias-internas.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                {{ __('Transferencias') }}
            </a>
            @endcan

            @can('ver transferencias')
            <a href="{{ route('mantenimientos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('mantenimientos.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('mantenimientos.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ __('Mantenimientos') }}
            </a>
            @endcan

            @can('ver desincorporaciones')
            <a href="{{ route('desincorporaciones.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('desincorporaciones.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('desincorporaciones.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Desincorporaciones') }}
            </a>
            @endcan

            @can('ver distribuciones')
            <a href="{{ route('distribuciones-direccion.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('distribuciones-direccion.*') ? 'bg-brand-purple/10 text-white shadow-[0_0_20px_rgba(168,85,247,0.15)] border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('distribuciones-direccion.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                {{ __('Distribución Dir.') }}
            </a>
            @endcan

            <!-- Categoría de Bienes -->
            <div class="px-4 mt-6 mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Extras</span>
            </div>

            @can('ver areas')
            <a href="{{ route('areas.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('areas.*') ? 'bg-brand-purple/10 text-white border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('areas.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ __('Áreas') }}
            </a>
            @endcan

            @can('ver estados')
            <a href="{{ route('estados.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('estados.*') ? 'bg-brand-purple/10 text-white border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('estados.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Estados') }}
            </a>
            @endcan

            @can('ver categorias')
            <a href="{{ route('categorias.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('categorias.*') ? 'bg-brand-purple/10 text-white border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('categorias.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ __('Categorías de Bienes') }}
            </a>
            @endcan

            @can('ver departamentos')
            <a href="{{ route('departamentos.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('departamentos.*') ? 'bg-brand-purple/10 text-white border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('departamentos.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ __('Deptos / Servicios') }}
            </a>
            @endcan

            @can('ver estatus actas')
            <a href="{{ route('estatus-actas.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('estatus-actas.*') ? 'bg-brand-purple/10 text-white border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('estatus-actas.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Estatus Actas') }}
            </a>
            @endcan

            @can('gestionar usuarios')
            <a href="{{ route('activity-log.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('activity-log.*') ? 'bg-brand-purple/10 text-white border border-brand-purple/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('activity-log.*') ? 'text-brand-lila' : 'text-gray-500 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Historial Actividad') }}
            </a>
            @endcan

        </nav>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-white/5 bg-[#0a0a0a]/30">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="h-10 w-10 rounded-full bg-linear-to-tr from-brand-purple to-brand-lila flex items-center justify-center text-white font-bold text-sm shadow-lg">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate w-32">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center justify-center w-full px-4 py-2 text-xs font-bold text-red-400 uppercase tracking-widest bg-red-500/10 rounded-lg hover:bg-red-500/20 transition-colors border border-red-500/20 hover:border-red-500/40">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    {{ __('Cerrar Sesión') }}
                </a>
            </form>
        </div>
    </aside>

    <!-- Mobile Header -->
    <div class="md:hidden bg-[#0a0a0a] border-b border-white/5 h-16 flex items-center justify-between px-4 fixed top-0 left-0 right-0 z-50">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-8 w-auto fill-current text-brand-lila" />
        </a>
        <div class="flex items-center gap-2">
            @if(isset($totalAlertas) && $totalAlertas > 0)
            <a href="{{ route('dashboard') }}" class="relative p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1 right-1 h-4 w-4 bg-rose-500 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-lg shadow-rose-500/40 animate-pulse">
                    {{ $totalAlertas > 9 ? '!' : $totalAlertas }}
                </span>
            </a>
            @endif
            <button @click="open = !open" class="text-gray-400 hover:text-white transition focus:outline-none">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="open" @click="open = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/80 z-40 md:hidden backdrop-blur-sm"></div>

    <!-- Mobile Sidebar -->
    <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0a0a0a] border-r border-white/5 md:hidden flex flex-col pt-16">
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Dashboard') }}
                @if(isset($totalAlertas) && $totalAlertas > 0)
                <span class="ml-auto bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg shadow-rose-500/40">
                    {{ $totalAlertas > 99 ? '99+' : $totalAlertas }}
                </span>
                @endif
            </a>
            @can('gestionar usuarios')
            <a href="{{ route('usuarios.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('usuarios.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Usuarios') }}
            </a>
            @endcan

            <!-- Gestión de Inventario -->
            <div class="px-4 mt-6 mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Inventario</span>
            </div>

            @can('ver bienes')
            <a href="{{ route('bienes.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('bienes.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Bienes DTIC') }}
            </a>
            @endcan

            @can('ver bienes externos')
            <a href="{{ route('bienes-externos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('bienes-externos.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Bienes Externos') }}
            </a>
            @endcan

            <!-- Operaciones -->
            <div class="px-4 mt-6 mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Operaciones</span>
            </div>

            @can('ver transferencias')
            <a href="{{ route('transferencias-internas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('transferencias-internas.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Transferencias') }}
            </a>
            @endcan

            @can('ver transferencias')
            <a href="{{ route('mantenimientos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('mantenimientos.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Mantenimientos') }}
            </a>
            @endcan

            @can('ver desincorporaciones')
            <a href="{{ route('desincorporaciones.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('desincorporaciones.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Desincorporaciones') }}
            </a>
            @endcan

            @can('ver distribuciones')
            <a href="{{ route('distribuciones-direccion.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('distribuciones-direccion.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Distribución Dir.') }}
            </a>
            @endcan

            <!-- Categoría de Bienes -->
            <div class="px-4 mt-6 mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Extras</span>
            </div>

            @can('ver areas')
            <a href="{{ route('areas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('areas.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Áreas') }}
            </a>
            @endcan

            @can('ver estados')
            <a href="{{ route('estados.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('estados.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Estados') }}
            </a>
            @endcan

            @can('ver categorias')
            <a href="{{ route('categorias.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('categorias.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Categorías de Bienes') }}
            </a>
            @endcan

            @can('ver departamentos')
            <a href="{{ route('departamentos.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('departamentos.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Deptos / Servicios') }}
            </a>
            @endcan

            @can('ver estatus actas')
            <a href="{{ route('estatus-actas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('estatus-actas.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Estatus Actas') }}
            </a>
            @endcan

            @can('gestionar usuarios')
            <a href="{{ route('activity-log.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('activity-log.*') ? 'bg-brand-purple/10 text-white' : 'text-gray-400' }}">
                {{ __('Historial Actividad') }}
            </a>
            @endcan
        </nav>

        <div class="p-4 border-t border-dark-800">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="h-8 w-8 rounded-full bg-brand-purple flex items-center justify-center text-white text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="text-sm text-white">{{ Auth::user()->name }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block w-full text-center py-2 text-xs font-bold text-red-400 uppercase bg-red-500/10 rounded-lg">
                    {{ __('Cerrar Sesión') }}
                </a>
            </form>
        </div>
    </div>
</div>