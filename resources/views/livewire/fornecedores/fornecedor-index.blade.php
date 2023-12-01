<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Fornecedores') }}
        </h2>
    </x-slot>
    
    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end">
                <x-button white class="mb-3" label="Novo Fornecedor" wire:click="$dispatch('create')" />
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:fornecedores.fornecedor-table />
                </div>
            </div>
        </div>
    </div>

    <livewire:fornecedores.fornecedor-create-modal />
    {{-- <livewire:fornecedores.fornecedor-edit-modal /> --}}
</div>