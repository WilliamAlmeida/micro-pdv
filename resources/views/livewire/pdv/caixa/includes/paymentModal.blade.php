<x-modal.card title="Recebimento" blur wire:model.defer="paymentModal" max-width="3xl"
    x-on:close="$dispatch('onClosePaymentModal')"
    >
    <div class="grid grid-cols-3 gap-x-3 gap-y-2">
        <div class="col-span-full">
            <div class="flex flex-row justify-between font-bold">
                <div>
                    <span class="text-sm sm:text-xl block">
                        Total à Pagar
                    </span>
                    <span class="text-2xl sm:text-3xl">
                        R$ {{ number_format($caixa->venda?->valor_total, 2, ',', '.') }}
                    </span>
                </div>
                @if($pagamentoForm->troco != 0)
                    <div class="text-center">
                        @if($pagamentoForm->troco < 0)
                            <span class="text-sm sm:text-xl block bg-red-400 rounded-lg">Falta</span>
                            <span class="text-2xl sm:text-3xl">R$ {{ number_format($pagamentoForm->troco * -1, 2, ',', '.') }}</span>
                        @else
                            <span class="text-sm sm:text-xl block bg-lime-400 rounded-lg">Troco</span>
                            <span class="text-2xl sm:text-3xl">R$ {{ number_format($pagamentoForm->troco, 2, ',', '.') }}</span>
                        @endif
                    </div>
                @endif
                <div class="text-end">
                    <span class="text-sm sm:text-xl block">
                        Total Informado
                    </span>
                    <span class="text-2xl sm:text-3xl">
                        R$ {{ number_format($pagamentoForm->informado, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <x-inputs.currency label="Desconto (R$)" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.1s="pagamentoForm.desconto"
        id="desconto_valor"
        />

        <div class="col-span-full">
            <div class="grid grid-cols-2 gap-4">
                <x-inputs.currency label="Dinheiro" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.1s="pagamentoForm.dinheiro" />
                <x-inputs.currency label="Ticket" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.1s="pagamentoForm.ticket" />
            </div>
        </div>

        <div class="col-span-full">
            <div class="grid grid-cols-2 gap-4">
                <x-inputs.currency label="Cartão de Débito" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.1s="pagamentoForm.cartao_debito" />
                <x-inputs.currency label="Cartão de Crédito" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.1s="pagamentoForm.cartao_credito" />
            </div>
        </div>

        <div class="col-start-2 pt-2">
            <x-button primary label="Convênio" class="w-full" />
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <x-button flat label="Voltar" x-on:click="close" />
            @if($pagamentoForm->troco > 0)
                <x-button positive label="Finalizar" wire:click="salvar_venda" />
            @endif
        </div>
    </x-slot>
</x-modal.card>