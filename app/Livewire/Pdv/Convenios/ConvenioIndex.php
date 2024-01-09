<?php

namespace App\Livewire\Pdv\Convenios;

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
class ConvenioIndex extends Component
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
    public $convenio_selecionado;

    #[Locked]
    public $formas_pagamento;

    public function mount()
    {
        $this->caixa_show();

        if(!$this->caixa) {
            return $this->redirect('dashboard', true);
        }

        if(!$this->caixa->vendas->count()) {
            return $this->redirect(route('pdv.index'), true);
        }

        if(is_numeric($this->c)) {
            $this->visualizar_convenio($this->c);
        }
    }

    public function caixa_show()
    {
        if(auth()->user()->caixa()->exists()) {
            $this->caixa = auth()->user()->caixa()->with([
                'vendas' => fn($q) => $q->with('pagamentos')->whereIn('status', [4])
            ])->first();

            if($this->caixa && $this->caixa->vendas) {

                $pagamentos = $this->caixa->vendas->map(function($item) {
                    return $item->pagamentos->map(function($item) {
                        return $item->only('forma_pagamento', 'valor');;
                    });
                })->collapse()->groupBy('forma_pagamento')->map(function($item) {
                    return $item->sum('valor');
                });

                $this->caixa->pagamentos = $pagamentos;
            }
        }
    }

    public function cancelar_convenio($params=null)
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

        $convenio = $this->convenio_selecionado;

        if(!$convenio) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Convênio não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Você tem certeza?',
                'description' => 'Deseja cancelar este Convênio?',
                'acceptLabel' => 'Sim',
                'method'      => 'cancelar_convenio',
                'params'      => 'Cancel',
            ]);

            $this->set_focus(['button' => 'cancel']);
            return;
        }

        DB::beginTransaction();

        try {
            $baixa_estoque = [];

            foreach ($convenio->itens as $key => $value) {
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
                    'tipo' => 'convenio_can',
                    'quantidade' => $quantidade
                ];
            }

            if(count($baixas)) {
                $resultado = EstoqueMovimentacoes::insert($baixas);

                if($resultado) {
                    foreach ($convenio->itens as $key => $value) {
                        $value->produtos->update(['estoque_atual' => floatval($value->produtos->estoque_atual) + floatval($baixa_estoque[$value->produtos_id])]);
                    }
                }
            }

            // $convenio->itens()->delete();
            // $convenio->pagamentos()->delete();
            $convenio->update(['status' => 8]);

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Convênio cancelado com sucesso!',
                'icon'        => 'success'
            ]);

            DB::commit();

            $this->mount();

        } catch (\Throwable $th) {
            // throw $th;

            DB::rollBack();

            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Falha ao cancelar a convênio.',
                'icon'        => 'error'
            ]);
        }
    }

    public function visualizar_convenio($id)
    {
        $this->caixa_show();

        $this->convenio_selecionado = $this->caixa->vendas->firstWhere('id', $id);
        $this->c = $id;

        if($this->convenio_selecionado) {
            $value = Str::upper($this->convenio_selecionado->pagamentos->pluck('forma_pagamento')->join(', '));
            $this->formas_pagamento = str_replace('_', ' ', $value);
        }else{
            $this->reset('formas_pagamento');
        }
    }

    public function imprimir_convenio($params=null)
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

        $convenio = $this->convenio_selecionado;

        if(!$convenio) {
            $this->notification([
                'title'       => 'Aviso!',
                'description' => 'Convênio não encontrado.',
                'icon'        => 'error'
            ]);
            return $this->redirect(route('pdv.convenios'), true);
        }

        if($params == null) {
            $this->dialog()->confirm([
                'title'       => 'Deseja Reimprimir esta Convênio?',
                'description' => 'Você não terá como cancelar após a confirmação!',
                'acceptLabel' => 'Sim',
                'method'      => 'imprimir_convenio',
                'params'      => 'Print',
            ]);

            $this->set_focus(['button' => 'confirm']);
            return;
        }

        try {
            $result = $this->printTicket($convenio->id);
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
        return view('livewire.pdv.convenios.convenio-index');
    }
}
