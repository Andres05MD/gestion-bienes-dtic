<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-white bg-gray-100 dark:bg-[#050505] min-h-full">
        
        <!-- Decoration glow (Optional: Lower opacity for dashboard) -->
        <div class="fixed top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[500px] h-[500px] bg-brand-purple/10 blur-[120px] rounded-full pointer-events-none opacity-40 z-0"></div>
        <div class="fixed bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-brand-lila/10 blur-[120px] rounded-full pointer-events-none opacity-40 z-0"></div>

        <div class="relative z-10 flex min-h-screen">
            @include('layouts.navigation')

            <!-- Main Content Wrapper -->
            <div class="md:pl-64 flex-1 flex flex-col min-h-screen transition-all duration-300">
                
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/50 dark:bg-dark-900/50 backdrop-blur-md border-b border-gray-100 dark:border-dark-800 shadow-sm pt-16 md:pt-0 sticky top-0 z-20 transition-all duration-300">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main x-data="{}" class="flex-1 {{ !isset($header) ? 'pt-16 md:pt-0' : '' }}">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
