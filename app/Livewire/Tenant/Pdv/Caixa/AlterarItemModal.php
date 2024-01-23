<?php

namespace App\Livewire\Tenant\Pdv\Caixa;

use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use App\Traits\HelperActions;

use Livewire\Attributes\Locked;
use App\Traits\Pdv\CaixaActions;

class AlterarItemModal extends Component
{
    use Actions;
    use CaixaActions;
    use HelperActions;

    public $caixa;

    public $produto_selecionado;

    public $editProductModal = false;
    #[Locked]
    public $edicao_preco;
    public $edicao_quantidade;
    #[Locked]
    public $edicao_preco_total;

    #[On('alterar_item')]
    public function alterar_item($id)
    {
        $this->caixa = $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('dashboard'), true);
        }

        if(!$this->caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('dashboard'), true);
        }

        $item = $this->caixa->venda->itens()->whereId($id)->first();

        $this->produto_selecionado  = $item;
        $this->edicao_quantidade    = $this->produto_selecionado->quantidade;
        $this->edicao_preco         = number_format($this->produto_selecionado->preco, 2, ',', '.');
        $this->edicao_preco_total   = number_format($this->produto_selecionado->valor_total, 2, ',', '.');

        $this->js('$openModal("editProductModal")');
        
        $this->set_focus('edicao_quantidade', true);
    }

    public function updatedEdicaoQuantidade($value) 
    {   
        if($value > 0) $this->edicao_preco_total   = number_format(floatval($value) * $this->produto_selecionado->preco, 2, ',', '.');
    }

    public function salvar_alteracao_item()
    {
        if(!$this->produto_selecionado) return;

        $this->caixa = $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('dashboard'), true);
        }

        if(!$this->caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('dashboard'), true);
        }

        $item       = $this->produto_selecionado;
        $quantidade = $this->edicao_quantidade;

        if($quantidade <= 0) {
            $item->delete();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item removido com sucesso.',
                'icon'        => 'success'
            ]);

        }else{
            $item->update([
                'quantidade' => $quantidade,
                'valor_total' => ($quantidade * $item->preco) - $item->desconto,
            ]);
    
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item atualizado com sucesso.',
                'icon'        => 'success'
            ]);
        }

        $this->reset('editProductModal');
        
        $this->dispatch('refreshCaixa');
    }

    public function render()
    {
        return view('livewire.tenant.pdv.caixa.alterar-item-modal');
    }
}
