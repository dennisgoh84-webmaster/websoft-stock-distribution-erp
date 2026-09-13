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
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50">
            <!-- Backdrop (mobile drawer) -->
            <div x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="sidebarOpen = false"
                    class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
                    style="display: none;"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0">
                @include('layouts.navigation')
            </aside>

            <!-- Main column -->
            <div class="lg:pl-64">
                <!-- Top bar -->
                <header class="sticky top-0 z-30 bg-white border-b border-slate-200">
                    <div class="flex items-center gap-3 h-16 px-4 sm:px-6 lg:px-8">
                        <!-- Hamburger (mobile) -->
                        <button type="button" @click="sidebarOpen = true" class="lg:hidden -ms-1 p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Search -->
                        <form method="GET" action="{{ route('products.index') }}" class="flex-1 max-w-md">
                            <label for="global-search" class="sr-only">{{ __('Search products') }}</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                                </svg>
                                <input id="global-search" type="search" name="search" value="{{ request('search') }}"
                                        placeholder="{{ __('Search products…') }}"
                                        class="w-full ps-9 pe-3 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            </div>
                        </form>

                        <!-- Account -->
                        <div class="ms-auto">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center gap-2 px-2 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-lg text-slate-600 bg-white hover:bg-slate-50 focus:outline-none transition ease-in-out duration-150">
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold uppercase shrink-0">
                                            {{ Str::substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                        <span class="hidden sm:block">{{ Auth::user()->name }}</span>

                                        <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="px-4 py-2 border-b border-slate-100">
                                        <div class="font-medium text-sm text-slate-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</div>
                                    </div>

                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <!-- Page Heading -->
                @isset($header)
                    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Page Content -->
                <main>
                    @if (session('status') || session('error'))
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                            @if (session('status'))
                                <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mt-2 first:mt-0 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
