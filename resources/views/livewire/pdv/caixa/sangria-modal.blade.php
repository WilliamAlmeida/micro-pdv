<x-modal.card title="Sangria" blur wire:model.defer="withdrawalCashModal" max-width="sm"
{{-- x-on:close="$dispatch('onCloseWithdrawalCashModal')" --}}
>
    <div class="grid grid-cols-1 gap-y-4">
        <x-inputs.currency label="Valor" placeholder="0,00" prefix="R$" thousands="." decimal="," wire:model="sangriaForm.valor"
        id="sangria_valor"
        wire:keyup.enter="salvar_sangria"
        inputmode="numeric"
        />

        <x-input label="Descrição" placeholder="Informe um motivo" wire:model="sangriaForm.motivo"
        wire:keyup.enter="salvar_sangria"
        />
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <div class="flex gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="Salvar" wire:click="salvar_sangria" />
            </div>
        </div>
    </x-slot>
</x-modal.card>