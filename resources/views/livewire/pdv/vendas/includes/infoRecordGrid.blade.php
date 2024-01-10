@if($venda_selecionada)
    <div class="dark:text-white px-2">
        <strong>Formas de Pagamento:</strong> <span>{{ $formas_pagamento }}</span>
    </div>

    @if($formas_pagamento != 'CONVENIO')
        <div class="flex sm:flex-row justify-between font-bold text-center dark:text-white py-2 px-2">
            <div>
                Sub Total<br/>R$ @money($venda_selecionada->itens->sum('valor_total'))
            </div>
            <div>
                Desconto<br/>R$ @money($venda_selecionada->desconto)
            </div>
            <div>
                Total<br/>R$ @money($venda_selecionada->valor_total - $venda_selecionada->desconto)
            </div>
            <div>
                Informado<br/>R$ @money($venda_selecionada->pagamentos->sum('valor'))
            </div>
            <div>
                Troco<br/>R$ @money($venda_selecionada->troco)
            </div>
        </div>
    @endif

    <div class="flex flex-row items-stretch sm:justify-end gap-2 p-2 border-t-2">
        @if($formas_pagamento != 'CONVENIO')
            <div>
                <x-button primary label="Emitir Nota" disabled />
            </div>
        @endif
        <div>
            <x-button primary label="Reimprimir Venda" wire:click="imprimir_venda" />
        </div>
        <div>
            <x-button negative label="Cancelar Venda" wire:click="cancelar_venda" />
        </div>
    </div>
@endif