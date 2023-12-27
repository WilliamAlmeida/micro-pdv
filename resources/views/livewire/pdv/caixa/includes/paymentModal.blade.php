<x-modal.card title="{{ $pagamentoForm->convenio ? 'Convênio' : 'Recebimento' }}" blur wire:model.defer="paymentModal" max-width="3xl"
    x-on:close="$dispatch('onClosePaymentModal')" persistent
    >

    {{-- @if($pagamentoForm->convenio) --}}
        <div class="{{ (!$pagamentoForm->convenio) ? 'hidden' : 'grid grid-cols-3 gap-x-3 gap-y-2' }}">

            <x-select
            label="Cliente"
            wire:model.defer="pagamentoForm.cliente_id"
            placeholder="Pesquise pelo nome"
            :async-data="route('api.clientes')"
            option-label="nome_fantasia"
            option-value="id"
            x-on:selected="$wire.pesquisar_cliente()"
            x-on:clear="$wire.pesquisar_cliente()"
            id="cliente_id"
            class="col-span-2"
            />

            <x-input label="CPF" value="{{ $cliente_selecionado?->cpf }}" disabled />

            {{-- <x-inputs.password label="Matrícula" wire:model.blur="pagamentoForm.convenio_matricula" /> --}}

        </div>
    {{-- @else --}}
    @if(!$pagamentoForm->convenio)
        <div class="grid grid-cols-3 gap-x-3 gap-y-2">

            <div class="col-span-full">
                <div class="flex flex-row justify-between font-bold">
                    <div>
                        <span class="text-sm sm:text-xl block">Total à Pagar</span>
                        <span class="text-2xl sm:text-3xl">R$ {{ number_format($caixa->venda?->valor_total, 2, ',', '.') }}</span>
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
                        <span class="text-sm sm:text-xl block">Total Informado</span>
                        <span class="text-2xl sm:text-3xl">R$ {{ number_format($pagamentoForm->informado, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <x-inputs.currency label="Desconto (R$)" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.500ms="pagamentoForm.desconto"
            id="desconto_valor"
            />

            <div class="col-span-full">
                <div class="grid grid-cols-2 gap-4">
                    <x-inputs.currency label="Dinheiro" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.500ms="pagamentoForm.dinheiro" />
                    <x-inputs.currency label="Ticket" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.500ms="pagamentoForm.ticket" />
                </div>
            </div>

            <div class="col-span-full">
                <div class="grid grid-cols-2 gap-4">
                    <x-inputs.currency label="Cartão de Débito" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.500ms="pagamentoForm.cartao_debito" />
                    <x-inputs.currency label="Cartão de Crédito" placeholder="0,00" thousands="." decimal="," wire:model.live.debounce.500ms="pagamentoForm.cartao_credito" />
                </div>
            </div>

            <div class="col-start-2 pt-2">
                <x-button primary label="Convênio" class="w-full" wire:click="$set('pagamentoForm.convenio', true)" />
            </div>
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            @if($pagamentoForm->convenio)
                <x-button flat label="Voltar" wire:click="$set('pagamentoForm.convenio', false); $wire.dispatch('setFocus', [{id:'desconto_valor'}])" />
            @else
                <x-button flat label="Cancelar" x-on:click="close" />
            @endif

            @if(!$pagamentoForm->convenio && $pagamentoForm->troco >= 0 || $pagamentoForm->convenio && $cliente_selecionado)
                <x-button positive label="Finalizar" wire:click="salvar_venda" />
            @endif
        </div>
    </x-slot>
</x-modal.card>