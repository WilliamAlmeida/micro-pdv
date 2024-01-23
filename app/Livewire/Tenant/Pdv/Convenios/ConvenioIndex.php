<?php

namespace App\Livewire\Tenant\Pdv\Convenios;

use Livewire\Component;
use App\Models\Tenant\Clientes;
use WireUi\Traits\Actions;
use App\Models\Tenant\ConveniosHead;
use App\Traits\HelperActions;
use App\Models\Tenant\ConveniosItens;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Traits\Pdv\CaixaActions;
use App\Traits\Pdv\CaixaTickets;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\EstoqueMovimentacoes;
use App\Models\Tenant\ConveniosRecebimentos;
use App\Livewire\Forms\Tenant\Pdv\Convenio\ReceivementForm;
use App\Livewire\Forms\Tenant\Pdv\Convenio\DivideItemForm;
use App\Livewire\Forms\Tenant\Pdv\Convenio\ReturnItemForm;

#[Layout('components.layouts.caixa')]
class ConvenioIndex extends Component
{
    use Actions;
    use CaixaActions;
    use CaixaTickets;
    use HelperActions;

    #[Locked]
    public $caixa;

    public $vendas_status = 0;
    public $vendas_ate;

    public $cliente_id;
    #[Locked]
    public $cliente_selecionado;

    #[Locked]
    public $itens_convenio = [];
    public $itens_selecionados = [];

    #[Locked]
    public $produto_selecionado;
    #[Locked]
    public $produto_quantidade = 0;

    public $returnProductModal = false;
    public ReturnItemForm $devolucaoForm;

    public $divideProductModal = false;
    public DivideItemForm $fracionamentoForm;

    public $receivementModal = false;
    public ReceivementForm $recebimentoForm;

    #[Locked]
    public $recebimentos_convenio = [];

