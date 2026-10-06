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
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-cream text-primary-900">
        <x-flash-messages />

        <div class="min-h-screen grid lg:grid-cols-2">
            <aside class="relative hidden lg:flex flex-col justify-between bg-primary-900 text-gray-400 p-12 overflow-hidden">
                <a href="/" wire:navigate class="relative inline-flex text-white" aria-label="PropNest home">
                    <x-brand-logo tone="light" icon-size="h-8 w-8" text-size="text-[22px]" />
                </a>

                <svg class="absolute -right-24 -bottom-16 w-[560px] text-white/[0.04]" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <path d="M7 17.5 16 9l9 8.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11.5 23 16 18.75 20.5 23" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <div class="relative max-w-md">
                    <p class="display !text-white text-5xl">Homes, <em class="text-accent-300">honestly</em> listed.</p>
                    <p class="mt-6 text-[15px] leading-relaxed">Every listing is reviewed before it goes live, and every agent is checked by a person. You always know who you are talking to.</p>
                </div>

                <p class="relative font-mono text-[11px] uppercase tracking-[0.14em] text-gray-500">Verified agents · Real prices · Direct messages</p>
            </aside>

            <main class="flex flex-col items-center justify-center px-4 py-12 sm:px-6">
                <a href="/" wire:navigate class="lg:hidden mb-10 text-primary-900" aria-label="PropNest home">
                    <x-brand-logo icon-size="h-8 w-8" text-size="text-[22px]" />
                </a>

                <div class="w-full max-w-md bg-white rounded-xl ring-1 ring-gray-900/10 p-8 sm:p-10">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
