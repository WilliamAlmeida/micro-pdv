<div class="m-2">
    <div class="flex flex-col sm:flex-row gap-y-3 sm:gap-y-0 gap-x-0 sm:gap-x-3">
        @if($produto_selecionado)
            <div class="flex-none">
                <x-input label="Cód." placeholder="0" value="{{ $produto_selecionado?->id }}" class="max-w-[100px]" disabled />
            </div>

            <div class="grow sm:flex-auto">
                <x-input label="Pesquisa" placeholder="Pesquise pelo Produto" value="{{ $produto_selecionado?->titulo }}" disabled />
            </div>
        
            @if($editProductPrice)
            <div class="flex-1 sm:flex-none">
                <x-input label="Quantidade" placeholder="0.00" wire:model="pesquisa_quantidade" class="max-w-[100px]" disabled
                id="pesquisar_quantidade"
                wire:keyup.enter="inserir_quantidade"
                wire:keyup.escape="escape_inserir_quantidade"
                />
            </div>
            
            <div class="flex-1 sm:flex-none">
                <x-input label="Preço" placeholder="0.00" wire:model="pesquisa_preco" class="max-w-[100px]" type="number"
                id="pesquisar_preco"
                wire:keyup.enter="atualizar_preco"
                wire:keyup.escape="cancelar_pesquisa_produto"
                />
            </div>
            @else
            <div class="flex-1 sm:flex-none">
                <x-input label="Quantidade" placeholder="0.00" wire:model="pesquisa_quantidade" class="sm:max-w-[100px]" inputmode="numeric"
                id="pesquisar_quantidade"
                wire:keyup.enter="inserir_quantidade"
                wire:keyup.escape="escape_inserir_quantidade"
                />
            </div>
            
            <div class="flex-1 sm:flex-none">
                <x-input label="Preço" placeholder="0.00" wire:model="pesquisa_preco"  class="sm:max-w-[100px]" disabled
                id="pesquisar_preco"
                />
            </div>
            @endif
        @else
            <div class="flex-grow">
                <div x-data="{ pesquisaProduto: @entangle('pesquisa_produto') }">
                    <x-input label="Pesquisa" placeholder="Pesquise pelo Produto" 
                    x-model="pesquisaProduto"
                    id="pesquisar_produto" 
                    x-on:keyup.enter="pesquisaProduto !== '' ? $wire.pesquisar_produto() : ''" 
                    x-on:keyup.escape="$wire.escape_pesquisar_produto()"
                    x-on:keyup.slash="$wire.encerrar_venda(); pesquisaProduto = ''"
                    />
                </div>
            </div>
        @endif
    </div>
</div>