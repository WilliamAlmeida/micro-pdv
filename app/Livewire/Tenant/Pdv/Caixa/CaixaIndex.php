<?php

namespace App\Livewire\Tenant\Pdv\Caixa;

use Livewire\Component;
use WireUi\Traits\Actions;
use App\Models\Tenant\Clientes;
use App\Models\Tenant\Produtos;
use App\Models\Tenant\EstoqueMovimentacoes;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;

use App\Livewire\Forms\Tenant\Pdv\Caixa\PaymentForm;

use App\Traits\HelperActions;
use App\Traits\Pdv\CaixaActions;
use App\Traits\Pdv\CaixaTickets;

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

    public $paymentModal = false;
    public PaymentForm $pagamentoForm;

    public $searchProductModal = false;
    public $editProductPrice = false;
    public $produtos_encontrados = [];
    public $pesquisa_produto;
    public $pesquisa_preco = null;
    public $pesquisa_quantidade = null;

    #[On('refreshCaixa')]
    public function mount()
    {
        $this->caixa = $this->caixa_show();

        if($this->caixa->validDataAbertura()) {
            if($this->caixa->venda) {
                $this->js('
                setTimeout(() => {
                    $wireui.dialog({
                        title: "ATENÇÃO!",
                        description: "Fechamento do Caixa Obrigatório, pois esta caixa foi aberto em '.\Carbon\Carbon::parse($this->caixa->created_at)->format('d/m/Y').'!",
                        icon: "warning"
                    });
                }, 100);
                ');
            }else{
                if( !$this->caixa->vendas->count() &&
                    !$this->caixa->convenios_recebimentos->count() &&
                    !$this->caixa->sangrias->count() &&
                    !$this->caixa->entradas->count()
                ) {
                    $this->caixa->update(['created_at' => now()]);
                }else{
                    return $this->redirect(route('tenant.pdv.fechamento', tenant()));
                }
            }
        }

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
        
        $this->redirect(route('tenant.dashboard', tenant()), true);
    }

    public function escape_pesquisar_produto()
    {
        if($this->pesquisa_produto != null) {
            $this->reset('pesquisa_produto');
        }else{
            $this->sair_caixa();
        }
    }

    public function escape_inserir_quantidade()
    {
        if($this->pesquisa_quantidade != null) {
            $this->reset('pesquisa_quantidade');
        }else{
            $this->reset('produto_selecionado', 'pesquisa_preco');
            $this->set_focus('pesquisar_produto');
        }
    }

    #[On('selecionar_produto')]
    public function selecionar_produto($produto_id, $produto = null)
    {
        if($produto != null) {
            $this->produto_selecionado = $produto;
        }else{
            $this->produto_selecionado = Produtos::select('id','titulo','preco_varejo as preco', 'estoque_atual')->first($produto_id);
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
        $this->editProductPrice = true;
        $this->set_focus('pesquisar_preco', true);
    }
    
    public function atualizar_preco()
    {
        $this->editProductPrice = false;
        $this->set_focus('pesquisar_quantidade', true);
    }

    public function cancelar_pesquisa_produto()
    {
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
            return $this->redirect(route('tenant.dashboard', tenant()), true);
        }

        if(!$caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('tenant.dashboard', tenant()), true);
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
            
            $this->set_focus(['button' => 'confirm']);
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

    public function encerrar_venda()
    {
        if(!$this->caixa->venda?->valor_total) return;

        $this->pagamentoForm->reset();
        $this->pagamentoForm->resetValidation();

        $this->pagamentoForm->valor_total = $this->caixa->venda->valor_total;

        $this->js('$openModal("paymentModal")');

        $this->set_focus('desconto_valor');
    }

    // public function updatedPagamentoForm()
    // {
    //     if(!$this->pagamentoForm->convenio) $this->pagamentoForm->calculeChangeBack($this->caixa->venda);
    // }

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
            return $this->redirect(route('tenant.dashboard', tenant()), true);
        }

        if(!$caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('tenant.dashboard', tenant()), true);
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
                    'status' => 1,
                ]);

            }else{
                $result = $this->pagamentoForm->storePayment($caixa);
                throw_unless(!$result, $result);

                $caixa->venda->update([
                    'desconto' => $this->pagamentoForm->currency2Decimal($this->pagamentoForm->desconto),
                    'troco' => $this->pagamentoForm->currency2Decimal($this->pagamentoForm->troco),
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

    #[On('onClosePaymentModal')]
    public function onClosePaymentModal()
    {
        $this->set_focus('pesquisar_produto');
    }

    public function render()
    {
        $abreviation = null;
        foreach (explode(' ', \Str::of(tenant('nome_fantasia'))->squish()) as $value) {
            $abreviation .= $value[0];
        }

        return view('livewire.tenant.pdv.caixa.caixa-index', ['abreviation' => $abreviation]);
    }
}
