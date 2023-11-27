<div>
    <x-modal.card title="Edição do Cfop" blur wire:model.defer="cfopEditModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-inputs.maskable
            label="Cfop"
            mask="#.###"
            placeholder="0.000"
            wire:model.live="cfop_label"
            emitFormatted="true"
            id="edit_cfop"
            />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Descrição" placeholder="Descrição" wire:model="descricao" id="descricao" />
            </div>
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Aplicação" placeholder="Aplicação" wire:model="aplicacao" id="aplicacao" />
            </div>
        </div>
     
        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                <x-button flat negative label="Deletar" wire:click="delete" />
     
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Atualizar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>
