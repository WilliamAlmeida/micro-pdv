<div>
    <div class="grid grid-flow-row sm:grid-flow-col dark:text-white">
        <div class="py-2">
            <div class="flex flex-row flex-wrap gap-1 sm:flex-col ml-2 bg-gray-300 dark:bg-gray-800 p-2">
                <div class="basis-full font-bold text-center">Ações</div>
                <x-button primary label="/ Encerrar" :disabled="!count($caixa->vendas) || !$caixa->vendas->firstWhere('status', 0) ? true : false" />
                <x-button primary label="* Orçamento" disabled="true" />
                <x-button primary label="Sangria" />
                <x-button primary label="Entrada" />
                <x-button primary label="Registros" :disabled="!count($caixa->vendas) || $caixa->vendas->firstWhere('status', 1) ? true : false" />
                <x-button primary label="Fechar Caixa" />
                <x-button primary label="Convênio" disabled="true" />
                <x-button primary label="Reimpressão" :disabled="!count($caixa->vendas) || $caixa->vendas->firstWhere('status', 1) ? true : false" />
            </div>
        </div>

        <div class="col-span-4 px-2 sm:px-0 sm:py-2 sm:pr-2">
            <div class="grid grid-flow-row grid-cols-2">
                <div class="pr-2">
                    <div class="font-bold bg-gray-300 dark:bg-gray-800 py-4 px-2 my-auto">
                        Operador: {{ $caixa->user->name ?: 'Desconhecido' }}
                        <br/>
                        Caixa aberto em: {{ Carbon\Carbon::parse($caixa->created_at)->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                <div class="flex flex-row-reverse text-2xl sm:text-3xl font-bold text-end bg-green-500 py-4 px-2">
                    <div class="self-center italic">Valor Total: R$ {{ number_format($caixa->venda?->valor_total, 2, ',', '.') }}</div>
                </div>
                
                <div class="row-span-2 col-span-2 pt-2 sm:pl-2">
                    <table class="table-auto text-xs sm:text-base">
                        <thead>
                            <tr class="border-b-2 border-indigo-500">
                                <th class="font-bold sm:w-24 text-start pl-1">Cód.</th>
                                <th class="font-bold text-start">Item</th>
                                <th class="font-bold text-start sm:text-end">Quant.</th>
                                <th class="font-bold text-start sm:text-end">Valor Unit.</th>
                                <th class="font-bold text-start sm:text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($caixa->venda)
                            @foreach($caixa->venda->itens as $key => $item)
                            <tr class="border-b-2 border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800" wire:key="{{ $item->id }}">
                                <td class="pl-1">{{ str_pad($item->produtos_id, 6, "0", STR_PAD_LEFT) }}</td>
                                <td>{{ $item->descricao }}</td>
                                <td class="sm:text-end">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                                <td class="sm:text-end">R$ {{ number_format($item->preco, 2, ',', '.') }}</td>
                                <td class="sm:text-end">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                <td class="text-center py-2">
                                    <div class="flex sm:hidden justify-center gap-1">
                                        <x-dropdown>
                                            <x-slot name="trigger">
                                                <x-button.circle primary icon="pencil" />
                                            </x-slot>
                                            <x-dropdown.item label="Alterar" />
                                            <x-dropdown.item separator label="Cancelar" />
                                        </x-dropdown>
                                    </div>
                                    <div class="hidden sm:flex justify-center gap-1">
                                        <x-button.circle primary icon="pencil" />
                                        <x-button.circle negative icon="x" />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
