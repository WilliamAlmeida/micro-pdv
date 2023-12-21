<?php

namespace App\Livewire\Pdv\Caixa;

use Livewire\Component;
use App\Models\Produtos;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;

#[Layout('components.layouts.caixa')]
class CaixaIndex extends Component
{
    use Actions;

    public $caixa;

    public $produto_selecionado;

    public $editProductModal = false;
    #[Locked]
    public $edicao_preco;
    public $edicao_quantidade;
    #[Locked]
    public $edicao_preco_total;

    public $searchProductModal = false;
    public $editProductPrice = false;
    public $produtos_encontrados = [];
    public $pesquisa_produto;
    public $pesquisa_preco = null;
    public $pesquisa_quantidade = null;

    public function mount()
    {
        $this->caixa = $this->caixa_show();

        $this->set_focus('pesquisar_produto');
    }

    private function caixa_show()
    {
        if(!auth()->user()->caixa()->exists()) {
            $this->caixa = auth()->user()->caixa()->create([
                'tipo_venda' => 'caixa',
                'status' => 0,
            ]);
        }else{
            $this->caixa = auth()->user()->caixa()->with('vendas')->first();
            if($this->caixa && $this->caixa->venda) {
                $this->caixa->venda->update(['valor_total' => $this->caixa->venda->itens()->sum('valor_total')]);
                $this->caixa->venda->itens;
                $this->caixa->venda->pagamentos;
            }
        }

        return $this->caixa;
    }

    public function sair_caixa($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja sair do Caixa?',
                'acceptLabel' => 'Sim',
                'method'      => 'sair_caixa',
                'params'      => 'Leave',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }
        
