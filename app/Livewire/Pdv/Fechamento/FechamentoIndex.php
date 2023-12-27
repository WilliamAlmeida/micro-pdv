<?php

namespace App\Livewire\Pdv\Fechamento;

use Livewire\Component;
use WireUi\Traits\Actions;
use App\Traits\HelperActions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.caixa')]
class FechamentoIndex extends Component
{
    use Actions;
    use HelperActions;

    #[Locked]
    public $caixa;

    #[Locked]
    public $gaveta_total = 0;

    #[Locked]
    public $pagamentos;

    #[Locked]
    public $formas_pagamento;

    public function mount()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect(route('dashboard'), true);
        }

        if($this->caixa->status) {
            return $this->redirect(route('dashboard'), true);
        }
    }

    public function caixa_show()
    {
        if(auth()->user()->caixa()->exists()) {
            $this->caixa = auth()->user()->caixa()->with([
                'vendas' => fn($q) => $q->with('pagamentos')->whereStatus(1),
                'entradas', 'sangrias'
            ])->first();

            if($this->caixa && $this->caixa->vendas) {
                $pagamentos = $this->caixa->vendas->map(function($item) {
                    return $item->pagamentos->map(function($item) {
                        return $item->only('forma_pagamento', 'valor');;
                    });
                })->collapse()->groupBy('forma_pagamento')->map(function($item) {
                    return $item->sum('valor');
                });

                $this->pagamentos = $pagamentos;

                $this->gaveta_total = $this->caixa->entradas->sum('valor') ?? 0;
                $this->gaveta_total -= $this->caixa->sangrias->sum('valor') ?? 0;
                $this->gaveta_total += $this->caixa->pagamentos['dinheiro'] ?? 0;
            }
        }

        // $caixa->pagamentos['total_caixa'] = (object) [
        //     'valor' => 0
        // ];

        // foreach (['dinheiro', 'ticket', 'cartao_debito', 'cartao_credito'] as $key) {
        //     $vendas = $caixa->vendas()->whereHas('pagamentos', function($query) use ($key) {
        //         return $query->where('forma_pagamento', '=', $key);
        //     })->get();

        //     $caixa->pagamentos[$key] = (object) [
        //         'valor' => $vendas->sum('valor_total'),
        //         'total' => $vendas->count()
        //     ];

        //     $caixa->pagamentos['total_caixa']->valor += $caixa->pagamentos[$key]->valor;
        // }

        // $caixa->total_gaveta = ($caixa->valor_inicial + ($caixa->pagamentos['dinheiro']->valor ?? 0) + $caixa->entrada_total) - ($caixa->descontos + $caixa->sangria_total);
    }

    public function fechar_caixa($params=null)
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect(route('dashboard'), true);
        }

        if($this->caixa->venda) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Você esta com uma venda em andamento.',
                'icon'        => 'warning'
            ]);
            // return $this->redirect(route('pdv.index'), true);
            return;
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja fechar o Caixa?',
                'acceptLabel' => 'Sim',
                'method'      => 'fechar_caixa',
                'params'      => 'Close',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        DB::beginTransaction();

        try {
            $this->caixa->update([
                'status' => 1
            ]);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Caixa fechado com sucesso.',
                'icon'        => 'success'
            ]);

            DB::commit();

            return $this->redirect(route('dashboard'), true);

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollback();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Falha ao tentar fechar o caixa.',
                'icon'        => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pdv.fechamento.fechamento-index');
    }
}
