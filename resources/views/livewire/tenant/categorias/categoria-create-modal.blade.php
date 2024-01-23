<div>
    <x-modal.card title="Cadastro de Categoria" blur wire:model.defer="categoriaCreateModal" max-width="sm">
        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
            <x-input label="Título" placeholder="Título" wire:model="titulo" />
        </div>
     
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Salvar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
