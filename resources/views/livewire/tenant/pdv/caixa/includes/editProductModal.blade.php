    <x-modal.card title="Alteração de Item" blur wire:model.defer="editProductModal" max-width="3xl"
    x-on:close="$dispatch('onCloseEditProductModal')"
    >
    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <x-input label="Descrição" placeholder="Pesquise pelo Produto" value="{{ $produto_selecionado?->descricao }}" disabled />
        </div>

        <x-input label="Quantidade" placeholder="0.00" wire:model.live.debounce.2s="edicao_quantidade"
        type="number"
        id="edicao_quantidade"
        inputmode="numeric"
        wire:keyup.enter="salvar_alteracao_item"
        />
        <x-inputs.currency label="Valor Unitário" placeholder="0,00" prefix="R$" thousands="." decimal="," wire:model="edicao_preco" disabled />

        <div class="col-span-2">
            <x-inputs.currency label="Valor do Item" placeholder="0,00" prefix="R$" thousands="." decimal="," wire:model="edicao_preco_total" disabled />
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <div class="flex gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="Atualizar" wire:click="salvar_alteracao_item" />
            </div>
        </div>
    </x-slot>
</x-modal.card>