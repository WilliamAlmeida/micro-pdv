<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Funções') }}
        </h2>
    </x-slot>

    <livewire:admin.roles.role-create-modal lazy />
    <livewire:admin.roles.role-edit-modal lazy />

    <div class="pt-6 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end" wire:loading.remove wire:target="create">
                <x-button white class="mb-3" label="Nova Função" wire:click="$dispatch('create')"  />
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:admin.roles.role-table/>
                </div>
            </div>
        </div>
    </div>
</div>