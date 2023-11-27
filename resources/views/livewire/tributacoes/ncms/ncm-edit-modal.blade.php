<div>
    <x-modal.card title="Edição do Ncm" blur wire:model.defer="ncmEditModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-inputs.maskable
            label="Ncm"
            mask="####.##.##"
            placeholder="0000.00.00"
            wire:model.live="ncm_label"
            emitFormatted="true"
            id="edit_ncm"
            />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Descrição" placeholder="Descrição" wire:model.live="descricao" id="edit_descricao" />
            </div>

            <x-input type="number" label="Aliq. IPI" placeholder="000.00" wire:model.live="aliq_ipi" min="0" max="100" step="0.1" id="edt_aliq_ipi" />
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
