<table class="table-auto text-xs sm:text-base min-w-full">
    <thead>
        <tr class="dark:text-white border-b-2 border-lime-600 dark:border-indigo-500">
            <th class="font-bold sm:w-24 text-start pl-1">Cód.</th>
            <th class="font-bold text-start">Item</th>
            <th class="font-bold text-start sm:text-end">Quant.</th>
            <th class="font-bold text-start sm:text-end">Valor Unit.</th>
            <th class="font-bold text-start sm:text-end">Total</th>
            {{-- <th></th> --}}
        </tr>
    </thead>
    <tbody class="dark:text-white">
        @if($venda_selecionada)
            @foreach($venda_selecionada->itens as $key => $item)
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
                <td colspan="5" class="text-center text-[#6b6a6a]">Nenhuma venda selecionada.</td>
            </tr>
        @endif
    </tbody>
</table>