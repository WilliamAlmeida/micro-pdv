<x-modal.card title="Produtos" blur wire:model.defer="searchProductModal" max-width="3xl"
    x-on:close="$dispatch('onCloseSearchProductModal')"
    >
    <div class="grid grid-cols-1 gap-4">
        <table class="table-auto text-xs sm:text-base border border-indigo-600">
            <thead>
                <tr>
                    <th scope="col" width="10%">Código</th>
                    <th scope="col" width="30%">Descrição</th>
                    <th class="text-start" scope="col" width="17.5%">Estoque</th>
                    <th class="text-start" scope="col" width="17.5%">Valor Unitário</th>
                    <th scope="col" width="7.5%"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos_encontrados as $key => $produto)
                    <tr wire:key="{{ $key }}" class="hover:bg-slate-500">
                        <th scope="row" width="10%">{{ $produto->id }}</th>
                        <td width="30%" name="titulo">{{ $produto->titulo }}</td>
                        <td width="17.5%">{{ $produto->estoque_atual ? $produto->estoque_atual : 0 }}</td>
                        <td width="17.5%" name="preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td width="7.5%">
                            <x-button sm secondary icon="plus" class="my-1" label="Selecionar" wire:click="selecionar_produto({{ $produto->id }})" />
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