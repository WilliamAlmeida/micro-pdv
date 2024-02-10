<?php

namespace App\Livewire\Tenant\Relatorios\CaixaOperando;

use App\Models\Tenant\Caixa;
use Livewire\Attributes\Locked;
use Livewire\Component;
use WireUi\Traits\Actions;

class CaixaOperandoIndex extends Component
{
    use Actions;

    #[Locked]
    public $caixas = [];

    public $caixa_selecionado;

    public function mount()
    {
        $this->caixas = Caixa::with('user:id,name,email')->whereStatus(0)->get();
    }

    public function select_caixa()
    {
        $this->dispatch('set_caixa', $this->caixa_selecionado);
    }

    public function render()
    {
        return view('livewire.tenant.relatorios.caixa-operando.caixa-operando-index');
    }
}
