<table class="table-auto text-xs sm:text-base min-w-full">
    <thead>
        <tr class="dark:text-white border-b-2 border-lime-600 dark:border-indigo-500">
            {{-- <th></th> --}}
            <th class="font-bold sm:w-24 text-start pl-1">Controle Caixa</th>
            <th class="font-bold text-start sm:text-end">Valor Total</th>
            <th class="font-bold text-start sm:text-end">Desconto</th>
            <th class="font-bold text-start sm:text-end">Valor Pago</th>
            <th class="font-bold text-start sm:text-end">Recebido Em</th>
        </tr>
    </thead>
    <tbody class="dark:text-white text-xs sm:text-base">
        @forelse($recebimentos_convenio as $key => $recebimento)
        <tr class="border-b-2 border-gray-800 hover:bg-gray-200 dark:hover:bg-gray-800" wire:key="{{ $recebimento->id }}">
            {{-- <td class="pl-1">
                <x-checkbox lg id="checkbox{{ $recebimento->id }}" wire:model.defer="recebimentos_selecionados" value="{{ $recebimento->id }}" />
            </td> --}}
            <td>{{ str_pad($recebimento->caixa_id, 4, "0", STR_PAD_LEFT) }}</td>
            <td class="sm:text-end">R$ @money($recebimento->valor_total)</td>
            <td class="sm:text-end">R$ @money($recebimento->desconto)</td>
            <td class="sm:text-end">R$ @money($recebimento->valor_total - $recebimento->desconto)</td>
            <td class="sm:text-end">{{ \Carbon\Carbon::parse($recebimento->created_at)->format('d/m/Y H:i:s') }}</td>
        </tr>
        @empty
        <tr class="border-b-2 border-gray-800">
            <td colspan="5" class="text-center text-[#6b6a6a]">Nenhum recebimento encontrado.</td>
        </tr>
        @endforelse
    </tbody>
</table>