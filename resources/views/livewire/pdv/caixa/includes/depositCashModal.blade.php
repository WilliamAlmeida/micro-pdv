<x-modal.card title="Entrada" blur wire:model.defer="depositCashModal" max-width="sm"
x-on:close="$dispatch('onCloseDepositCashModal')"
>
    <div class="grid grid-cols-1 gap-y-4">
        <x-inputs.currency label="Valor" placeholder="0,00" prefix="R$" thousands="." decimal="," wire:model="entradaForm.valor"
        id="entrada_valor"
        wire:keyup.enter="salvar_entrada"
        />

        <x-input label="Descrição" placeholder="Informe um motivo" wire:model="entradaForm.motivo"
        wire:keyup.enter="salvar_entrada"
        />
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <div class="flex gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="Salvar" wire:click="salvar_entrada" />
            </div>
        </div>
    </x-slot>
</x-modal.card>