<?php

namespace App\Livewire\Pdv\Caixa;

use Livewire\Component;
use App\Models\Produtos;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use App\Models\EstoqueMovimentacoes;

use App\Livewire\Forms\Pdv\EntradaForm;
use App\Livewire\Forms\Pdv\PaymentForm;

use App\Livewire\Forms\Pdv\SangriaForm;
use App\Models\Clientes;
use App\Traits\HelperActions;
use App\Traits\Pdv\CaixaActions;
use App\Traits\Pdv\CaixaTickets;
use Exception;

#[Layout('components.layouts.caixa')]
class CaixaIndex extends Component
{
    use Actions;
    use CaixaActions;
    use CaixaTickets;
    use HelperActions;

    public $caixa;

    public $produto_selecionado;
    public $cliente_selecionado;

    public $editProductModal = false;
    #[Locked]
    public $edicao_preco;
    public $edicao_quantidade;
    #[Locked]
    public $edicao_preco_total;

    public $withdrawalCashModal = false;
    public SangriaForm $sangriaForm;

    public $depositCashModal = false;
    public EntradaForm $entradaForm;

    public $paymentModal = false;
    public PaymentForm $pagamentoForm;

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

        $unique_key = sha1(auth()->id().'.pdv.pesquisar_produto'.$pesquisa);

