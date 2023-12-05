<?php

namespace App\Livewire\Pdv\Caixa;

use Livewire\Component;

class CaixaIndex extends Component
{
    public $caixa;
    
    public function mount()
    {
        $this->caixa = $this->caixa_show();
    }

    private function caixa_show()
    {
        if(!auth()->user()->caixa()->exists()) {
            $this->caixa = auth()->user()->caixa()->create([
                'tipo_venda' => 'caixa',
                'status' => 0,
            ]);
        }else{
            $this->caixa = auth()->user()->caixa()->with('vendas')->first();
            if($this->caixa && $this->caixa->venda) {
                $this->caixa->venda->update(['valor_total' => $this->caixa->venda->itens()->sum('valor_total')]);
                $this->caixa->venda->itens;
                $this->caixa->venda->pagamentos;
            }
        }

        return $this->caixa;
    }

    public function render()
    {
        return view('livewire.pdv.caixa.caixa-index');
    }
}
