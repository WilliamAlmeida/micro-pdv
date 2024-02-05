<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Funções') }}
        </h2>
    </x-slot>
    
    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end" wire:loading.remove wire:target="create">
                <x-button white class="mb-3" label="Nova Permissão" wire:click="$dispatch('create')"  />
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:admin.permissions.permission-table/>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin.permissions.permission-create-modal />

    {{-- <livewire:admin.permissions.permission-edit-modal /> --}}

    {{-- <x-modal.card title="Opções em Massa" wire:model.defer="simpleModal" align="center">
        <div class="grid grid-cols-1 gap-4">
        <p class="text-gray-600">
            As ações só ocorrerão nas Linhas Selecionadas.
        </p>

        <x-button negative label="Deletar Permissões" wire:click="$dispatchTo('admin.permissions.permission-table', 'bulkToggleDeleteEvent')" />
        </div>
    </x-modal.card> --}}
</div>