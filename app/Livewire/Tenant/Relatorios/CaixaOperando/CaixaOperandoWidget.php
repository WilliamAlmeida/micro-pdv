<?php

namespace App\Livewire\Tenant\Relatorios\CaixaOperando;

use Livewire\Component;
use App\Models\Tenant\Caixa;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

#[Lazy]
class CaixaOperandoWidget extends Component
{
    #[Locked]
    public $caixa_selecionado;
    
    public function placeholder()
    {
        return <<<'HTML'
        <div class="w-full flex justify-center items-center">
            Carregando...
        </div>
        HTML;
    }

    public function mount($caixa = null)
    {
        $this->caixa_selecionado = $caixa;
    }

    #[On('set_caixa')]
    public function set_caixa($caixa)
    {
        $this->caixa_selecionado = $caixa;
    }

    public function render()
    {
        $caixa = null;

        if($this->caixa_selecionado) {
            $caixa = Caixa::whereId($this->caixa_selecionado)
            // ->with('vendas','convenios_recebimentos','itens')
            ->with(['venda' => fn($q) => $q->with('itens:id,descricao,quantidade,preco,valor_total,vendas_head_id')])
            ->first();
        }

        return view('livewire.tenant.relatorios.caixa-operando.caixa-operando-widget', ['caixa' => $caixa]);
    }
}
