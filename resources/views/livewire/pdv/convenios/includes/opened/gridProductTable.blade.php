<table class="table-auto text-xs sm:text-base min-w-full">
    <thead>
        <tr class="dark:text-white border-b-2 border-lime-600 dark:border-indigo-500">
            <th></th>
            {{-- <th class="font-bold sm:w-24 text-start pl-1">Controle Caixa</th> --}}
            {{-- <th class="font-bold sm:w-24 text-start pl-1">Controle Venda</th> --}}
            <th class="font-bold sm:w-24 text-start pl-1">Controle Convênio</th>
            <th class="font-bold sm:w-24 text-start pl-1">Código Produto</th>
            <th class="font-bold text-start">Item</th>
            <th class="font-bold text-start sm:text-end">Quant.</th>
            <th class="font-bold text-start sm:text-end">Valor Unit.</th>
            <th class="font-bold text-start sm:text-end">Total</th>
            <th class="font-bold text-start sm:text-end">Registrado Em</th>
        </tr>
    </thead>
    <tbody class="dark:text-white text-xs sm:text-base">
        @forelse($itens_convenio as $key => $item)
        <tr class="border-b-2 border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800" wire:key="{{ $item->id }}">
            <td class="pl-1">
                <x-checkbox lg id="checkbox{{ $item->id }}" wire:model.defer="itens_selecionados" value="{{ $item->id }}" data-valor_total="{{ $item->valor_total }}" />
            </td>
            {{-- <td>{{ str_pad($item->convenio->caixa_id, 4, "0", STR_PAD_LEFT) }}</td> --}}
            {{-- <td>{{ str_pad($item->convenio->vendas_head_id ?? 0, 4, "0", STR_PAD_LEFT) }}</td> --}}
            <td>{{ str_pad($item->convenios_head_id, 4, "0", STR_PAD_LEFT) }}</td>
            <td>{{ str_pad($item->produtos_id, 4, "0", STR_PAD_LEFT) }}</td>
            <td>{{ $item->descricao }}</td>
            <td class="sm:text-end">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
            <td class="sm:text-end">R$ {{ number_format($item->preco, 2, ',', '.') }}</td>
            <td class="sm:text-end">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
            <td class="sm:text-end">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}</td>
        </tr>
        @empty
        <tr class="border-b-2 border-gray-800">
            <td colspan="8" class="text-center text-[#6b6a6a]">Nenhum item encontrado.</td>
        </tr>
        @endforelse
    </tbody>
</table>