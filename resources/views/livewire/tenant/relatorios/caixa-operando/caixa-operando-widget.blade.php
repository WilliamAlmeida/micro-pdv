<div>
    @if($caixa)
        <div wire:poll.3s>
            <div class="flex justify-end text-sm text-gray-400 dark:text-gray-500 select-none">Recarregando em <x-cooldown class="text-sm w-7 text-center" count="3" interval="100" amount="0.1" /> segundos</div>
            <table class="table-auto text-xs sm:text-base min-w-full">
                <thead>
                    <tr class="dark:text-white border-b-2 border-lime-600 dark:border-indigo-500">
                        <th class="font-bold sm:w-24 text-start pl-1">Cód.</th>
                        <th class="font-bold text-start">Item</th>
                        <th class="font-bold text-start sm:text-end">Quant.</th>
                        <th class="font-bold text-start sm:text-end">Valor Unit.</th>
                        <th class="font-bold text-start sm:text-end">Total</th>
                    </tr>
                </thead>
                <tbody class="dark:text-white">
                    @if($caixa->venda)
                        @foreach($caixa->venda->itens as $key => $item)
                        <tr class="border-b-2 border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800" wire:key="{{ $item->id }}">
                            <td class="pl-1">{{ str_pad($item->produtos_id, 4, "0", STR_PAD_LEFT) }}</td>
                            <td>{{ $item->descricao }}</td>
                            <td class="sm:text-end">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                            <td class="sm:text-end">R$ {{ number_format($item->preco, 2, ',', '.') }}</td>
                            <td class="sm:text-end">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="border-b-2 border-gray-800">
                            <td colspan="5" class="text-center text-[#6b6a6a]">Nenhum item lançado.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
</div>