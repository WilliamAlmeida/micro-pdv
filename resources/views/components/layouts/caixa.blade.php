<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-cloak
    x-data="{ theme: localStorage.getItem('theme') || localStorage.setItem('theme', 'system') }"
    x-init="$watch('theme', val => localStorage.setItem('theme', val))"
    x-bind:class="{'dark': theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)}"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(tenant())
            <meta name="tenant" content="{{ tenant('id') }}">
        @endif

        <title>{{ tenant('nome_fantasia') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ global_asset('build/assets/plugins/cleave.min.js?id=1') }}" data-navigate-track></script>
    </head>
    <body class="font-sans antialiased scrollbar">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @if (isset($navigation))
                @include('layouts.app.navigation')
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <x-notifications />
        <x-dialog />
        @wireUiScripts
    </body>
</html>
