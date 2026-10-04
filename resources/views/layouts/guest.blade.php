<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PropNest') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <x-flash-messages />

        <div class="min-h-screen flex flex-col lg:flex-row bg-cream">
            <div
                class="relative lg:w-1/2 lg:min-h-screen h-40 sm:h-56 lg:h-auto flex-shrink-0"
                style="background: linear-gradient(180deg, rgba(9,38,39,.75), rgba(9,38,39,.92)), url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1200&h=1600&fit=crop') center/cover;"
            >
                <div class="relative h-full flex flex-col justify-between p-6 lg:p-10">
                    <a href="/" wire:navigate class="inline-flex text-white">
                        <x-brand-logo icon-size="h-10 w-10" text-size="text-xl lg:text-2xl" />
                    </a>

                    <div class="hidden lg:block">
                        <p class="font-heading text-2xl xl:text-3xl font-semibold text-white leading-snug mb-6">
                            Find your place.<br>List with confidence.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white/15 text-white backdrop-blur-sm">500+ listings</span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white/15 text-white backdrop-blur-sm">Verified agents</span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white/15 text-white backdrop-blur-sm">Nationwide coverage</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 flex items-center justify-center px-4 py-10 sm:px-6 lg:px-12">
                <div class="w-full max-w-md animate-fade-in-up">
                    <a href="/" wire:navigate class="lg:hidden flex justify-center mb-6 text-primary-700">
                        <x-brand-logo icon-size="h-9 w-9" text-size="text-xl" />
                    </a>

                    <div class="bg-white rounded-2xl shadow-2xl shadow-primary-900/10 ring-1 ring-black/5 p-8 sm:p-10 lg:p-12">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
