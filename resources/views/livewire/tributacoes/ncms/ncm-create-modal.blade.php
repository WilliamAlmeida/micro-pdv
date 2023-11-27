<div>
    <x-modal.card title="Cadastro de Ncm" blur wire:model.defer="ncmCreateModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-inputs.maskable
            label="Ncm"
            mask="####.##.##"
            placeholder="0000.00.00"
            wire:model.live="ncm_label"
            emitFormatted="true"
            />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Descrição" placeholder="Descrição" wire:model.live="descricao" />
            </div>

            <x-input type="number" label="Aliq. IPI" placeholder="000.00" wire:model.live="aliq_ipi" min="0" max="100" step="0.1" />
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
