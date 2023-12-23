<?php

namespace App\Livewire\Forms\Pdv;

use App\Models\Clientes;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PaymentForm extends Form
{
    #[Locked]
    public $cliente_selecionado;

    public $convenio = false;
    public $cliente_id;

    public $desconto;
    #[Locked]
    public $informado;
    #[Locked]
    public $troco;

    public $dinheiro;
    public $ticket;
    public $cartao_debito;
    public $cartao_credito;

    public function calculeChangeBack($venda): void
    {
        $this->informado = $this->dinheiro + $this->ticket + $this->cartao_debito + $this->cartao_credito - $this->desconto;
        if($this->informado != 0) {
            $this->troco = $this->informado - $venda->valor_total;
        }else{
            $this->reset('troco');
        }
    }

    public function storePayment($caixa): string|null
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

    public function resetAgreetment(): void
    {
        $this->reset('convenio_nome', 'convenio_matricula', 'convenio_selecionado');
    }

    public function storeAgreetment($caixa): string|null
    {
        try {
            $caixa->venda->pagamentos()->create([
                'caixa_id' => $caixa->id,
                'forma_pagamento' => 'convenio',
                'valor' => 0
            ]);

            return null;

        } catch (\Throwable $th) {
            //throw $th;

            return $th->getMessage();
        }
    }
}
