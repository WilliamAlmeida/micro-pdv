<div>
    <x-modal.card title="Cadastro de Cest" blur wire:model.defer="cestCreateModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-inputs.maskable
            label="Cest"
            mask="##.###.##"
            placeholder="00.000.00"
            wire:model.live="cest_label"
            emitFormatted="true"
            />
     
            <div class="col-span-1 sm:col-span-2">
                <x-input label="Descrição" placeholder="Descrição" wire:model.live="descricao" />
            </div>

            <x-select
            label="Ncm"
            wire:model.defer="ncm_id"
            placeholder="Ncm"
            :async-data="route('api.ncms')"
            option-label="ncm"
            option-value="id"
            />
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
