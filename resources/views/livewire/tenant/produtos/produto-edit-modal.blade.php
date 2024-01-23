<div>
    <x-modal.card title="Edição de Produto" blur wire:model.defer="produtoEditModal">
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
            <x-select
            label="Categoria"
            placeholder="Categoria"
            :options="$array_categorias"
            option-label="titulo"
            option-value="id"
            wire:model.defer="form.categoria"
            id="edit_categoria"
            />
            
            <x-input label="Título" placeholder="Título" wire:model="form.titulo" id="edit_titulo" />
            
            <x-inputs.currency label="Preço" placeholder="0,00" prefix="R$" thousands="." decimal="," wire:model.blur="form.preco_varejo" inputmode="numeric" id="edit_preco_varejo" />
            
            <x-inputs.currency label="Preço Promocional" placeholder="0,00" prefix="R$" thousands="." decimal="," wire:model.blur="form.preco_promocao" inputmode="numeric" id="edit_preco_promocao" />

            @if($form->preco_promocao && $form->preco_promocao > 0)
                <x-datetime-picker
                label="Inicio da Promoção"
                placeholder="Inicio da Promoção"
                parse-format="DD-MM-YYYY"
                without-time="true"
                wire:model.defer="form.promocao_inicio"
                id="edit_promocao_inicio"
                />
                <x-datetime-picker
                label="Fim da Promoção"
                placeholder="Fim da Promoção"
                parse-format="DD-MM-YYYY"
                without-time="true"
                wire:model.defer="form.promocao_fim"
                id="edit_promocao_fim"
                />
            @endif

            <x-input label="Estoque Atual" placeholder="0.00" wire:model="form.estoque_atual" disabled="true" id="edit_estoque_atual" />

            <x-toggle left-label="Produto em Destaque" wire:model.defer="form.destaque" id="edit_destaque" />
        </div>
        <div class="mt-3">
            <x-textarea wire:model="form.descricao" label="Descrição" placeholder="Descrição" id="edit_descricao" />
        </div>
        
        <x-slot name="footer">
            <div class="flex justify-between gap-x-4">
                @if(!$produto?->trashed())
                    <x-button flat negative label="Desativar" wire:click="delete" />
                @else
                    <x-button flat positive label="Restaurar" wire:click="restore" />
                    <x-button flat negative label="Deletar" wire:click="delete" />
                @endif

                <div class="flex">
                    <x-button flat label="Cancelar" x-on:click="close" />
                    <x-button primary label="Salvar" wire:click="save" />
                </div>
            </div>
        </x-slot>
    </x-modal.card>
</div>

{{-- <script defer>
    setTimeout(() => {
        $openModal('produtoCreateModal');
    }, 500);
</script> --}}