        $produtos = cache()->remember($unique_key, 10, function() use ($pesquisa) {
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
                $produtos->where('titulo', 'like', '%'.$pesquisa.'%');
            }

            return $produtos->get();
        });

        // $produtos = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual');

        // if(is_numeric($pesquisa)) {
        //     $produtos->where(function($query) use ($pesquisa) {
        //         return $query
        //             ->where('codigo_barras_1', $pesquisa)
        //             ->orWhere('codigo_barras_2', $pesquisa)
        //             ->orWhere('codigo_barras_3', $pesquisa)
        //             ->orWhere('id', $pesquisa);
        //     });
        // }else{
        //     $produtos->where('titulo', 'like', '%'.$pesquisa.'%');
        // }

        // $produtos = $produtos->get();

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
            $this->selecionar_produto($produtos[0]->id, $produtos[0]);
        }else{
            $this->js('$openModal("searchProductModal")');
        }
    }

    public function selecionar_produto($produto_id, $produto = null)
    {
        if($produto != null) {
            $this->produto_selecionado = $produto;
        }else{
            $this->produto_selecionado = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual')->find($produto_id);
        }

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
                'title'       => 'Aviso!',
                'description' => 'Não foi possivel cancelar o Item.',
                'icon'        => 'error'
            ]);
        }

        $this->mount();
    }

    public function alterar_item($item_id)
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
        
        $this->mount();
    }

    public function realizar_sangria()
    {
        $this->sangriaForm->reset();
        $this->sangriaForm->resetValidation();

        $this->js('$openModal("withdrawalCashModal")');

        $this->set_focus('sangria_valor');
    }

    public function salvar_sangria()
    {
        $this->sangriaForm->validate();

        $caixa = $this->caixa_show();

        if(!$caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        if($this->sangriaForm->valor > $this->caixa->vendas_encerradas()->sum('valor_total') + $this->caixa->entrada_total) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Valor da Sangria superior ao Valor do Caixa.',
                'icon'        => 'info'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $resultado = $this->caixa->sangrias()->create([
                'tipo'      => 's',
                'motivo'    => $this->sangriaForm->motivo,
                'valor'     => $this->sangriaForm->valor
            ]);
    
            if($resultado) {
                $this->caixa->update(['sangria_total' => $this->caixa->sangrias()->sum('valor')]);
                $result = $this->printSangria($resultado->id);
                throw_if(array_key_exists('error', $result), $result['message']);
            }
    
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Sangria finalizada com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->reset('withdrawalCashModal');

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Não foi possivel realizar a Sangria.',
                'icon'        => 'error'
            ]);
        }
    }

    public function realizar_entrada()
    {
        $this->entradaForm->reset();
        $this->entradaForm->resetValidation();

        $this->js('$openModal("depositCashModal")');

        $this->set_focus('entrada_valor');
    }

    public function salvar_entrada()
    {
        $this->entradaForm->validate();

        $caixa = $this->caixa_show();

        if(!$caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect('dashboard', true);
        }

        DB::beginTransaction();

        try {
            $resultado = $this->caixa->entradas()->create([
                'tipo'      => 'e',
                'motivo'    => $this->entradaForm->motivo,
                'valor'     => $this->entradaForm->valor
            ]);
    
            if($resultado) {
                $this->caixa->update(['entrada_total' => $this->caixa->entradas()->sum('valor')]);
                $result = $this->printEntrada($resultado->id);
                throw_if(array_key_exists('error', $result), $result['message']);
            }
    
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Entrada finalizada com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->reset('depositCashModal');

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Não foi possivel realizar a Entrada.',
                'icon'        => 'error'
            ]);
        }
    }

    public function encerrar_venda()
    {
        if(!$this->caixa->venda?->valor_total) return;

        $this->pagamentoForm->reset();
        $this->pagamentoForm->resetValidation();

        $this->js('$openModal("paymentModal")');

        $this->set_focus('desconto_valor');
    }

    public function updatedPagamentoForm()
    {
        if(!$this->pagamentoForm->convenio) $this->pagamentoForm->calculeChangeBack($this->caixa->venda);
    }

    public function updatedPagamentoFormConvenio($value)
    {
        if($value == true || $value == false) $this->pagamentoForm->resetAgreetment();
    }

    public function pesquisar_cliente()
    {
        if($this->pagamentoForm->cliente_id) {
            $this->cliente_selecionado = Clientes::find($this->pagamentoForm->cliente_id);
        }else{
            $this->reset('cliente_selecionado');
        }
    }

    public function salvar_venda()
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

        $caixa->venda->pagamentos()->delete();

        DB::beginTransaction();

        try {
            if($this->pagamentoForm->convenio) {
                $result = $this->pagamentoForm->storeAgreetment($caixa);
                throw_unless(!$result, $result);

                $caixa->venda->update([
                    'desconto' => 0,
                    'troco' => 0,
                    'status' => 4
                ]);

            }else{
                $result = $this->pagamentoForm->storePayment($caixa);
                throw_unless(!$result, $result);

                $caixa->venda->update([
                    'desconto' => $this->pagamentoForm->desconto,
                    'troco' => $this->pagamentoForm->troco,
                    'status' => 1
                ]);
            }

            $baixa_estoque = [];

            foreach ($caixa->venda->itens as $key => $value) {
                // if(in_array($value->produtos_id, $baixa_estoque)) {
                //     $baixa_estoque[$itens->produtos_id] += $value->quantidade;
                // }else{
                    $baixa_estoque[$value->produtos_id] = $value->quantidade;
                // }
            }

            $venda_id = $caixa->venda->id;

            // $caixa = $this->caixa_show();

            $baixas = [];
            foreach ($baixa_estoque as $produto_id => $quantidade) {
                $baixas[] = [
                    'produtos_id' => $produto_id,
                    'tipo' => 'venda',
                    'quantidade' => $quantidade
                ];
            }

            if(count($baixas)) {
                $resultado = EstoqueMovimentacoes::insert($baixas);

                if($resultado) {
                    foreach ($caixa->venda->itens as $key => $value) {
                        $value->produtos->update(['estoque_atual' => floatval($value->produtos->estoque_atual) - floatval($baixa_estoque[$value->produtos_id])]);
                    }
                }
            }

            if(!$this->pagamentoForm->convenio) {
                $result = $this->printTicket($venda_id);
                throw_if(array_key_exists('error', $result), $result['message']);
            }

            DB::commit();

            if($this->pagamentoForm->convenio) {
                $this->notification([
                    'title'       => 'Aviso!',
                    'description' => 'Convênio finalizado com sucesso.',
                    'icon'        => 'success'
                ]);
            }else{
                $this->notification([
                    'title'       => 'Aviso!',
                    'description' => 'Venda finalizada com sucesso.',
                    'icon'        => 'success'
                ]);
            }

            $this->reset('paymentModal');

        } catch (\Exception $e) {
            DB::rollback();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => $e->getMessage(),
                'icon'        => 'error'
            ]);
        }
    }

    public function imprimir_ultima_venda($params=null)
    {
        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Deseja Reimprimir a Última Venda?',
                'description' => 'Você não terá como cancelar após a confirmação!',
                'acceptLabel' => 'Sim',
                'method'      => 'imprimir_ultima_venda',
                'params'      => 'Print',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        try {
            $result = $this->printTicket_Last($this->caixa);
            throw_if(array_key_exists('error', $result), $result['message']);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => $result['message'],
                'icon'        => 'success'
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            //throw $e;

            DB::rollback();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => $e->getMessage(),
                'icon'        => 'error'
            ]);
        }

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

    #[On('onCloseWithdrawalCashModal')]
    #[On('onCloseDepositCashModal')]
    public function onCloseWithdrawalDepositCashModal()
    {
        $this->set_focus('pesquisar_produto');
    }

    #[On('onClosePaymentModal')]
    public function onClosePaymentModal()
    {
        $this->set_focus('pesquisar_produto');
    }

    private function not($value)
    {
        $this->notification([
            'title'       => $value,
            'icon'        => 'info'
        ]);
    }

    public function render()
    {
        return view('livewire.pdv.caixa.caixa-index');
    }
}
