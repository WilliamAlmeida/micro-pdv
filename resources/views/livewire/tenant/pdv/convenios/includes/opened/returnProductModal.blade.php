    <x-modal.card title="Devolução de Item" blur wire:model.defer="returnProductModal" max-width="3xl"
    {{-- x-on:close="$dispatch('onCloseReturnProductModal')" --}}
    >
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-data="{
        quantidade: @entangle('produto_quantidade'),
        nova_quantidade: @entangle('devolucaoForm.quantidade'),
        quantidade_restante: 0,
        calculeDiff() {
            if(this.nova_quantidade) {
                let restante = parseFloat(this.quantidade) - parseFloat(this.nova_quantidade);
                if(restante < 0) { this.nova_quantidade = this.quantidade; restante = 0; }
                this.quantidade_restante = restante;
            }else{
                this.quantidade_restante = this.quantidade;
            }
        },
        init() {
            this.calculeDiff();
        }
    }">
        <div class="sm:col-span-3">
            <x-input label="Descrição" placeholder="Pesquise pelo Produto" value="{{ $produto_selecionado?->descricao }}" disabled />
        </div>

        <x-input label="Quantidade" placeholder="0.00" wire:model="produto_quantidade" type="number" name="quantidade" disabled />

        <x-input label="Quantidade hà devolver" placeholder="0.00" wire:model="devolucaoForm.quantidade"
        type="number"
        id="devolver_quantidade"
        inputmode="numeric"
        wire:keyup.enter="salvar_devolver_item"
        @input="calculeDiff"
        />

        <x-input label="Quantidade Restante" placeholder="0.00" x-model="quantidade_restante" type="number" disabled />
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-4">
            <div class="flex gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="Devolver" wire:click="salvar_devolver_item" />
            </div>
        </div>
    </x-slot>
</x-modal.card>