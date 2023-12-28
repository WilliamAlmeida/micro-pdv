<div x-data="{
    class: 'text-indigo-500',
    initSelect() {
        let element = document.querySelector('#searchProductTable tbody tr:first-child input[type=radio]');
        element.checked = true;
        $dispatch('setFocus', [{'id': element.id}]);
    }
}">
<x-modal.card title="Produtos" blur wire:model.defer="searchProductModal" max-width="3xl"
    x-on:close="$dispatch('onCloseSearchProductModal')"
    x-on:open="initSelect"
    >
    <div class="grid grid-cols-1 gap-4">
        <table class="table-auto text-xs sm:text-base" id="searchProductTable">
            <thead x-on:click="selectRadio">
                <tr>
                    <th class="text-start" scope="col" width="10%">Código</th>
                    <th scope="col" width="30%">Descrição</th>
                    <th class="text-end" scope="col" width="17.5%">Estoque</th>
                    <th class="text-end" scope="col" width="17.5%">Valor Unitário</th>
                    <th scope="col" width="7.5%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos_encontrados as $key => $produto)
                    <tr wire:key="{{ $produto->id }}">
                        <th scope="row" width="10%">
                            <x-radio lg label="{{ $produto->id }}"
                                id="radio_{{ $produto->id }}" name="radio_produtos"
                                wire:keyup.enter.once="selecionar_produto({{ $produto->id }})"
                            />
                        </th>
                        <td width="30%" name="titulo">{{ $produto->titulo }}</td>
                        <td width="17.5%" class="text-end">{{ $produto->estoque_atual ? $produto->estoque_atual : 0 }}</td>
                        <td width="17.5%" name="preco" class="text-end">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td width="7.5%">
                            <x-button primary sm icon="plus" wire:click="selecionar_produto({{ $produto->id }})" class="my-1" />
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <div class="flex">
                <x-button flat label="Cancelar" x-on:click="close" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
</div>