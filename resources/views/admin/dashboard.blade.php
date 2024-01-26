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
                <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col gap-3">
                    <span class="text-lg font-bold">{{ __("Empresas") }}</span>

                    @foreach(auth()->user()->empresa as $empresa)
                        <a class="hover:text-indigo-500 transition-colors" href="{{ route('tenant.dashboard', $empresa) }}">{{ $empresa->nome_fantasia }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
