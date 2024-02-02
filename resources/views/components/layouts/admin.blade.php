<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-cloak
    x-data="{ theme: localStorage.getItem('theme') || localStorage.setItem('theme', 'system') }"
    x-init="$watch('theme', val => localStorage.setItem('theme', val))"
    x-bind:class="{'dark': theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)}"
>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Conheça o Wil PDV, a solução em nuvem para restaurantes, mercados, lojas de roupas, petshops, lanchonetes, sorveterias e outros segmentos. Simplifique sua gestão de vendas e estoque com nosso sistema PDV intuitivo e eficiente.">
        <meta name="keywords" content="wil pdv, micro pdv na nuvem, restaurantes, mercado, loja de roupa, petshop, lanchonetes, sorveterias, sistema pdv, gestão de vendas, nuvem para empresas, software de ponto de venda, gerenciamento de estoque, controle de vendas, software para comércio">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(tenant())
            <meta name="tenant" content="{{ tenant('id') }}">
        @endif

        <title>{{ config('app.name', 'Laravel') }} LA</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased scrollbar">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.admin.navigation')

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

        <livewire:admin.artisan-panel />

        @wireUiScripts
    </body>
</html>