        $this->redirect('dashboard', true);
    }

    public function escape_pesquisar_produto()
    {
        if($this->pesquisa_produto != null) {
            $this->not('cancelar pesquisa: '.$this->pesquisa_produto);

            $this->reset('pesquisa_produto');
        }else{
            $this->not('sair caixa');

            $this->sair_caixa();
        }
    }

    public function escape_inserir_quantidade()
    {
        if($this->pesquisa_quantidade != null) {
            $this->not('cancelar quantidade: '.$this->pesquisa_quantidade);

            $this->reset('pesquisa_quantidade');
        }else{
            $this->not('voltar para pesquisa');

            $this->reset('produto_selecionado', 'pesquisa_preco');
            $this->set_focus('pesquisar_produto');
        }
    }

    public function pesquisar_produto()
    {
        if(empty($this->pesquisa_produto)) return;

        $pesquisa = $this->pesquisa_produto;

        $produtos = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual');

        if(is_numeric($pesquisa)) {
            $produtos->where(function($query) use ($pesquisa) {
                return $query
                    ->where('codigo_barras_1', $pesquisa)
                    ->orWhere('codigo_barras_2', $pesquisa)
                    ->orWhere('codigo_barras_3', $pesquisa)
                    ->orWhere('id', $pesquisa);
            });
        }else{
            $produtos->where('titulo', 'like', '%'.$pesquisa.'%')->get();
        }

        $produtos = $produtos->get();

        if(!$produtos->count()) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Nenhum Produto encontrado.',
                'icon'        => 'warning'
            ]);
            return;
        }

        $this->produtos_encontrados = $produtos;
        
        $this->reset('pesquisa_produto');
        
        if($produtos->count() == 1) {
            $this->selecionar_produto($produtos[0]->id);
        }else{
            $this->js('$openModal("searchProductModal")');
        }
    }

    public function selecionar_produto($produto_id)
    {
        $this->produto_selecionado = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual')->find($produto_id);

        $this->pesquisa_preco = $this->produto_selecionado->preco ?? 0;

        $this->reset(['pesquisa_quantidade', 'searchProductModal']);

        $this->set_focus('pesquisar_quantidade');
    }

    public function inserir_quantidade()
    {
        $quantidade = floatval($this->pesquisa_quantidade);

        if($quantidade == 0) {
            $this->reset('pesquisa_quantidade');
            return;
        }

        if($quantidade < 0) {
            $this->pesquisa_quantidade = $quantidade * -1;

            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja alterar o preço?',
                'icon'        => 'question',
                'accept'      => [
                    'label'  => 'Sim',
                    'method' => 'habilitar_edicao_preco',
                ],
                'reject' => [
                    'label'  => 'Não',
                    'method' => 'set_focus',
                    'params' => ['pesquisar_quantidade', true],
                ],
                'onDismiss' => [
                    'method' => 'set_focus',
                    'params' => ['pesquisar_quantidade', true],
                ]
            ]);
            
            $this->set_focus(['button' => 'confirm']);
            return;
        }

        $this->lancar_item();
    }

    public function habilitar_edicao_preco()
    {
        $this->not('habilita alteração do preço');

        $this->editProductPrice = true;
        $this->set_focus('pesquisar_preco', true);
    }
    
    public function atualizar_preco()
    {
        $this->not('finaliza alteração do preço');

        $this->editProductPrice = false;
        $this->set_focus('pesquisar_quantidade', true);
    }

    public function cancelar_pesquisa_produto()
    {
        $this->not('cancelar pesquisa produto');

        $this->reset('produto_selecionado', 'pesquisa_preco', 'pesquisa_quantidade', 'pesquisa_produto', 'editProductPrice');
        $this->set_focus('pesquisar_produto');
    }

    private function lancar_item()
    {
        $produto = $this->produto_selecionado;

        if(!$produto) return;

        $this->caixa = $this->caixa_show();

        DB::beginTransaction();

        try {
            if(!$this->caixa->venda) {
                $this->caixa->venda = $this->caixa->venda()->create([
                    'status' => 0,
                ]);
            }

            $this->caixa->venda->itens()->create([
                'caixa_id'      => $this->caixa->id,
                'produtos_id'   => $produto->id,
                'descricao'     => $produto->titulo,
                'quantidade'    => floatval($this->pesquisa_quantidade),
                'preco'         => floatval($this->pesquisa_preco),
                'valor_total'   => floatval($this->pesquisa_quantidade) * floatval($this->pesquisa_preco)
            ]);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item lançado com sucesso.',
                'icon'        => 'success'
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();
        }

        $this->cancelar_pesquisa_produto();
        $this->mount();
    }

    public function cancelar_item($item_id, $params=null)
    {
        $caixa = $this->caixa_show();

        if(!$caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        if(!$caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        $item = $caixa->venda->itens()->whereId($item_id)->first();

        if($params == null) {
            $message = "{$item->descricao} ({$item->quantidade} x R$ {$item->preco} = R$ {$item->valor_total})";
            $message .= "<br/>";
            $message .= "Você não terá como cancelar após a confirmação!";
        
            $this->dialog()->confirm([
                'title'       => 'Deseja cancelar este Item?',
                'description' => $message,
                'icon'        => 'question',
                'accept'      => [
                    'label'  => 'Sim',
                    'method' => 'cancelar_item',
                    'params'      => [$item_id, 'Cancel'],
                ],
                'reject' => [
                    'label'  => 'Não',
                    'method' => 'set_focus',
                    'params' => 'pesquisar_produto',
                ],
                'onDismiss' => [
                    'method' => 'set_focus',
                    'params' => 'pesquisar_produto',
                ]
            ]);
            
            $this->set_focus(['button' => 'cancel']);
            return;
        }

        try {
            $resultado = $item->delete();

            if(!$caixa->venda->itens()->count()) {
                $caixa->venda->delete();
            }

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item removido com sucesso.',
                'icon'        => 'success'
            ]);
        } catch (\Throwable $th) {
            //throw $th;

            $this->notification([
                'title'       => 'Falha no cancelamento!',
                'description' => 'Não foi possivel cancelar o Item.',
                'icon'        => 'error'
            ]);
        }

        $this->mount();
    }

    public function alterar_item($item_id, $params=null)
    {
        $caixa = $this->caixa_show();

        if(!$caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        if(!$caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        $item = $caixa->venda->itens()->whereId($item_id)->first();

        $this->produto_selecionado  = $item;
        $this->edicao_quantidade    = $this->produto_selecionado->quantidade;
        $this->edicao_preco         = number_format($this->produto_selecionado->preco, 2, ',', '.');
        $this->edicao_preco_total   = number_format($this->produto_selecionado->valor_total, 2, ',', '.');

        $this->js('$openModal("editProductModal")');
        
        $this->set_focus('edicao_quantidade');
    }

    public function updatedEdicaoQuantidade($value) 
    {   
        if($value > 0) $this->edicao_preco_total   = number_format(floatval($value) * $this->produto_selecionado->preco, 2, ',', '.');
    }

    public function salvar_alteracao_item()
    {
        if(!$this->produto_selecionado) return;

        $caixa = $this->caixa_show();

        if(!$caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        if(!$caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        $item       = $this->produto_selecionado;
        $quantidade = $this->edicao_quantidade;

        if($quantidade <= 0) {
            $item->delete();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item removido.',
                'icon'        => 'success'
            ]);

        }else{
            $item->update([
                'quantidade' => $quantidade,
                'valor_total' => ($quantidade * $item->preco) - $item->desconto,
            ]);
    
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item atualizado.',
                'icon'        => 'success'
            ]);
        }

        $this->reset('editProductModal');
        
        $this->mount();
    }

    #[On('onCloseSearchProductModal')]
    public function onCloseSearchProductModal()
    {
        $this->reset('produtos_encontrados');

        if($this->produto_selecionado) {
            $this->set_focus('pesquisar_quantidade');
        }else{
            $this->set_focus('pesquisar_produto');
        }
    }

    #[On('onCloseEditProductModal')]
    public function onCloseEditProductModal()
    {
        $this->reset('produto_selecionado', 'edicao_quantidade', 'edicao_preco', 'edicao_preco_total');

        $this->set_focus('pesquisar_produto');
    }

    private function not($value)
    {
        $this->notification([
            'title'       => $value,
            'icon'        => 'info'
        ]);
    }

    public function set_focus($values, $select=false)
    {
        if(is_array($values)) {
            if(isset($values['button'])) {
                if($values['button'] == 'confirm') {
                    $this->set_focus(['query' => '[x-ref="accept"] button']);
                }else{
                    $this->set_focus(['query' => '[x-ref="reject"] button']);
                }
            }else{
                $this->dispatch('setFocus', $values);
            }
        }else if(is_string($values)) {
            $this->dispatch('setFocus', ['id' => $values, 'select' => $select]);
        }
    }

    public function render()
    {
        return view('livewire.pdv.caixa.caixa-index');
    }
}
