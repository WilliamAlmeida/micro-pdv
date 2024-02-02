<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-3">

                    @if(count(auth()->user()->empresa))
                        <span class="text-lg font-bold col-span-full">{{ __("Empresas") }}</span>
 
                        @foreach(auth()->user()->empresa as $empresa)
                            <x-card class="rounded-t-md rounded-b-md border-2 border-indigo-500 hover:bg-gray-400/10 dark:bg-none hover:dark:bg-white/5 transition-all" shadow="none">
                                <span class="text-lg uppercase text-indigo-500">{{ $empresa->nome_fantasia }}</span>
                                <div class="flex justify-end items-center">
                                    <x-button icon="login" label="Acessar" primary href="{{ route('tenant.dashboard', $empresa) }}" class="hover:-translate-y-1 transition-transform" />
                                </div>
                            </x-card>
                        @endforeach
                    @else
                        @if(auth()->user()->isAdmin() || auth()->user()->isEmpresa())
                            <div class="col-span-full">
                                <div class="flex flex-row justify-center items-center max-w-lg mx-auto gap-x-8">
                                    <x-icon name="home" class="w-24 h-24 text-indigo-200" />

                                    <div class="flex flex-col justify-around items-center gap-y-4">
                                        <span class="select-none">Cadastre sua primeira Empresa e<br/>desfrute desse incrivel PDV!</span>
                                        <x-button label="Registrar Empresa" primary href="{{ route('admin.empresa.create') }}" wire:navigate class="hover:-translate-y-2 transition-transform" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
