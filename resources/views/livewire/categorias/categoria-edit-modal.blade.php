<div>
    <x-modal.card title="Edição de Categoria" blur wire:model.defer="categoriaEditModal" max-width="md">
        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
            <x-input label="Título" placeholder="Título" wire:model="titulo" id="edit_titulo" />
        </div>
     
        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                @if(!$categoria?->trashed())
                    <x-button flat negative label="Desativar" wire:click="delete" />
                @else
                    <x-button flat positive label="Restaurar" wire:click="restore" />
                    <x-button flat negative label="Deletar" wire:click="delete" />
                @endif
     
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Atualizar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
