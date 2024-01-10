<?php

namespace App\Livewire\Pdv\Convenios;

use Livewire\Component;
use App\Models\Clientes;
use WireUi\Traits\Actions;
use App\Models\ConveniosHead;
use App\Traits\HelperActions;
use App\Models\ConveniosItens;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Traits\Pdv\CaixaActions;
use App\Traits\Pdv\CaixaTickets;
use Illuminate\Support\Facades\DB;
use App\Models\EstoqueMovimentacoes;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.caixa')]
class ConvenioIndex extends Component
{
    use Actions;
    use CaixaActions;
    use CaixaTickets;
    use HelperActions;

    #[Locked]
    public $caixa;

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

    #[Validate('required|min:1|numeric', as: 'quantidade')]
    public $devolver_quantidade = 0;

    public $divideProductModal = false;

    #[Validate('required|min:1|numeric', as: 'quantidade')]
    public $fracionar_quantidade = 0;

    public function mount()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect('dashboard', true);
        }

        if(!$this->caixa->convenios->count()) {
            return $this->redirect(route('pdv.index'), true);
        }

        $this->vendas_ate = now()->addDay()->format('d-m-Y');
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
                $convenios = ConveniosHead::with('caixa','cliente','venda','recebimentos','itens');

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
        $this->reset('itens_selecionados');

        $itens = ConveniosItens::with('convenio','recebimento');

        $itens->whereHas('convenio', function($query) {
            return $query->whereClientesId($this->cliente_id);
        });

        $itens->whereStatus(0);

        if($this->vendas_ate) {
            $data_final = \Carbon\Carbon::parse($this->vendas_ate)->endOfDay()->format('Y-m-d H:i:s');
            $itens->where('created_at', '<=', $data_final);
        }

        $this->itens_convenio = $itens->get();
    }

    public function devolver_item()
    {
        if(empty($this->itens_selecionados)) return;

        $this->produto_selecionado  = collect($this->itens_convenio)->firstWhere('id', $this->itens_selecionados[0]);
        $this->produto_quantidade   = $this->produto_selecionado->quantidade;
        $this->devolver_quantidade  = 0;
        
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
            return $this->redirect(route('dashboard'), true);
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

        $this->validateOnly('devolver_quantidade');

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

        if($this->devolver_quantidade > $item->quantidade) $this->devolver_quantidade = $item->quantidade;

        try {
            $devolucoes = [
                'produtos_id' => $item->produtos_id,
                'tipo' => 'convenio_dev',
                'quantidade' => $this->devolver_quantidade
            ];

            $resultado = EstoqueMovimentacoes::insert($devolucoes);

            if($resultado) {
                $item->produto->update(['estoque_atual' => $item->produto->estoque_atual + $this->devolver_quantidade]);

                if($this->devolver_quantidade == $item->quantidade) {
                    $item->delete();
                }else{
                    $item->update([
                        'quantidade' => $item->quantidade - $this->devolver_quantidade,
                        'valor_total' => (($item->quantidade - $this->devolver_quantidade) * $item->preco) - $item->desconto,
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
            DB::rollBack();

            throw $th;

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
        $this->fracionar_quantidade  = 0;
        
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
            return $this->redirect(route('dashboard'), true);
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

        $this->validateOnly('fracionar_quantidade');

        if($this->fracionar_quantidade >= $this->produto_selecionado->quantidade) {
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
                'quantidade' => $item->quantidade - $this->fracionar_quantidade,
                'valor_total' => (($item->quantidade - $this->fracionar_quantidade) * $item->preco) - $item->desconto,
            ]);

            $new_item = $item->replicate()->fill([
                'quantidade' => $this->fracionar_quantidade,
                'valor_total' => ($this->fracionar_quantidade * $item->preco) - $item->desconto,
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

    public function render()
    {
        return view('livewire.pdv.convenios.convenio-index');
    }
}
