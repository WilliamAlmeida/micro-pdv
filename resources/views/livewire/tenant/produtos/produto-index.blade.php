<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Produtos') }}
        </h2>
    </x-slot>
    
    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end gap-2">
                <x-button white class="mb-3" label="Novo Produto" wire:click="$dispatch('create')" />
                <x-button white class="mb-3" label="Importar Produtos" x-on:click="$dispatch('import')" />
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:tenant.produtos.produto-table />
                </div>
            </div>
        </div>
    </div>

    <livewire:tenant.produtos.produto-create-modal />
    <livewire:tenant.produtos.produto-edit-modal />

    <livewire:tenant.produtos.produto-import-modal />
</div>