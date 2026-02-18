<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} — Iniciar Sesión</title>
        <meta name="description" content="Sistema de Gestión de Bienes — Acceso seguro al panel de administración">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Outfit:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
        
        <style>
            body { font-family: 'Outfit', sans-serif; }

            /* ===== Animated Grid Background ===== */
            .login-grid-bg {
                background-image: 
                    linear-gradient(rgba(168, 85, 247, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(168, 85, 247, 0.03) 1px, transparent 1px);
                background-size: 60px 60px;
                animation: gridShift 20s linear infinite;
            }
            @keyframes gridShift {
                0% { background-position: 0 0; }
                100% { background-position: 60px 60px; }
            }

            /* ===== Floating Orbs ===== */
            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.4;
                animation: orbFloat 8s ease-in-out infinite;
            }
            .orb-1 {
                width: 300px; height: 300px;
                background: radial-gradient(circle, #a855f7 0%, transparent 70%);
                top: -5%; left: -5%;
                animation-delay: 0s;
            }
            .orb-2 {
                width: 250px; height: 250px;
                background: radial-gradient(circle, #c084fc 0%, transparent 70%);
                bottom: -5%; right: 10%;
                animation-delay: -3s;
            }
            .orb-3 {
                width: 200px; height: 200px;
                background: radial-gradient(circle, #7c3aed 0%, transparent 70%);
                top: 40%; left: 60%;
                animation-delay: -5s;
            }
            @keyframes orbFloat {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -20px) scale(1.05); }
                66% { transform: translate(-20px, 20px) scale(0.95); }
            }

            /* ===== Particles ===== */
            .particle {
                position: absolute;
                width: 3px;
                height: 3px;
                background: #a855f7;
                border-radius: 50%;
                opacity: 0;
                animation: particleRise 6s ease-in-out infinite;
            }
            @keyframes particleRise {
                0% { opacity: 0; transform: translateY(100vh) scale(0); }
                20% { opacity: 0.8; }
                80% { opacity: 0.3; }
                100% { opacity: 0; transform: translateY(-20vh) scale(1); }
            }

            /* ===== Glowing Line ===== */
            .glow-line {
                position: absolute;
                width: 1px;
                height: 100%;
                background: linear-gradient(to bottom, transparent, #a855f7, transparent);
                opacity: 0.15;
            }

            /* ===== Card Shine Effect ===== */
            .login-card {
                position: relative;
                overflow: hidden;
            }
            .login-card::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: conic-gradient(from 0deg, transparent 0%, transparent 40%, rgba(168,85,247,0.06) 50%, transparent 60%, transparent 100%);
                animation: cardShine 8s linear infinite;
                pointer-events: none;
            }
            @keyframes cardShine {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* ===== Decorative Panel Animations ===== */
            .hex-grid {
                position: absolute;
                inset: 0;
                opacity: 0.08;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23a855f7' fill-opacity='0.4'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }

            .pulse-ring {
                position: absolute;
                border: 1px solid rgba(168, 85, 247, 0.15);
                border-radius: 50%;
                animation: pulseRing 4s ease-out infinite;
            }
            @keyframes pulseRing {
                0% { transform: scale(0.5); opacity: 0.5; }
                100% { transform: scale(2.5); opacity: 0; }
            }

            /* ===== Typing cursor ===== */
            .typing-cursor {
                display: inline-block;
                width: 2px;
                height: 1.1em;
                background: #a855f7;
                margin-left: 2px;
                animation: blink 1s step-end infinite;
                vertical-align: text-bottom;
            }
            @keyframes blink {
                0%, 100% { opacity: 1; }
                50% { opacity: 0; }
            }

            /* ===== Stats Counter Animation ===== */
            .stat-number {
                background: linear-gradient(135deg, #a855f7, #c084fc, #d8b4fe);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* ===== Input Focus Glow ===== */
            .input-glow:focus {
                box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1), 0 0 20px rgba(168, 85, 247, 0.05);
            }

            /* ===== Button Shimmer ===== */
            .btn-shimmer {
                position: relative;
                overflow: hidden;
            }
            .btn-shimmer::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
                animation: shimmer 3s ease-in-out infinite;
            }
            @keyframes shimmer {
                0% { left: -100%; }
                100% { left: 100%; }
            }

            /* ===== Smooth entrance ===== */
            .fade-up {
                animation: fadeUp 0.8s ease-out forwards;
                opacity: 0;
            }
            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .fade-up-delay-1 { animation-delay: 0.1s; }
            .fade-up-delay-2 { animation-delay: 0.2s; }
            .fade-up-delay-3 { animation-delay: 0.3s; }
            .fade-up-delay-4 { animation-delay: 0.4s; }
            .fade-up-delay-5 { animation-delay: 0.5s; }
        </style>
    </head>
    <body class="antialiased bg-[#030305] text-gray-300">
        
        <div class="min-h-screen flex login-grid-bg relative overflow-hidden">

            <!-- Floating Orbs (Global) -->
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>

            <!-- Particles -->
            @for ($i = 0; $i < 12; $i++)
            <div class="particle" style="left: {{ rand(5, 95) }}%; animation-delay: {{ $i * 0.5 }}s; animation-duration: {{ rand(5, 9) }}s;"></div>
            @endfor

            <!-- Vertical Glow Lines -->
            <div class="glow-line" style="left: 25%;"></div>
            <div class="glow-line" style="left: 50%;"></div>
            <div class="glow-line" style="left: 75%;"></div>

            <!-- ============================================ -->
            <!-- LEFT PANEL: Decorative / Branding           -->
            <!-- ============================================ -->
            <div class="hidden lg:flex lg:w-[55%] relative items-center justify-center p-12 overflow-hidden">
                
                <!-- Hex Grid Pattern -->
                <div class="hex-grid"></div>

                <!-- Pulse Rings -->
                <div class="pulse-ring" style="width: 200px; height: 200px; top: 50%; left: 50%; margin-top: -100px; margin-left: -100px;"></div>
                <div class="pulse-ring" style="width: 200px; height: 200px; top: 50%; left: 50%; margin-top: -100px; margin-left: -100px; animation-delay: 1.3s;"></div>
                <div class="pulse-ring" style="width: 200px; height: 200px; top: 50%; left: 50%; margin-top: -100px; margin-left: -100px; animation-delay: 2.6s;"></div>

                <!-- Main Content -->
                <div class="relative z-10 max-w-lg text-center">
                    
                    <!-- Logo Mark -->
                    <div class="fade-up fade-up-delay-1">
                        <div class="mx-auto w-24 h-24 rounded-3xl bg-gradient-to-br from-brand-purple/20 to-brand-lila/10 border border-brand-purple/20 flex items-center justify-center mb-10 backdrop-blur-sm">
                            <svg class="w-12 h-12 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>

                    <div class="fade-up fade-up-delay-2">
                        <h1 class="text-5xl font-extrabold text-white tracking-tight leading-tight mb-4">
                            Gestión de<br>
                            <span class="stat-number">Bienes DTIC</span>
                        </h1>
                    </div>

                    <div class="fade-up fade-up-delay-3">
                        <p class="text-gray-400 text-lg leading-relaxed mb-12 max-w-sm mx-auto">
                            Plataforma integral para la administración, control y seguimiento de activos institucionales.
                        </p>
                    </div>

                    <!-- Feature Pills -->
                    <div class="fade-up fade-up-delay-4 flex flex-wrap gap-3 justify-center mb-14">
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/[0.03] border border-white/[0.06] text-sm text-gray-400">
                            <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Seguridad Avanzada
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/[0.03] border border-white/[0.06] text-sm text-gray-400">
                            <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Tiempo Real
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/[0.03] border border-white/[0.06] text-sm text-gray-400">
                            <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Reportes
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="fade-up fade-up-delay-5 grid grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-extrabold stat-number">100%</div>
                            <div class="text-[10px] uppercase tracking-[0.15em] text-gray-500 mt-1 font-semibold">Digital</div>
                        </div>
                        <div class="text-center border-x border-white/5">
                            <div class="text-2xl font-extrabold stat-number">24/7</div>
                            <div class="text-[10px] uppercase tracking-[0.15em] text-gray-500 mt-1 font-semibold">Disponible</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-extrabold stat-number">SSL</div>
                            <div class="text-[10px] uppercase tracking-[0.15em] text-gray-500 mt-1 font-semibold">Encriptado</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ============================================ -->
            <!-- RIGHT PANEL: Login Form                      -->
            <!-- ============================================ -->
            <div class="w-full lg:w-[45%] flex items-center justify-center p-8 sm:p-16 relative">
                
                <!-- Subtle divider line -->
                <div class="hidden lg:block absolute left-0 top-[10%] bottom-[10%] w-px bg-gradient-to-b from-transparent via-brand-purple/20 to-transparent"></div>

                <div class="w-full max-w-xl">
                    
                    <!-- Mobile Logo (only visible on small screens) -->
                    <div class="lg:hidden text-center mb-10 fade-up">
                        <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-purple/20 to-brand-lila/10 border border-brand-purple/20 flex items-center justify-center mb-5 backdrop-blur-sm">
                            <svg class="w-10 h-10 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-extrabold text-white mb-2">Gestión de Bienes</h1>
                        <p class="text-gray-400 text-base">Sistema de control de activos</p>
                    </div>

                    <!-- Login Card -->
                    <div class="login-card bg-white/[0.02] backdrop-blur-xl rounded-[2.5rem] border border-white/[0.06] p-10 sm:p-14 shadow-2xl shadow-black/50">
                        
                        {{ $slot }}

                    </div>

                    <!-- Footer -->
                    <div class="mt-10 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-[0.2em] font-semibold">
                            &copy; {{ date('Y') }} {{ config('app.name') }} &bull; DTIC
                        </p>
                    </div>
                </div>
            </div>

        </div>
        @fluxScripts
    </body>
</html>
