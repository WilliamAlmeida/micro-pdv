@if($convenio_selecionado)
    <div class="dark:text-white px-2">
        <strong>Formas de Pagamento:</strong> <span>{{ $formas_pagamento }}</span>
    </div>

    <div class="flex sm:flex-row justify-between font-bold text-center dark:text-white py-2 px-2">
        <div>
            Sub Total<br/>R$ @money($convenio_selecionado->itens->sum('valor_total'))
        </div>
        <div>
            Desconto<br/>R$ @money($convenio_selecionado->desconto)
        </div>
        <div>
            Total<br/>R$ @money($convenio_selecionado->valor_total - $convenio_selecionado->desconto)
        </div>
        <div>
            Informado<br/>R$ @money($convenio_selecionado->pagamentos->sum('valor'))
        </div>
        <div>
            Troco<br/>R$ @money($convenio_selecionado->troco)
        </div>
    </div>

    <div class="flex flex-row items-stretch sm:justify-end gap-2 p-2 border-t-2">
        <div>
            <x-button primary label="Emitir Nota" disabled />
        </div>
        <div>
            <x-button primary label="Reimprimir Convênio" wire:click="imprimir_convenio" />
        </div>
        <div>
            <x-button negative label="Cancelar Convênio" wire:click="cancelar_convenio" />
        </div>
    </div>
@endif