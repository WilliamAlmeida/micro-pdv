<div>
    <x-modal.card title="Cadastro de Cfop" blur wire:model.defer="cfopCreateModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-inputs.maskable
            label="Ncm"
            mask="#.###"
            placeholder="0.000"
            wire:model.live="cfop_label"
            emitFormatted="true"
            />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Descrição" placeholder="Descrição" wire:model="descricao" />
            </div>
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Aplicação" placeholder="Aplicação" wire:model="aplicacao" />
            </div>
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
