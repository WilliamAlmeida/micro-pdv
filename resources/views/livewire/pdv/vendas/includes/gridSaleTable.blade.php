<table class="table-auto text-xs w-full">
    <thead>
        <tr class="dark:text-white border-b-2 border-lime-600 dark:border-indigo-500">
            <th class="text-start">Controle</th>
            {{-- <th class="text-start">NFCe</th> --}}
            <th class="text-start">Horário</th>
            <th class="text-end">Valor (R$)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($caixa->vendas as $key => $dados)
        <tr class="h-[35px] border-y-2 border-gray-800 {{ $venda_selecionada && $venda_selecionada->id == $dados->id ?
        'bg-lime-600 hover:bg-lime-500 dark:border-gray-700 dark:bg-indigo-500 dark:hover:bg-indigo-400'
        :
        'bg-gray-300 hover:bg-gray-200 dark:border-gray-700 dark:bg-transparent dark:hover:bg-gray-700'
        }}" data-id="{{ $dados->id }}">
            <td>{{ str_pad($dados->id, 6, "0", STR_PAD_LEFT) }}</td>
            <td>{{ \Carbon\Carbon::parse($dados->created_at)->format('H:i:s') }}</td>
            <td class="text-end">{{ number_format($dados->valor_total - $dados->desconto, 2, ',', '.') }}</td>
            <td class="text-end p-1">
                @if(!$venda_selecionada || $venda_selecionada && $venda_selecionada->id != $dados->id)
                    <x-button icon="eye" primary xs wire:click="visualizar_venda({{ $dados->id }})" />
                @endif
            </td>
        </tr>
        @empty
        @endforelse
    </tbody>
</table>