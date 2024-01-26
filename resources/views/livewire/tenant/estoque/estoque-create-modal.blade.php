<div>
    <x-modal.card title="{{ $type == 'up' ? 'Entrada de Estoque' : 'Baixa de Estoque' }}" blur wire:model.defer="estoqueCreateModal" max-width="3xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1 sm:col-span-2">
                <div class="flex gap-4">
                    <div class="flex-none w-20">
                        <x-input label="Código" value="{{ $produto?->id }}" placeholder="0" :disabled="true" />
                    </div>
                    <div class="w-full">
                        <x-select
                        label="Produto"
                        wire:model.defer="form.produtos_id"
                        placeholder="Selecione um Produto"
                        :async-data="route('api.produtos', ['empresa' => tenant() ?? null])"
                        option-label="titulo"
                        option-value="id"
                        x-on:selected="$wire.fillProduto()"
                        x-on:clear="$wire.cleanProduto()"
                        />
                    </div>
                </div>
            </div>

            @if($produto)
                <div class="col-span-1 sm:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <x-input label="Estoque Mínimo" value="{{ $produto?->estoque_mininmo }}" placeholder="0.00" :disabled="true" />
                        <x-input label="Estoque Atual" value="{{ $produto->estoque_atual }}" placeholder="0.00" :disabled="true" />
                        <x-input label="Estoque Acrescentar" wire:model="form.quantidade" placeholder="0.00" />
                        {{-- <x-input label="Estoque Confirmar" placeholder="0.00" :disabled="true" /> --}}
                    </div>
                </div>
    
                <div class="col-span-1 sm:col-span-2">
                    <x-input label="Motivo" wire:model="form.motivo" placeholder="Informe um Motivo" />
                </div>
            @endif

            {{-- <x-select label="Fornecedor" placeholder="Selecione um Fornecedor" :options="$array_fornecedores" option-label="nome_fantasia" option-value="id" wire:model.defer="form.fornecedores_id" /> --}}
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    @if($type === 'up')
                        <x-button primary label="Dar Entrada" wire:click="save_entrada" />
                    @elseif($type === 'down')
                        <x-button primary label="Dar Baixa" wire:click="save_baixa" />
                    @endif
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>