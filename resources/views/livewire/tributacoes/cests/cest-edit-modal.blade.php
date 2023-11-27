<div>
    <x-modal.card title="Edição do Cest" blur wire:model.defer="cestEditModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-inputs.maskable
            label="Cest"
            mask="##.###.##"
            placeholder="00.000.00"
            wire:model.live="cest_label"
            emitFormatted="true"
            id="edit_cest"
            />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Descrição" placeholder="Descrição" wire:model.live="descricao" id="edit_descricao" />
            </div>

            <x-select
            label="Ncm"
            wire:model.defer="ncm_id"
            placeholder="Ncm"
            :async-data="route('api.ncms')"
            option-label="ncm"
            option-value="id"
            id="edit_ncm_id"
            />
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
