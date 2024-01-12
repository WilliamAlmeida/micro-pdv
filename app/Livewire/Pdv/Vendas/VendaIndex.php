<?php

namespace App\Livewire\Pdv\Vendas;

use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use App\Traits\HelperActions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Traits\Pdv\CaixaActions;
use Illuminate\Support\Facades\DB;
use App\Models\EstoqueMovimentacoes;
use App\Traits\Pdv\CaixaTickets;

#[Layout('components.layouts.caixa')]
class VendaIndex extends Component
{
    use Actions;
    use CaixaActions;
    use CaixaTickets;
    use HelperActions;

    #[Url('c')]
    public $c;

    #[Locked]
    public $caixa;

    #[Locked]
    public $venda_selecionada;

    #[Locked]
    public $formas_pagamento;

    public function mount()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect(route('dashboard'), true);
        }

        if(!$this->caixa->vendas->count()) {
            return $this->redirect(route('pdv.index'), true);
        }

        if(is_numeric($this->c)) {
            $this->visualizar_venda($this->c);
        }
    }

    public function caixa_show()
    {
        if(auth()->user()->caixa()->exists()) {
            $this->caixa = auth()->user()->caixa()->with([
                'vendas' => fn($q) => $q->with('pagamentos')->whereIn('status', [1])
            ])->first();

            if($this->caixa && $this->caixa->vendas) {
                $pagamentos = $this->caixa->vendas->map(function($item) {
                    $value = $item->pagamentos->map(function($item) {
                        return $item->only('forma_pagamento', 'valor');;
                    });

                    if($value->firstWhere('forma_pagamento', 'convenio')) {
                        $value->transform(function($item_2) use ($item) {
                            $item_2['valor'] = $item->valor_total;
                            return $item_2;
                        });
                    }

                    return $value;
                })->collapse()->groupBy('forma_pagamento')->map(function($item) {
                    return $item->sum('valor');
                });

                $this->caixa->pagamentos = $pagamentos;
            }
        }
    }

    public function cancelar_venda($params=null)
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

        $venda = $this->venda_selecionada;

        if(!$venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.vendas'), true);
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja cancelar esta Venda?',
                'acceptLabel' => 'Sim',
                'method'      => 'cancelar_venda',
                'params'      => 'Cancel',
            ]);

            $this->set_focus(['button' => 'cancel']);
            return;
        }

        DB::beginTransaction();

        try {
            $baixa_estoque = [];

            foreach ($venda->itens as $key => $value) {
                // if(in_array($value->produtos_id, $baixa_estoque)) {
                //     $baixa_estoque[$itens->produtos_id] += $value->quantidade;
                // }else{
                    $baixa_estoque[$value->produtos_id] = $value->quantidade;
                // }
            }

            $baixas = [];
            foreach ($baixa_estoque as $produto_id => $quantidade) {
                $baixas[] = [
                    'produtos_id' => $produto_id,
                    'tipo' => 'venda_can',
                    'quantidade' => $quantidade
                ];
            }

            if(count($baixas)) {
                $resultado = EstoqueMovimentacoes::insert($baixas);

                if($resultado) {
                    foreach ($venda->itens as $key => $value) {
                        $value->produtos->update(['estoque_atual' => floatval($value->produtos->estoque_atual) + floatval($baixa_estoque[$value->produtos_id])]);
                    }
                }
            }

            // $venda->itens()->delete();
            // $venda->pagamentos()->delete();
            $venda->update(['status' => 8]);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda cancelda com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->mount();

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Falha ao cancelar a venda.',
                'icon'        => 'error'
            ]);
        }
    }

    public function visualizar_venda($id)
    {
        $this->caixa_show();

        $this->venda_selecionada = $this->caixa->vendas->firstWhere('id', $id);
        $this->c = $id;

        if($this->venda_selecionada) {
            $value = Str::upper($this->venda_selecionada->pagamentos->pluck('forma_pagamento')->join(', '));
            $this->formas_pagamento = str_replace('_', ' ', $value);
        }else{
            $this->reset('formas_pagamento');
        }
    }

    public function imprimir_venda($params=null)
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

        $venda = $this->venda_selecionada;

        if(!$venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Venda não encontrada.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.vendas'), true);
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Deseja Reimprimir esta Venda?',
                'description' => 'Você não terá como cancelar após a confirmação!',
                'acceptLabel' => 'Sim',
                'method'      => 'imprimir_venda',
                'params'      => 'Print',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        try {
            $result = $this->printTicket($venda->id);
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

    public function render()
    {
        return view('livewire.pdv.vendas.venda-index');
    }
}
