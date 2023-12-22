<?php

namespace App\Livewire\Forms\Pdv;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PaymentForm extends Form
{
    public $desconto;
    #[Locked]
    public $informado;
    #[Locked]
    public $troco;

    public $dinheiro;
    public $ticket;
    public $cartao_debito;
    public $cartao_credito;

    #[Locked]
    public $convenio = false;
    public $convenio_nome;
    public $convenio_matricula;

    public function calcular($venda): void
    {
        $this->informado = $this->dinheiro + $this->ticket + $this->cartao_debito + $this->cartao_credito - $this->desconto;
        if($this->informado != 0) {
            $this->troco = $this->informado - $venda->valor_total;
        }else{
            $this->reset('troco');
        }
    }

    public function store($caixa): string|null
    {
        try {
            if($this->dinheiro) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'dinheiro',
                    'valor' => $this->dinheiro
                ]);
            }
    
            if($this->ticket) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'ticket',
                    'valor' => $this->ticket
                ]);
            }
    
            if($this->cartao_debito) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'cartao_debito',
                    'valor' => $this->cartao_debito
                ]);
            }
    
            if($this->cartao_credito) {
                $caixa->venda->pagamentos()->create([
                    'caixa_id' => $caixa->id,
                    'forma_pagamento' => 'cartao_credito',
                    'valor' => $this->cartao_credito
                ]);
            }

            return null;

        } catch (\Throwable $th) {
            //throw $th;

            return $th->getMessage();
        }
    }
}
