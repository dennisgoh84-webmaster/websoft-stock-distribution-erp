<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 bg-slate-900">
            <div class="flex flex-col items-center gap-3">
                <a href="/" class="flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-600 shadow-lg shadow-amber-900/30">
                    <x-application-logo class="w-8 h-8 text-white" />
                </a>
                <div class="text-center">
                    <div class="font-bold text-lg text-white tracking-tight">{{ __('Websoft') }}</div>
                    <div class="text-xs text-slate-400">{{ __('Stock Distribution ERP') }}</div>
                </div>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-xl shadow-slate-950/40 overflow-hidden rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
