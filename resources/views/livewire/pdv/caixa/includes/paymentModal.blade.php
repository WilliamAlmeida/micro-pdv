<div x-data="{
    convenio: @entangle('pagamentoForm.convenio'),
    cliente_selecionado: @entangle('cliente_selecionado'),
    desconto: @entangle('pagamentoForm.desconto'),
    dinheiro: @entangle('pagamentoForm.dinheiro'),
    ticket: @entangle('pagamentoForm.ticket'),
    cartao_debito: @entangle('pagamentoForm.cartao_debito'),
    cartao_credito: @entangle('pagamentoForm.cartao_credito'),
    informado: @entangle('pagamentoForm.informado'),
    troco: @entangle('pagamentoForm.troco'),
    valor_total: @entangle('pagamentoForm.valor_total'),

    init() {
        this.configInputMask()
    },

    configInputMask() {
        const options = {numeral: true, numeralDecimalMark: ',', delimiter: '.', numeralDecimalScale: 2, numeralThousandsGroupStyle: 'thousand'};

        new Cleave('input[x-model=dinheiro]', options);
        new Cleave('input[x-model=ticket]', options);
        new Cleave('input[x-model=cartao_debito]', options);
        new Cleave('input[x-model=cartao_credito]', options);
        new Cleave('input[x-model=desconto]', options);
    },

    calculeChangeBack() {
        function fixNumber(value) {
            if(!value.length) return 0;

            value = value.replace('.', '');
            value = value.replace(',', '.');
            return parseFloat(parseFloat(value).toFixed(2));
        }

        let
            dinheiro        = this.dinheiro != null ? fixNumber(this.dinheiro) : 0,
            ticket          = this.ticket != null ? fixNumber(this.ticket) : 0,
            cartao_debito   = this.cartao_debito != null ? fixNumber(this.cartao_debito) : 0,
            cartao_credito  = this.cartao_credito != null ? fixNumber(this.cartao_credito) : 0,
            desconto        = this.desconto != null ? fixNumber(this.desconto) : 0;

        this.informado = dinheiro + ticket + cartao_debito + cartao_credito + desconto;
        if(this.informado != 0) {
            troco = this.informado - parseFloat(this.valor_total);
        }else{
            troco = 0;
        }

        this.troco = troco.toFixed(2);
    }
}">

<x-modal.card title="{{ $pagamentoForm->convenio ? 'Convênio' : 'Recebimento' }}" blur wire:model.defer="paymentModal" max-width="3xl"
    x-on:close="$dispatch('onClosePaymentModal')" persistent
    >

    <div x-show="convenio == true" class="grid grid-cols-3 gap-x-3 gap-y-2">
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

    <div x-show="convenio == false">
        <div class="grid grid-cols-3 gap-x-3 gap-y-2">
            <!-- Conteúdo anterior permanece inalterado -->
            
            <!-- Desconto -->
            <x-input label="Desconto (R$)" x-mask:dynamic="$money($input, ',')" placeholder="0,00" thousands="." decimal="," x-model="desconto" id="desconto_valor" @input="calculeChangeBack" />
            
            <!-- Dinheiro e Ticket -->
            <div class="col-span-full">
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Dinheiro" placeholder="0,00" x-model="dinheiro" @input="calculeChangeBack" />
                    <x-input label="Ticket" placeholder="0,00" x-model="ticket" @input="calculeChangeBack" />
                </div>
            </div>
            
            <!-- Cartão de Débito e Cartão de Crédito -->
            <div class="col-span-full">
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Cartão de Débito" x-mask:dynamic="$money($input, ',')" placeholder="0,00" thousands="." decimal="," x-model="cartao_debito" @input="calculeChangeBack" />
                    <x-input label="Cartão de Crédito" x-mask:dynamic="$money($input, ',')" placeholder="0,00" thousands="." decimal="," x-model="cartao_credito" @input="calculeChangeBack" />
                </div>
            </div>
            
            <!-- Convênio -->
            <div class="col-start-2 pt-2 pb-2">
                <x-button primary label="Convênio" class="w-full" x-on:click="convenio = true" />
            </div>
        </div>
        
        <!-- Cálculo do Troco -->
        <div class="flex flex-row justify-between font-bold">
            <div>
                <span class="text-sm sm:text-xl block">Total à Pagar</span>
                <span class="text-2xl sm:text-3xl" x-text="'R$ ' + Math.abs(valor_total).toFixed(2)"></span>
            </div>
            <div class="text-center" x-show="(troco != 0)">
                <div x-show="(troco < 0)">
                    <span class="text-sm sm:text-xl block bg-red-400 rounded-lg">Falta</span>
                    <span class="text-2xl sm:text-3xl" x-text="'R$ ' + Math.abs(troco).toFixed(2)"></span>
                </div>
                <div x-show="(troco > 0)">
                    <span class="text-sm sm:text-xl block bg-lime-400 rounded-lg">Troco</span>  
                    <span class="text-2xl sm:text-3xl" x-text="'R$ ' + Math.abs(troco).toFixed(2)"></span>
                </div>
            </div>
            <div class="text-end">
                <span class="text-sm sm:text-xl block">Total Informado</span>
                <span class="text-2xl sm:text-3xl" x-text="'R$ ' + informado.toFixed(2)"></span>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <x-button x-show="convenio" flat label="Voltar" wire:click="$set('pagamentoForm.convenio', false); $wire.dispatch('setFocus', [{id:'desconto_valor'}])" />
            <x-button x-show="!convenio" flat label="Cancelar" x-on:click="close" />

            <x-button x-show="(convenio == false && troco >= 0 && informado || convenio == true && cliente_selecionado !== null)" positive label="Finalizar" wire:click="salvar_venda" />
        </div>
    </x-slot>
</x-modal.card>

</div>