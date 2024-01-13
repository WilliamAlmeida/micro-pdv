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
    public $convenios;

    #[Locked]
    public $formas_pagamento;

    #[Locked]
    public $fechamento_obrigatorio = false;

    public function mount()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect(route('dashboard'), true);
        }

        if($this->caixa->status) {
            return $this->redirect(route('dashboard'), true);
        }

        $this->fechamento_obrigatorio = !$this->caixa->validDataAbertura();

        if($this->fechamento_obrigatorio) {
            $this->js('
            setTimeout(() => {
                $wireui.dialog({
                    title: "ATENÇÃO!",
                    description: "Fechamento do Caixa Obrigatório, pois esta caixa foi aberto em '.\Carbon\Carbon::parse($this->caixa->created_at)->format('d/m/Y').'!",
                    icon: "warning"
                });
            }, 100);
            ');
        }
    }

    public function caixa_show()
    {
        if(auth()->user()->caixa()->exists()) {
            /* Obtém o objeto 'caixa' associado ao usuário autenticado */
            $this->caixa = auth()->user()->caixa()->with([
                'vendas' => fn($q) => $q->with('pagamentos')->whereStatus(1),
                'entradas', 'sangrias',
                'convenios_recebimentos.pagamentos',
                'convenios_itens_pendentes', 'convenios_itens_cancelados', 'convenios_itens_pagos'
            ])->first();

            /* Verifica se existe o objeto caixa e se há vendas associadas a ele */
            if ($this->caixa && $this->caixa->vendas) {
                $quant_convenios = 0;

                /* Mapeia as vendas para obter os pagamentos, filtrando apenas aqueles com status 1 */
                $pagamentos = $this->caixa->vendas->map(function ($item) use (&$quant_convenios) {
                    $value = $item->pagamentos->map(function ($item) {
                        return $item->only('forma_pagamento', 'valor');
                    });

                    /* Verifica se a forma de pagamento é 'convenio' e ajusta o valor */
                    if ($value->firstWhere('forma_pagamento', 'convenio')) {
                        $value->transform(function ($item_2) use ($item) {
                            $item_2['valor'] = $item->valor_total;
                            return $item_2;
                        });

                        $quant_convenios++;
                    }

                    return $value;

                })->collapse()->groupBy('forma_pagamento')->map(function ($item) {
                    return $item->sum('valor');
                });

                /* Configura a variável $convenios com a quantidade e valor total de pagamentos com 'convenio' */
                $this->convenios['lancados'] = ['quant' => $quant_convenios, 'valor' => ($pagamentos['convenio'] ?? 0)];
                unset($pagamentos['convenio'], $quant_convenios);

                /* Mapeia os pagamentos dos convênios recebidos no caixa */
                $recebimentos = $this->caixa->convenios_recebimentos->map(function ($item) {
                    return $item->pagamentos->map(function ($item) {
                        return $item->only('forma_pagamento', 'valor');
                    });;

                })->collapse()->groupBy('forma_pagamento')->map(function ($item) {
                    return $item->sum('valor');
                });

                /* Adiciona os valores dos recebimentos ao array de pagamentos */
                foreach ($recebimentos as $key => $value) {
                    if ($pagamentos->has($key)) {
                        $pagamentos[$key] += $value;
                    } else {
                        $pagamentos[$key] = $value;
                    }
                }

                /* Limpa a variável $recebimentos e configura a variável $pagamentos */
                unset($recebimentos);
                $this->pagamentos = $pagamentos;

                /* Calcula o total na gaveta considerando entradas, sangrias e pagamentos em dinheiro */
                $this->gaveta_total = $this->caixa->entradas->sum('valor') ?? 0;
                $this->gaveta_total -= $this->caixa->sangrias->sum('valor') ?? 0;
                $this->gaveta_total += $this->pagamentos['dinheiro'] ?? 0;
            }

            // $this->caixa = auth()->user()->caixa()->with([
            //     'vendas' => fn($q) => $q->with('pagamentos')->whereStatus(1),
            //     'entradas', 'sangrias',
            //     'convenios_recebimentos.pagamentos',
            //     'convenios_itens_pendentes', 'convenios_itens_cancelados', 'convenios_itens_pagos'
            // ])->first();

            // if($this->caixa && $this->caixa->vendas) {
            //     $quant_convenios = 0;

            //     $pagamentos = $this->caixa->vendas->map(function($item) use (&$quant_convenios) {
            //         $value = $item->pagamentos->map(function($item) {
            //             return $item->only('forma_pagamento', 'valor');;
            //         });

            //         if($value->firstWhere('forma_pagamento', 'convenio')) {
            //             $value->transform(function($item_2) use ($item) {
            //                 $item_2['valor'] = $item->valor_total;
            //                 return $item_2;
            //             });

            //             $quant_convenios++;
            //         }

            //         return $value;

            //     })->collapse()->groupBy('forma_pagamento')->map(function($item) {
            //         return $item->sum('valor');
            //     });

            //     $this->convenios['lancados'] = ['quant' => $quant_convenios, 'valor' => ($pagamentos['convenio'] ?? 0)];
            //     unset($pagamentos['convenio'], $quant_convenios);

            //     $recebimentos = $this->caixa->convenios_recebimentos->map(function($item) {
            //         $value = $item->pagamentos->map(function($item) {
            //             return $item->only('forma_pagamento', 'valor');;
            //         });

            //         return $value;

            //     })->collapse()->groupBy('forma_pagamento')->map(function($item) {
            //         return $item->sum('valor');
            //     });

            //     foreach($recebimentos as $key => $value) {
            //         if($pagamentos->has($key)) {
            //             $pagamentos[$key] += $value;
            //         }else{
            //             $pagamentos[$key] = $value;
            //         }
            //     }

            //     unset($recebimentos);
            //     $this->pagamentos = $pagamentos;

            //     $this->gaveta_total = $this->caixa->entradas->sum('valor') ?? 0;
            //     $this->gaveta_total -= $this->caixa->sangrias->sum('valor') ?? 0;
            //     $this->gaveta_total += $this->pagamentos['dinheiro'] ?? 0;
            // }
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