    public function mount()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect(route('tenant.dashboard'), true);
        }

        if(!$this->caixa->convenios->count()) {
            return $this->redirect(route('tenant.pdv.index'), true);
        }

        // $this->vendas_ate = now()->addDay()->format('d-m-Y');
        // $this->cliente_id = 3;
        // $this->cliente_selecionado = Clientes::find($this->cliente_id);
        // $this->filtrar_itens();
        // $this->itens_selecionados = $this->itens_convenio->pluck('id') ?? [];
    }

    public function caixa_show()
    {
        if(auth()->user()->caixa()->exists()) {
            $this->caixa = auth()->user()->caixa()->first();

            if($this->caixa) {
                $convenios = ConveniosHead::with('caixa','cliente','venda','itens');

                $this->caixa->convenios = $convenios->get();
            }

            // if($this->caixa && $this->caixa->vendas) {

            //     $pagamentos = $this->caixa->vendas->map(function($item) {
            //         return $item->pagamentos->map(function($item) {
            //             return $item->only('forma_pagamento', 'valor');;
            //         });
            //     })->collapse()->groupBy('forma_pagamento')->map(function($item) {
            //         return $item->sum('valor');
            //     });

            //     $this->caixa->pagamentos = $pagamentos;
            // }
        }
    }

    public function updatedVendasAte($value)
    {
        if(empty($value)) {
            $this->reset('cliente_id', 'cliente_selecionado', 'itens_convenio');
        }
    }

    public function pesquisar_cliente()
    {
        if($this->cliente_id) {
            $this->cliente_selecionado = Clientes::find($this->cliente_id);
        }else{
            $this->reset('cliente_selecionado', 'itens_convenio', 'itens_selecionados');
        }
    }

    public function filtrar_itens()
    {
        $this->reset('itens_selecionados', 'itens_convenio', 'recebimentos_convenio');

        if($this->vendas_status == 0 || $this->vendas_status == 1 || $this->vendas_status == 2) {
            $itens = ConveniosItens::with('convenio','recebimento');
            
            $itens->whereHas('convenio', function($query) {
                return $query->whereClientesId($this->cliente_id);
            });

            if(!in_array($this->vendas_status, [0, 1, 2])) $this->vendas_status = 0;

            $itens->whereStatus($this->vendas_status);

            if($this->vendas_ate) {
                $data_final = \Carbon\Carbon::parse($this->vendas_ate)->endOfDay()->format('Y-m-d H:i:s');
                $itens->where('created_at', '<=', $data_final);
            }

            $this->itens_convenio = $itens->get();

        }else if($this->vendas_status == 3) {
            $recebimentos = ConveniosRecebimentos::with('itens','pagamentos');

            $recebimentos->whereHas('itens.convenio', function($query) {
                return $query->whereClientesId($this->cliente_id);
            });

            if($this->vendas_ate) {
                $data_final = \Carbon\Carbon::parse($this->vendas_ate)->endOfDay()->format('Y-m-d H:i:s');
                $recebimentos->where('created_at', '<=', $data_final);
            }

            $this->recebimentos_convenio = $recebimentos->get();
        }
    }

    public function devolver_item()
    {
        if(empty($this->itens_selecionados)) return;

        $this->produto_selecionado  = collect($this->itens_convenio)->firstWhere('id', $this->itens_selecionados[0]);
        $this->produto_quantidade   = $this->produto_selecionado->quantidade;
        $this->devolucaoForm->reset();

        $this->js('$openModal("returnProductModal")');
        $this->set_focus('devolver_quantidade');
    }

    public function salvar_devolver_item($params=null)
    {
        $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('tenant.dashboard'), true);
        }

        if(!$this->cliente_selecionado) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Cliente não selecionado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        if(!$this->produto_selecionado) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item não selecionado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        $this->devolucaoForm->validate();

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja finalizar esta Devolução?',
                'acceptLabel' => 'Sim',
                'method'      => 'salvar_devolver_item',
                'params'      => 'Return',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        DB::beginTransaction();

        $item = $this->produto_selecionado;

        if($this->devolucaoForm->quantidade > $item->quantidade) $this->devolucaoForm->quantidade = $item->quantidade;

        try {
            $devolucoes = [
                'produtos_id' => $item->produtos_id,
                'tipo' => 'convenio_dev',
                'quantidade' => $this->devolucaoForm->quantidade
            ];

            $resultado = EstoqueMovimentacoes::insert($devolucoes);

            if($resultado) {
                $item->produto->update(['estoque_atual' => $item->produto->estoque_atual + $this->devolucaoForm->quantidade]);

                if($this->devolucaoForm->quantidade == $item->quantidade) {
                    $item->update(['status' => 2]);
                }else{
                    $item->update([
                        'quantidade' => $item->quantidade - $this->devolucaoForm->quantidade,
                        'valor_total' => (($item->quantidade - $this->devolucaoForm->quantidade) * $item->preco) - $item->desconto,
                    ]);
                }
            }

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Devolução do Item finalizada com sucesso!',
                'icon'        => 'success'
            ]);

            if(!$item->convenio->itens()->count()) $item->convenio->delete();

            DB::commit();

            $this->reset('returnProductModal', 'produto_selecionado');

            $this->filtrar_itens();

        } catch (\Throwable $th) {
            // throw $th;
            
            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Falha ao realizar a devolução do item.',
                'icon'        => 'error'
            ]);
        }
    }

    public function fracionar_item()
    {
        if(empty($this->itens_selecionados)) return;

        $this->produto_selecionado  = collect($this->itens_convenio)->firstWhere('id', $this->itens_selecionados[0]);
        $this->produto_quantidade   = $this->produto_selecionado->quantidade;
        $this->fracionamentoForm->reset();
        
        $this->js('$openModal("divideProductModal")');
        $this->set_focus('fracionar_quantidade');
    }

    public function salvar_fracionar_item($params=null)
    {
        $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('tenant.dashboard'), true);
        }

        if(!$this->cliente_selecionado) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Cliente não selecionado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        if(!$this->produto_selecionado) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Item não selecionado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        $this->fracionamentoForm->validate();

        if($this->fracionamentoForm->quantidade >= $this->produto_selecionado->quantidade) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Quantidade informada não pode ser maior ou igual a quantidade atual.',
                'icon'        => 'error'
            ]);
            return;
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja finalizar o Fracinamento?',
                'acceptLabel' => 'Sim',
                'method'      => 'salvar_fracionar_item',
                'params'      => 'Return',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        DB::beginTransaction();

        $item = $this->produto_selecionado;

        try {
            $item->update([
                'quantidade' => $item->quantidade - $this->fracionamentoForm->quantidade,
                'valor_total' => (($item->quantidade - $this->fracionamentoForm->quantidade) * $item->preco) - $item->desconto,
            ]);

            $new_item = $item->replicate()->fill([
                'quantidade' => $this->fracionamentoForm->quantidade,
                'valor_total' => ($this->fracionamentoForm->quantidade * $item->preco) - $item->desconto,
            ]);

            $new_item->save();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Fracionamento do Item finalizada com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->reset('divideProductModal', 'produto_selecionado');

            $this->filtrar_itens();

        } catch (\Throwable $th) {
            // throw $th;
            
            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Falha ao realizar o fracionamento do item.',
                'icon'        => 'error'
            ]);
        }
    }

    public function iniciar_recebimento()
    {
        if(empty($this->itens_selecionados)) return;

        $this->recebimentoForm->reset();
        $this->recebimentoForm->resetValidation();

        $itens_selecionados = $this->itens_selecionados;

        $itens_recebimento = collect($this->itens_convenio)->filter(function($item) {
            return in_array($item->id, $this->itens_selecionados);
        });

        unset($itens_selecionados);

        $this->recebimentoForm->valor_total = $itens_recebimento->sum('valor_total');
        $this->recebimentoForm->dinheiro    = $itens_recebimento->sum('valor_total');
        $this->recebimentoForm->informado   = $itens_recebimento->sum('valor_total');

        $this->js('$openModal("receivementModal")');

        $this->set_focus('desconto_valor');
    }

    public function salvar_recebimento()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('tenant.dashboard'), true);
        }

        if(!$this->cliente_selecionado) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Cliente não selecionado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        if(empty($this->itens_selecionados)) return;

        $itens_selecionados = $this->itens_selecionados;

        $itens_recebimento = collect($this->itens_convenio)->filter(function($item) {
            return in_array($item->id, $this->itens_selecionados);
        });

        unset($itens_selecionados);

        DB::beginTransaction();

        try {
            $recebimento = ConveniosRecebimentos::create([
                'caixa_id'      => $this->caixa->id,
                'desconto'      => $this->recebimentoForm->currency2Decimal($this->recebimentoForm->desconto),
                'troco'         => $this->recebimentoForm->currency2Decimal($this->recebimentoForm->troco),
                'valor_total'   => $this->recebimentoForm->currency2Decimal($this->recebimentoForm->valor_total)
            ]);

            $result = $this->recebimentoForm->storePayment($recebimento);
            throw_unless(!$result, $result);

            $resultado = ConveniosItens::whereIn('id', $itens_recebimento->pluck('id'))->update([
                'convenios_recebimentos_id' => $recebimento->id,
                'status' => 1
            ]);

            // $result = $this->printTicket($venda_id);
            // throw_if(array_key_exists('error', $result), $result['message']);

            DB::commit();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Recebimento finalizado com sucesso.',
                'icon'        => 'success'
            ]);

            $this->reset('receivementModal', 'produto_selecionado');

            $this->filtrar_itens();

        } catch (\Exception $e) {
            DB::rollback();

            throw $e;

            $this->notification([
                'title'       => 'Aviso!',
                'description' => $e->getMessage(),
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.pdv.convenios.convenio-index');
    }
}
